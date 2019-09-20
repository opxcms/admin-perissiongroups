<?php

namespace Modules\Admin\PermissionGroups;

use Illuminate\Support\Facades\Facade;

/**
 * @method  static array getAllPermissionsNested()
 * @method  static string name()
 * @method  static string get($key)
 * @method  static string path($path = '')
 * @method  static string trans($key, $parameters = [], $locale = null)
 * @method  static array|string|null  config($key = null)
 * @method  static mixed view($view)
 */
class AdminPermissionGroups extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'admin_permission_groups';
    }
}
