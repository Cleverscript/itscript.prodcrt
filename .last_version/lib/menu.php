<?php

namespace Itserw\Lotoswcr;

use Bitrix\Main\Localization\Loc;

class Menu
{
    /**
     * Event. Occurs when the main menu is built.
     * @param array $arGlobalMenu An array of the global menu.
     * @param array $arModuleMenu Module menu array.
     */
    public static function adminOnBuildGlobalMenu(&$arGlobalMenu, &$arModuleMenu) //&$arGlobalMenu, &$arModuleMenu
    {
		//add css icon menu
        $arGlobalMenu['global_itserw'] = [
            'menu_id' => 'global_itserw',
            'text' => 'Itserw',
            'title' => 'Itserw',
            'sort' => 100,
            'items_id' => 'global_itserw',
            'items' => [
            ]
        ];  
    }
}
