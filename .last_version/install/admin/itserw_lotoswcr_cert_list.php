<?php 
$basePath = $_SERVER["DOCUMENT_ROOT"];
$filePath = "modules/itserw.lotoswcr/admin/itserw_lotoswcr_cert_list.php";
if(file_exists($basePath . "/bitrix/" . $filePath)) {
    require($basePath . "/bitrix/" . $filePath);
} elseif($basePath . "/local/" . $filePath) {
    require($basePath . "/local/" . $filePath);
}