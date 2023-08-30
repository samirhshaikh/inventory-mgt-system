<?php
namespace Tests\Traits;

trait AuthenticationToken
{
    /**
     * @param $user
     * @return string
     */
    public function authenticate($user): string
    {
        $response = $this->postJson("doLogin", [
            "username" => $user["username"],
            "password" => "password",
        ]);

        $responseData = json_decode($response->getContent(), true);

        return $responseData["api_token"] ?? "";
    }
}
