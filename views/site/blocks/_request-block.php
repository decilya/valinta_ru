<?php

/***
 * @var \app\models\Request $request
 */

use app\models\User;
use kartik\select2\Select2;
use yii\bootstrap\Alert;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Html;


if (!empty($model) && is_object($model) && $model instanceof User) {
    $request->fio = $model->fio;
    $request->email = $model->email;
    $request->phone = $model->phone;
}

$this->title = 'Сметная программа бесплатно*';
$jsBtn = "

let userAgreement = $('#request-request_agreement');

if (userAgreement.is(':checked')) {
    $('#submitBtn').attr('disabled', false);
} else {
    $('#submitBtn').attr('disabled', true);
}

userAgreement.on('click', function () {
    if ($('#request-request_agreement').is(':checked')) {
        $('#submitBtn').attr('disabled', false);
    } else {
        $('#submitBtn').attr('disabled', true);
    }
});
";

$this->registerJs($jsBtn, 3);

$this->registerCssFile('@web/css/datepicker.min.css');
$this->registerJsFile('@web/js/datepicker.min.js', [
    'depends' => 'yii\web\JqueryAsset',
    'position' => yii\web\View::POS_HEAD
]);
?>

<script>

    $(document).ready(function () {

        $('#requestForm').on('beforeValidate', function () {
            $('#submitBtn').attr('disabled', true);
        });

        $('#requestForm').on('afterValidate', function (e, v, i) {

            if (i.length > 0) {
                $('#submitBtn').attr('disabled', false);
            }

        });

        function isNumber(n) { return /^-?[\d.]+(?:e-?\d+)?$/.test(n); }

        function calcCost() {
            setTimeout(
                function () {
                    let listDb = [];
                    let days = $('#request-access_days').val();

                    $('li.select2-selection__choice').each(function (i, elem) {
                        listDb.push($(elem).text().substring(1))
                    });

                    $.ajax({
                        url: 'site/ajax-calc-db',
                        method: 'POST',
                        dataType: 'json',
                        data: {
                            listDb: listDb,
                            days: days
                        },
                        success: function (data) {
                            
                            if (isNumber(data)) {
                                $('#costDb').text(data);
                            }
                        },
                    });


                }, 0
            );
        }


        $('#request-access_days').on('change', function () {
            calcCost();
        });

        $('#request-databasestmp').on('change', function () {
            calcCost();
        });

    });
</script>

<div class="requestBlock">


    <div class="container">
        <?php if (!empty($msg)) : ?>

            <div class="messageBlock">
                <div class="container" style="text-align: center;">
                    <?php
                    echo Alert::widget([
                        'options' => ['class' => 'alertt-' . $msg['status']],
                        'body' => $msg['body'],
                    ]);
                    ?>
                </div>

            </div>

        <?php endif; ?>

        <div class="roww">
            <div class="leftSide">
                <p>
                    Оставьте Вашу заявку и менеджер свяжется с Вами в ближайшее время:<br>ПН-ПТ с 9.00 до 18.00 МСК.
                </p>
            </div>
            <div class="rightSide">

                <?php $form = ActiveForm::begin([
                        'enableAjaxValidation' => true,
                        'options' => [
                            'name' => 'requestForm',
                            'id' => 'requestForm'
                        ]]
                ); ?>

                <div class="formRow">
                    <?= $form->field($request, 'fio')->textInput() ?>
                </div>

                <div class="formRow">
                    <?= $form->field($request, 'email')->textInput() ?>
                </div>

                <div class="formRow">
                    <?= $form->field($request, 'phone')->textInput() ?>
                </div>

                <div class="formRow">
                    <?= $form->field($request, 'inn')->textInput(['type' => 'number']) ?>
                </div>

                <div class="formRow">
                    <?= $form->field($request, 'databasesTmp')->widget(Select2::classname(), [
                        'language' => 'ru',
                        'options' => [
                            'placeholder' => 'Выберите...',
                            'multiple' => true,
                        ],
                        'data' => ArrayHelper::map($databasesTmp, 'id', 'realName'),
                        'showToggleAll' => false
                    ]) ?>
                </div>

                <div class="formRow">
                    <?= $form->field($request, 'access_days')->textInput(['type' => 'number']) ?>
                </div>

                <div class="formRow" style="margin-bottom: 20px;">
                    <p class="control-label">Стоимость заказа - <span id="costDb">0</span>р</p>
                </div>

                <div class="formRow">
                    <?= $form->field($request, 'desired_date')->textInput(['class' => 'datepicker-here form-control']) ?>
                </div>

                <div class="formRow">
                    <?= $form->field($request, 'comment')->textInput() ?>
                </div>

                <div class="formRow">
                    <?= $form->field($request, 'request_agreement', ['template' => '{input}{label}{hint}{error}'])->checkbox(['class' => 'checkStyle', 'label' => '<span class="b-iagree">я принимаю </span> <a target="_blank" href="/request-agreement">условия предоставления ПП SmetaWIZARD</a>']) ?>
                </div>

                <div class="formRow submitRow">
                    <?php echo Html::submitButton('Отправить заявку', ['id' => 'submitBtn', 'class' => 'btn btn-smeta']) ?>
                </div>

                <?php ActiveForm::end(); ?>

            </div>

            <div class="leftSide">
                <em>
                    *Доступ с набором бесплатных баз предоставляется не более чем на 3 дня.
                </em>
            </div>

        </div>
    </div>

</div>
