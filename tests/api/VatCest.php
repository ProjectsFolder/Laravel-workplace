<?php

use App\Domain\Entity\Vat\VatData;
use App\Domain\Interfaces\Output\VatGetterInterface;
use Codeception\Stub;
use PHPUnit\Framework\MockObject\Stub\ReturnCallback;

class VatCest
{
    private $id;

    public function _before(ApiTester $I)
    {
        $I->sendPost('/register', [
            'name' => 'test',
            'password' => 'secret',
            'roles' => ['ROLE_ADMIN'],
        ]);
        $response = $I->sendPost('/login', [
            'name' => 'test',
            'password' => 'secret',
        ]);
        $response = json_decode($response, true);
        $I->haveHttpHeader('Authorization', "Bearer {$response['data']['token']}");

        $vatGetter = Stub::makeEmpty(VatGetterInterface::class);
        $vatGetter->method('get')->will(new ReturnCallback(function () {
            $args = func_get_args();
            if ('LV44103001941' == $args[0]) {
                $data = new VatData();
                $data->setValid(true);
                $data->setName('name');
                $data->setCountryCode('LV');
                $data->setAddress('address');
                $data->setVatNumber('44103001941');
                $data->setRequestDate(new DateTime());

                return $data;
            }

            return null;
        }));
        $I->haveInstance(VatGetterInterface::class, $vatGetter);

        $response = $I->sendPost('/vat/check?vat=LV44103001941');
        $I->canSeeResponseCodeIsSuccessful();
        $I->seeResponseContainsJson(['success' => true]);
        $response = json_decode($response, true);
        $this->id = $response['data']['id'];
    }

    // tests
    public function checkVatFailsTest(ApiTester $I)
    {
        $I->sendPost('/vat/check?vat');
        $I->canSeeResponseCodeIs(400);
        $I->seeResponseContainsJson(['success' => false]);

        $I->sendPost('/vat/check?vat=12345');
        $I->canSeeResponseCodeIs(400);
        $I->seeResponseContainsJson(['success' => false]);

        $I->sendPost('/vat/check?vat=LV1234567890');
        $I->canSeeResponseCodeIs(400);
        $I->seeResponseContainsJson(['success' => false]);
    }

    public function getVatListTest(ApiTester $I)
    {
        $I->sendGet('/vat/');
        $I->canSeeResponseCodeIsSuccessful();
        $I->seeResponseContainsJson(['success' => true]);
    }

    public function getVatTest(ApiTester $I)
    {
        $next = $this->id + 1;

        $I->sendGet("/vat/$next");
        $I->seeResponseCodeIs(404);
        $I->seeResponseContainsJson(['success' => false]);

        $I->sendGet("/vat/$this->id");
        $I->seeResponseContainsJson(['success' => true]);
    }

    public function updateVatTest(ApiTester $I)
    {
        $next = $this->id + 1;

        $I->sendPut("/vat/$next");
        $I->seeResponseCodeIs(404);
        $I->seeResponseContainsJson(['success' => false]);

        $I->sendPut("/vat/$this->id", [
            'valid' => 0
        ]);
        $I->seeResponseContainsJson(['success' => true]);
    }

    public function deleteVatTest(ApiTester $I)
    {
        $next = $this->id + 1;

        $I->sendDelete("/vat/$next");
        $I->seeResponseCodeIs(404);
        $I->seeResponseContainsJson(['success' => false]);

        $I->sendDelete("/vat/$this->id");
        $I->seeResponseContainsJson(['success' => true]);
    }
}
