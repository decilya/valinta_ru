<?php

/* @var $this \yii\web\View */

/* @var $content string */

use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use app\assets\AppAsset;
use kartik\alert\Alert;
use app\assets\BootboxAsset;

AppAsset::register($this);
BootboxAsset::overrideSystemConfirm();
 $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body id="adminPages">
    <?php $this->beginBody() ?>
    <div class="wrap">

        <?php
        NavBar::begin([
            'brandLabel' => 'Valinta.ru',
            'brandUrl' => null,
            'options' => [
                'class' => 'navbar-inverse navbar-fixed-top',
            ],
        ]);

        echo Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-right'],
            'items' => [
                ['label' => 'Заказчики', 'url' => '/customer/admin-customers-list', 'visible' => Yii::$app->user->identity->is_admin],
                ['label' => 'Заказы', 'url' => '/order/admin-list', 'visible' => Yii::$app->user->identity->is_admin],
                ['label' => 'Сметчики', 'url' => '/user/index', 'visible' => Yii::$app->user->identity->is_admin],
                ['label' => 'Заявки', 'url' => '/request/index', 'visible' => Yii::$app->user->identity->is_admin],
                ['label' => 'Отчеты', 'url' => '/report/all', 'visible' => Yii::$app->user->identity->is_admin],

                Yii::$app->user->isGuest ? (
                ['label' => 'Login', 'url' => '/site/login']
                ) : (
                    '<li>'
                    . Html::beginForm('/site/logout', 'post', ['class' => 'navbar-form'])
                    . Html::submitButton(
                        'Выйти (' . Yii::$app->user->identity->login . ')',
                        ['class' => 'btn btn-link']
                    )
                    . Html::endForm()
                    . '</li>'
                )
            ],
        ]);
        NavBar::end();
        ?>
        <div class="container">
            <?= $content ?>
        </div>
    </div>

    <?php if (!empty(Yii::$app->params['enableYandexCounter'])) : ?>
        <!-- Yandex.Metrika counter -->
        <script type="text/javascript">
            (function (d, w, c) {
                (w[c] = w[c] || []).push(function () {
                    try {
                        w.yaCounter40335760 = new Ya.Metrika({
                            id: 40335760,
                            clickmap: true,
                            trackLinks: true,
                            accurateTrackBounce: true,
                            webvisor: true,
                            trackHash: true
                        });
                    } catch (e) {
                    }
                });

                var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () {
                    n.parentNode.insertBefore(s, n);
                };

                s.type = "text/javascript";
                s.async = true;
                s.src = "https://mc.yandex.ru/metrika/watch.js";
                if (w.opera == "[object Opera]") {
                    d.addEventListener("DOMContentLoaded", f, false);
                } else {
                    f();
                }
            })(document, window, "yandex_metrika_callbacks");
        </script>
        <noscript>
            <div><img src="https://mc.yandex.ru/watch/40335760" style="position:absolute; left:-9999px;" alt=""/></div>
        </noscript>
        <!-- /Yandex.Metrika counter -->
    <?php endif; ?>


    <?php $this->endBody() ?>
<!--    <script src="/libs/vue.js"></script>-->
    </body>
    </html>
<?php $this->endPage() ?>