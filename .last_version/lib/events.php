<?php
namespace Itscript\Prodcrt;

use Exception;
use Bitrix\Main;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc as Loc;
use Bitrix\Main\Config\Option;
use Itscript\Prodcrt\CertTable;

abstract class Events 
{
    protected static $moduleId = 'itscript.prodcrt';

    public static function OnAfterCertApplyHandler(int $id, array $fields) {
        
        $filter = ['ID' => $id, 'ACTIVE' => 'Y'];

        // Get ORM entity
        $cert = CertTable::getByPrimary($id, ['select' => [
            'ID',
            'ACTIVE',
            'MODEL',
            'FIO',
            'FILE_ID',
            'SENDED',
            'U_EMAIL' => 'USER.EMAIL',
        
        ]])->fetch();

        if ($cert['ACTIVE']=='Y' && intval($cert['FILE_ID'])>0 && $cert['SENDED']!='Y') {

            $eventName =  Option::get(self::$moduleId, 'ITSCRIPT_PRODCRT_EVENT_TYPE');
            $eventTplId = Option::get(self::$moduleId, 'ITSCRIPT_PRODCRT_EVENT_TYPE_EMAIL_TEMPLATE_ID');

            if (!empty($eventName) && $eventTplId) {
            
                $send = \CEvent::Send(
                    $eventName,
                    's1',
                    [
                        'ID'    => $id,
                        'EMAIL' => $cert['U_EMAIL'],
                        'MODEL' => $cert['MODEL'],
                        'FIO'   => $cert['FIO'],
                    ],
                    "Y",
                    $eventTplId,
                    [$cert['FILE_ID']]
                );

                if ($send) {
                    CertTable::update($id, ['SENDED' => 'Y']);
                }
            }

           // exit();

            \Bitrix\Main\Diag\Debug::writeToFile([$id, $fields, $cert, 'SEND' => $send]);
        }
    }
}