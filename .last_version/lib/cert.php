<?php
namespace Itserw\Lotoswcr;

use Bitrix\Main\UserTable;
use Bitrix\Main\Type\Date;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\BooleanField;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\Entity\Validator\Length;
use Bitrix\Main\Entity\Validator\RegExp;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Query\Join;

class CertTable extends DataManager
{
	public static function getMap()
	{
		return [
			new IntegerField('ID', [
				'title' => 'ID',
				'primary' => true,
				'autocomplete' => true
            ]),

			new IntegerField('USER_ID', [
				'title' => Loc::getMessage('CERT_TABLE_TITLE_USER_ID'),
				'required' => true,
				'format' => '/^[0-9]{1,}$/',
				'validation' => function () {
					return [
						new RegExp('/^[0-9]{1,}$/')
					];
				},
            ]),

			(new Reference(
					'USER',
					UserTable::class,
					Join::on('this.USER_ID', 'ref.ID')
			))->configureJoinType('inner'),


			new IntegerField('ORDER_ID', [
				'title' => Loc::getMessage('CERT_TABLE_TITLE_ORDER_ID'),
				/*'required' => true,*/
				'format' => '/^[0-9]{1,}$/',
				'validation' => function () {
					return [
						new RegExp('/^[0-9]{1,}$/')
					];
				},
            ]),

			(new Reference(
					'ORDER',
					OrderTable::class,
					Join::on('this.ORDER_ID', 'ref.ID')
			))->configureJoinType('inner'),

            new BooleanField('ACTIVE', [
				'title' => Loc::getMessage('CERT_TABLE_TITLE_ACTIVE'),
                'values' => array('N', 'Y')
            ]),

			new StringField('FIO', [
				'title' => Loc::getMessage('CERT_TABLE_TITLE_FIO'),
                'required' => true,
				'size' => 256,
				'validation' => function () {
					return [
						new Length(null, 256),
					];
				},
            ]),

			new StringField('EMAIL', [
				'title' => Loc::getMessage('CERT_TABLE_TITLE_EMAIL'),
                'required' => true,
				'size' => 256,
				'validation' => function () {
					return [
						new Length(null, 256),
					];
				},
            ]),

			new StringField('CITY', [
				'title' => Loc::getMessage('CERT_TABLE_TITLE_CITY'),
                'required' => true,
				'size' => 256,
				'validation' => function () {
					return [
						new Length(null, 256),
					];
				},
            ]),

			new StringField('MODEL', [
				'title' => Loc::getMessage('CERT_TABLE_TITLE_MODEL'),
                'required' => true,
				'size' => 256,
				'validation' => function () {
					return [
						new Length(null, 256),
					];
				},
            ]),

			new IntegerField('FILE_ID', [
				'title' => Loc::getMessage('CERT_TABLE_TITLE_FILE_ID'),
				'format' => '/^[0-9]{1,}$/',
            ]),

			new DatetimeField('DATE_INSERT', [
				'title' => Loc::getMessage('CERT_TABLE_TITLE_DATE_INSERT'),
				'default_value' => new DateTime
			]),

			new DatetimeField('ORDER_DATE_INSERT', [
				'title' => Loc::getMessage('CERT_TABLE_TITLE_ORDER_DATE_INSERT'),
				'default_value' => new DateTime
			]),

        ];
	}
}