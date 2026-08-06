<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class SupabasePushTokenService extends SupabaseBaseService
{
    private string $prefix;

    public function __construct()
    {
        parent::__construct();

        $this->prefix = DB::getDriverName() === 'pgsql' ? 'laravel.' : '';
    }

    public function upsertToken(string $userId, string $token, ?string $platform = null): bool
    {
        return DB::table($this->prefix.'push_tokens')
            ->updateOrInsert(
                ['user_id' => $userId],
                [
                    'token' => $token,
                    'platform' => $platform,
                    'updated_at' => now(),
                ]
            );
    }

    public function getTokensByUserId(string $userId): array
    {
        return DB::table($this->prefix.'push_tokens')
            ->where('user_id', $userId)
            ->get()
            ->map(fn ($row) => [
                'id' => (int) $row->id,
                'user_id' => $row->user_id,
                'token' => $row->token,
                'platform' => $row->platform,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ])
            ->values()
            ->all();
    }

    public function getPushTokensByUser(string $userId): array
    {
        return $this->getTokensByUserId($userId);
    }

    public function deleteToken(string $token): bool
    {
        return DB::table($this->prefix.'push_tokens')
            ->where('token', $token)
            ->delete() > 0;
    }
}
