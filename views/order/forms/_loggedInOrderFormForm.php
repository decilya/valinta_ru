<?php
/**
 * @var \app\models\forms\LoggedInOrderForm $fastLoginOrderForm
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

<?= \yii\helpers\Html::submitButton('Отправить', ['id' => 'loginBtn']); ?>

<?php \yii\widgets\ActiveForm::end(); ?>