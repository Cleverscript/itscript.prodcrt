<?php
/** @global CMain $APPLICATION */

use Bitrix\Main;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc; 
use Bitrix\Main\Config\Option;
use Bitrix\Main\Application;
use Bitrix\Main\UI\PageNavigation;
use Itserw\Lotoswcr\CertTable;

$module_id = "itserw.lotoswcr";

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
require_once(dirname(__FILE__)."/../include.php");
require_once(dirname(__FILE__)."/../prolog.php");

IncludeModuleLangFile(__FILE__);

// Check access
$FORM_RIGHT = $APPLICATION->GetGroupRight($module_id);
if($FORM_RIGHT<="D") $APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));

if(!Loader::includeModule($module_id)){
	CAdminMessage::ShowMessage(Loc::getMessage("ITSERW_LOTOSWCR_INCLUDE_MODULE_ERROR", ['#MODULE_ID#' => $module_id]));
}

CJSCore::Init(array('ajax', 'json', 'ls', 'session', 'jquery', 'popup', 'pull'));

$adminListTableID = 'b_itserw_lotoswcr_cert';

$adminSort = new CAdminSorting($adminListTableID, 'ID', 'ASC');
$adminList = new CAdminUiList($adminListTableID, $adminSort);

// Set filter field panel
$filterFields = array(
    array(
        "id" => "ID",
        "name" => 'ID',
        "filterable" => "=",
        "default" => true
    ),
    array(
        "id" => "USER_ID",
        "name" => Loc::getMessage("ITSERW_LOTOSWCR_TITLE_USER_ID"),
        "type" => "int",
        "filterable" => "="
    ),
    array(
        "id" => "ORDER_ID",
        "name" => Loc::getMessage("ITSERW_LOTOSWCR_TITLE_ORDER_ID"),
        "type" => "int",
        "filterable" => "="
    ),
    array(
        "id" => "CITY",
        "name" => Loc::getMessage("ITSERW_LOTOSWCR_TITLE_CITY"),
        "type" => "text",
        "filterable" => "%"
    ),
    array(
        "id" => "MODEL",
        "name" => Loc::getMessage("ITSERW_LOTOSWCR_TITLE_MODEL"),
        "type" => "text",
        "filterable" => "%"
    ),
);

$filter = array();

$adminList->AddFilter($filterFields, $filter);

if ($listID = $adminList->GroupAction()) {

    $action = $_REQUEST['action'];

    if (!empty($_REQUEST['action_button'])) {
        $action = $_REQUEST['action_button'];
    }

    $checkUseCoupons = ($action == 'delete');
    $discountList = array();

    if ($_REQUEST['action_target'] == 'selected') {
        $listID = array();
        $formIterator = CertTable::getList(array(
            'select' => array('ID'),
            'filter' => $filter
        ));
        while ($form = $formIterator->fetch()) {
            $listID[] = $form['ID'];
        }
        unset($form, $formIterator);
    }

    if ($adminList->IsGroupActionToAll()) {
        $arID = array();
        $formIterator = CertTable::getList(array(
            'select' => array('ID'),
            'filter' => $filter
        ));
        while ($arRes = $formIterator->fetch()) {
            $listID[] = $arRes['ID'];
        }
        unset($arRes, $rsData);
    }

    $listID = array_filter($listID);

    if (!empty($listID)) {
        switch ($action) {
            case 'delete':
                foreach ($listID as &$recordId) {
                    $result = CertTable::delete($recordId);
                    if (!$result->isSuccess()) {
                        $adminList->AddGroupError(implode('<br>', $result->getErrorMessages()), $recordId);
                    }
                    unset($result);
                }
                unset($recordId);
                break;
        }
    }
    unset($discountList, $action, $listID);

    if ($adminList->hasGroupErrors()) {
        $adminSidePanelHelper->sendJsonErrorResponse($adminList->getGroupErrors());
    } else {
        $adminSidePanelHelper->sendSuccessResponse();
    }
}

$headerList = array();
$headerList['ID'] = array(
    'id' => 'ID',
    'content' => 'ID',
    'sort' => 'ID',
    'default' => true
);

