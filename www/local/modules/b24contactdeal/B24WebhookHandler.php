<?php

use Bitrix\Main\DI\ServiceLocator;

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Method Not Allowed');
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    die('Invalid JSON');
}

if (CModule::IncludeModule('b24contactdeal') && CModule::IncludeModule('main')) {

    $webhookService = ServiceLocator::getInstance()->get('b24contactdeal.service.webhook');

    $webhookService->process($data);
}

header('Content-Type: application/json');
echo json_encode(['status' => 'success']);

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php');
?>