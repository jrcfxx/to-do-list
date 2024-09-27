<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Models\Role as SpatieRole;


//Roles and permissions templates should extend the templates provided by the Laravel Permission package (SpatieRole and SpatiePermission)
class Role extends SpatieRole
{   

    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'guard_name'];

    /*  This function defines a many-to-many relationship - Many Roles can have many Permissions */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class,'role_has_permissions');
    }
    
    /*  This function defines a one-to-many relationship - Role belongs to many Users */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
