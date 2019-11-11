<?php

$this->title = "Регистрация";
?>

<div class="site-content">
    <div class="b-site-content__useful-art"><?= $this->title; ?></div>
    <div class="container">
        <div class="registration__title">
            <h3>Зарегистрироваться как...</h3>
        </div>
        <div class="col-lg-1 col col-sm-1 col-md-1"></div>
        <div class="col-lg-2 col col-sm-2 col-md-2"></div>
        <div class="col-lg-6 col col-sm-6 col-md-6" style="">
            <div class="wrap__smetchik-zakazchik">
                <a href="<?= \yii\helpers\Url::to(['site/register']) ?>"
                   class="btn-reg-smetch-zak">Сметчик</a>
                <a href="<?= \yii\helpers\Url::to(['customer/customer-registration']) ?>"
                   class="btn-reg-smetch-zak">Заказчик</a>
            </div>
        </div>
        <div class="col-lg-2 col col-sm-2 col-md-2"></div>
        <div class="col-lg-1 col col-sm-1 col-md-1"></div>
    </div>


    

</div>