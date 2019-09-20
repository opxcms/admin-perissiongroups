<?php

namespace Modules\Admin\PermissionGroups;

use Core\Foundation\Module\BaseModule;

class PermissionGroups extends BaseModule
{
    /** @var string  Module name */
    protected $name = 'admin_permission_groups';

    /** @var string  Module path */
    protected $path = __DIR__;

    public function getAllPermissionsNested(): array
    {
        $moduleNames = array_keys($this->app->getModulesList());

        $result = [];

        foreach ($moduleNames as $moduleName) {
            $module = $this->app->getModule($moduleName);

            if ($module === null) {
                continue;
            }

            $permissionsFileName = $module->path('Manage' . DIRECTORY_SEPARATOR . 'permissions.php');

            if (!file_exists($permissionsFileName)) {
                continue;
            }

            /** @var array $permissions */
            $permissions = require $permissionsFileName;

            if(empty($permissions)) {
                continue;
            }

            $group = ['children' => []];
            $group['caption'] = trans("{$moduleName}::manage.module_name");
            foreach ($permissions as $permission) {
                $group['children'][] = ['id' => "{$moduleName}::{$permission}", 'caption' => trans("{$moduleName}::permissions.{$permission}")];
            }
            $result[] = $group;

        }

        return $result;
    }
}
