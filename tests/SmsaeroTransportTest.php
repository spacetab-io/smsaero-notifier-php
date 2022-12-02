<?php

declare(strict_types=1);

namespace Spacetab\SmsaeroNotifier\Tests;

use Spacetab\SmsaeroNotifier\SmsaeroTransport;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\Message\MessageInterface;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\Test\TransportTestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class SmsaeroTransportTest extends TransportTestCase
{
    public function createTransport(HttpClientInterface $client = null): SmsaeroTransport
    {
        return new SmsaeroTransport(
            'test%40example.com',
            'longSecretApiKey',
            'SMS Aero',
            'INTERNATIONAL',
            $client ?: $this->createMock(HttpClientInterface::class)
        );
    }

    public function toStringProvider(): iterable
    {
        yield ['smsaero://gate.smsaero.ru?from=SMS Aero&channel=INTERNATIONAL', $this->createTransport()];
    }

    public function supportedMessagesProvider(): iterable
    {
        yield [new SmsMessage('0611223344', 'Hello!')];
    }

    public function unsupportedMessagesProvider(): iterable
    {
        yield [new ChatMessage('Hello!')];
        yield [$this->createMock(MessageInterface::class)];
    }
}
