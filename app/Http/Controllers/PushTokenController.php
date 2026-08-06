<?php

namespace App\Http\Controllers;

use App\Services\SupabasePushTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PushTokenController extends Controller
{
    protected SupabasePushTokenService $pushTokenService;

    public function __construct(SupabasePushTokenService $pushTokenService)
    {
        $this->pushTokenService = $pushTokenService;
    }

    public function index(): JsonResponse
    {
        $tokens = $this->pushTokenService->getTokensByUserId((string) Auth::id());

        return response()->json([
            'success' => true,
            'data' => $tokens,
        ]);
    }

    public function destroy(string $token): JsonResponse
    {
        $deleted = $this->pushTokenService->deleteToken($token);

        if (! $deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Não foi possível remover o token.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Token removido com sucesso.',
        ]);
    }
}
