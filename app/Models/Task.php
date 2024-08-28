<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'task';


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'priority',
        'status',
        'due_date',
        'completeness_date',
        'delete_date',
    ];

    /*  This function defines a one-to-many relationship - One task can have many taskchanges*/
    public function taskchanges(): HasMany
    {
        return $this->hasMany(TaskChange::class);
    }

    /*  This function defines that a Task is associated with one specific User  */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}