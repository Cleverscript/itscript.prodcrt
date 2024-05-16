<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = [
	"GROUPS" => [],
	"PARAMETERS" => [
		"AJAX_MODE" => [],

		"LIMIT" => [
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_WCR_CONT"),
			"TYPE" => "STRING",
			"DEFAULT" => "20",
		],
		"SET_TITLE" => [
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_SET_TITLE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		],

		"CACHE_TIME"  =>  ["DEFAULT"=>36000000],
		"CACHE_GROUPS" => [
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("CP_BP_CACHE_GROUPS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		],
	],
];
