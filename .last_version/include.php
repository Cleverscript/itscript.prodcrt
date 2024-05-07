<?php
use Bitrix\Main;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;

$module_id = "itserw.lotoswcr";

$defaultOptions = Option::getDefaults($module_id);

//define("ITSCRIPT_QUESTION_MODULE_ID", $module_id);
//define("ITSERW_LOTOSWCR_CONFIG_DEBUG", Option::get('ITSERW_LOTOSWCR_CONFIG_DEBUG', $defaultOptions['ITSERW_LOTOSWCR_CONFIG_DEBUG']));