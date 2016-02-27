<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TodoList
 *
 * @property integer $id
 * @property string $title
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TodoListItem[] $todoListItems
 */
class TodoList extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'todo_list';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function todoListItems()
    {
        return $this->hasMany(TodoListItem::class);
    }

    public function getPoints()
    {
        return [
            ["82.910206","55.04982"],
            ["82.903137","55.060355"],
            ["82.954886","55.013059"],
        ];
    }
}
