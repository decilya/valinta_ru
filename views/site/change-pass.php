<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Смена пароля';

$this->registerJs("
	$('#changePass-form').on('afterValidate' , function(event, messages, errorAttributes){
		if(errorAttributes.length){
			$('.hint').addClass('animate').one('animationend webkitAnimationEnd oAnimationEnd MSAnimationEnd', function(){
				$(this).removeClass('animate');
			});
		}
	});
");

$this->registerJs("
	window.formIsSubmitting = false;

	jQuery('#changePass-form').on('beforeSubmit', function(){

		console.log(window.formIsSubmitting);

		if(window.formIsSubmitting == true) return false;

		window.formIsSubmitting = true;

		window.formIsSubmitting = true;
		$(this).find('button[type=\"submit\"]').attr('disabled', 'disabled');

	});
",3);

?>

<div class="site-changePass">

	<div class="formBlock">
		<h1>Смена пароля</h1>

		<?php if($msg == null) : ?>

<!--			<p class="hint">(не менее 6 символов, включая одну заглавную букву и одну цифру)</p>-->

			<?php $form = ActiveForm::begin([
				'id' => 'changePass-form',
				'options' => ['class' => 'form-horizontal'],
			]); ?>

			<?= $form->field($model, 'pass_change', [
				'template' => '{label}{input}{hint}<div class="table">{error}</div>'
			])->passwordInput()->label('Пароль') ?>
<!--      ['placeholder' => 'подтвердите пароль']-->
			<?= $form->field($model, 'pass_change_repeat', [
			'template' => '{label}{input}{hint}<div class="table">{error}</div>'
		])->passwordInput()->label('Подтверждение пароля') ?>


			<div class="submitRow">
				<?= Html::submitButton('СМЕНИТЬ ПАРОЛЬ', ['class' => 'btn btn-success btn-primary', 'name' => 'login-button']) ?>
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
