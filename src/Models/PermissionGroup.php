<?php

namespace Modules\Admin\PermissionGroups\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermissionGroup extends Model
{
    use SoftDeletes;

    protected $table = 'permission_groups';

    protected $dates = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    /**
     * Get all properties.
     *
     * @return  BelongsToMany
     */
//    public function properties(): BelongsToMany
//    {
//        return $this->belongsToMany(Property::class, 'property_group_has_property');
//    }
}