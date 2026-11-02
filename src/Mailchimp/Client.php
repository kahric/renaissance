<?php

declare(strict_types=1);

namespace Kahric\TheTheme\Mailchimp;

use Kahric\TheTheme\Config;
use Kahric\TheTheme\Helper\Cache;
use MailchimpMarketing\ApiClient;

final readonly class Client
{
    public function __construct(
        public ApiClient $mailchimp = new ApiClient(),
    )
    {
        // Mailchimp keys are "<apiKey>-<server>". Pad so both vars exist when the
        // key is empty or missing the server suffix (avoids undefined-key warnings).
        [$apiKey, $server] = array_pad(explode('-', Config::getAsString('mailchimp-key'), 2), 2, '');

        $this->mailchimp->setConfig([
            'apiKey' => $apiKey,
            'server' => $server
        ]);
    }

    public function getLists(): array
    {
        return Cache::memoize(
            'mailchimp_lists',
            function() {
                $response = $this->mailchimp->lists->getAllLists();

                $result = [];
                foreach ($response->lists as $list) {
                    $result[$list->id] = $list->name;
                }

                return $result;
            },
        );
    }
}