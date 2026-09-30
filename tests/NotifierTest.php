<?php

declare(strict_types=1);

namespace RazorInformatics\RiNotifierPhp\Tests;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use RazorInformatics\RiNotifierPhp\Account;
use RazorInformatics\RiNotifierPhp\Constants;
use RazorInformatics\RiNotifierPhp\Contact;
use RazorInformatics\RiNotifierPhp\DeHash;
use RazorInformatics\RiNotifierPhp\Gateway;
use RazorInformatics\RiNotifierPhp\Label;
use RazorInformatics\RiNotifierPhp\Message;
use RazorInformatics\RiNotifierPhp\Notifier;

class NotifierTest extends TestCase
{
    public function testConfiguresTheClient(): void
    {
        $client = (new \ReflectionProperty(Notifier::class, 'client'))->getValue(new Notifier('secret-key'));

        $this->assertInstanceOf(Client::class, $client);
        $this->assertSame('https://notifier.razorinformatics.co.ke/api/', (string) $client->getConfig('base_uri'));
        $this->assertSame(
            ['Authorization' => 'Bearer secret-key', 'Accept' => 'application/json'],
            array_intersect_key($client->getConfig('headers'), ['Authorization' => 0, 'Accept' => 0]),
        );
    }

    public function testCreatesTheServices(): void
    {
        $notifier = new Notifier('secret-key');

        $this->assertInstanceOf(Message::class, $notifier->message());
        $this->assertInstanceOf(Account::class, $notifier->account());
        $this->assertInstanceOf(Contact::class, $notifier->contacts());
        $this->assertInstanceOf(Label::class, $notifier->labels());
        $this->assertInstanceOf(DeHash::class, $notifier->decoder());
    }

    public function testTheGatewayDefaultsToNotifier(): void
    {
        $notifier = new Notifier('secret-key');
        $gateway = new \ReflectionProperty(Gateway::class, 'gateway');

        $this->assertSame(Constants::GATEWAY_NOTIFIER, $gateway->getValue($notifier->gateway()));
        $this->assertSame(Constants::GATEWAY_ONFON_MEDIA, $gateway->getValue($notifier->gateway(Constants::GATEWAY_ONFON_MEDIA)));
    }
}
