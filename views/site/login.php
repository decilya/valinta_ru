<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use kartik\alert\Alert;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
//use app\assets\AppAsset;

$this->title = 'Войти';

//AppAsset::register($this);
//
$this->registerJs("
		$('#login-form').on('afterValidateAttribute', function(){
		if($('#auth-login').val().length < 1 || $('#auth-password').val().length < 1){
			$('.field-auth-error').find('.help-block').text('');
		}
	});
");
?>

<div class="site-login">

	<div class="formBlock">
		<h1>Вход в личный кабинет</h1>
		<?php $form = ActiveForm::begin([
			'id' => 'login-form',
			'options' => ['class' => 'form-horizontal'],
			'fieldConfig' => [
				'template' => "<div class='topInfo'>{label}<div class='error-block'>{error}</div></div>\n<div class='inputBlock'>{input}</div>",
				'labelOptions' => ['class' =>'control-label'],
			],
		]); ?>

		<?= $form->field($model, 'login') ?>

		<?= $form->field($model, 'password')->passwordInput() ?>

		<?= $form->field($model, 'error') ?>

		<div class="submitRow">
			<div class="restorePassDiv">
				<a class="restorePassA" href="/recover" target="_blank">Восстановить пароль</a>
			</div>
			<div class="buttonDiv">
				<?= Html::submitButton('ВОЙТИ', ['class' => 'btn btn-success btn-primary', 'name' => 'login-button']) ?>
			</div>
		</div>

		<?php ActiveForm::end(); ?>
	</div>



</div>
