<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Todo;

class TodoEloquent extends Controller
{
    
    public function index(){
        // READ
        $todos = Todo::all();      
    }

    public function single(){
        // SINGLE
        $todo = Todo::find(1);
    }
    
    public function store() {
        // STORE
        $todo = new Todo();
        $todo->title = 'Learn Laravel';
        $todo->completed = false;
        $todo->save();
    }

    public function destroy(){
        // DELETE
        $todo = Todo::find(1);
        $todo->delete();
    }

    public function update(){
        // UPDATE
        $todo = Todo::find(1);
        $todo->title = 'Learn Laravel deeply';
        $todo->save();
    }

}
