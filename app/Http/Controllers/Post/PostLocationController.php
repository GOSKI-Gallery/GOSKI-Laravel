<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Services\StaticMapService;
use App\Services\SupabasePostService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostLocationController extends Controller
{
    protected SupabasePostService $supabase;

    public function __construct(SupabasePostService $supabase)
    {
        $this->supabase = $supabase;
    }

    public function show(string $postId, Request $request)
    {
        $prefix = DB::getDriverName() === 'pgsql' ? 'laravel.' : '';

        $post = DB::table($prefix.'posts')
            ->join($prefix.'users', 'posts.user_id', '=', 'users.id')
            ->where('posts.id', $postId)
            ->select(
                'posts.id',
                'posts.user_id',
                'posts.image_url',
                'posts.description',
                'posts.latitude',
                'posts.longitude',
                'posts.location_name',
                'posts.created_at',
                'users.username',
                'users.profile_photo_url',
            )
            ->first();

        if ($post === null || $post->latitude === null || $post->longitude === null) {
            return response()->json([
                'success' => false,
                'message' => 'Post sem localização.',
            ], 404);
        }

        $nearby = $this->supabase->getNearbyPosts(
            $postId,
            (float) $post->latitude,
            (float) $post->longitude
        );

        $latitude = (float) $post->latitude;
        $longitude = (float) $post->longitude;

        $map = null;
        if (is_numeric($request->query('width')) && is_numeric($request->query('height'))) {
            $width = max(100, min(1200, (int) $request->query('width')));
            $height = max(100, min(1200, (int) $request->query('height')));

            $mapService = app(StaticMapService::class);

            $map = $mapService->tileGrid($latitude, $longitude, $width, $height);

            $map['pins'] = array_map(
                fn (array $nearbyPost) => array_merge($nearbyPost, $mapService->pixelOffset(
                    (float) $nearbyPost['latitude'],
                    (float) $nearbyPost['longitude'],
                    $latitude,
                    $longitude,
                    $width,
                    $height,
                    $map['zoom']
                )),
                $nearby
            );
        }

        return response()->json([
            'success' => true,
            'post' => [
                'id' => (int) $post->id,
                'user_id' => $post->user_id,
                'image_url' => $post->image_url,
                'description' => $post->description,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'location_name' => $post->location_name,
                'created_at' => $post->created_at,
                'users' => [
                    'id' => $post->user_id,
                    'username' => $post->username,
                    'profile_photo_url' => $post->profile_photo_url,
                ],
            ],
            'nearby' => $nearby,
            'map' => $map,
        ]);
    }
}
