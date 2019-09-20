<?php

return [
    'items' => [
        'groups' => [
            'caption' => 'admin_permission_groups::manage.groups',
            'route' => 'admin_permission_groups::groups_list',
            'section' => 'system/settings',
            'permission' => 'admin_permission_groups::edit',
        ],
    ],

    'routes' => [
        'admin_permission_groups::groups_list' => [
            'route' => '/admin/groups',
            'loader' => 'manage/api/module/admin_permission_groups/permission_groups_list',
        ],
        'admin_permission_groups::groups_add' => [
            'route' => '/admin/groups/add',
            'loader' => 'manage/api/module/admin_permission_groups/permission_groups_edit/add',
        ],
        'admin_permission_groups::groups_edit' => [
            'route' => '/admin/groups/edit/:id',
            'loader' => 'manage/api/module/admin_permission_groups/permission_groups_edit/edit',
        ],
    ]
];