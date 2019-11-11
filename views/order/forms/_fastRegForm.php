<?php
/**
 * @var \app\models\forms\FastLoginOrderForm $fastLoginOrderForm
 * @var \app\models\forms\FastRegOrderForm $fastRegOrderForm
 * @var array $staticDBsContent
 * @var bool $newReg
 * @var \yii\web\View $this
 */
?>

<?php $loginForm = \yii\widgets\ActiveForm::begin([
    'enableAjaxValidation' => true,
    'options' => [
        'name' => 'registrationCustomerForm',
        'id' => 'registrationCustomerForm',
        'class' => 'loginForm',
    ]
]); ?>

<?= $loginForm->field($fastRegOrderForm, 'login')->textInput(); ?>
<?= $loginForm->field($fastRegOrderForm, 'password')->passwordInput(); ?>
<?= $loginForm->field($fastRegOrderForm, 'rePassword')->passwordInput(); ?>

<?= \yii\helpers\Html::submitButton('Отправить', ['id' => 'loginBtn']); ?>

<?php \yii\widgets\ActiveForm::end(); ?>