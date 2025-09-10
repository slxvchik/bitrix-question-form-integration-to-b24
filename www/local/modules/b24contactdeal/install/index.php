<?php

use Bitrix\Main\EventManager;
use Bitrix\Main\Localization\Loc;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

Loc::loadMessages(__FILE__);

class B24ContactDeal extends CModule
{
    public $MODULE_ID = 'b24contactdeal';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $PARTNER_NAME;

    public function __construct()
    {
        $arModuleVersion = array();
        include __DIR__ . '/version.php';
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_NAME = Loc::getMessage('B24CONTACTDEAL_SETTINGS_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('B24CONTACTDEAL_SETTINGS_DESCRIPTION');
        $this->PARTNER_NAME = 'Vyacheslav';
    }

    public function DoInstall(): bool
    {

        if (IsModuleInstalled($this->MODULE_ID)) {
            return true;
        }

        $this->InstallDB();
        $this->InstallEvents();
        $this->InstallFiles();

        RegisterModule($this->MODULE_ID);

        return true;
    }

    public function DoUninstall(): bool
    {

        $this->UnInstallDB();
        $this->UnInstallEvents();
        $this->UnInstallFiles();

        unRegisterModule($this->MODULE_ID);
        return true;
    }

    /**
     * @throws \Sprint\Migration\Exceptions\MigrationException
     * @throws Exception
     */
    public function InstallDB(): bool
    {
        if (!CModule::IncludeModule('sprint.migration')) {
            global $APPLICATION;
            $APPLICATION->throwException(Loc::getMessage('B24CONTACTDEAL_MODULE_DEPENDENCY_EXCEPTION'));
            return false;
        }

        try {
            (new Sprint\Migration\Installer(
                array(
                    'migration_dir'          => __DIR__ . '/migrations/',
                    'migration_dir_absolute' => true,
                )
            ))->up();
        } catch (Exception $e) {
            global $APPLICATION;
            $APPLICATION->ThrowException($e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * @throws \Sprint\Migration\Exceptions\MigrationException
     * @throws Exception
     */
    public function UnInstallDB(): bool
    {
        if (!CModule::IncludeModule('sprint.migration')) {
            global $APPLICATION;
            $APPLICATION->throwException(Loc::getMessage('B24CONTACTDEAL_MODULE_DEPENDENCY_EXCEPTION'));
            return false;
        }

        try {
            (new Sprint\Migration\Installer(
                array(
                    'migration_dir'          => __DIR__ . '/migrations/',
                    'migration_dir_absolute' => true,
                )
            ))->down();
        } catch (Exception $e) {
            global $APPLICATION;
            $APPLICATION->ThrowException($e->getMessage());
            return false;
        }

        return true;
    }

    public function InstallEvents(): bool
    {
        $eventManager = EventManager::getInstance();
        $eventManager->registerEventHandler(
            'main',
            'OnBeforeEventAdd',
            $this->MODULE_ID,
            '\\Bitrix\\B24ContactDeal\\FormHandler',
            'onBeforeFormSubmit'
        );
        return true;
    }

    public function UnInstallEvents(): bool
    {
        $eventManager = EventManager::getInstance();
        $eventManager->unRegisterEventHandler(
            'main',
            'OnBeforeEventAdd',
            $this->MODULE_ID,
            '\\Bitrix\\B24ContactDeal\\FormHandler',
            'onBeforeFormSubmit'
        );
        return true;
    }

    public function InstallFiles(): bool
    {
        return true;
    }

    public function UnInstallFiles(): bool
    {
        return true;
    }
}