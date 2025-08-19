<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;



class TodoQueryBuilder extends Controller
{
    

    public function index(){
        // CREATE
        DB::insert("INSERT INTO todos (title, completed) VALUES (?, ?)", [
            'Learn Laravel', false
        ]);
        // READ
        $todos = DB::select("SELECT * FROM todos");
        $todo = DB::select("SELECT * FROM todos WHERE id = ?", [1]);
        // UPDATE
        DB::update("UPDATE todos SET title = ? WHERE id = ?", [
            'Learn Laravel deeply', 1
        ]);
        // DELETE
        DB::delete("DELETE FROM todos WHERE id = ?", [1]);
    }

    public function something(Request $request){

    }

}
