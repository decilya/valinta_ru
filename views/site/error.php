<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use app\models\Site;
use yii\helpers\Html;

$this->title = $name;
?>
<div class="site-error">

	<div class="container error">
		<img src="/img/errorBg.png" />
		<div>
			<h2>Страница не найдена или доступ к ней запрещен</h2>
			<p>для продолжения работы перейдите по ссылке:</p>
			<div class="linkHolder">
				<a href="/">Valinta.ru</a>
			</div>
		</div>

	</div>

</div>
