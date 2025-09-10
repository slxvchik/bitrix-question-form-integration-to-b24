<?php

namespace Bitrix\B24ContactDeal\Model;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectNotFoundException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\Result;
use Bitrix\Main\SystemException;

class ContactDealTable
{
    private static $entityClass = null;
    private static $hlblockname = 'ContactDeal';
    private static $tableName = 'b24contactdeal_table';

    /**
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private static function getEntityClass()
    {
        if(self::$entityClass !== null) {
            return self::$entityClass;
        }

        if (!Loader::includeModule('highloadblock')) {
            throw new ObjectNotFoundException('Модуль highloadblock не найден');
        }

        $hlblock = HighloadBlockTable::getList([
            'filter' => [
                '=NAME' => self::$hlblockname,
                '=TABLE_NAME' => self::$tableName
            ]
        ])->fetch();

        if (!$hlblock) {
            throw new ObjectNotFoundException('Highloadblock ' . self::$hlblockname . ' не найден');
        }

        $entity = HighloadBlockTable::compileEntity($hlblock);

        self::$entityClass = $entity->getDataClass();

        return self::$entityClass;
    }

    /**
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ArgumentException
     * @throws ObjectNotFoundException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private static function findByDealId(int $dealId): array|false {
        $entityClass = self::getEntityClass();

        return $entityClass::getList([
            'select' => ['*'],
            'filter' => ['=UF_B24_DEAL_ID' => $dealId],
            'limit' => 1,
        ])->fetch();
    }

    /**
     * @throws LoaderException
     * @throws ObjectNotFoundException
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    private static function getByDealId(int $dealId): array {
        $contactDeal = self::findByDealId($dealId);

        if ($contactDeal === false) {
            throw new ObjectNotFoundException('Лид не найден, id: ' . $dealId);
        }

        return $contactDeal;
    }

    /**
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws Exception
     */
    public static function add(int $dealId, string $dealStatus): Result {
        $entityClass = self::getEntityClass();

        return $entityClass::add([
            'UF_B24_DEAL_ID' => $dealId,
            'UF_B24_DEAL_STATUS' => $dealStatus
        ]);
    }

    /**
     * @throws ObjectNotFoundException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     */
    public static function update(int $dealId, string $dealStatus): Result {
        $contactDealId = self::getByDealId($dealId)['ID'];

        $entityClass = self::getEntityClass();

        return $entityClass::update($contactDealId, [
            'UF_B24_DEAL_STATUS' => $dealStatus
        ]);
    }

    /**
     * @throws ObjectNotFoundException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     * @throws Exception
     */
    public static function delete(int $dealId): void {
        $contactDealId = self::getByDealId($dealId)['ID'];

        $entityClass = self::getEntityClass();

        $entityClass::delete($contactDealId);
    }

    /**
     * @throws LoaderException
     * @throws ArgumentException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function exists(int $dealId): bool {
        return !empty(self::findByDealId($dealId));
    }
}