<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TodoListItem
 *
 * @property integer $id
 * @property integer $todo_list_id
 * @property string $title
 * @property string $type
 * @property integer $position
 * @property integer $after
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class TodoListItem extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'todo_list_item';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'type', 'position', 'after'];
}
