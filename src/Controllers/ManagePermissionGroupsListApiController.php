<?php

namespace Modules\Admin\PermissionGroups\Controllers;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Core\Http\Controllers\APIListController;
use Modules\Admin\Authorization\AdminAuthorization;
use Modules\Admin\PermissionGroups\Models\PermissionGroup;

class ManagePermissionGroupsListApiController extends APIListController
{
    public $caption = 'admin_permission_groups::manage.groups';
    public $source = 'manage/api/module/admin_permission_groups/permission_groups_list/groups';

    public $delete = 'manage/api/module/admin_permission_groups/permission_groups_actions/delete';
    public $restore = 'manage/api/module/admin_permission_groups/permission_groups_actions/restore';

    public $add = 'admin_permission_groups::groups_add';
    public $edit = 'admin_permission_groups::groups_edit';

    public $children = false;

    public $filters = [
        'show_deleted' => [
            'caption' => 'admin_permission_groups::manage.filter_by_deleted',
            'type' => 'checkbox',
            'enabled' => false,
            'value' => 'deleted',
            'options' => ['deleted' => 'admin_permission_groups::manage.filter_value_deleted', 'only_deleted' => 'admin_permission_groups::manage.filter_value_only_deleted'],
        ],
    ];

    /**
     * Returns list component with associated settings.
     *
     * @return  JsonResponse
     */
    public function getIndex(): JsonResponse
    {
        if(!AdminAuthorization::can('admin_permission_groups::edit')) {
            return $this->returnNotAuthorizedResponse();
        }

        return $this->responseListComponent();
    }

    /**
     * Get list of groups with filters.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     */
    public function postGroups(Request $request): JsonResponse
    {
        if(!AdminAuthorization::can('admin_permission_groups::edit')) {
            return $this->returnNotAuthorizedResponse();
        }

        $filters = $request->input('filters');

        $groups = $this->makeQuery();

        $groups = $this->applyFilters($groups, $filters);

        $groups = $groups->paginate(50);

        /** @var Collection $groups */
        if ($groups->count() > 0) {
            $groups->transform(function ($group) {
                return $this->formatGroup($group);
            });
        }

        $response = $groups->toArray();

        return response()->json($response);
    }

    /**
     * Format property to list item.
     *
     * @param PermissionGroup $group
     *
     * @return  array
     */
    protected function formatGroup(PermissionGroup $group):array
    {
        return $this->makeListRecord(
            $group->getAttribute('id'),
            $group->getAttribute('name'),
            null,
            null,
            (bool)$group->getAttribute('default') ? 'admin_permission_groups::manage.default' : null,
            true,
            $group->getAttribute('deleted_at') !== null
        );
    }

    /**
     * Make base list query.
     *
     * @return  EloquentBuilder
     */
    protected function makeQuery(): EloquentBuilder
    {
        /** @var EloquentBuilder $query */
        $query = PermissionGroup::query()->select('permission_groups.*');

        return $query;
    }

    /**
     * Apply filters to query.
     *
     * @param EloquentBuilder $query
     * @param array $filters
     *
     * @return  EloquentBuilder
     */
    public function applyFilters(EloquentBuilder $query, $filters): EloquentBuilder
    {
        if (isset($filters['show_deleted'])) {
            if ($filters['show_deleted'] === 'deleted') {
                $query->withTrashed();
            } elseif ($filters['show_deleted'] === 'only_deleted') {
                $query->onlyTrashed();
            }
        }
        return $query;
    }
}