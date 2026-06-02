<?php

namespace Tests\Unit;

use App\Http\Requests\Post\CreatePostRequest;
use App\Http\Requests\User\RegisterUserRequest;
use PHPUnit\Framework\TestCase;

class RequestRulesTest extends TestCase
{
    public function test_create_post_request_rules(): void
    {
        $request = new CreatePostRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('description', $rules);
        $this->assertStringContainsString('required', $rules['description']);
        $this->assertStringContainsString('string', $rules['description']);
        $this->assertStringContainsString('max:255', $rules['description']);

        $this->assertArrayHasKey('image_url', $rules);
        $this->assertStringContainsString('required', $rules['image_url']);
        $this->assertStringContainsString('image', $rules['image_url']);
    }

    public function test_create_post_request_is_authorized(): void
    {
        $request = new CreatePostRequest();
        $this->assertTrue($request->authorize());
    }

    public function test_register_user_request_rules(): void
    {
        $request = new RegisterUserRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('username', $rules);
        $this->assertStringContainsString('required', $rules['username']);
        $this->assertStringContainsString('unique:users,username', $rules['username']);

        $this->assertArrayHasKey('email', $rules);
        $this->assertStringContainsString('required', $rules['email']);

        $this->assertArrayHasKey('password', $rules);
        $this->assertStringContainsString('required', $rules['password']);
        $this->assertStringContainsString('min:6', $rules['password']);

        $this->assertArrayHasKey('password_confirmation', $rules);
        $this->assertStringContainsString('required', $rules['password_confirmation']);
        $this->assertStringContainsString('same:password', $rules['password_confirmation']);
    }

    public function test_register_user_request_is_authorized(): void
    {
        $request = new RegisterUserRequest();
        $this->assertTrue($request->authorize());
    }
}
