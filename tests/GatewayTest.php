<?php

declare(strict_types=1);

namespace RazorInformatics\RiNotifierPhp\Tests;

use PHPUnit\Framework\Attributes\TestWith;
use RazorInformatics\RiNotifierPhp\Constants;
use RazorInformatics\RiNotifierPhp\Gateway;

class GatewayTest extends TestCase
{
    #[TestWith(['details'])]
    #[TestWith(['fetchDetails'])]
    #[TestWith(['getDetails'])]
    public function testFetchesTheGatewayBalance(string $method): void
    {
        $gateway = new Gateway($this->client($this->json(['data' => ['balance' => 42]])), 'key', Constants::GATEWAY_ROAM_TECH);

        $result = $gateway->{$method}();

        $this->assertRequest('GET', 'v2/balance', 'gateway=roam');
        $this->assertSame(10, $this->lastOptions()['timeout']);
        $this->assertSame(Constants::STATUS_SUCCESS, $result['status']);
        $this->assertSame(42, $result['data']->balance);
    }
}
