<div class="wcr-list-over">
    <h5><?=getMessage('T_WCR_CERT_LIST_TITLE');?></h5>
    <?php if (!empty($arResult['ITEMS'])): ?>
        <?php if (count($arResult['ITEMS'])): ?>
        <ul>
            <?php foreach($arResult['ITEMS'] as $key => $order): ?>
            <li>
                <b>
                <?=getMessage('T_ORDER_INFO', [
                        '#O_ID#' => $key, 
                        '#O_DATE_INS#' => $order['ORDER_DATE_INSERT']->format("d.m.Y H:i:s")
                ]);
                ?>
                </b>
            
                <?php foreach($order['CERTS'] as $cert): ?>
                    <br/>
                    <a href="<?=$cert['FILE_SRC'];?>" target="_blank">
                        <?=getMessage('T_CERT_DOWN', ['#ID#' => $cert['ID']]);?>
                    </a>
                <?php endforeach; ?> 
            </li>    
            <?php endforeach; ?>    
        </ul>

        <?php
        $APPLICATION->IncludeComponent(
            "bitrix:main.pagenavigation",
            "",
            array(
                "NAV_OBJECT" => $arResult['NAV'],
                "SEF_MODE" => $arParams['NAV_SEF_MODE'],
            ),
            false
        );
        ?>
        <? endif; ?>
    <?php else: ?>
        <p><div class="alert warning"><?=getMessage('T_ITEMS_EMPTY');?></div></p>
    <?php endif; ?>
</div>


<?php

/*echo '<pre>';
print_r($arResult);
print_r($arParams);
echo '</pre>';*/
