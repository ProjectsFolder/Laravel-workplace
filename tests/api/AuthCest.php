<?php

class AuthCest
{
    public function _before(ApiTester $I)
    {
        $I->sendPost('/register', [
            'name' => 'test',
            'password' => 'secret',
            'roles' => ['ROLE_ADMIN']
        ]);
        $I->seeResponseContainsJson(['success' => true]);

        $I->sendPost('/register', [
            'name' => 'test2',
            'password' => 'secret',
        ]);
        $I->seeResponseContainsJson(['success' => true]);
    }

    public function registerTest(ApiTester $I)
    {
        $I->sendPost('/register', [
            'name' => 'test',
            'password' => 'secret',
        ]);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'success' => 'boolean',
        ]);
        $I->seeResponseContainsJson(['success' => false]);

        $I->sendPost('/register', [
            'name' => 'test2',
            'password' => '',
        ]);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'success' => 'boolean',
        ]);
        $I->seeResponseContainsJson(['success' => false]);
    }

    public function loginTest(ApiTester $I)
    {
        $I->sendPost('/login', [
            'name' => 'test',
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
            'name' => 'test',
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
            'name' => 'test',
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
            'jsondata' => json_encode(['name' => 'test', 'password' => 'secret']),
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
            'jsondata' => json_encode(['name' => 'test', 'password' => 'secret2']),
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
            'name' => 'test',
            'password' => 'secret',
        ]);
        $response = json_decode($response, true);
        $I->haveHttpHeader('Authorization', "Bearer {$response['data']['token']}");
        $I->sendPut('/token/refresh');
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

    public function accessTest(ApiTester $I)
    {
        $response = $I->sendPost('/login', [
            'name' => 'test2',
            'password' => 'secret',
        ]);
        $response = json_decode($response, true);
        $I->haveHttpHeader('Authorization', "Bearer {$response['data']['token']}");

        $I->sendGet('/vat/');
        $I->canSeeResponseCodeIs(403);
        $I->seeResponseContainsJson(['success' => false]);

        $response = $I->sendPost('/login', [
            'name' => 'test',
            'password' => 'secret',
        ]);
        $response = json_decode($response, true);
        $I->haveHttpHeader('Authorization', "Bearer {$response['data']['token']}");

        $I->sendGet('/vat/');
        $I->canSeeResponseCodeIsSuccessful();
        $I->seeResponseContainsJson(['success' => true]);
    }
}
