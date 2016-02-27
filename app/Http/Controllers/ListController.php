<?php

namespace App\Http\Controllers;

use App\Models\TodoList;
use App\Models\TodoListItem;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Mockery\CountValidator\Exception;
use Illuminate\Support\Facades\DB;

class ListController extends Controller
{
    /**
     * @param Request $request
     *
     * @return array
     */
    public function create(Request $request)
    {
        $list   = $request->get('list');
        $id = null;

        if (!$list) {
            throw new Exception('list data can not be empty');
        }

        DB::transaction(function () use ($list, &$id) {
            $listModel = new TodoList([
                'title' => array_get($list, 'title')
            ]);

            $listModel->save();

            foreach (array_get($list, 'items', []) as $item) {
                $todoListItem = new TodoListItem($item);

                $todoListItem->todoList()->associate($listModel);
                $todoListItem->save();
            }

            $id = $listModel->id;
        });

        return TodoList::where(['id' => $id])->with(['todoListItems'])->firstOrFail();
    }

    /**
     * @param int     $id
     * @param Request $request
     *
     * @return mixed
     */
    public function update($id, Request $request)
    {
        $list = $request->get('list');

        DB::transaction(function () use ($list, &$result, $id) {
            $listModel = TodoList::where(['id' => $id])->firstOrFail();

            if (array_get($list, 'title')) {
                $listModel->title = array_get($list, 'title');
                $listModel->save();
            }

            foreach ($listModel->todoListItems as $item) {
                $item->delete();
            }

            foreach (array_get($list, 'items', []) as $listItem) {
                $todoListItem = new TodoListItem($listItem);

                $todoListItem->todoList()->associate($listModel);
                $todoListItem->save();
            }
        });

        return TodoList::where(['id' => $id])->with(['todoListItems'])->firstOrFail();
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function show($id)
    {
        return TodoList::where(['id' => $id])->with(['todoListItems'])->firstOrFail();
    }

    public function buildWay($id)
    {
        throw new Exception('Not implemented');
    }
}
