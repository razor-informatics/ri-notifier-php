<?php

declare(strict_types=1);

namespace RazorInformatics\RiNotifierPhp\Tests;

use PHPUnit\Framework\Attributes\TestWith;
use RazorInformatics\RiNotifierPhp\Constants;
use RazorInformatics\RiNotifierPhp\DeHash;

class DeHashTest extends TestCase
{
    #[TestWith(['send'])]
    #[TestWith(['decode'])]
    public function testLooksUpTheHash(string $method): void
    {
        $decoder = new DeHash($this->client($this->json(['data' => ['phone' => '254700100100']])));

        $result = $decoder->{$method}('abc123');

        $this->assertRequest('POST', 'hash/lookup');
        $this->assertSame(['hash' => 'abc123'], $this->formBody());
        $this->assertSame(Constants::STATUS_SUCCESS, $result['status']);
        $this->assertSame('254700100100', $result['data']->phone);
    }

    public function testAnEmptyHashIsRejected(): void
    {
        $result = (new DeHash($this->client()))->decode('');

        $this->assertNoRequest();
        $this->assertSame(['status' => Constants::STATUS_ERROR, 'message' => 'hash must be defined.', 'data' => []], $result);
    }
}
