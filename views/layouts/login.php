<?php

/* @var $this \yii\web\View */

/* @var $content string */

use yii\helpers\Html;
use app\assets\AppAsset;

AppAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <style>
        .banner {
            display: none;
            min-height: 90px;
            background: url('/img/blackF.png') #00294f center no-repeat;
            padding-bottom: 35px;
        }
    </style>
</head>
<body id="loginPage">z
<!--<a href="http://wizardsoft.ru/about/news/1255-chjornaya-pyatnitsa/" target="_blank" class="banner"></a>-->
<?php $this->beginBody() ?>

<div class="wrap">
    <?= $content ?>
</div>


<?php $this->endBody() ?>
<!--<script src="/libs/vue.js"></script>-->
</body>
</html>
<?php $this->endPage() ?>
