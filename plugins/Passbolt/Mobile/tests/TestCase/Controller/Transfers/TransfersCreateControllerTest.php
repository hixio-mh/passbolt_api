<?php
declare(strict_types=1);

/**
 * Passbolt ~ Open source password manager for teams
 * Copyright (c) Passbolt SA (https://www.passbolt.com)
 *
 * Licensed under GNU Affero General Public License version 3 of the or any later version.
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Passbolt SA (https://www.passbolt.com)
 * @license       https://opensource.org/licenses/AGPL-3.0 AGPL License
 * @link          https://www.passbolt.com Passbolt(tm)
 * @since         3.1.0
 */
namespace Passbolt\Mobile\Test\TestCase\Controller\Transfers;

use App\Test\Lib\AppIntegrationTestCase;
use App\Test\Lib\Model\AuthenticationTokenModelTrait;
use Cake\Utility\Security;
use Passbolt\Mobile\Model\Entity\Transfer;
use Passbolt\Mobile\Test\Lib\Model\TransfersModelTrait;

class TransfersCreateControllerTest extends AppIntegrationTestCase
{
    use TransfersModelTrait;
    use AuthenticationTokenModelTrait;

    public $fixtures = [
        'app.Base/Users',
    ];

    public function testMobileTransfersCreateController_Success()
    {
        $data = [
            'total_pages' => 1,
            'status' => Transfer::TRANSFER_STATUS_START,
            'hash' => Security::hash('test', 'sha256', true),
        ];
        $this->authenticateAs('ada');
        $this->postJson('/mobile/transfers.json', $data);
        $this->assertSuccess();
        $this->assertTransferAttributes($this->_responseJsonBody);
        $this->assertAuthTokenAttributes($this->_responseJsonBody->authentication_token);
    }

    public function testMobileTransfersCreateController_ErrorNoData()
    {
        $this->authenticateAs('ada');
        $this->postJson('/mobile/transfers.json');
        $this->assertError(400);
    }

    public function testMobileTransfersCreateController_ErrorUserIsDeleted()
    {
        $data = [
            'total_pages' => 1,
            'status' => Transfer::TRANSFER_STATUS_START,
            'hash' => Security::hash('test', 'sha256', true),
        ];
        $this->authenticateAs('sofia');
        $this->postJson('/mobile/transfers.json', $data);
        $this->assertError(400);
        $this->assertTrue(isset($this->_responseJsonBody->user_id->user_is_soft_deleted));
    }

    public function testMobileTransfersCreateController_ErrorEmptyData()
    {
        $this->authenticateAs('ada');
        $this->postJson('/mobile/transfers.json', []);
        $this->assertError(400);
    }

    public function testMobileTransfersCreateController_ErrorNotAuthenticated()
    {
        $data = [
            'total_pages' => 1,
            'status' => Transfer::TRANSFER_STATUS_START,
            'hash' => Security::hash('test', 'sha256', true),
        ];
        $this->postJson('/mobile/transfers.json', $data);
        $this->assertAuthenticationError();
    }
}