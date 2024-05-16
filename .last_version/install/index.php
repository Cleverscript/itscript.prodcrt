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
 * Class itscript_prodcrt
 */

if (class_exists("itscript_prodcrt")) return;

class itscript_prodcrt extends CModule
{
    public $MODULE_ID = "itscript.prodcrt";
    public $SOLUTION_NAME = "prodcrt";
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

    public function __construct() {

        $arModuleVersion = array();
        include(__DIR__ . "/version.php");

        $this->exclusionAdminFiles = array(
            '..',
            '.'
        );

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = Loc::getMessage("ITSCRIPT_PRODCRT_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("ITSCRIPT_PRODCRT_MODULE_DESC");

        $this->PARTNER_NAME = Loc::getMessage("ITSCRIPT_PRODCRT_PARTNER_NAME");
        $this->PARTNER_URI = Loc::getMessage("ITSCRIPT_PRODCRT_PARTNER_URI");

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
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/itscript_prodcrt_cert_list.php"
        );
        File::deleteFile(
            $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/itscript_prodcrt_cert_edit.php"
        );

        Directory::deleteDirectory(
            $_SERVER["DOCUMENT_ROOT"] . '/bitrix/components/' . $this->getVendor() . '/prodcrt.list'
        );
        Directory::deleteDirectory(
            $_SERVER["DOCUMENT_ROOT"] . '/bitrix/components/' . $this->getVendor() . '/prodcrt.form'
        );

    }

    /**
     * Function register events solution
     */
    function InstallEvents() {

        EventManager::getInstance()->registerEventHandler(
            'main', 
            'OnBuildGlobalMenu', 
            $this->MODULE_ID, 
            '\Itscript\Prodcrt\Menu', 
            'adminOnBuildGlobalMenu', 
            9999
        );
    
        EventManager::getInstance()->registerEventHandler(
            $this->MODULE_ID, 
            "OnAfterCertApply", 
            $this->MODULE_ID, 
            "\Itscript\Prodcrt\Events", 
            "OnAfterCertApplyHandler", 
            "100"
        );
    }

    /**
     * Function unregister events solution
     */
    function UnInstallEvents() {

        $eventManager = EventManager::getInstance();
        $eventManager->unRegisterEventHandler('main', 'OnBuildGlobalMenu', $this->MODULE_ID, '\Itscript\Prodcrt\Menu', 'adminOnBuildGlobalMenu');
        $eventManager->unRegisterEventHandler($this->MODULE_ID, 'OnAfterCertApply', $this->MODULE_ID, '\Itscript\Prodcrt\Events', 'OnAfterCertApplyHandler');
    
    }


    public function InstallEventSend() {

        $eventName = "ITSCRIPT_PRODCRT_APPLY_CERT";

        $obEventType = new CEventType;
        $obEventType->Add([
            "EVENT_TYPE" => 'email',
            "EVENT_NAME"    => $eventName ,
            "NAME"          => Loc::getMessage("ITSCRIPT_PRODCRT_APPLY_CERT"),
            "LID"           => "ru",
            "DESCRIPTION"   => Loc::getMessage("ITSCRIPT_PRODCRT_APPLY_CERT_DESCRIPTION")
        ]);

        $oEventMessage = new CEventMessage();
        $arFields = [
            "ACTIVE" => "Y",
            "EVENT_NAME" => $eventName,
            "LID" => array("s1"),
            "LANGUAGE_ID" => "ru",
            "EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
            "EMAIL_TO" => "#EMAIL#",
            "SUBJECT" => Loc::getMessage("ITSCRIPT_PRODCRT_APPLY_CERT_SUBJECT"),
            "MESSAGE" => Loc::getMessage("ITSCRIPT_PRODCRT_APPLY_CERT_MESSAGE"),
            "BODY_TYPE" => "html"
        ];
        
        $id = $oEventMessage->Add($arFields);

        Option::set($this->MODULE_ID, 'ITSCRIPT_PRODCRT_EVENT_TYPE', $eventName);
        Option::set($this->MODULE_ID, 'ITSCRIPT_PRODCRT_EVENT_TYPE_EMAIL_TEMPLATE_ID', $id);

    }


    public function UnInstallEventSend() {
        $eventName = "ITSCRIPT_PRODCRT_APPLY_CERT";

        $obEventType = new CEventType;
        $obEventType->Delete($eventName);

        $oEventMessage = new CEventMessage();
        $oEventMessage->Delete(Option::get($this->MODULE_ID, 'ITSCRIPT_PRODCRT_EVENT_TYPE_EMAIL_TEMPLATE_ID'));

    }


    // Create entity table in database
    public function InstallDB() {

        global $DB, $DBType, $APPLICATION;
        $this->errors = $DB->RunSQLBatch(self::GetPath() . "/install/db/" . $DBType . "/install.sql");

        if($this->errors !== false) {

            $APPLICATION->ThrowException(implode("", $this->errors));

            return false;
        }

        return true;
    }

    public function UninstallDB($arParams = array()) {

        global $DB, $DBType, $APPLICATION;

        $this->errors = false;

        if (array_key_exists('savedata', $arParams) && $arParams['savedata'] != 'Y') {

            $this->errors = $DB->RunSQLBatch(self::GetPath() . "/install/db/" . $DBType . "/uninstall.sql");

            if ($this->errors !== false) {

                $APPLICATION->ThrowException(implode("", $this->errors));

                return false;
            }
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
					"MESSAGE" => GetMessage("ITSCRIPT_PRODCRT_CHECK_ISS_MODULE_EXT_ERROR",
						["#MODULE_ID#" => $module_id]
					),
					"DETAILS" => GetMessage("ITSCRIPT_PRODCRT_CHECK_ISS_MODULE_EXT_ERROR_ALT",
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

        $this->InstallEvents();
        $this->InstallEventSend();

        return true;
    }

    function DoUninstall() {

        global $APPLICATION, $step;
        $step = intval($step);

        //var_dump($this->GetPath());

        if ($step < 2) {

            $APPLICATION->IncludeAdminFile(
                GetMessage('ITSCRIPT_PRODCRT_UNINSTALL_TITLE'),
                $this->GetPath() . '/install/unstep1.php'
            );

        } elseif ($step == 2) {

            ModuleManager::unRegisterModule($this->MODULE_ID);
            $this->UnInstallEvents();
            $this->UnInstallFiles();
            $this->UninstallDB(array('savedata' => $_REQUEST['savedata']));
            $this->UnInstallEventSend();

        }

        $GLOBALS['errors'] = $this->errors;
    }

    function GetModuleRightList() {
        return [
            "reference_id" => array("D", "K", "S", "W"),
            "reference" => [
                "[D] " . Loc::getMessage("ITSCRIPT_PRODCRT_DENIED"),
                "[K] " . Loc::getMessage("ITSCRIPT_PRODCRT_READ_COMPONENT"),
                "[S] " . Loc::getMessage("ITSCRIPT_PRODCRT_WRITE_SETTINGS"),
                "[W] " . Loc::getMessage("ITSCRIPT_PRODCRT_FULL")
            ]
        ];
    }
}