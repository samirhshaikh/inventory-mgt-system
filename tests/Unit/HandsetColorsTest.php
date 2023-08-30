<?php

namespace Tests\Unit;

use App\Models\HandsetColors;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;
use Tests\Traits\AuthenticationToken;

class HandsetColorsTest extends TestCase
{
    use AuthenticationToken;

    protected User $user;
    protected HandsetColors $handset_color;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->handset_color = HandsetColors::factory()->create();
        $this->handset_color["id"] = $this->handset_color::lastInsertId();

        $this->authenticate($this->user);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        User::where("username", $this->user["username"])->delete();
        HandsetColors::where("name", $this->handset_color["name"])->delete();
    }

    /** @test **/
    public function test_change_active_status()
    {
        //Failure
        $this->postJson("handset-colors/change-active-status")->assertStatus(
            JsonResponse::HTTP_UNPROCESSABLE_ENTITY
        );

        $this->postJson("handset-colors/change-active-status", [
            "Id" => $this->handset_color["id"],
            "value" => "1",
        ])
            ->assertStatus(JsonResponse::HTTP_OK)
            ->assertJsonStructure(["message"]);
    }
}
