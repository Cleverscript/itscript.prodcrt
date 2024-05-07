<?php 
/** @global CMain $APPLICATION */

use Bitrix\Main;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc; 
use Bitrix\Main\Config\Option;
use Bitrix\Main\Application;
use Bitrix\Main\UI\PageNavigation;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Engine\CurrentUser;
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

$adminListTableID = 'b_itserw_lotoswcr_cert';

$selfFolderUrl = $adminPage->getSelfFolderUrl();
$listUrl = $selfFolderUrl . "itserw_lotoswcr_cert_list.php?lang=" . LANGUAGE_ID;
$listUrl = $adminSidePanelHelper->editUrlToPublicPage($listUrl);

$request = Main\Context::getCurrent()->getRequest();
$prefix = '';

$aTabs = array(
    array(
        "DIV" => "edit1",
        "TAB" => Loc::getMessage("ITSERW_LOTOSWCR_TITLE"),
        "ICON" => "lotoswcr_form",
        "TITLE" => Loc::getMessage("ITSERW_LOTOSWCR_TITLE"),
    ),
);

$eventFormID = 'formControl';
$tabControl = new CAdminForm($eventFormID, $aTabs);

$tabControl->SetShowSettings(false);

$errors = array();
$fields = array();
$copy = false;
$eventID = (int)$request->get('ID');
if ($eventID < 0)
    $eventID = 0;

if ($eventID > 0)
    $copy = ($request->get('action') == 'copy');

$allFields = [
    'CITY' => 'string',
    'MODEL' => 'string',
    'FIO' => 'string',
    'EMAIL' => 'string'
];

if (check_bitrix_sessid()
    && !$readOnly
    && $request->isPost()
    && (string)$request->getPost('Update') == 'Y'
) {
    $adminSidePanelHelper->decodeUriComponent($request);
    $rawData = $request->getPostList();

    //echo '<pre>';
    //var_dump($rawData);
    //echo '<pre>';
	
    // set value fields
    $fields = [
        'ID' => $rawData->get('ID'),
        'ACTIVE' => $rawData->get('ACTIVE'),
        'DATE_INSERT' => new DateTime(),
        'ORDER_DATE_INSERT' => new DateTime(),
        'USER_ID' => CurrentUser::get()->getId(),
        'ORDER_ID' => CurrentUser::get()->getId(),
        'CITY' => trim($rawData->get('CITY')),
        'MODEL' => trim($rawData->get('MODEL')),
        'EMAIL' => trim($rawData->get('EMAIL'))
    ];

    // is add new
    if (!$eventID) {
        //$fields['ENTITY_ID'] = $rawData->get('ENTITY_ID');
        //$fields['USER_ID'] = CurrentUser::get()->getId();
    }

    if ($eventID == 0 || $copy) {
        $result = CertTable::add($fields);
    } else {
        $result = CertTable::update($eventID, $fields);
    }
    if (!$result->isSuccess()) {
        $errors = $result->getErrorMessages();
    } else {
        if ($eventID == 0 || $copy)
            $eventID = $result->getId();

        if ((string)$request->getPost('apply') != '') {
            $applyUrl = $selfFolderUrl . 'itserw_lotoswcr_cert_edit.php?lang=' . LANGUAGE_ID . '&ID=' . $eventID . '&' . $tabControl->ActiveTabParam();
            LocalRedirect($applyUrl);
        } else {
            LocalRedirect($listUrl);
        }
    }
    unset($result, $rawData);
}
$APPLICATION->SetTitle(
    $eventID == 0
        ? Loc::getMessage("ITSERW_LOTOSWCR_ADD")
        : (
    !$copy
        ? Loc::getMessage('ITSERW_LOTOSWCR_EDIT', array('#ID#' => $eventID))
        : Loc::getMessage('ITSERW_LOTOSWCR_FORM_EDIT_COPY', array('#ID#' => $eventID))
    )
);

$APPLICATION->SetTitle(Loc::getMessage("ITSERW_LOTOSWCR_PAGE_TITLE"));

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php'); ?>

<?php
$contextMenuItems = array(
    array(
        'ICON' => 'btn_list',
        'TEXT' => Loc::getMessage("ITSERW_LOTOSWCR_LIST"),
        'LINK' => $listUrl
    )
);

if (!$readOnly && $eventID > 0) {
    if (!$copy) {
        $addUrl = $selfFolderUrl . "itserw_lotoswcr_cert_edit.php?lang=" . LANGUAGE_ID;
        $addUrl = $adminSidePanelHelper->editUrlToPublicPage($addUrl);
        if (!$adminSidePanelHelper->isPublicFrame())
            $addUrl = $adminSidePanelHelper->setDefaultQueryParams($addUrl);
        $contextMenuItems[] = array(
            'ICON' => 'btn_new',
            'TEXT' => Loc::getMessage("ITSERW_LOTOSWCR_ADD"),
            'LINK' => $addUrl
        );
        $copyUrl = $selfFolderUrl . "itserw_lotoswcr_cert_edit.php?lang=" . LANGUAGE_ID . "&ID=" . $eventID . "&action=copy";
        $copyUrl = $adminSidePanelHelper->editUrlToPublicPage($copyUrl);
        if (!$adminSidePanelHelper->isPublicFrame())
            $copyUrl = $adminSidePanelHelper->setDefaultQueryParams($copyUrl);
        $contextMenuItems[] = array(
            'ICON' => 'btn_copy',
            'TEXT' => Loc::getMessage("ITSERW_LOTOSWCR_EDIT"),
            'LINK' => $copyUrl
        );
        $deleteUrl = $selfFolderUrl . "itserw_lotoswcr_cert_list.php?lang=" . LANGUAGE_ID . "&ID=" . $eventID . "&action=delete&" . bitrix_sessid_get();
        $buttonAction = "LINK";
        if ($adminSidePanelHelper->isPublicFrame()) {
            $deleteUrl = $adminSidePanelHelper->editUrlToPublicPage($deleteUrl);
            $buttonAction = "ONCLICK";
        }
        $contextMenuItems[] = array(
            'ICON' => 'btn_delete',
            'TEXT' => Loc::getMessage("ITSERW_LOTOSWCR_DELETE"),
            $buttonAction => "javascript:if(confirm('" . CUtil::JSEscape(Loc::getMessage("ITSERW_LOTOSWCR_DELETE_ALERT")) . "')) top.window.location.href='" . $deleteUrl . "';",
            'WARNING' => 'Y',
        );
    }
    $formIterator = QnaTable::getList(
        [
            'filter' => ['ID' => $eventID],
            'select'=>['*']
        ]
    );
    $event = $formIterator->fetch();
}