$headerList['USER_ID'] = array(
    'id' => 'USER_ID',
    'content' => Loc::getMessage('ITSERW_LOTOSWCR_TITLE_USER_ID'),
    'title' => Loc::getMessage('ITSERW_LOTOSWCR_TITLE_USER_ID'),
    'sort' => 'USER_ID',
    'default' => false
);
$headerList['ORDER_ID'] = array(
    'id' => 'ORDER_ID',
    'content' => Loc::getMessage('ITSERW_LOTOSWCR_TITLE_ORDER_ID'),
    'title' => Loc::getMessage('ITSERW_LOTOSWCR_TITLE_ORDER_ID'),
    'sort' => 'ORDER_ID',
    'default' => false
);
$headerList['CITY'] = array(
    'id' => 'CITY',
    'content' => Loc::getMessage('ITSERW_LOTOSWCR_TITLE_CITY'),
    'title' => Loc::getMessage('ITSERW_LOTOSWCR_TITLE_CITY'),
    'sort' => 'CITY',
    'default' => true
);
$headerList['MODEL'] = array(
    'id' => 'MODEL',
    'content' => Loc::getMessage('ITSERW_LOTOSWCR_TITLE_MODEL'),
    'title' => Loc::getMessage('ITSERW_LOTOSWCR_TITLE_MODEL'),
    'sort' => 'MODEL',
    'default' => true
);
$headerList['FIO'] = array(
    'id' => 'FIO',
    'content' => Loc::getMessage('ITSERW_LOTOSWCR_TITLE_FIO'),
    'title' => Loc::getMessage('ITSERW_LOTOSWCR_TITLE_FIO'),
    'sort' => 'FIO',
    'default' => true
);
$headerList['EMAIL'] = array(
    'id' => 'EMAIL',
    'content' => Loc::getMessage('ITSERW_LOTOSWCR_TITLE_EMAIL'),
    'title' => Loc::getMessage('ITSERW_LOTOSWCR_TITLE_EMAIL'),
    'sort' => 'EMAIL',
    'default' => true
);

$headerList['ACTIVE'] = array(
    'id' => 'ACTIVE',
    'content' => Loc::getMessage('ITSERW_LOTOSWCR_TITLE_ACTIVE'),
    'title' => Loc::getMessage('ITSERW_LOTOSWCR_TITLE_ACTIVE'),
    'sort' => 'ACTIVE',
    'default' => false
);

$headerList['DATE_INSERT'] = array(
    'id' => 'DATE_INSERT',
    'content' => Loc::getMessage('ITSERW_LOTOSWCR_TITLE_DATE_INSERT'),
    'title' => Loc::getMessage('ITSERW_LOTOSWCR_TITLE_DATE_INSERT'),
    'sort' => 'CREATED',
    'default' => true
);
$headerList['ORDER_DATE_INSERT'] = array(
    'id' => 'ORDER_DATE_INSERT',
    'content' => Loc::getMessage('ITSERW_LOTOSWCR_TITLE_ORDER_DATE_INSERT'),
    'title' => Loc::getMessage('ITSERW_LOTOSWCR_TITLE_ORDER_DATE_INSERT'),
    'sort' => 'CREATED',
    'default' => true
);


$listHeader = array_keys($headerList);

$adminList->AddHeaders($headerList);

$selectFields = array_fill_keys($adminList->GetVisibleHeaderColumns(), true);
$selectFields['ID'] = true;
$selectFieldsMap = array_fill_keys(array_keys($headerList), false);
$selectFieldsMap = array_merge($selectFieldsMap, $selectFields);

if (!isset($by)) {
    $by = 'ID';
}
if (!isset($order)) {
    $order = 'ASC';
}

$rowList = array();
$usePageNavigation = true;
$navyParams = array();

$navyParams = \CDBResult::GetNavParams(CAdminUiResult::GetNavSize($adminListTableID));
if ($navyParams['SHOW_ALL']) {
    $usePageNavigation = false;
} else {
    $navyParams['PAGEN'] = (int)$navyParams['PAGEN'];
    $navyParams['SIZEN'] = (int)$navyParams['SIZEN'];
}

global $by, $order;

$getListParams = array(
    'select' => $selectFields,
    'filter' => $filter,
    'order' => array($by => $order)
);

if ($usePageNavigation) {
    $getListParams['limit'] = $navyParams['SIZEN'];
    $getListParams['offset'] = $navyParams['SIZEN'] * ($navyParams['PAGEN'] - 1);
}
$totalPages = 0;
if ($usePageNavigation) {
    $totalCount = CertTable::getCount($getListParams['filter']);
    if ($totalCount > 0) {
        $totalPages = ceil($totalCount / $navyParams['SIZEN']);
        if ($navyParams['PAGEN'] > $totalPages)
            $navyParams['PAGEN'] = $totalPages;
        $getListParams['limit'] = $navyParams['SIZEN'];
        $getListParams['offset'] = $navyParams['SIZEN'] * ($navyParams['PAGEN'] - 1);
    } else {
        $navyParams['PAGEN'] = 1;
        $getListParams['limit'] = $navyParams['SIZEN'];
        $getListParams['offset'] = 0;
    }
}

$getListParams['select'] = array_keys($getListParams['select']);

/*echo '<pre>';
print_r([
    $totalCount,
    $navyParams,
    $selectFieldsMap,
    $adminListTableID,
    'getListParams' => $getListParams,
    LANGUAGE_ID
]);
//print_r([$getListParams, $adminListTableID]);
echo '</pre>';*/


