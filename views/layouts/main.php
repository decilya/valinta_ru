<?php

/* @var $this \yii\web\View */

/* @var $content string */

use app\models\Site;
use yii\helpers\Html;
use app\assets\AppAsset;
use kartik\alert\Alert;
use app\assets\BootboxAsset;

AppAsset::register($this);
BootboxAsset::overrideSystemConfirm();
?><?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name='wmail-verification' content='cb0660740ff50e57e66b412bafa09301'/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="yandex-verification" content="cfa09985484c6ac8"/>
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head(); ?>

    <style>
        .banner {
            font-family: 'Roboto', sans-serif;
            font-weight: 700 !important;
            font-size: 24px;
            display: flex;
            align-items: center;
            text-align: center;
            min-height: 57px;
            height: auto;
            background-image: url("/img/happytree.png");
            /*background-size: cover;*/
            text-transform: uppercase;
            text-decoration: none !important;
            margin-bottom: 20px !important;
        }
    </style>

    <script type="text/javascript">
        $(document).ready(function () {

            var defaults = {
                containerID: 'toTop', // fading element id
                containerHoverID: 'toTopHover', // fading element hover id
                scrollSpeed: 1200,
                easingType: 'linear'
            };

            $().UItoTop({easingType: 'easeOutQuart'});

        });
    </script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css">
    <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js" crossorigin="anonymous"></script>
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
</head>

<body id="frontPages">

<input id="typeOfUser" type="hidden" value="<?= \app\models\Auth::getUserType(); ?>"
       data-user_id="<?= \app\models\Auth::getUserRealId(); ?>">

<?php $this->beginBody() ?>

<div class="wrap">


    <header class="fixbag">

        <div id="topHeader">
            <?php include('topMenu.php'); ?>
        </div>

        <div class="container resp-cont">
            <div class="resp-cont__wrap">
                <div class="block__logo">
                    <div class="row-logo">
                        <div class="">
                            <div class="logoHolder">
                                <a href="/">
                                    <img src="/img/logo.png"/>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="block__menu">
                    <?php if (isset(Yii::$app->user->identity->is_user)) : ?>
                        <div class="b-middle-nav b-middle-nav-user">

                            <ul class="menu2">
                                <li><span><a class="li" href="/about">о портале</a></span></li>
                                <li><span>Заказчикам: <a href="/order/create">разместить заказ</a></span></li>
                                <li>
                            <span>
                                Сметчикам:
                                <?php if (Yii::$app->user->isGuest) { ?>
                                    <a href="/register"> РЕГИСТРАЦИЯ НА ПОРТАЛЕ </a>
                                <?php } else { ?>

                                    <?php if (\app\models\Auth::getUserType() === \app\models\Auth::TYPE_USER) { ?>
                                        <a href="/register"> Редактировать анкету </a>
                                    <?php } else { ?>
                                        <a href="/register"> РЕГИСТРАЦИЯ НА ПОРТАЛЕ </a>
                                    <?php }
                                } ?>

                            </span></li>
                            </ul>
                        </div>
                    <?php else : ?>

                        <div class="b-middle-nav">
                            <ul class="menu2">
                                <li><span><a class="li" href="/about">о портале</a></span></li>
                                <li><span>Заказчикам: <a href="/order/create">разместить заказ</a></span></li>
                                <li>
                            <span>
                                Сметчикам:
                                <?php if (Yii::$app->user->isGuest) { ?>
                                    <a href="/register"> РЕГИСТРАЦИЯ НА ПОРТАЛЕ </a>
                                <?php } else { ?>
                                    <a href="/register">Редактировать анкету </a>
                                <?php } ?>

                            </span>
                                </li>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <div class="b-bottom-nav">
                        <ul class="menu">
                            <li id="nav-logo">
                                <a href="/">
                                    <img src="/img/hnyvalinta.png" height="41"/>
                                </a>
                            </li>

                            <a href="/order/index">
                                <li>Заказы</li>
                            </a>
                            <a href="/">
                                <li>Сметчики</li>
                            </a>
                            <a href="/check">
                                <li>Проверка смет</li>
                            </a>
                            <a href="/request">
                                <li>Сметная программа бесплатно*</li>
                            </a>

                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </header>

    <?= $content; ?>

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
<div id="btnUp"></div>
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

<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }

    gtag('js', new Date());

    gtag('config', 'UA-128576397-1');
</script>

<!--<script src="/libs/vue.js"></script>-->
</body>
</html>
<?php $this->endPage() ?>
