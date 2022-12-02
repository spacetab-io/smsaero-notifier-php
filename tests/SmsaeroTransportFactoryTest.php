<?php

declare(strict_types=1);

namespace Spacetab\SmsaeroNotifier\Tests;

use Spacetab\SmsaeroNotifier\SmsaeroTransportFactory;
use Symfony\Component\Notifier\Test\TransportFactoryTestCase;

final class SmsaeroTransportFactoryTest extends TransportFactoryTestCase
{
    public function createFactory(): SmsaeroTransportFactory
    {
        return new SmsaeroTransportFactory();
    }

    public function createProvider(): iterable
    {
        yield [
            'smsaero://host.test?from=SMS Aero&channel=INTERNATIONAL',
            'smsaero://test%40example.com:longSecretApiKey@host.test?from=SMS Aero&channel=INTERNATIONAL',
        ];
    }

    public function supportsProvider(): iterable
    {
        yield [true, 'smsaero://test%40example.com:longSecretApiKey@default?from=MyApp'];
        yield [false, 'somethingElse://test%40example.com:longSecretApiKey@default?from=MyApp'];
    }

    public function unsupportedSchemeProvider(): iterable
    {
        yield ['somethingElse://test%40example.com:longSecretApiKey@default?from=MyApp&channel=DIRECT'];
        yield ['somethingElse://test%40example.com:longSecretApiKey@default?from=MyApp']; // missing "channel" option
        yield ['somethingElse://test%40example.com:longSecretApiKey@default']; // missing "from" option
    }
}
