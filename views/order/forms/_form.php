<?php

use app\models\Site;
use yii\bootstrap\Alert;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use \yii\helpers\ArrayHelper;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\Request | \app\models\Order */
/* @var $form yii\widgets\ActiveForm */

$extraPhonesCount = $model->extraPhones !== null ? count($model->extraPhones) : 0;

$js = "

	jQuery('#form-order').on('afterInit', function(e){
			for(var i = 2; i < (2 + " . $extraPhonesCount . ") ;i++){
					jQuery('#form-order').yiiActiveForm('add', {
    \"id\": \"order-extraphones-\" + i,
    \"name\": \"Order[extraPhones][\" + i + \"]\",
    \"container\": \".field-order-extraphones-\" + i,
    \"input\": \"#order-extraphones-\" + i,
    \"enableAjaxValidation\": true,
    \"validate\": function(attribute, value, messages, deferred, \$form) {
        yii.validation.regularExpression(value, messages, {
            \"pattern\": /^79|^\\+7\\(9/,
            \"not\": false,
            \"message\": \"Пожалуйста, введите верный номер мобильного телефона.\",
            \"skipOnEmpty\": 1
        });
        yii.validation.string(value, messages, {
            \"message\": \"Мобильный телефон must be a string.\",
            \"min\": 11,
            \"tooShort\": \"Пожалуйста, введите верный номер мобильного телефона.\",
            \"skipOnEmpty\": 1
        });
        yii.validation.regularExpression(value, messages, {
            \"pattern\": /^((?!_).)*$/,
            \"not\": false,
            \"message\": \"Пожалуйста, введите верный номер мобильного телефона.\",
            \"skipOnEmpty\": 1
        });
    }
});

$('#order-extraphones-' + i).inputmasks(mask.optsRU);

	}		
});

$('#byAgreement').on('change', function () {
    if ($('#byAgreement').prop('checked')) {
        $('#order-price').val('');
        $('#form-order').yiiActiveForm('validateAttribute', 'order-price');
        $('#order-price').attr('disabled', true);
    } else {
        $('#order-price').attr('disabled', false);
        $('#form-order').yiiActiveForm('validateAttribute', 'order-price');
    }
});
";

$this->registerJs($js, 3);
$this->registerJsFile(Yii::getAlias('@web') . "/js/extraPhoneNumbers.js");


?>
<div class="request-form">

    <?php
    $form = ActiveForm::begin([
        'enableAjaxValidation' => true,
        'id' => 'form-order',
        'options' => [
            'class' => 'form-horizontal col-lg-12',
            'enctype' => 'multipart/form-data',
            'data-role' => 'mainForm',
            'data-model-name' => $model->formName(),
            'data-phone-numbers-amount' => $extraPhonesCount + 1,
        ],
    ]);
    ?>

    <?= $form->field($model, 'fio')->textInput(['maxlength' => true]) ?>

    <?php if (isset($showEmail) && ($showEmail === true)) {
        echo $form->field($model, 'email')->textInput(['maxlength' => true]);
    } ?>

    <?= $form->field($model, 'phone', [
        'template' => '{label}{input}<span data-role="numberAdd" class="numberControl numberAdd"></span>{error}',
        'options' => [
            'data-number-index' => 1,
            'data-role' => 'phoneField',
            'class' => 'form-group'
        ]
    ])->textInput([
        'data-number-index' => 1
    ])->label("Телефон") ?>

    <?php
    if (!empty($model->extraPhones)) {
        foreach ($model->extraPhones as $key => $number) {
            echo $form->field($model, 'extraPhones[' . $key . ']', [
                'template' => '{label}{input}<span data-role="numberRemove" class="numberControl numberRemove" data-number-index="' . $key . '"></span>{error}',
                'options' => [
                    'data-number-index' => $key,
                    'data-role' => 'phoneField',
                    'class' => 'form-group'
                ]
            ])->textInput([
                'data-number-index' => $key
            ])->label(false);
        }
    }
    ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]); ?>


    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-4" style="padding-left: 0;">
            <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-lg-8 col-md-8 col-sm-8">
            <div class="form-group field-order-price has-success">
                <input style="margin-top: 35px !important;" id="byAgreement" type="checkbox" class="checkStyle"
                       name="byAgreement" <?php if ($model->price === 0) { ?> checked <?php } ?>>
                <label for="byAgreement" style="display: inline-block;margin-bottom: 0px !important;vertical-align: middle">По договоренности</label>
            </div>
        </div>
    </div>

    <?= $form->field($model, 'professions')->widget(Select2::classname(), [
        'language' => 'ru',
        'options' => [
            'placeholder' => 'Выберите профессиональную область...',
            'multiple' => true,
        ],
        'data' => ArrayHelper::map($staticDBsContent['professions'], 'id', 'title'),
        'showToggleAll' => false
    ]) ?>

    <?= $form->field($model, 'normBases')->widget(Select2::classname(), [
        'language' => 'ru',
        'options' => [
            'placeholder' => 'Выберите нормативные базы...',
            'multiple' => true,
        ],
        'data' => ArrayHelper::map($staticDBsContent['normBases'], 'id', 'title'),
        'showToggleAll' => false
    ]) ?>

    <?= $form->field($model, 'smetaDocs')->widget(Select2::classname(), [
        'language' => 'ru',
        'options' => [
            'placeholder' => 'Выберите сметную документацию...',
            'multiple' => true,
        ],
        'data' => ArrayHelper::map($staticDBsContent['smetaDocs'], 'id', 'title'),
        'showToggleAll' => false
    ]) ?>

    <?= $form->field($model, 'text')->textarea(['rows' => 5, 'cols' => 5]) ?>

    <?php if ((isset($isCreate)) && ($isCreate)) { ?>
        <?= $form->field($model, 'user_agreement', ['template' => '{input}{label}{hint}{error}'])->checkbox(['class' => 'checkStyle', 'label' => 'я принимаю <a target="_blank" href="/agreement" style="text-decoration: underline">правила сервиса</a>']) ?>
    <?php } ?>

    <div class="form-group text-center2 mb--button orderEdit">
        <?php

        if ((isset($isCreate)) && ($isCreate)) {
            // echo Html::submitButton('Разместить заказ', ['id' => 'submitBtn', 'class' => 'btn btn-success  my']);
            ?>

            <a type="submit" id="submitBtn" class="btn btn-success  my" disabled="disabled">Разместить заказ</a>

        <?php } else {
            if ((int)$model->published != 0) {
                echo Html::submitButton($model->isNewRecord ? 'Отправить' : 'Обновить', ['id' => 'submitBtn', 'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary updatePage orderEdit-btn']);

                echo "<br><span class='isAdmin'><!--<span class=\"update-order-btn__star\">&#042;</span>-->Сохранение изменений и увеличение срока публикации заказа до " . Yii::$app->params['dayOfProlongationForOrder'] . " дней.</span>";
            }
        } ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    if ($('#byAgreement').prop('checked')) {
        $('#order-price').val('');
        $('#order-price').attr('disabled', true);
    } else {
        $('#order-price').attr('disabled', false);
    }

</script>