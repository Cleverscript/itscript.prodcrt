<?php
namespace Itserw\Lotoswcr\Controller;

use Bitrix\Main\Error;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Engine\ActionFilter\Authentication;
use Bitrix\Main\Engine\ActionFilter\Csrf;
use Bitrix\Main\Engine\ActionFilter\Base;
use Bitrix\Main\Engine\CurrentUser;
use Itserw\Lotoswcr\CertTable;
use Itserw\Lotoswcr\Util;
use Bitrix\Sale\Order;

Loader::includeModule('itserw.lotoswcr');
Loader::includeModule('sale');

class Cert extends Controller
{
    public function configureActions(): array
    {
        return [];
    }

	public function addAction(array $fields):? array
	{
        $orderId = intval($fields['ORDER_ID']);

        if (!$orderId) {
            $this->addError(Loc::getMessage('REQUEST_ADD_ORDER_ID_NULL_ALERT'));
        }

        $addResult = [];

        $user = CurrentUser::get();
        $order = Order::load($orderId);
        $propertyCollection = $order->getPropertyCollection();
        $basket = $order->getBasket();
        $basketItems = $basket->getBasketItems();
        
        $arOrderProps = $propertyCollection->getArray();
        $arrPropCity = array_values(array_filter($arOrderProps['properties'], fn($prop) => $prop['CODE']==='CITY'));
        if (count($arrPropCity)) {
            $arrPropCity = array_shift($arrPropCity);
            if (is_array($arrPropCity['VALUE'])) {
                $arrPropCity['VALUE'] = array_shift($arrPropCity['VALUE']);
            }
        }

        $arrOrder = $order->toArray();

        foreach ($basket as $item) {

            if (!in_array($item->getProductId(), $fields['ITEMS'])) continue;
 
            if (Util::exRequestCertByOrder($orderId, $item->getProductId())) continue;

            $cert = CertTable::createObject();
            $cert->set('ORDER_DATE_INSERT', $arrOrder['DATE_INSERT']);
            $cert->set('USER_ID', $user->getId());
            $cert->set('ORDER_ID', $fields['ORDER_ID']);
            $cert->set('CITY', $arrPropCity['VALUE']);
            $cert->set('MODEL', $item->getField('NAME'));

            $cert->set('PRODUCT_ID', $item->getProductId());

            $cert->set('FIO', $propertyCollection->getPayerName()->getValue() ?: $user->getFullName() ?: $user->getLogin());
            $cert->set('EMAIL', $propertyCollection->getUserEmail()->getValue() ?: $user->getEmail());
    
            $result = $cert->save();

            if (!$result->isSuccess()) {
                $this->addError(new Error($result->getErrorMessages()));
                return null;
            }
            
            $addResult[] = $result->getId();
        }

        if(count($addResult)==1) {
            $resp = ['ALERT' => Loc::getMessage('REQUEST_ADD_SUCCESS_ALERT', 
                ['#ID#' => $addResult[0]]), 
                'STATUS' => 'success'
            ];
        } elseif(count($addResult)>1) {
            $resp =['ALERT' => Loc::getMessage('REQUEST_ADDS_SUCCESS_ALERT', 
                ['#IDS#' => implode(', ', array_map(fn($id) => '#' . $id, $addResult))]),
                'STATUS' => 'success'
            ];
        } else {
            $resp = [
                'ALERT' => Loc::getMessage('REQUEST_ADDS_NULL_ALERT'), 
                'STATUS' => 'warning'
            ];
        }

        return $resp;
	}

	public function viewAction(int $id):? array
	{
        $cert = CertTable::getByPrimary($id)->fetchObject();

		if (!$cert)
		{
			$this->addError(new Error('Could not find item.', 400));

			return null;
		} 

		return $cert->toArray();
	}
}