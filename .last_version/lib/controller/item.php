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

Loader::includeModule('itscript.lotoswcr');

class Item extends Controller
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

	public function addAction(array $fields):? array
	{
        $question = CertTable::createObject();
        $question->set('USER_ID', CurrentUser::get()->getId());
        $question->set('ENTITY_ID', $fields['ENTITY_ID']);
        $question->setActive($fields['ACTIVE']);
        $question->setUrl($fields['URL']);
        $question->setQuestion(Util::clearQuestionText($fields['QUESTION']));

        $result = $question->save();

        if (!$result->isSuccess())
        {
            $this->addError(new Error($result->getErrorMessages()));
            return null;
        }
        
        $id = $result->getId();

		return ['ID' => $id, 'ALERT' => Loc::getMessage('REQUEST_ADD_SUCCESS_ALERT', ['#ID#' => $id])];
	}

	public function viewAction(int $id):? array
	{

        $cert = CertTable::getByPrimary($id)->fetchObject();

        //echo '<pre>';
        //var_dump($cert);
        //echo '</pre>';

		if (!$cert)
		{
			$this->addError(new Error('Could not find item.', 400));

			return null;
		} 

		return $cert->toArray();
	}
}