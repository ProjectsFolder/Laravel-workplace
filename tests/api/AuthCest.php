<?php

class AuthCest
{
    public function _before(ApiTester $I)
    {
    }

    public function registerTest(ApiTester $I)
    {
        $I->sendPost('/register', [
            'name' => 'asylum29',
            'password' => '',
        ]);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'success' => 'boolean',
        ]);
        $I->seeResponseContainsJson(['success' => false]);

        $I->sendPost('/register', [
            'name' => 'asylum29',
            'password' => 'secret',
        ]);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'success' => 'boolean',
        ]);
    }

    public function loginTest(ApiTester $I)
    {
        $I->sendPost('/login', [
            'name' => 'asylum29',
            'password' => 'secret',
        ]);
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'success' => 'boolean',
            'data' => [
                'token' => 'string',
                'expires_in' => 'integer',
            ],
        ]);
        $I->seeResponseContainsJson(['success' => true]);


        $I->sendPost('/login', [
            'name' => 'asylum29',
            'password' => 'secret2',
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'success' => 'boolean',
            'message' => 'string',
        ]);
        $I->seeResponseContainsJson(['success' => false]);

        $I->sendPost('/login', [
            'name' => 'asylum29',
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'success' => 'boolean',
            'message' => 'string',
        ]);
        $I->seeResponseContainsJson(['success' => false]);

        $I->sendPost('/login', [
            'password' => 'secret2',
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'success' => 'boolean',
            'message' => 'string',
        ]);
        $I->seeResponseContainsJson(['success' => false]);
    }

    public function customLoginTest(ApiTester $I)
    {
        $I->sendPost('/custom/login', [
            'jsondata' => json_encode(['name' => 'asylum29', 'password' => 'secret']),
        ]);
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'success' => 'boolean',
            'data' => [
                'token' => 'string',
                'expires_in' => 'integer',
            ],
        ]);
        $I->seeResponseContainsJson(['success' => true]);

        $I->sendPost('/custom/login', [
            'jsondata' => json_encode(['name' => 'asylum29', 'password' => 'secret2']),
        ]);
        $I->seeResponseCodeIs(400);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'success' => 'boolean',
            'message' => 'string',
        ]);
        $I->seeResponseContainsJson(['success' => false]);
    }

    public function refreshTokenTest(ApiTester $I)
    {
        $response = $I->sendPost('/login', [
            'name' => 'asylum29',
            'password' => 'secret',
        ]);
        $response = json_decode($response, true);
        $I->haveHttpHeader('Authorization', "Bearer {$response['data']['token']}");
        $I->sendPut('/token/refresh', [
            'name' => 'asylum29',
            'password' => 'secret',
        ]);
        $I->seeResponseCodeIsSuccessful();
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'success' => 'boolean',
            'data' => [
                'token' => 'string',
                'expires_in' => 'integer',
            ],
        ]);
        $I->seeResponseContainsJson(['success' => true]);
    }
}
