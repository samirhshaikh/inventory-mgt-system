<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Http\JsonResponse;
use Tests\Traits\AuthenticationToken;

class UserTest extends TestCase
{
    use AuthenticationToken;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->authenticate($this->user);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        User::where('username', $this->user['username'])->delete();
    }

    /** @test **/
    public function testChangeActiveStatus()
    {
        //Failure
        $this
            ->json('POST', 'users/change-active-status')
            ->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        ;

        $this
            ->json('POST', 'users/change-active-status', [
                'Id' => $this->user['username'],
                'value' => '1'
            ])
            ->assertStatus(JsonResponse::HTTP_OK)
            ->assertJsonStructure([
                'message'
            ])
        ;
    }

    /** @test **/
    public function testChangeAdminStatus()
    {
        //Failure
        $this
            ->json('POST', 'users/change-admin-status')
            ->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        ;

        $this
            ->json('POST', 'users/change-admin-status', [
                'Id' => $this->user['username'],
                'value' => '1'
            ])
            ->assertStatus(JsonResponse::HTTP_OK)
            ->assertJsonStructure([
                'message'
            ])
        ;
    }

    /** @test **/
    public function testGetSingle()
    {
        //Failure
        $this
            ->json('GET', 'users/get-single')
            ->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        ;

        //Failure
        $user = User::factory()->make();
        $this
            ->json('GET', 'users/get-single', [
                'username' => $user['username']
            ])
            ->assertStatus(JsonResponse::HTTP_NOT_FOUND)
            ->assertJsonStructure([
                'message'
            ])
        ;

        $this
            ->json('GET', 'users/get-single', [
                'username' => $this->user['username']
            ])
            ->assertStatus(JsonResponse::HTTP_OK)
            ->assertJsonStructure([
                'response' => [
                    'record'
                ]
            ])
        ;
    }

    /** @test **/
    public function testGetAll()
    {
        $this
            ->json('GET', 'datatables/users/data')
            ->assertStatus(JsonResponse::HTTP_OK)
            ->assertJsonStructure([
                'total_rows',
                'rows' => [
                    '*' => [
                        'CreatedBy',
                        'CreatedDate',
                        'IsActive',
                        'IsAdmin',
                        'Password',
                        'UpdatedBy',
                        'UpdatedDate',
                        'UserName'
                    ]
                ],
                'page_no'
            ])
        ;
    }

    /** @test **/
    public function testCheckDuplicateName()
    {
        //Failure
        $this->
            json('POST', 'users/check-duplicate-name')
            ->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        ;

        $this
            ->json('POST', 'users/check-duplicate-name', [
                'username' => $this->user['username']
            ])
            ->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'message'
            ])
        ;

        $user = User::factory()->make();
        $this
            ->json('POST', 'users/check-duplicate-name', [
                'username' => $user['username']
            ])
            ->assertStatus(JsonResponse::HTTP_OK)
        ;
    }

    /** @test **/
    public function testDelete()
    {
        //Failure
        $this->
        json('POST', 'users/delete')
            ->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        ;

        $user = User::factory()->make();
        $this
            ->json('POST', 'users/delete', [
                'username' => $user['username']
            ])
            ->assertStatus(JsonResponse::HTTP_NOT_FOUND)
        ;

        $this
            ->json('POST', 'users/delete', [
                'username' => $this->user['username']
            ])
            ->assertStatus(JsonResponse::HTTP_OK)
            ->assertJsonStructure([
                'message'
            ])
        ;
    }

    /** @test **/
    public function testSave()
    {
        //Failure
        $this->
        json('POST', 'users/save')
            ->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        ;

        $this
            ->json('POST', 'users/save', [
                'UserName' => $this->user['username'],
                'operation' => 'add'
            ])
            ->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        ;

        $this
            ->json('POST', 'users/save', [
                'UserName' => $this->user['username'],
                'Password' => 'test_password',
                'operation' => 'edit'
            ])
            ->assertStatus(JsonResponse::HTTP_OK)
            ->assertJsonStructure([
                'message',
                'response' => [
                    'username'
                ]
            ])
        ;

        $user = User::factory()->make();
        $this
            ->json('POST', 'users/save', [
                'UserName' => $user['username'],
                'Password' => $user['password']
            ])
            ->assertStatus(JsonResponse::HTTP_OK)
            ->assertJsonStructure([
                'message',
                'response' => [
                    'username'
                ]
            ])
        ;
        User::where('username', $user['username'])->delete();
    }
}
