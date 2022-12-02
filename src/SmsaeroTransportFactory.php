<?php

declare(strict_types=1);

namespace Spacetab\SmsaeroNotifier;

use Symfony\Component\Notifier\Exception\UnsupportedSchemeException;
use Symfony\Component\Notifier\Transport\AbstractTransportFactory;
use Symfony\Component\Notifier\Transport\Dsn;

final class SmsaeroTransportFactory extends AbstractTransportFactory
{
    public function create(Dsn $dsn): SmsaeroTransport
    {
        $scheme = $dsn->getScheme();

        if ('smsaero' !== $scheme) {
            throw new UnsupportedSchemeException($dsn, 'smsaero', $this->getSupportedSchemes());
        }

        $email = $dsn->getUser();
        $authToken = $dsn->getPassword();
        $from = $dsn->getOption('from', 'SMS Aero');
        $channel = $dsn->getOption('channel', 'INTERNATIONAL');
        $host = 'default' === $dsn->getHost() ? null : $dsn->getHost();

        return (new SmsaeroTransport(
            $email, $authToken, $from, $channel,
            $this->client, $this->dispatcher
        ))->setHost($host);
    }

    protected function getSupportedSchemes(): array
    {
        return ['smsaero'];
    }
}
