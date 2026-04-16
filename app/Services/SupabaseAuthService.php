<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class SupabaseAuthService extends SupabaseBaseService
{
    public function signUp(string $email, string $password, string $username)
    {
        return $this->client(false)->post("{$this->url}/auth/v1/signup", [
            'email' => $email,
            'password' => $password,
            'data' => ['username' => $username],
        ])->json();
    }

    public function signIn(string $email, string $password)
    {
        return $this->client(false)->post("{$this->url}/auth/v1/token?grant_type=password", [
            'email' => $email,
            'password' => $password,
        ])->json();
    }

    public function getUser(string $token)
    {
        return Http::withHeaders([
            'apikey' => $this->anonKey,
            'Authorization' => "Bearer {$token}",
        ])->get("{$this->url}/auth/v1/user")->json();
    }

    public function updateUser(string $userId, array $validatedData, $avatar = null)
    {
        $dbUpdateData = [];
        $authUpdateResponse = null;

        if (!empty($validatedData['password'])) {
            $authUpdateResponse = $this->client()
                ->put("{$this->url}/auth/v1/admin/users/{$userId}", [
                    'password' => $validatedData['password']
                ])->json();
            
            if (isset($authUpdateResponse['error'])) {
                return $authUpdateResponse;
            }
        }

        if ($avatar) {
            $fileName = 'profiles/' . $userId . '/' . time() . '.' . $avatar->extension();
            $this->uploadImage('profiles', $fileName, $avatar);
            $dbUpdateData['profile_photo_url'] = $this->getPublicUrl('profiles', $fileName);
        }

        if (!empty($validatedData['username'])) {
            $dbUpdateData['username'] = $validatedData['username'];
        }

        if (count($dbUpdateData) > 0) {
            return $this->client()->patch("{$this->url}/rest/v1/users?id=eq.{$userId}", $dbUpdateData)->json();
        }

        return $authUpdateResponse ?? ['success' => 'No data to update'];
    }

    public function uploadImage(string $bucket, string $path, $file)
    {
        $url = "{$this->url}/storage/v1/object/{$bucket}/{$path}";
        
        return Http::withHeaders([
            'apikey' => $this->anonKey,
            'Authorization' => "Bearer {$this->key}",
            'Content-Type' => $file->getMimeType(),
        ])->withBody(file_get_contents($file->getRealPath()), $file->getMimeType())
        ->post($url)
        ->throw()
        ->json();
    }

    public function getPublicUrl(string $bucket, string $path)
    {
        return "{$this->url}/storage/v1/object/public/{$bucket}/{$path}";
    }
}
