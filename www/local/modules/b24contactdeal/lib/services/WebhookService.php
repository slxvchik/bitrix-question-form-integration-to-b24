<?php

namespace Bitrix\B24ContactDeal\Services;

use Bitrix\B24ContactDeal\Model\ContactDealTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Messenger\Internals\Exception\RuntimeException;
use Bitrix\Main\ObjectNotFoundException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;

class WebhookService
{
    private DealService $dealService;
    private string $applicationToken;

    public function __construct(
        DealService $dealService,
        $applicationToken
    ) {
        $this->dealService = $dealService;
        $this->applicationToken = $applicationToken;
    }

    /**
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws LoaderException
     * @throws ArgumentException
     * @throws SystemException
     */
    public function process(array $data): void
    {

        if (empty($data['auth']['application_token']) || $data['auth']['application_token'] !== $this->applicationToken) {
            throw new RuntimeException('Wrong application token');
        }

        if (empty($data['event']) || !in_array($data['event'], ['ONCRMDEALADD', 'ONCRMDEALUPDATE'])) {
            throw new RuntimeException('Wrong event');
        }

        $dealId = $data['data']['FIELDS']['ID'];

        $b24Deal = $this->dealService->getDeal($dealId);

        if (ContactDealTable::exists($b24Deal['ID'])) {
            ContactDealTable::update($b24Deal['ID'], $b24Deal['STAGE_ID']);
        } else {
            ContactDealTable::add($b24Deal['ID'], $b24Deal['STAGE_ID']);
        }
    }
}