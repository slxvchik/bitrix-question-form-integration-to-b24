<?php

namespace Bitrix\B24ContactDeal\Services;

use Bitrix\B24ContactDeal\Api\B24ApiClient;
use Bitrix\Main\ArgumentException;

class ContactService
{
    private B24ApiClient $api;

    public function __construct(
        B24ApiClient $api
    ) {
        $this->api = $api;
    }

    /**
     * @throws ArgumentException
     */
    private function addContact(string $name, string $email): int
    {
        $fields = [
            'NAME' => $name,
            'EMAIL' => [['VALUE' => $email, 'VALUE_TYPE' => 'WORK']]
        ];

        return (int) $this->api->call('crm.contact.add', ['fields' => $fields]);
    }

    /**
     * @throws ArgumentException
     */
    private function findContactByEmail(string $email): ?int
    {
        $result = $this->api->call('crm.contact.list', [
            'filter' => ['EMAIL' => $email],
            'select' => ['ID']
        ]);

        return !empty($result) ? (int)$result[0]['ID'] : null;
    }

    /**
     * @throws ArgumentException
     */
    public function getOrCreateContact(string $name, string $email): int
    {
        $contactId = $this->findContactByEmail($email);

        if (!$contactId) {
            $contactId = $this->addContact($name, $email);
        }

        return $contactId;
    }
}