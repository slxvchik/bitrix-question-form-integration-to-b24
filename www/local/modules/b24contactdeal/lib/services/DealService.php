<?php

namespace Bitrix\B24ContactDeal\Services;

use Bitrix\B24ContactDeal\Api\B24ApiClient;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class DealService
{
    private B24ApiClient $api;
    private ContactService $contactService;
    private string $messageFieldId;

    public function __construct(
        B24ApiClient $api,
        ContactService $contactService,
        string $messageFieldId
    ) {
        $this->api = $api;
        $this->contactService = $contactService;
        $this->messageFieldId = $messageFieldId;
    }

    /**
     * @throws ArgumentException
     */
    public function addDeal(string $name, string $email, string $text): int
    {
        $contactId = $this->contactService->getOrCreateContact($name, $email);

        $fields = [
            'TITLE' => Loc::getMessage('B24CONTACTDEAL_B24_DEAL_TITLE', array(
                '#EMAIL#' => $email,
            )),
            'SOURCE_ID' => 'WEB',
            'CONTACT_ID' => $contactId,
            $this->messageFieldId => $text,
        ];
        $dealId = $this->api->call('crm.deal.add', ['fields' => $fields]);
        return (int) $dealId;
    }

    /**
     * @throws ArgumentException
     */
    public function getDeal(string $dealId): array
    {
        $deal = $this->api->call('crm.deal.get', ['ID' => $dealId]);
        $dealId = $deal['ID'];
        $dealStageId = $deal['STAGE_ID'];

        return [
            'ID' => $dealId,
            'STAGE_ID' => $dealStageId,
        ];
    }
}