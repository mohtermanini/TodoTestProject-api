<?php

namespace Tests\Feature\Auth\Auth;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DestroyAuthTest extends TestCase
{
    use RefreshDatabase;

    private $alice;
    protected function setUp(): void
    {
        parent::setUp();
        $this->alice = $this->createMemberUser();
    }
    public function test_user_can_logout()
    {
        Sanctum::actingAs($this->alice);

        $response = $this->deleteJson(route('auth.destroy'));

        $response->assertStatus(204);
    }
}