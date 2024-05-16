# Prodcert - модуль "Регистрация гарантийных сертификатов" для 1C-Bitrix

---

Модуль позволяет пользователям запросить выпуск гарантийного сертификата на товары из своего заказа.

---

### Установка

- 1. Загрузите архив с модулем в директорию /bitrix/modules используя FTP или через админку
- 2. Распакуйте архив с модулем
- 3. Переименуйте появившуюся директорию /bitrix/modules/.last_version в /bitrix/modules/itscript.prodcrt
- 4. Установите модуль стандартным образом (Рабочий стол => Marketplace => Установленные решения)
- 5. Встройте на страницу личного кабинета пользователя 2 компонента

```php
 <?$APPLICATION->IncludeComponent(
	"itscript:prodcrt.list",
	"",
	Array(
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"DISPLAY_DATE" => "Y",
		"LIMIT" => "20",
		"NAV_SEF_MODE" => "Y",
		"SET_TITLE" => "Y"
	)
);?><?$APPLICATION->IncludeComponent(
	"itscript:prodcrt.form",
	"",
	Array(
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"DISPLAY_DATE" => "Y",
		"NAV_SEF_MODE" => "Y"
	)
);?>
```

![Иллюстрация к проекту](https://github.com/Cleverscript/itscript.prodcrt/raw/main/1.png)
![Иллюстрация к проекту](https://github.com/Cleverscript/itscript.prodcrt/raw/main/2.png)
![Иллюстрация к проекту](https://github.com/Cleverscript/itscript.prodcrt/raw/main/32.png)
![Иллюстрация к проекту](https://github.com/Cleverscript/itscript.prodcrt/raw/main/42.png)