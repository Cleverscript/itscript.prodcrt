<?php
use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Context;
use Bitrix\Main\IO\File;
use Bitrix\Main\IO\Directory;

Loc::loadMessages(__FILE__);

/**
 * Class itserw_lotoswcr
 */

if (class_exists("itserw_lotoswcr")) return;

class itserw_lotoswcr extends CModule
{
    public $MODULE_ID = "itserw.lotoswcr";
    public $SOLUTION_NAME = "lotoswcr";
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $PARTNER_NAME;
    public $PARTNER_URI;
    public $MODULE_SORT;
    public $SHOW_SUPER_ADMIN_GROUP_RIGHTS;
    public $MODULE_GROUP_RIGHTS;

    public $eventManager;

    function __construct() {

        $arModuleVersion = array();
        include(__DIR__ . "/version.php");

        $this->exclusionAdminFiles = array(
            '..',
            '.'
        );

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = Loc::getMessage("ITSERW_LOTOSWCR_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("ITSERW_LOTOSWCR_MODULE_DESC");

        $this->PARTNER_NAME = Loc::getMessage("ITSERW_LOTOSWCR_PARTNER_NAME");
        $this->PARTNER_URI = Loc::getMessage("ITSERW_LOTOSWCR_PARTNER_URI");

        $this->MODULE_SORT = 1;
        $this->SHOW_SUPER_ADMIN_GROUP_RIGHTS = 'Y';
        $this->MODULE_GROUP_RIGHTS = "Y";

        $this->eventManager = EventManager::getInstance();
    }

    public function isVersionD7() {

        return CheckVersion(ModuleManager::getVersion("main"), "14.00.00");

    }

    public function GetPath($notDocumentRoot = false) {
        if ($notDocumentRoot) {

            return str_ireplace(Application::getDocumentRoot(), '', dirname(__DIR__));

        } else {

            return dirname(__DIR__);

        }
    }

    public static function getModuleId(): string {

        return basename(dirname(__DIR__));

    }

    public function getVendor(): string  {
        $expl = explode('.', $this->MODULE_ID);
        return $expl[0];
    }

    function InstallFiles() {

        if (!CopyDirFiles(
            $this->GetPath() . '/install/admin', 
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/", true)) {

            return false;
        }
        if (!CopyDirFiles(
            $this->GetPath() . '/install/bitrix', 
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/", true)) {

            return false;
        }

        if (!CopyDirFiles(
            $this->GetPath() . '/install/components', 
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/", true, true)) {
            return false;
        }

        return true;
    }

    function UnInstallFiles() {

        File::deleteFile(
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/itserw_lotoswcr_cert_list.php"
        );
        File::deleteFile(
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/itserw_lotoswcr_cert_edit.php"
        );

        //Directory::deleteDirectory($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/{$this->getVendor()}");

    }

    /**
     * Function register events solution
     */
    function InstallEvents() {

        $eventManager = EventManager::getInstance();
        $eventManager->registerEventHandler('main', 'OnBuildGlobalMenu', $this->MODULE_ID, '\Itserw\Lotoswcr\Menu', 'adminOnBuildGlobalMenu', 9999);
    }

    /**
     * Function unregister events solution
     */
    function UnInstallEvents() {

        $eventManager = EventManager::getInstance();
        $eventManager->unRegisterEventHandler('main', 'OnBuildGlobalMenu', $this->MODULE_ID, '\Itserw\Lotoswcr\Menu', 'adminOnBuildGlobalMenu');
    
    }


    // Create entity table in database
    public function InstallDB() {

        global $DB, $APPLICATION;
        $this->errors = $DB->RunSQLBatch(self::GetPath() . '/install/db/install.sql');

        if($this->errors !== false) {

            $APPLICATION->ThrowException(implode("", $this->errors));

            return false;
        }

        return true;
    }

    public function UninstallDB() {

        global $DB, $APPLICATION;
        $this->errors = $DB->RunSQLBatch(self::GetPath() . '/install/db/uninstall.sql');
        if ($this->errors !== false) {

            $APPLICATION->ThrowException(implode("", $this->errors));

            return false;
        }

        return true;
    }

	/**
	 * Checking if dependent modules are installed
	 * @param $module_id
	 * @return bool
	 */
    function checkIssetExtModules($module_id) {

    	if (!Loader::includeModule($module_id)) {
			\CAdminMessage::ShowMessage(
                [
					"MESSAGE" => GetMessage("ITSERW_LOTOSWCR_CHECK_ISS_MODULE_EXT_ERROR",
						["#MODULE_ID#" => $module_id]
					),
					"DETAILS" => GetMessage("ITSERW_LOTOSWCR_CHECK_ISS_MODULE_EXT_ERROR_ALT",
						["#MODULE_ID#" => $module_id]
					),
					"HTML" => true,
					"TYPE" => "ERROR"
                ]
			);
			return false;
		}

		return true;
	}

    function DoInstall() {

        ModuleManager::registerModule($this->MODULE_ID);

        if (!$this->InstallFiles()) {
            return false;
        }
        if (!$this->InstallDB()) {
            return false;
        }

        //$this->InstallEvents();
        //$this->InstallAgents();itscript_answers_list.php

        return true;
    }

    function DoUninstall() {

        ModuleManager::unRegisterModule($this->MODULE_ID);
        //$this->UnInstallEvents();
        $this->UnInstallFiles();
        $this->UninstallDB();
        //$this->UnInstallAgents();

        return true;
    }

    function GetModuleRightList() {
        return [
            "reference_id" => array("D", "K", "S", "W"),
            "reference" => [
                "[D] " . Loc::getMessage("ITSERW_LOTOSWCR_DENIED"),
                "[K] " . Loc::getMessage("ITSERW_LOTOSWCR_READ_COMPONENT"),
                "[S] " . Loc::getMessage("ITSERW_LOTOSWCR_WRITE_SETTINGS"),
                "[W] " . Loc::getMessage("ITSERW_LOTOSWCR_FULL")
            ]
        ];
    }
}