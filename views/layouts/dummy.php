<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $content string */
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8"/>
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body id="dummyPage">

    <?php $this->beginBody() ?>

    <div class="wrap">
        <div class="container">
            <?= $content ?>
        </div>
    </div>

    <?php $this->endBody() ?>
<!--    <script src="/libs/vue.js"></script>-->
    </body>
    </html>
<?php $this->endPage() ?>