<?php
namespace itscript\Prodcrt;

use Bitrix\Main;
use Bitrix\Main\Config\Option;
use Itscript\Prodcrt\CertTable;

IncludeModuleLangFile(__FILE__);

class Util
{
    const MODULE_ID = "itscript.prodcrt";

    /**
     * Check exists product id from order in requests
     * @param int $orderId, int $productId
     */
    public static function exRequestCertByOrder(int $orderId, int $productId) : bool
    {
        $row = CertTable::getList(array(
            'select' => [
                'ID', 
            ],
            'filter' => array('=ORDER_ID' => $orderId, '=PRODUCT_ID' => $productId),
            'count_total' => true
        ))->fetchAll();
        
        return count($row)>0;
    }

    /**
     * Function print var
     * @param $value
     */
    public static function debug($value) {
        echo "<br/><pre style='padding:10px; border:1px solid #DDD; background-color:#EEE; text-color:#000; font-family:Verdana; font-size:13px;'>";

        switch (gettype($value))
        {
            case 'integer':
            case 'double':
            case 'string':
            case 'array':
                print_r($value);
                break;
            default:
                var_dump($value);
                break;
        }

        echo "</pre><br/>";
    }

    public static function uploadFile(array $file, string $del = 'N'): ?int
    {
        return \CFile::SaveFile([
            "name" => $file["name"],
            "size" => $file["size"],
            "tmp_name" => $file["tmp_name"],
            "type" => $file["type"],
            "old_file" => "",
            "del" => $del,
            "MODULE_ID" => "itscript.prodcrt"
        ], "/itscript_prodcrt_cert/");
    }

}