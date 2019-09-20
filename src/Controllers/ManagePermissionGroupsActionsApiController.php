<?php

namespace Modules\Admin\PermissionGroups\Controllers;

use Core\Http\Controllers\APIListController;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;
use Modules\Admin\Authorization\AdminAuthorization;
use Modules\Admin\PermissionGroups\Models\PermissionGroup;

class ManagePermissionGroupsActionsApiController extends APIListController
{
    /**
     * Delete groups with given ids.
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function postDelete(Request $request): JsonResponse
    {
        if(!AdminAuthorization::can('admin_permission_groups::edit')) {
            return $this->returnNotAuthorizedResponse();
        }

        $ids = $request->all();

        /** @var EloquentBuilder $groups */
        $groups = PermissionGroup::query()->whereIn('id', $ids)->get();

        if ($groups->count() > 0) {
            /** @var PermissionGroup $group */
            foreach ($groups as $group) {
                $group->delete();
            }
        }

        return response()->json(['message' => 'success']);
    }

    /**
     * Restore groups with given ids.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function postRestore(Request $request): JsonResponse
    {
        if(!AdminAuthorization::can('admin_permission_groups::edit')) {
            return $this->returnNotAuthorizedResponse();
        }

        $ids = $request->all();

        /** @var EloquentBuilder $groups */
        $groups = PermissionGroup::query()->whereIn('id', $ids)->onlyTrashed()->get();

        if ($groups->count() > 0) {
            /** @var PermissionGroup $group */
            foreach ($groups as $group) {
                $group->restore();
            }
        }

        return response()->json(['message' => 'success']);
    }
}