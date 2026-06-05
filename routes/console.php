<?php

use App\Services\SupabaseAuthService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('db:fresh-supabase', function () {
    $this->components->warn('Este comando irá:');
    $this->line('  1. Rodar migrate:fresh (apagar todas as tabelas Laravel e recriá-las)');
    $this->line('  2. Apagar TODOS os usuários do auth.users (Supabase Auth) via Admin API');
    $this->line('');

    if (! $this->components->confirm('Tem certeza que deseja continuar?')) {
        $this->components->info('Comando cancelado.');

        return;
    }

    $this->components->task('Migrate:fresh', function () {
        Artisan::call('migrate:fresh', ['--force' => true]);
    });

    $this->components->task('Limpando auth.users via Admin API', function () use (&$deleted) {
        $authService = app(SupabaseAuthService::class);
        $deleted = $authService->deleteAllUsers();
    });

    $this->components->info('Usuários removidos do auth.users: '.count($deleted));

    if ($this->components->confirm('Deseja rodar db:seed?', true)) {
        $this->components->task('db:seed', function () {
            Artisan::call('db:seed', ['--force' => true]);
        });
    }

    $this->components->success('Pronto! Ambiente limpo e recriado.');
})->purpose('Recreate DB & clean Supabase Auth users');
