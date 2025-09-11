<?php

namespace Bitrix\B24ContactDeal;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\Localization\Loc;
use CEventLog;

Loc::loadMessages(__FILE__);

class FormHandler
{

    /**
     * @throws ArgumentException
     */
    public static function onBeforeFormSubmit(&$event, &$lid, &$arFields) {

        if ($event !== 'FEEDBACK_FORM') {
            return;
        }

        $name = $arFields['AUTHOR'];
        $email = $arFields['AUTHOR_EMAIL'];
        $comment = $arFields['TEXT'];

        $dealService = ServiceLocator::getInstance()->get('b24contactdeal.service.deal');
        $dealId = $dealService->addDeal($name, $email, $comment);

        CEventLog::Add([
            'SEVERITY' => 'INFO',
            'AUDIT_TYPE_ID' => 'ADDING_DEAL_TO_B24',
            'MODULE_ID' => 'main',
            'DESCRIPTION' => Loc::getMessage('B24CONTACTDEAL_LOG_DEAL_ADDING', array(
                '#DEAL_ID#' => $dealId,
                '#NAME#' => $name,
                '#EMAIL#' => $email,
                '#COMMENT#' => $comment
            )),
        ]);

    }
}