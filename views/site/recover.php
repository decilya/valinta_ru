<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
//use app\assets\AppAsset;

$this->title = 'Восстановление пароля';

//AppAsset::register($this);

$this->registerJs("
	jQuery('#recover-form').on('beforeSubmit', function(){
		$(this).find('button[type=\"submit\"]').attr('disabled', 'disabled');
	});
",3);
?>

<div class="site-recover">

	<div class="formBlock">
		<h1>Восстановление пароля</h1>

		<?php if($msg == null) : ?>

		<?php $form = ActiveForm::begin([
			'id' => 'recover-form',
			'enableAjaxValidation' => true,
			'options' => ['class' => 'form-horizontal'],
		]); ?>

		<?= $form->field($model, 'login') ?>

		<div class="submitRow">
			<?= Html::submitButton('ВОССТАНОВИТЬ', ['class' => 'btn btn-success btn-primary', 'name' => 'login-button']) ?>
		</div>

		<?php ActiveForm::end(); ?>

		<?php else : ?>

			<p class="message"><?= $msg['body'] ?></p>
		<div class="submitRow">
			<a class="btn btn-warning" href="/login">ЗАКРЫТЬ</a>
		</div>
		<?php endif; ?>

	</div>

</div>
