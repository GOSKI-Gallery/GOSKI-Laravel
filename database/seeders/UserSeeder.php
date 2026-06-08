<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\SupabaseAuthService;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    protected $supabase;

    public function __construct(SupabaseAuthService $supabase)
    {
        $this->supabase = $supabase;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createSupabaseUser([
            'email' => 'admin@teste.com',
            'password' => 'SenhaSegura123',
            'username' => 'AdminMaster',
            'role' => 'admin',
        ]);

        User::factory(3)->make()->each(function ($userFake) {
            $this->createSupabaseUser([
                'email' => $userFake->email,
                'password' => 'password123',
                'username' => $userFake->username ?? 'user_'.rand(1, 999),
            ]);
        });
    }

    private function createSupabaseUser(array $data)
    {
        $response = $this->supabase->signUp(
            $data['email'],
            $data['password'],
            $data['username']
        );

        if (isset($response['error_code']) || isset($response['error'])) {
            $error = $response['msg'] ?? $response['error']['message'] ?? 'Erro desconhecido';
            $this->command->error("Falha: {$data['email']} -> ".$error);

            return;
        }

        $userId = $response['id'] ?? $response['user']['id'] ?? null;

        if ($userId !== null && isset($data['role'])) {
            User::where('id', $userId)->update(['role' => $data['role']]);
        }

        $this->command->info("Sucesso: Usuário {$data['username']} criado no Supabase!");
    }
}