$formIterator = new CAdminUiResult(CertTable::getList($getListParams), $adminListTableID);
if ($usePageNavigation) {
    $formIterator->NavStart($getListParams['limit'], $navyParams['SHOW_ALL'], $navyParams['PAGEN']);
    $formIterator->NavRecordCount = $totalCount;
    $formIterator->NavPageCount = $totalPages;
    $formIterator->NavPageNomer = $navyParams['PAGEN'];
} else {
    $formIterator->NavStart();
}
$onlyDel = false;
$yesNo = [
    'N' => Loc::getMessage("ITSERW_LOTOSWCR_TITLE_NO"),
    'Y' => Loc::getMessage("ITSERW_LOTOSWCR_TITLE_YES"),
];
CTimeZone::Disable();
$adminList->SetNavigationParams($formIterator, array("BASE_LINK" => $selfFolderUrl . "itserw_lotoswcr_cert_list.php"));
while($form = $formIterator->fetch()) {
    $result[]=$form;
}
$prm['SELECT'] = $getListParams['select'];
//TenderComp::reflection($result, $prm);

foreach($result as $form)
{
    $form['ID'] = (int)$form['ID'];
    $urlEdit = $selfFolderUrl . 'itserw_lotoswcr_cert_edit.php?ID=' . $form['ID'] . '&lang=' . LANGUAGE_ID;
    $urlEdit = $adminSidePanelHelper->editUrlToPublicPage($urlEdit);

    $rowList[$form['ID']] = $row = &$adminList->AddRow(
        $form['ID'],
        $form,
        $urlEdit,
        Loc::getMessage("ITSERW_LOTOSWCR_EDIT")
    );

    if ($onlyDel) {
        $row->AddViewField('ID', $form['ID']);
    } else {
        $row->AddViewField('ID', '<a href="' . $urlEdit . '">' . $form['ID'] . '</a>');
    }

    if ($selectFieldsMap['USER_ID']) {
        $row->AddViewField('USER_ID', $form['USER_ID']);
    }

    if ($selectFieldsMap['ORDER_ID']) {
        $row->AddViewField('ORDER_ID', $form['ORDER_ID']);
    }

    if ($selectFieldsMap['CITY']) {
        $row->AddViewField('CITY', $form['CITY']);
    }

    if ($selectFieldsMap['MODEL']) {
        $row->AddViewField('MODEL', $form['MODEL']);
    }

    if ($selectFieldsMap['FIO']) {
        $row->AddViewField('FIO', $form['FIO']);
    }

    if ($selectFieldsMap['EMAIL']) {
        $row->AddViewField('EMAIL', $form['EMAIL']);
    }

    if ($selectFieldsMap['ACTIVE']) {
        $row->AddViewField('ACTIVE', $yesNo[$form['ACTIVE']]);
    }
    
    if ($selectFieldsMap['ITSERW_LOTOSWCR_TITLE_DATE_INSERT']) {
        $row->AddViewField('ITSERW_LOTOSWCR_TITLE_DATE_INSERT', $form['ITSERW_LOTOSWCR_TITLE_DATE_INSERT']->format('d.m.Y H:i:s'));
    }

    if ($selectFieldsMap['ITSERW_LOTOSWCR_TITLE_ORDER_DATE_INSERT']) {
        $row->AddViewField('ITSERW_LOTOSWCR_TITLE_ORDER_DATE_INSERT', $form['ITSERW_LOTOSWCR_TITLE_ORDER_DATE_INSERT']->format('d.m.Y H:i:s'));
    }

    $actions = array();
    if (!$onlyDel) {
        $actions[] = array(
            'ICON' => 'edit',
            'TEXT' => Loc::getMessage("ITSERW_LOTOSWCR_EDIT"),
            'LINK' => $urlEdit,
            'DEFAULT' => true
        );
    }
    if (!$readOnly) {
        $actions[] = array(
            'ICON' => 'delete',
            'TEXT' => Loc::getMessage("ITSERW_LOTOSWCR_DELETE"),
            'ACTION' => "if (confirm('" . Loc::getMessage("ITSERW_LOTOSWCR_DELETE_ALERT") . "')) " . $adminList->ActionDoGroup($form['ID'], 'delete')
        );
    }
    $row->AddActions($actions);
    unset($actions, $row);
}
CTimeZone::Enable();

$adminList->AddGroupActionTable([
    'delete' => true,
    'for_all' => true,

]);

$contextMenu = array();

if (!$readOnly) {
    $addUrl = $selfFolderUrl . "itserw_lotoswcr_cert_edit.php?lang=" . LANGUAGE_ID;
    $addUrl = $adminSidePanelHelper->editUrlToPublicPage($addUrl);
    $contextMenu[] = array(
        'ICON' => 'btn_new',
        'TEXT' => Loc::getMessage('ITSERW_LOTOSWCR_ADD'),
        'TITLE' => Loc::getMessage('ITSERW_LOTOSWCR_ADD'),
        'LINK' => $addUrl
    );
}

if (!empty($contextMenu)) {
    $adminList->setContextSettings(array("pagePath" => $selfFolderUrl . "itserw_lotoswcr_cert_list.php"));
    $adminList->AddAdminContextMenu($contextMenu);
}

$adminList->CheckListMode();

$APPLICATION->SetTitle(Loc::getMessage("ITSERW_LOTOSWCR_PAGE_TITLE"));

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');

$adminList->DisplayFilter($filterFields);
$adminList->DisplayList();

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');
