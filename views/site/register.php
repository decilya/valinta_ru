<?php

/* @var $this yii\web\View */

use app\assets\MaskAsset;

$this->title = 'Регистрация';

MaskAsset::register($this);

//$this->registerJsFile(Yii::getAlias('@web')."/js/extraPhoneNumbers.js");

?>


<div class="site-register">

    <?= $content ?>

</div>