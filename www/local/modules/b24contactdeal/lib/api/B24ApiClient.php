<?php

namespace Bitrix\B24ContactDeal\Api;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Web\Json;
use CEventLog;
use http\Exception\RuntimeException;

class B24ApiClient
{
    protected string $apiUrl;
    protected HttpClient $httpClient;

    public function __construct(
        string $apiUrl
    ) {
        $this->apiUrl = $apiUrl;
        $this->httpClient = new HttpClient();
    }

    /**
     * @throws ArgumentException
     */
    public function call(string $method, array $params = []): array|int|null
    {
        $url = $this->apiUrl . $method . '.json';
        $this->httpClient->post($url, $params);

        $result = $this->httpClient->getResult();
        $decodedResult = Json::decode($result);

        if (isset($decodedResult['error'])) {
            $this->handleError($decodedResult);
        }

        return $decodedResult['result'] ?? null;
    }

    protected function handleError(array $errorResponse): void
    {
        $errorMessage = "B24 API Error: " . $errorResponse['error_description'];

        CEventLog::Add([
            'SEVERITY' => 'ERROR',
            'AUDIT_TYPE_ID' => 'B24_CONTACT_DEALS',
            'MODULE_ID' => 'main',
            'DESCRIPTION' => "Ошибка при работе с API Bitrix24: " . $errorResponse['error_description'],
        ]);

        throw new RuntimeException($errorMessage);
    }

}