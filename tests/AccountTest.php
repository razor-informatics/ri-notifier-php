<?php

declare(strict_types=1);

namespace RazorInformatics\RiNotifierPhp\Tests;

use PHPUnit\Framework\Attributes\TestWith;
use RazorInformatics\RiNotifierPhp\Account;
use RazorInformatics\RiNotifierPhp\Constants;

class AccountTest extends TestCase
{
    #[TestWith(['details'])]
    #[TestWith(['fetchDetails'])]
    #[TestWith(['getDetails'])]
    public function testFetchesTheBalance(string $method): void
    {
        $account = new Account($this->client($this->json(['data' => ['balance' => 250.5]])));

        $result = $account->{$method}();

        $this->assertRequest('GET', 'balance');
        $this->assertSame(10, $this->lastOptions()['timeout']);
        $this->assertSame(Constants::STATUS_SUCCESS, $result['status']);
        $this->assertSame(250.5, $result['data']->balance);
    }
}