$contextMenu = new CAdminContextMenu($contextMenuItems);
$contextMenu->Show();
unset($contextMenu, $contextMenuItems);

if (!empty($errors)) {
    $errorMessage = new CAdminMessage(
        array(
            'DETAILS' => implode('<br>', $errors),
            'TYPE' => 'ERROR',
            'MESSAGE' => Loc::getMessage("ITSERW_LOTOSWCR_FORM_EDIT_ERROR_SAVE"),
            'HTML' => true
        )
    );
    echo $errorMessage->Show();
    unset($errorMessage);
}

$bLinked = ($copy) && $_POST["linked_state"] !== 'N';

$tabControl->BeginPrologContent();
$tabControl->EndPrologContent();
$tabControl->BeginEpilogContent();
echo GetFilterHiddens("filter_"); ?>
    <input type="hidden" name="linked_state" id="linked_state" value="<?php if ($bLinked) echo 'Y'; else echo 'N'; ?>">
    <input type="hidden" name="Update" value="Y">
    <input type="hidden" name="lang" value="<?=LANGUAGE_ID;?>">
    <input type="hidden" name="ID" value="<?=$eventID;?>">
<?php
if ($copy) {
    ?>
    <input type="hidden" name="action" value="copy">
<?php
}
if (!empty($returnUrl)) { ?>
    <input type="hidden" name="return_url" value="<?=htmlspecialcharsbx($returnUrl);?>">
<?php
}
echo bitrix_sessid_post();
$tabControl->EndEpilogContent();

$eventActionUrl = $selfFolderUrl . 'itserw_lotoswcr_cert_edit.php?lang=' . LANGUAGE_ID;
$eventActionUrl = $adminSidePanelHelper->setDefaultQueryParams($eventActionUrl);

$tabControl->Begin(["FORM_ACTION" => $eventActionUrl]);

$tabControl->BeginNextFormTab();

if ($eventID > 0 && !$copy) {
    $tabControl->AddViewField($prefix . 'ID', 'ID', $eventID, false);
}
$tabControl->AddCheckBoxField("ACTIVE",
    Loc::getMessage("ITSERW_LOTOSWCR_TITLE_ACTIVE").":",
    false, array("Y", "N"),
    ($event['ACTIVE'] == "Y" || $fields['ACTIVE'] == "Y")
);

//echo '<pre>';
//var_dump($_POST);
//echo '<pre>';

// is add new
if (!$eventID) {
    $tabControl->BeginCustomField('ORDER_ID', Loc::getMessage("ITSERW_LOTOSWCR_TITLE_ORDER_ID"), true);
    ?>
      <tr id="tr_ORDER_ID">
          <td class="adm-detail-content-cell-l"><?=$tabControl->GetCustomLabelHTML();?></td>
          <td class="adm-detail-content-cell-r">
                <input type="text" name="ORDER_ID" id="ORDER_ID" value="<?=$fields['ORDER_ID'];?>">
          </td>
      </tr>
      <?
    $tabControl->EndCustomField('ORDER_ID', '');
}

foreach ($allFields as $fld => $type) {
    $tabControl->BeginCustomField($fld, Loc::getMessage("ITSERW_LOTOSWCR_FIELD_TITLE_" . $fld), false);
    ?>
    <tr id="tr_<?=$fld?>">
        <td class="adm-detail-content-cell-l"><?=$tabControl->GetCustomLabelHTML(); ?></td>
        <td class="adm-detail-content-cell-r">
            <? if ($type=='string') { ?>
                <input type="text" size='35' name="<?=$fld?>" id="<?=$fld?>" value="<?=$event[$fld]?>">
            <? } elseif ($type=='int') { ?>
            <input type="int" min="0" size='35' name="<?=$fld?>" id="<?=$fld?>" value="<?=$event[$fld]?>">
            <? } elseif ($type=='text') { ?>

                <?php if ($fld=='QUESTION' && $eventID):?>
                    <textarea cols="60" rows="5" name="" disabled><?=$event[$fld]??$fields[$fld]?></textarea>
                    <input type="hidden" name="<?=$fld?>" id="<?=$fld?>" value="<?=$event[$fld]?>">
                <?php else: ?>
                    <textarea cols="60" rows="5" name="<?=$fld?>" id="<?=$fld?>"><?=$event[$fld]??$fields[$fld]?></textarea>
                <?php endif;?>

            
            <? } elseif ($type=='date') { ?>
                <?=CAdminCalendar::CalendarDate($fld, $event[$fld], 19, true);?>
            <? } ?>
        </td>
    </tr>
    <?
    $tabControl->EndCustomField($fld, '');
}


$tabControl->Buttons(array('disabled' => $readOnly, 'back_url' => $listUrl));
$tabControl->Show();
$tabControl->End();
?>
<?php require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');
