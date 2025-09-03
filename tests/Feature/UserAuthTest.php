<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserAuthTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local'); // fake filesystem
    }

    /** @test */
    public function user_can_register()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        dd($response->getContent());

        $response->assertStatus(201);
    }

    /** @test */
    public function user_can_login()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token']);
    }
}
