<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.users.index', [
            'users' => [
                [
                    'id' => 1,  
                    'name' => 'carlos',
                ],
                [
                    'id' => 2,  
                    'name' => 'julia',
                ]
            ]
        ]);
    }

    public function detail()
    {
        return view('admin.users.detail', [
            'user' => [
                'id' => 1,  
                'name' => 'carlos',
            ]
        ]);
    }

    public function remove(){
        return view('admin.users.remove', [
            'user' => [
                'id' => 1,  
                'name' => 'carlos',
            ]
        ]);
    }

       public function delete(Request $request){
        if ($request->has('id')) {
            return redirect()->back()->with('error', 'Erro ao deletar o usuario. Tente novamente');
        }

        else {
            return redirect()->route('admin.users.index')->with('success', 'Usuario deletado com sucesso');
        }
    }
}