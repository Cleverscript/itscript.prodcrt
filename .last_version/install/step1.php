<?php
if (!check_bitrix_sessid()) return;
IncludeModuleLangFile(__FILE__);

/** @global CMain $APPLICATION */
?>
<form action="<?=$APPLICATION->GetCurPage();?>">
	<input type="hidden" name="lang" value="<?=LANG;?>">
    <?php
	$message = new CAdminMessage(['MESSAGE' => GetMessage("T_INSTALL"), 'TYPE' => 'OK']);
	echo $message->Show();
	echo GetMessage("T_INSTALL_TEXT");
	?>
	<input style='display:none' type="submit" name="" value="OK">
</form>