<?php
namespace Ram\EmailSandbox\Transport;

use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mailer\SentMessage;
use Ram\EmailSandbox\Models\EmailMessage;
use Illuminate\Support\Str;

class EmailSandboxTransport extends AbstractTransport
{
    protected function doSend(SentMessage $message): void
    {
        $email = $message->getOriginalMessage();

        if (!$email instanceof \Symfony\Component\Mime\Email) {
            if ($email instanceof \Symfony\Component\Mime\Message) {
                $email = \Symfony\Component\Mime\MessageConverter::toEmail($email);
            } else {
                throw new \RuntimeException('The EmailSandbox transport requires an instance of Symfony\Component\Mime\Email.');
            }
        }

        $storagePath = config('email-sandbox.storage_path');

        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        $attachments = [];

        foreach ($email->getAttachments() as $attachment) {
            $filename = Str::random(10).'_'.$attachment->getFilename();
            $path = $storagePath.'/'.$filename;
            $body = $attachment->getBody();

            if (is_resource($body)) {   
                $body = stream_get_contents($body);
            }

            file_put_contents($path, $body);
            $attachments[] = $filename;
        }

        $formatAddresses = function($addresses) {
            return array_map(function($address) {
                return ['name' => $address->getName(), 'address' => $address->getAddress()];
            }, $addresses);
        };

        $formatHeaders = function($headers) {
            $result = [];
            foreach ($headers as $header) {
                $name = $header->getName();
                $value = $header->getBodyAsString();
                if (!isset($result[$name])) {
                    $result[$name] = [];
                }
                $result[$name][] = $value;
            }
            return $result;
        };

        $record = EmailMessage::create([
            'subject' => $email->getSubject(),
            'from' => $formatAddresses($email->getFrom()),
            'to' => $formatAddresses($email->getTo()),
            'cc' => $formatAddresses($email->getCc()),
            'bcc' => $formatAddresses($email->getBcc()),
            'html_body' => $email->getHtmlBody(),
            'text_body' => $email->getTextBody(),
            'headers' => $formatHeaders($email->getPreparedHeaders()->all()),
            'attachments' => $attachments,
        ]);

        file_put_contents($storagePath.'/'.$record->id.'.eml', $message->toString());
    }

    public function __toString(): string
    {
        return 'email-sandbox';
    }
}
