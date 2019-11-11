<?php

//
//die('logform');

/* @var $this \yii\web\View */

/* @var $content string */

use app\models\Site;
use yii\helpers\Html;
use app\assets\AppAsset;
use kartik\alert\Alert;
use app\assets\BootboxAsset;

AppAsset::register($this);
BootboxAsset::overrideSystemConfirm();

?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head(); ?>
        <style>
            .banner {
                display: none;
                min-height: 90px;
                background: url('/img/blackF.png') #00294f center no-repeat;
                padding-bottom: 35px;
            }
        </style>
    </head>
    <body id="frontPages">
    <!--  <a href="http://wizardsoft.ru/about/news/1255-chjornaya-pyatnitsa/" target="_blank" class="banner"></a> -->

    <?php $this->beginBody() ?>

    <div class="wrap">

        <header>
            <?php include('topMenu.php'); ?>
        </header>

        <div class="container">
            <?php

            $successFlashMessage = Yii::$app->session->getFlash('success');
            $errorFlashMessage = Yii::$app->session->getFlash('error');

            if ($successFlashMessage) {
                echo Alert::widget([
                    'type' => Alert::TYPE_SUCCESS,
                    'icon' => 'glyphicon glyphicon-ok-sign',
                    'body' => $successFlashMessage,
                    'showSeparator' => true,
                    'delay' => 2000
                ]);
            }

            if ($errorFlashMessage) {
                echo Alert::widget([
                    'type' => Alert::TYPE_DANGER,
                    'icon' => 'glyphicon glyphicon-remove-sign',
                    'body' => $errorFlashMessage,
                    'showSeparator' => true,
                    'delay' => 8000
                ]);
            }
            ?>
        </div>



        <?= $content ?>


        <footer>
            <div class="container">
                <div class="tableHolder">
                    <div class="tableCell">
                        <p class="b-footer__title">Полезные статьи</p>
                        <a href="/how-to-work-with-smetchik-freelancer" class="b-footer__article">Как правильно работать
                            со
                            сметчиком-фрилансером?</a><br>
                        <a href="/how-much-does-smeta-cost" class="b-footer__article">Сколько стоит составить смету?</a><br>
                        <a href="/how-to-choose-smetchik" class="b-footer__article">Как правильно выбрать
                            сметчика?</a><br>
                        <a href="/five_ways_to_protect" class="b-footer__article">5 способов сметчику-фрилансеру защитить себя<br> от мошенников, которые не хотят оплачивать заказ</a><br>
                    </div>

                    <div class="tableCell">
                        <p class="b-footer__title">Акции и предложения</p>
                        <a href="/request" class="b-footer__article">Предложение для сметчиков!</a><br>

                        <p class="b-footer__title" style="margin-bottom: 10px !important;">Новости</p>
                        <a href="/news" class="b-footer__article">Изменения на портале</a>

                    </div>
                    <div class="tableCell">
                        <p class="b-footer__title">Нужна консультация?</p>

                        <p class="b-footer__tel"> Телефон: <?= Yii::$app->params['mainSystemPhone']; ?></p>

                        <p class="b-footer__email">E-mail: <a href="mailto:info@valinta.ru">info@valinta.ru</a></p>

                        <noscriptp class="b-footer__privacy-policy">
                            <a href="/privacy-policy" title="Политика конфиденциальности" target="_blank">
                                Политика конфиденциальности
                            </a>
                        </noscriptp>
                        <p class='hotline'>
                            <span class='hotline-phrase'>&laquo;Горячая&raquo; линия: </span>
                            <a href='viber://pa?chatURI=valinta' class='hotline-viber' title='viber'></a>
                            <a title="Telegram" href="tg://resolve?domain=valinta_bot" class='hotline-telegram'></a>
                        </p>
                    </div>
                </div>
                <div class="b-footer__copyright">
                    <span class="b-footer__copyright">&copy; <?= date('Y', time()) ?>
                        ЧОУ ДПО &laquo;ИПАП&raquo;</span>
                </div>
            </div>
        </footer>
    </div>


    <?php if (!empty(Yii::$app->params['enableJivosite'])) : ?>
        <!-- BEGIN JIVOSITE CODE {literal} -->
        <script type='text/javascript'>
            (function () {
                var widget_id = 'g4osY8nQcM';
                var d = document;
                var w = window;

                function l() {
                    var s = document.createElement('script');
                    s.type = 'text/javascript';
                    s.async = true;
                    s.src = '//code.jivosite.com/script/widget/' + widget_id;
                    var ss = document.getElementsByTagName('script')[0];
                    ss.parentNode.insertBefore(s, ss);
                }

                if (d.readyState == 'complete') {
                    l();
                } else {
                    if (w.attachEvent) {
                        w.attachEvent('onload', l);
                    } else {
                        w.addEventListener('load', l, false);
                    }
                }
            })();
        </script>
        <!-- {/literal} END JIVOSITE CODE -->
    <?php endif; ?>

    <?php if (!empty(Yii::$app->params['enableYandexCounter'])): ?>
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


    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-128576397-1"></script>


    <?php $this->endBody() ?>
<!--    <script src="/libs/vue.js"></script>-->
    </body>
    </html>
<?php $this->endPage() ?>
