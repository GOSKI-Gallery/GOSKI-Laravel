<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SupabaseAuthService;
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

    public function remove($id){
        $user = User::findOrFail($id);
        return view('admin.users.remove', compact('user'));
    }

    public function destroy($id, Request $request, SupabaseAuthService $authService)
    {
        $user = User::findOrFail($id);
        
        // Verifica se o username foi digitado corretamente
        if ($request->input('username') !== $user->username) {
            return redirect()->back()->withErrors(['username' => 'Username incorreto']);
        }
        
        try {
            // Deleta do Supabase Auth
            $authService->deleteUser($user->id);
            
            // Deleta do banco de dados
            $user->delete();
            
            return redirect()->route('admin.users.index')->with('success', 'Usuário deletado com sucesso');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Erro ao deletar usuário: ' . $e->getMessage()]);
        }
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