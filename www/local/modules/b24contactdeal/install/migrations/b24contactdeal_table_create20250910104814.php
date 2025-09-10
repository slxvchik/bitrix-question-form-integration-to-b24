<?php

namespace Sprint\Migration;


class b24contactdeal_table_create20250910104814 extends Version
{
    protected $author = "admin";

    protected $description = "Создание таблицы для модуля b24contactdeal";

    protected $moduleVersion = "5.4.1";

    public function up()
    {
        $helper = $this->getHelperManager();

        $hlblockId = $helper->Hlblock()->saveHlblock([
            'NAME' => 'ContactDeal',
            'TABLE_NAME' => 'b24contactdeal_table',
        ]);

        $helper->Hlblock()->saveField($hlblockId, [
            'USER_TYPE_ID' => 'integer',
            'FIELD_NAME' => 'UF_B24_DEAL_ID',
        ]);

        $helper->Hlblock()->saveField($hlblockId, [
            'USER_TYPE_ID' => 'string',
            'FIELD_NAME' => 'UF_B24_DEAL_STATUS',
        ]);
    }

    public function down()
    {
        $helper = $this->getHelperManager();

        $hlblockId = $helper->Hlblock()->getHlblockIdIfExists('ContactDeal');

        $helper->UserTypeEntity()->deleteUserTypeEntitiesIfExists(
            'HLBLOCK_' . $hlblockId,
            [
                'UF_B24_DEAL_ID',
                'UF_B24_DEAL_STATUS'
            ]
        );

        $helper->Hlblock()->deleteHlblock($hlblockId);
    }
}
