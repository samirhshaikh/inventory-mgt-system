<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Http\JsonResponse;
use Tests\Traits\AuthenticationToken;

class UserTest extends TestCase
{
    use AuthenticationToken;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->authenticate($this->user);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        User::where("username", $this->user["username"])->delete();
    }

    /** @test **/
    public function test_change_active_status()
    {
        //Failure
        $this->postJson("users/change-active-status")->assertStatus(
            JsonResponse::HTTP_UNPROCESSABLE_ENTITY
        );

        $this->postJson("users/change-active-status", [
            "Id" => $this->user["username"],
            "value" => "1",
        ])
            ->assertStatus(JsonResponse::HTTP_OK)
            ->assertJsonStructure(["message"]);
    }

    /** @test **/
    public function test_change_admin_status()
    {
        //Failure
        $this->postJson("users/change-admin-status")->assertStatus(
            JsonResponse::HTTP_UNPROCESSABLE_ENTITY
        );

        $this->postJson("users/change-admin-status", [
            "Id" => $this->user["username"],
            "value" => "1",
        ])
            ->assertStatus(JsonResponse::HTTP_OK)
            ->assertJsonStructure(["message"]);
    }

    /** @test **/
    public function test_get_single()
    {
        //Failure
        $this->json("GET", "users/get-single")->assertStatus(
            JsonResponse::HTTP_UNPROCESSABLE_ENTITY
        );

        //Failure
        $user = User::factory()->make();
        $this->json("GET", "users/get-single", [
            "username" => $user["username"],
        ])
            ->assertStatus(JsonResponse::HTTP_NOT_FOUND)
            ->assertJsonStructure(["message"]);

        $this->json("GET", "users/get-single", [
            "username" => $this->user["username"],
        ])
            ->assertStatus(JsonResponse::HTTP_OK)
            ->assertJsonStructure([
                "response" => ["record"],
            ]);
    }

    /** @test **/
    public function test_get_all()
    {
        $this->json("GET", "datatables/users/data")
            ->assertStatus(JsonResponse::HTTP_OK)
            ->assertJsonStructure([
                "total_rows",
                "rows" => [
                    "*" => [
                        "CreatedBy",
                        "CreatedDate",
                        "IsActive",
                        "IsAdmin",
                        "Password",
                        "UpdatedBy",
                        "UpdatedDate",
                        "UserName",
                    ],
                ],
                "page_no",
            ]);
    }

    /** @test **/
    public function test_check_duplicate_name()
    {
        //Failure
        $this->postJson("users/check-duplicate-name")->assertStatus(
            JsonResponse::HTTP_UNPROCESSABLE_ENTITY
        );

        $this->postJson("users/check-duplicate-name", [
            "username" => $this->user["username"],
        ])
            ->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(["message"]);

        $user = User::factory()->make();
        $this->postJson("users/check-duplicate-name", [
            "username" => $user["username"],
        ])->assertStatus(JsonResponse::HTTP_OK);
    }

    /** @test **/
    public function test_delete_user()
    {
        //Failure
        $this->postJson("users/delete")->assertStatus(
            JsonResponse::HTTP_UNPROCESSABLE_ENTITY
        );

        $user = User::factory()->make();
        $this->postJson("users/delete", [
            "username" => $user["username"],
        ])->assertStatus(JsonResponse::HTTP_NOT_FOUND);

        $this->postJson("users/delete", [
            "username" => $this->user["username"],
        ])
            ->assertStatus(JsonResponse::HTTP_OK)
            ->assertJsonStructure(["message"]);
    }

    /** @test **/
    public function test_save_user()
    {
        //Failure
        $this->postJson("users/save")->assertStatus(
            JsonResponse::HTTP_UNPROCESSABLE_ENTITY
        );

        $this->postJson("users/save", [
            "UserName" => $this->user["username"],
            "operation" => "add",
        ])->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY);

        $this->postJson("users/save", [
            "UserName" => $this->user["username"],
            "Password" => "test_password",
            "operation" => "edit",
        ])
            ->assertStatus(JsonResponse::HTTP_OK)
            ->assertJsonStructure(["message", "response" => ["username"]]);

        $user = User::factory()->make();
        $this->postJson("users/save", [
            "UserName" => $user["username"],
            "Password" => $user["password"],
        ])
            ->assertStatus(JsonResponse::HTTP_OK)
            ->assertJsonStructure(["message", "response" => ["username"]]);
        User::where("username", $user["username"])->delete();
    }
}
