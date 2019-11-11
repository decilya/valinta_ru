<?php

use yii\widgets\ActiveForm;
use unclead\multipleinput\MultipleInput;
use yii\helpers\Html;

/**
 * @var \app\models\Customer $customer
 * @var \yii\web\View $this
 */
$this->title = 'Регистрация нового заказчика';

\app\assets\MaskAsset::register($this);

$this->registerJsFile(Yii::getAlias('@web') . "/js/extraPhoneNumbers.js");

?>
<style>
    .glyphicon {
        display: none;
    }
</style>
<script>
    $(document).ready(function () {
        $('#customer-customerphones-' + 0).inputmasks(mask.optsRU);

        let x = 0;
        $('.multiple-input-list__btn').on('click', function () {
            setTimeout(
                function () {
                    x++;

                    for (let i = 0; i <= x; i++) {
                        $('#customer-customerphones-' + i).inputmasks(mask.optsRU);
                    }

                }, 0
            );
        });

        $('#customerAgreementBtn').on('change', function () {

            if ($(this).prop('checked')) {
                $('#submitBtn').prop('disabled', false);
            } else {
                $('#submitBtn').prop('disabled', true);
            }
        });

        $('.field-customer-customerphones input').on('change', function () {
            if ($('.field-customer-customerphones .help-block').length > 0) {
                $('.field-customer-customerphones .help-block').remove();
            }
        });

    });
</script>

<div class="site-register">
    <div class="clientsMessageHolder">
        <div class="container">
            <div class="infoBlock">
                <img src="/img/newIcon/1.png">

                <p>
                    1. Зарегистрируйте <br/>
                    свой заказ.
                </p>
            </div>
            <div class="infoBlock">
                <img src="/img/newIcon/2.png">

                <p>
                    2. Дождитесь одобрения <br/>
                    и размещения Вашего заказа.
                </p>
            </div>
            <div class="infoBlock">
                <img src="/img/newIcon/3d.png"/>
                <!-- <p>3. Принимайте заказы<br>и зарабатывайте.</p>-->
                <p>
                    3. Выбирайте из предложений <br>
                    сметчиков и выполняйте работу <br>
                    в срок.
                </p>
            </div>
        </div>
    </div>

    <div class="registrationBlock">
        <div class="container">
            <h2><?= $this->title ?></h2>
            <?php $customerForm = ActiveForm::begin([
                'enableAjaxValidation' => true,
                'options' => [
                    'name' => 'registrationCustomerForm',
                    'id' => 'registrationCustomerForm'
                ]
            ]); ?>
            <div class="row">
                <?= $customerForm->field($customer, 'name')->textInput() ?>
                <?= $customerForm->field($customer, 'email')->textInput() ?>

                <?= $customerForm->field($customer, 'password')->passwordInput() ?>
                <?= $customerForm->field($customer, 'rePassword')->passwordInput() ?>


                <?= $customerForm->field($customer, 'customerPhones')->widget(MultipleInput::className(), [
                    'max' => 3,
                    'min' => 1,
                    'allowEmptyList' => false,
                    'enableGuessTitle' => true,
                    'addButtonPosition' => MultipleInput::POS_ROW,
                    'enableError' => true
                ])->label(false) ?>

                <input type="checkbox" id="customerAgreementBtn" class="checkStyle" name="customerAgreement" value="1">
                <label for="customerAgreementBtn">я принимаю
                    <a target="_blank" href="/agreement" style="text-decoration: underline">правила сервиса</a>
                </label>

                <div class="form-group submitRow">
                    <button id="submitBtn" type="submit" class="btn btn-success btn-lg" disabled>Зарегистрироваться
                    </button>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
