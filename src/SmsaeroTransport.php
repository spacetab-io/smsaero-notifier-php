<?php

declare(strict_types=1);

namespace Spacetab\SmsaeroNotifier;

use Symfony\Component\Notifier\Exception\TransportException;
use Symfony\Component\Notifier\Exception\UnsupportedMessageTypeException;
use Symfony\Component\Notifier\Message\MessageInterface;
use Symfony\Component\Notifier\Message\SentMessage;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\Transport\AbstractTransport;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface as HttpDecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface as HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface as HttpTransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class SmsaeroTransport extends AbstractTransport
{
    protected const HOST = 'gate.smsaero.ru';

    private ?string $email;
    private ?string $apiKey;
    private string $from;
    private string $channel;

    public function __construct(
        ?string $email,
        #[\SensitiveParameter] ?string $apiKey,
        string $from,
        string $channel,
        HttpClientInterface $client = null,
        EventDispatcherInterface $dispatcher = null
    ) {
        $this->email = \trim($email);
        $this->apiKey = \trim($apiKey);
        $this->from = \trim($from);
        $this->channel = \trim($channel);

        parent::__construct($client, $dispatcher);
    }

    public function __toString(): string
    {
        return sprintf('smsaero://%s?from=%s&channel=%s', $this->getEndpoint(), $this->from, $this->channel);
    }

    public function supports(MessageInterface $message): bool
    {
        return $message instanceof SmsMessage;
    }

    protected function doSend(MessageInterface $message): SentMessage
    {
        if (!$message instanceof SmsMessage) {
            throw new UnsupportedMessageTypeException(__CLASS__, SmsMessage::class, $message);
        }

        $body = [
            'number' => $message->getPhone(),
            'text' => $message->getSubject(),
            'channel' => $this->channel,
            'sign' => $this->from,
            'dateSend' => null,
            'callbackUrl' => null
        ];

        $endpoint = sprintf('https://%s/v2/sms/send', $this->getEndpoint());

        $response = $this->client->request('POST', $endpoint, [
            'auth_basic' => [$this->email, $this->apiKey],
            'body' => $body
        ]);

        try {
            $result = $response->toArray();
        } catch (HttpTransportExceptionInterface $e) {
            throw new TransportException('Could not reach the remote smsaero.ru server.', $response, 0, $e);
        } catch (HttpDecodingExceptionInterface $e) {
            throw new TransportException('Could not decode the response from remote smsaero.ru server.', $response, 0, $e);
        } catch (HttpExceptionInterface $e) {
            throw new TransportException('Unexpected response from remote smsaero.ru server.', $response, 0, $e);
        }

        $sentMessage = new SentMessage($message, (string) $this);
        $sentMessage->setMessageId((string) ($result['data']['id'] ?? ''));

        return $sentMessage;
    }
}
