<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Permission as SpatiePermission;

// Roles and permissions templates should extend the templates provided by the Laravel Permission package (SpatieRole and SpatiePermission)
class Permission extends SpatiePermission
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'permissions';


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'guard_name',
    ];


    // without specifying pivot columns manually, as the Laravel Permission package already manages this.
     /*  This function defines a many-to-many relationship - Many Permissions can belong to many Roles */
     public function roles(): BelongsToMany
     {
         return $this->belongsToMany(Role::class);
     }
}
