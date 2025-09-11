<?php

use Bitrix\B24ContactDeal\Api\B24ApiClient;
use Bitrix\B24ContactDeal\Services\ContactService;
use Bitrix\B24ContactDeal\Services\DealService;
use Bitrix\B24ContactDeal\Services\WebhookService;
use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\Loader;

if (Loader::includeModule('b24contactdeal')) {

    Loader::registerAutoLoadClasses('b24contactdeal', [
        'Bitrix\\B24ContactDeal\\FormHandler' => 'lib/FormHandler.php',
        'Bitrix\\B24ContactDeal\\Api\\B24ApiClient' => 'lib/api/B24ApiClient.php',
        'Bitrix\\B24ContactDeal\\Services\\DealService' => 'lib/services/DealService.php',
        'Bitrix\\B24ContactDeal\\Services\\ContactService' => 'lib/services/ContactService.php',
        'Bitrix\\B24ContactDeal\\Services\\WebhookService' => 'lib/services/WebhookService.php',
    ]);

    ServiceLocator::getInstance()->addInstanceLazy('b24contactdeal.api.client', [
        'className' => B24ApiClient::class,
        'constructorParams' => static function() {
            return [
                '#YOUR_API_URL_FOR_CREAD_DEAL#'
            ];
        }
    ]);

    ServiceLocator::getInstance()->addInstanceLazy('b24contactdeal.service.contact', [
        'className' => ContactService::class,
        'constructorParams' => static function() {
            return [
                ServiceLocator::getInstance()->get('b24contactdeal.api.client')
            ];
        }
    ]);

    ServiceLocator::getInstance()->addInstanceLazy('b24contactdeal.service.deal', [
        'className' => DealService::class,
        'constructorParams' => static function() {
            return [
                ServiceLocator::getInstance()->get('b24contactdeal.api.client'),
                ServiceLocator::getInstance()->get('b24contactdeal.service.contact'),
                '#YOUR_UF_CRM_FIELD_ID#'
            ];
        }
    ]);

    ServiceLocator::getInstance()->addInstanceLazy('b24contactdeal.service.webhook', [
        'className' => WebhookService::class,
        'constructorParams' => static function() {
            return [
                ServiceLocator::getInstance()->get('b24contactdeal.service.deal'),
                '#YOUR_WEBHOOK_APPLICATION_TOKEN#'
            ];
        }
    ]);
}
