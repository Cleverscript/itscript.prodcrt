<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\UI\PageNavigation;
use Bitrix\Main\FileTable;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Engine\CurrentUser;
use Itserw\Lotoswcr\CertTable;
use Bitrix\Main\Localization\Loc;
use Itserw\Lotoswcr\Util;

Loader::includeModule('itserw.lotoswcr');

IncludeTemplateLangFile(__FILE__);

class Lotoswcr extends CBitrixComponent
{
	public function onPrepareComponentParams($arParams) {

        /*echo '<pre>';
        print_r($arParams);
        echo '</pre>';*/

		$result = [
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => isset($arParams["CACHE_TIME"])? $arParams["CACHE_TIME"]: 36000000,
        ];

        $result = $result+$arParams;

		return $result;
	}

	public function executeComponent() {

        $userId = \Bitrix\Main\Engine\CurrentUser::get()->getId();

		if ($this->startResultCache(false, array(($this->arParams["CACHE_GROUPS"]==="N"? false: $userId)))) {
	        
            $items = [];
            // add assets

            //var_dump($this->getTemplateName());

            Asset::getInstance()->addCss($this->GetPath() . '/templates/' . $this->getTemplateName() . '/style.css');
            Asset::getInstance()->addJs($this->GetPath().'/templates/'. $this->getTemplateName() . '/js/script.js');

            // Create navigation
            $nav = new PageNavigation("nav");
            $nav->allowAllRecords(false)
                ->setPageSize($this->arParams['LIMIT'])
                ->initFromUri();

            $filter = [
                'ACTIVE' => 'Y', 
                'USER_ID' => $userId,
                '!=FILE_ID' => null
            ];

            

            // Get ORM entity
            $cert = CertTable::getList([
                'select' => ['*'],
                'filter' => $filter,
                'order' => ['ORDER_ID' => 'DESC'],
                'group' => ['ORDER_ID'],
                'offset' => $nav->getOffset(),
                'limit' => $nav->getLimit(),
                'count_total' => true
            ]);

            // Set full count elements entity
            $nav->setRecordCount($cert->getCount());

            // Fetch all items per page
            $rows  = $cert->fetchAll();

            foreach($rows as &$item) {
                $arFile = CFile::GetFileArray($item['FILE_ID']);
                $item['FILE_SRC'] = $arFile['SRC'];
                $items[$item['ORDER_ID']]['ORDER_DATE_INSERT'] = $item['ORDER_DATE_INSERT'];
                $items[$item['ORDER_ID']]['CERTS'][] = $item;
            }

            //Util::debug($items);

            $this->arResult["ITEMS"] = $items;
            $this->arResult['NAV'] = $nav;

            // Save data cache
            $this->SetResultCacheKeys(['ITEMS', 'NAV']);

            // Include template
            $this->includeComponentTemplate();

	    } else {
            $this->abortResultCache();
        }

        global $APPLICATION;
        $APPLICATION->SetTitle(Loc::getMessage('T_PAGE_TITLE'));
	}
}