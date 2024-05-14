
<div class="wcr-form-over">
    
    <? /*
    <button id="wcr-add-btn-js" class="btn btn-primary wcr-add-btn">
        <?=GetMessage("T_QUESTION_ADD");?>
    </button>*/ ?>

    <div id="wcr-form-over-js" class="wcr-form">
        <div id="wcr-form-js-alert" class="wcr-form-alert"></div>
        <form id="wcr-form-js" action="" method="POST">
            <div class="over-field">
                <label for="wcr-field"><?=GetMessage("T_QUESTION_FIELD_LABEL");?></label>

                <input id="order-id-js" type="text" name="ORDER_ID" value="<?=$_POST['ORDER_ID'] ?? null;?>" placeholder="ORDER_ID"/>

                <hr/>

                <div id="res-container-js">
                    <ul id="order-basket-items-js" class="order-basket-items"></ul>

                    <div class="over-field">
                        <button id="wcr-form-btn-js" type="button" class="btn btn-primary wcr-send-btn">
                            <?=GetMessage("T_BUTTON_SEND");?>
                        </button>
                    </div>
                </div>
        
            </div>


            <input type="hidden" name="USER_ID" value="<?=$arResult['USER_ID'];?>"/>
        </form>
    </div>
</div>

<?php

//echo '<pre>';
//print_r($arResult);
//print_r($arParams);
//echo '</pre>';
