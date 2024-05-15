<?php
/** @global CMain $APPLICATION */
if (!check_bitrix_sessid()) {
    return;
}
?>
<form action="<?=$APPLICATION->GetCurPage();?>">
    <p>
        <input type="hidden" name="lang" value="<?=LANG;?>">
        <input type="submit" name="" value="<?=GetMessage('MOD_BACK'); ?>">
    </p>
<form></form>