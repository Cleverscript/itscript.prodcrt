<?php 
$basePath = $_SERVER["DOCUMENT_ROOT"];
$filePath = "modules/itscript.prodcrt/admin/itscript_prodcrt_cert_edit.php";
if(file_exists($basePath . "/bitrix/" . $filePath)) {
    require($basePath . "/bitrix/" . $filePath);
} elseif($basePath . "/local/" . $filePath) {
    require($basePath . "/local/" . $filePath);
}

