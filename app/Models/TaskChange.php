<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class TaskChange extends Model
{
    use HasFactory;


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'task_change';


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'task_id',
        'change_date',
        'changed_field',
        'changed_content',
    ];

    /*  This function defines that a TaskChange is associated with one specific Task  */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

}

