<?php

use app\assets\MaskAsset;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use unclead\multipleinput\MultipleInput;

/** @var  \app\models\Customer $customer */

MaskAsset::register($this);
?>

<div class="customer-form">

    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => true,
        'options' => [
            'name' => 'customerForm',
            'id' => 'customerForm',
        ]
    ]); ?>
    <?= $form->field($customer, 'name')->textInput() ?>
    <?= $form->field($customer, 'email')->textInput() ?>

    <?= $form->field($customer, 'customerPhones')->widget(MultipleInput::className(), [
        'max' => 3,
        'min' => 1,
        'allowEmptyList' => false,
        'enableGuessTitle' => true,
        'addButtonPosition' => MultipleInput::POS_ROW,
    ])->label(false); ?>

    <div class="form-group submitRow update-user-btn-top wrap-btn-updt">
        <div class="update-user-btn">
            <?php if (isset(Yii::$app->user->identity)) {
                echo Html::submitButton('Обновить', ['class' => 'btn btn-updt btn-success']);
            } ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>