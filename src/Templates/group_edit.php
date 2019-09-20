<?php

namespace Modules\Shop\Properties\Templates;

use Core\Foundation\Template\Template;
use Modules\Admin\PermissionGroups\AdminPermissionGroups;

/**
 * HELP:
 *
 * ID parameter is shorthand for defining module and field name separated by `::`.
 * [$module, $name] = explode('::', $id, 2);
 * $captionKey = "{$module}::template.section_{$name}";
 *
 * PLACEMENT is shorthand for section and group of field separated by `/`.
 * [$section, $group] = explode('/', $placement);
 *
 * PERMISSIONS is shorthand for read permission and write permission separated by `|`.
 * [$readPermission, $writePermission] = explode('|', $permissions, 2);
 */

return [
    'sections' => [
        Template::section('admin_permission_groups::general'),
    ],
    'groups' => [
        Template::group('admin_permission_groups::common'),
        Template::group('admin_permission_groups::permissions'),
        Template::group('admin_permission_groups::timestamps'),
    ],
    'fields' => [
        // id
        Template::id('admin_permission_groups::id', 'general/common', 'admin_permission_groups::template.info_id'),
        // name
        Template::string('admin_permission_groups::name', 'general/common', '', [], '', 'required|unique:permission_groups,name,%id'),
        // default
        Template::checkbox('admin_permission_groups::default', 'general/common', false, 'admin_permission_groups::template.info_default'),
        // permissions
        Template::checkboxGroupedList('admin_permission_groups::permissions', 'general/permissions', [], AdminPermissionGroups::getAllPermissionsNested()),
        // timestamps
        Template::datetime('admin_permission_groups::created_at', 'general/timestamps', null, '', '', '|none'),
        Template::datetime('admin_permission_groups::updated_at', 'general/timestamps', null, '', '', '|none'),
        Template::datetime('admin_permission_groups::deleted_at', 'general/timestamps', null, '', '', '|none'),
    ],
];
