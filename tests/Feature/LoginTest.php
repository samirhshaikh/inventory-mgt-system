<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

class LoginTest extends TestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        User::where("username", $this->user["username"])->delete();
    }

    /** @test **/
    public function test_login_fail()
    {
        //Failure
        $this->postJson("doLogin")
            ->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(["errors"]);

        //Failure
        $this->postJson("doLogin", [
            "username" => $this->user["username"],
            "password" => "test123",
        ])
            ->assertStatus(JsonResponse::HTTP_UNAUTHORIZED)
            ->assertJsonStructure(["error"]);
    }

    /** @test **/
    public function test_valid_login()
    {
        $this->postJson("doLogin", [
            "username" => $this->user["username"],
            "password" => "password",
        ])
            ->assertStatus(JsonResponse::HTTP_OK)
            ->assertJsonStructure(["api_token"]);
    }
}
