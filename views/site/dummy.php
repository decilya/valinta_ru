<?php

/* @var $this yii\web\View */

$this->registerCssFile('@web/css/site.css');

?>

<style>
    .itemB{
        float: left;
        width: 175px;
        min-width: 175px;
        max-width: 175px;
    }

    .clear{
        clear: both;
    }

    .site-dummy{
        width: 1000px;
        margin: 0 auto;
    }

    a.linkTob{
        text-align: center;
        display: block;
    }

    .imgHolder .itemB img{
        margin: 0 auto;
        display: block;
    }

    .imgHolder{
        margin: 10px auto;
        margin-bottom: 10px;
    }

    h2{
        text-align: center;
    }
</style>


		<div class="site-dummy">
			<h2>Внимание! Вы используете устаревший браузер.</h2>
			<p>Допустимые версии:</p>

			<div class="imgHolder">
				<div class="itemB">
					<a href="http://windows.microsoft.com/ru-ru/internet-explorer/download-ie" target="_blank">
						<img src="/img/browsers/browser-ie.png" alt="Internet Explorer"/>
					</a><div class="clear"></div><br>
					<a class="linkTob" href="http://windows.microsoft.com/ru-ru/internet-explorer/download-ie" target="_blank">
						<span>Internet Explorer</span>
					</a>
				</div>

                <div class="itemB">
                    <a href="https://www.google.ru/chrome/browser/desktop/index.html" target="_blank">
                        <img src="/img/browsers/browser-chrome.png" alt="Chrome"/>
                    </a><div class="clear"></div><br>
                    <a class="linkTob" href="https://www.google.ru/chrome/browser/desktop/index.html" target="_blank">
                        <span>Chrome</span>
                    </a>
                </div>

                <div class="itemB">
                    <a href="http://www.opera.com/ru" target="_blank">
                        <img src="/img/browsers/browser-opera.png" alt="Opera"/>
                    </a><div class="clear"></div><br>
                    <a class="linkTob" href="http://www.opera.com/ru" target="_blank">
                        <span>Opera</span>
                    </a>
                </div>

                <div class="itemB">
                    <a href="https://www.mozilla.org/ru/firefox/new/" target="_blank">
                        <img src="/img/browsers/browser-firefox.png" alt="Firefox"/>
                    </a><div class="clear"></div><br>
                    <a class="linkTob" href="https://www.mozilla.org/ru/firefox/new/"
                       target="_blanhttp://windows.microsoft.com/ru-ru/internet-explorer/download-iek">
                        <span>Firefox</span>
                    </a>
                </div>
                <div class="clear"></div>

			</div>

            <div class="clear"></div>

			<p>Если у вас версия Internet Explorer 10 или выше, но вы видите эту страницу, в браузере нажмите ALT - в меню выберите Сервис - Параметры просмотра в режиме совместимости, снимите флажок - Отображать сайты интрасети в режиме совместимости.</p>
			<p>Если Вы <u>не имеете доступа</u> к установке программ, то обратитесь для решения этого вопроса к своему системному администратору.</p>
            <br>
			<h2>Почему Ваш браузер нужно обновить или заменить на другой?</h2>
			<ol>
				<li>Компания - разработчик Вашего браузера, официально прекратила его поддержку и рекомендует устанавливать новые версии своего браузера.</li>
				<li>Ваш браузер является устаревшим. Он не может предоставить все возможности, которые могут предоставить современные браузеры, а скорость его работы в несколько раз ниже.</li>
				<li>Ваш браузер не способен корректно отображать большинство сайтов.</li>
				<li>Использование Вашего браузера потенциально опасно, так как именно через него доступ к Вашему компьютеру имеют вирусы и мошенники.</li>
			</ol>

		</div>



