<?php
/**
 * @var \app\models\forms\FastLoginOrderForm $fastLoginOrderForm
 * @var array $staticDBsContent
 * @var bool $newReg
 * @var \yii\web\View $this
 */

$loginForm = \yii\widgets\ActiveForm::begin([
    'enableAjaxValidation' => true,
    'options' => [
        'name' => 'registrationCustomerForm',
        'id' => 'registrationCustomerForm',
        'class' => 'loginForm',
    ]
]); ?>

<?= $loginForm->field($fastLoginOrderForm, 'login')->textInput(); ?>
<?= $loginForm->field($fastLoginOrderForm, 'password')->passwordInput(['autocomplete' => 'new-password']); ?>

<?= \yii\helpers\Html::submitButton('Отправить', ['id' => 'loginBtn']); ?>


<?php \yii\widgets\ActiveForm::end(); ?>