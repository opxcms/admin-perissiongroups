<?php

namespace Modules\Admin\PermissionGroups\Controllers;

use Core\Foundation\Templater\Templater;
use Core\Http\Controllers\APIFormController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Authorization\AdminAuthorization;
use Modules\Admin\PermissionGroups\AdminPermissionGroups;
use Modules\Admin\PermissionGroups\Models\PermissionGroup;

class ManagePermissionGroupsEditApiController extends APIFormController
{
    public $addCaption = 'admin_permission_groups::manage.add_group';
    public $editCaption = 'admin_permission_groups::manage.edit_group';
    public $create = 'manage/api/module/admin_permission_groups/permission_groups_edit/create';
    public $save = 'manage/api/module/admin_permission_groups/permission_groups_edit/save';
    public $redirect = '/admin/groups/edit/';

    /**
     * Make group add form.
     *
     * @return  JsonResponse
     */
    public function getAdd(): JsonResponse
    {
        if(!AdminAuthorization::can('admin_permission_groups::edit')) {
            return $this->returnNotAuthorizedResponse();
        }

        $template = new Templater(AdminPermissionGroups::path('Templates' . DIRECTORY_SEPARATOR . 'group_add.php'));

        $template->fillDefaults();

        return $this->responseFormComponent(0, $template, $this->addCaption, $this->create);
    }

    /**
     * Make group edit form.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     */
    public function getEdit(Request $request): JsonResponse
    {
        if(!AdminAuthorization::can('admin_permission_groups::edit')) {
            return $this->returnNotAuthorizedResponse();
        }

        /** @var PermissionGroup $group */
        $id = $request->input('id');
        $group = PermissionGroup::withTrashed()->where('id', $id)->firstOrFail();

        $template = $this->makeTemplate($group, 'group_edit.php');

        return $this->responseFormComponent($id, $template, $this->editCaption, $this->save);
    }

    /**
     * Create new group.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     */
    public function postCreate(Request $request): JsonResponse
    {
        if(!AdminAuthorization::can('admin_permission_groups::edit')) {
            return $this->returnNotAuthorizedResponse();
        }

        $template = new Templater(AdminPermissionGroups::path('Templates' . DIRECTORY_SEPARATOR . 'group_add.php'));

        $template->resolvePermissions();

        $template->fillValuesFromRequest($request);

        if (!$template->validate()) {
            return $this->responseValidationError($template->getValidationErrors());
        }

        $values = $template->getEditableValues();

        $group = $this->updateGroupData(new PermissionGroup(), $values);

        // Refill template
        $template = $this->makeTemplate($group, 'group_edit.php');
        $id = $group->getAttribute('id');

        return $this->responseFormComponent($id, $template, $this->editCaption, $this->save, $this->redirect . $id);
    }

    /**
     * Save group.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     */
    public function postSave(Request $request): JsonResponse
    {
        if(!AdminAuthorization::can('admin_permission_groups::edit')) {
            return $this->returnNotAuthorizedResponse();
        }

        /** @var PermissionGroup $group */
        $id = $request->input('id');

        $group = PermissionGroup::withTrashed()->where('id', $id)->firstOrFail();

        $template = new Templater(AdminPermissionGroups::path('Templates' . DIRECTORY_SEPARATOR . 'group_edit.php'));

        $template->resolvePermissions();

        $template->fillValuesFromRequest($request);

        if (!$template->validate(['id' => $id])) {
            return $this->responseValidationError($template->getValidationErrors());
        }

        $values = $template->getEditableValues();

        $group = $this->updateGroupData($group, $values);

        // Refill template
        $template = $this->makeTemplate($group, 'group_edit.php');

        return $this->responseFormComponent($id, $template, $this->editCaption, $this->save);
    }

    /**
     * Fill template with data.
     *
     * @param string $filename
     * @param PermissionGroup $group
     *
     * @return  Templater
     */
    protected function makeTemplate(PermissionGroup $group, $filename): Templater
    {
        $template = new Templater(AdminPermissionGroups::path('Templates' . DIRECTORY_SEPARATOR . $filename));

        $template->fillValuesFromObject($group);

        if ($group->exists) {
            $permissions = $this->getPermissions($group->getAttribute('id'));

            $template->setValues(['permissions' => $permissions]);
        }

        return $template;
    }

    /**
     * Update property's data
     *
     * @param PermissionGroup $group
     * @param array $data
     *
     * @return  PermissionGroup
     */
    protected function updateGroupData(PermissionGroup $group, array $data): PermissionGroup
    {
        $this->setAttributes($group, $data, [
            'name', 'default',
        ]);

        $group->save();

        $permissions = $data['permissions'];

        $this->syncPermissions($group->getAttribute('id'), $permissions);

        return $group;
    }

    /**
     * Sync permissions to group.
     *
     * @param int $id
     * @param array $permissions
     *
     * @return  void
     */
    protected function syncPermissions(int $id, array $permissions): void
    {
        DB::table('permission_group_has_permission')
            ->where('group_id', $id)
            ->whereNotIn('permission', $permissions)
            ->delete();

        $actual = $this->getPermissions($id);

        $missing = array_diff($permissions, $actual);

        foreach ($missing as &$item) {
            $item = ['group_id' => $id, 'permission' => $item];
        }
        unset($item);

        DB::table('permission_group_has_permission')->insert($missing);
    }

    /**
     * Get permissions list for group by id.
     *
     * @param int $id
     *
     * @return  array
     */
    protected function getPermissions(int $id): array
    {
        $permissions = DB::table('permission_group_has_permission')
            ->where('group_id', $id)
            ->pluck('permission');

        return $permissions->toArray();
    }
}