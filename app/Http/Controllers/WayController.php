<?php

namespace App\Http\Controllers;

use App\Models\TodoList;
use App\Models\Way;

class WayController extends Controller
{
    public function show($id)
    {
        return Way::build(TodoList::findOrFail($id));
    }
}
