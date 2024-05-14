<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\UI\PageNavigation;
use Bitrix\Main\FileTable;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Engine\CurrentUser;
use Itserw\Lotoswcr\CertTable;
use Itserw\Lotoswcr\Util;

Loader::includeModule('itserw.lotoswcr');

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

		//if ($this->startResultCache(false, array(($this->arParams["CACHE_GROUPS"]==="N"? false: CurrentUser::get()->getUserGroups())))) {
	        
            // add assets

            //var_dump($this->getTemplateName());

            Asset::getInstance()->addCss($this->GetPath() . '/templates/' . $this->getTemplateName() . '/style.css');
            Asset::getInstance()->addJs($this->GetPath().'/templates/'. $this->getTemplateName() . '/js/script.js');

            // Create navigation
            $nav = new PageNavigation("nav");
            $nav->allowAllRecords(false)
                ->setPageSize($this->arParams['LIMIT'])
                ->initFromUri();

            $filter = [];
            if ($this->arParams['USE_PREMODERATION'] == 'Y') {
                $filter['ACTIVE'] = 'Y';
            }

            // Get ORM entity
            $questions = CertTable::getList([
                'select' => [
                    '*'
                ],
                'filter' => $filter,
                'order' => ['ID' => 'DESC'],
                'offset' => $nav->getOffset(),
                'limit' => $nav->getLimit(),
                'count_total' => true
            ]);

            // Set full count elements entity
            $nav->setRecordCount($questions->getCount());

            // Fetch all items per page
            $rows  = $questions->fetchAll();

            //Util::debug($rows);

            $this->arResult["ITEMS"] = $rows;
            $this->arResult['NAV'] = $nav;

            // Save data cache
            $this->SetResultCacheKeys(['ITEMS', 'NAV']);

            // Include template
            $this->includeComponentTemplate();

	    //} else {
            //$this->abortResultCache();
        //}
	}

    // Create full name user
    public function getFullName(array $arr, string $sfx): string {

        $glueName = trim(implode(' ', [
            $arr["{$sfx}_LAST_NAME"], 
            $arr["{$sfx}_NAME"], 
            $arr["{$sfx}_SECOND_NAME"]
        ]));

        return trim($glueName) ?: $arr["{$sfx}_LOGIN"];
    }

}