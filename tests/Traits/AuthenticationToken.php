<?php
namespace Tests\Traits;

trait AuthenticationToken
{
    /**
     * @param $user mixed
     * @return mixed
     */
    public function authenticate($user)
    {
        $response = $this->json('POST', 'doLogin', [
            'username' => $user['username'],
            'password' => 'password'
        ]);

        $responseData = json_decode($response->getContent(), true);

        return $responseData['api_token'] ?? '';
    }
}
