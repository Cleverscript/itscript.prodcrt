<?php
namespace Itserw\Lotoswcr\Controller;

use Bitrix\Main\Error;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Engine\CurrentUser;
use Itserw\Lotoswcr\CertTable;
use Itserw\Lotoswcr\Util;

Loader::includeModule('itserw.lotoswcr');
Loader::includeModule('sale');

Loc::loadMessages(__FILE__); 
IncludeModuleLangFile(__FILE__);

class Order extends Controller
{
    /*public function configureActions(): array
    {
        return [
            //Название вашего Action
            'add' => [
                //Отключение фильтра
                '-prefilters' => [
                    ActionFilter\Authentication::class,
                ],
                //Включение фильтра                
                'prefilters' => [
                    ActionFilter\Csrf::class,
                ],
            ],
        ];
    }*/

	public function getAction(int $id):? array
	{

        $order = \Bitrix\Sale\Order::load($id);

        //echo '<pre>';
        //var_dump($cert);
        //echo '</pre>';

		if (!$order) {
			$this->addError(new Error(
                Loc::getMessage('CERT_ORDER_NOT_FOUND_ERROR', ['#ORDER_ID#' => $id]), 
            404));

			return null;
		}

		if (!$order->isPaid()) {
			$this->addError(new Error(
                Loc::getMessage('CERT_ORDER_NOT_PAYED_ERROR', ['#ORDER_ID#' => $id]), 
            403));

			return null;
		}

        if ($order->getUserId() != CurrentUser::get()->getId()) {
			$this->addError(new Error(
                Loc::getMessage('CERT_ORDER_NOT_ACCESS_ERROR', ['#ORDER_ID#' => $id]), 
            403));

			return null;
		}


        //$order->getId();

		return $order->toArray();
	}
}