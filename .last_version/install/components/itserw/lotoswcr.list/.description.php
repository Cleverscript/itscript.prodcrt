<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("T_WCR_LIST_NAME"),
	"DESCRIPTION" => GetMessage("T_WCR_LIST_DESC"),
	"ICON" => "/images/news_list.gif",
	"SORT" => 1,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "Itscript",
		"CHILD" => array(
			"ID" => "wcr_list",
			"NAME" => GetMessage("T_WCR_LIST_NAME"),
			"SORT" => 10,
			"CHILD" => array(
				"ID" => "wcr_list_cmpx",
			),
		),
	),
);