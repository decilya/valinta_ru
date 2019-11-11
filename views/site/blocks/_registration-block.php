<?php

use app\models\User;
use yii\bootstrap\Alert;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use \yii\helpers\ArrayHelper;
use kartik\select2\Select2;

$this->registerJsFile(Yii::getAlias('@web') . "/js/extraPhoneNumbers.js");

$jsBtn = "


let userAgreement = $('#user-user_agreement');

if (userAgreement.is(':checked')) {
    $('#submitBtn').attr('disabled', false);
} else {
    $('#submitBtn').attr('disabled', true);
}

userAgreement.on('click', function () {
    if ($('#user-user_agreement').is(':checked')) {
        $('#submitBtn').attr('disabled', false);
    } else {
        $('#submitBtn').attr('disabled', true);
    }
});
";

$this->registerJs($jsBtn, 3);

$this->registerJS("
	function matchStart (term, text) {
	if (text.toUpperCase().indexOf(term.toUpperCase()) == 0) {
		return true;
	}

	return false;
	}

	$.fn.select2.amd.require(['select2/compat/matcher'], function (oldMatcher) {
	$('#user-city_id').select2({
		language: 'ru',
		matcher: oldMatcher(matchStart),
		allowClear: true,
		placeholder: 'Выберите город ...'
	});
	});

", 3);
?>

<script>
    $(document).ready(function () {
        $('#registrationForm').on('beforeValidate', function () {
            $('#submitBtn').attr('disabled', true);
        });

        $('#registrationForm').on('afterValidate', function (e, v, i) {

            if (i.length > 0) {
                $('#submitBtn').attr('disabled', false);
            }

        });
    });
</script>

<div class="clientsMessageHolder">
    <div class="container">
        <div class="infoBlock">
            <img src="/img/newIcon/1.png"/>
            <p>1. Зарегистрируйтесь<br>в базе сметчиков.</p>
        </div>
        <div class="infoBlock">
            <img src="/img/newIcon/2.png"/>
            <p>2. Дождитесь одобрения<br>и размещения Вашей анкеты.</p>
        </div>
        <div class="infoBlock">
            <img src="/img/newIcon/4o.png"/>
            <p>3. Принимайте заказы<br>и зарабатывайте.</p>
        </div>
    </div>
</div>

<?php if (!empty($msg)) : ?>

    <div class="messageBlock">
        <div class="container">
            <?php
            echo Alert::widget([
                'options' => ['class' => 'alert-' . $msg['status']],
                'body' => $msg['body'],
            ]);
            ?>
        </div>
    </div>

<?php endif; ?>

<div class="registrationBlock">
    <div class="container">
        <h2>Регистрация в базе сметчиков</h2>

        <?php if (\app\models\Auth::getUserType() !== \app\models\Auth::TYPE_CUSTOMER) { ?>

            <?php $form2 = ActiveForm::begin([
                'enableAjaxValidation' => true,
                'options' => [
                    'name' => 'registrationForm',
                    'id' => 'registrationForm',
                    'data-role' => 'mainForm',
                    'data-model-name' => $user->formName(),
                    'data-phone-numbers-amount' => 1
                ]
            ]); ?>

            <div class="roww">

                <?= $form2->field($user, 'fio')->textInput() ?>

                <?= $form2->field($user, 'email')->textInput() ?>

                <?= $form2->field($user, 'password')->passwordInput() ?>

                <?= $form2->field($user, 'password_repeat')->passwordInput() ?>

                <?= $form2->field($user, 'phone', [
                    'template' => '{label}{input}<span data-role="numberAdd" class="numberControl numberAdd"></span>{error}',
                    'options' => [
                        'data-number-index' => 1,
                        'data-role' => 'phoneField',
                        'class' => 'form-group'
                    ]
                ])->textInput([
                    'data-number-index' => 1
                ])->label("Мобильный телефон") ?>

                <?= $form2->field($user, 'city_id')->widget(Select2::classname(), [
                    'language' => 'ru',
                    'data' => ArrayHelper::map($staticDBsContent['cities'], 'id', 'name'),
                    'options' => ['placeholder' => 'Выберите город ...'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'noResults' => 'Ничего не найдено',
                    ],
                ]) ?>

                <?= $form2->field($user, 'professions')->widget(Select2::classname(), [
                    'language' => 'ru',
                    'options' => [
                        'placeholder' => 'Выберите профессиональную область...',
                        'multiple' => true,
                    ],
                    'data' => ArrayHelper::map($staticDBsContent['professions'], 'id', 'title'),
                    'showToggleAll' => false
                ]) ?>

                <?= $form2->field($user, 'smetaDocs')->widget(Select2::classname(), [
                    'language' => 'ru',
                    'options' => [
                        'placeholder' => 'Выберите сметную документацию...',
                        'multiple' => true
                    ],
                    'data' => ArrayHelper::map($staticDBsContent['smetaDocs'], 'id', 'title'),
                    'showToggleAll' => false
                ]) ?>

                <?= $form2->field($user, 'normBases')->widget(Select2::classname(), [
                    'language' => 'ru',
                    'options' => [
                        'placeholder' => 'Выберите нормативные базы...',
                        'multiple' => true,
                    ],
                    'data' => ArrayHelper::map($staticDBsContent['normBases'], 'id', 'title'),
                    'showToggleAll' => false
                ]) ?>

                <div class="b-wrap__attestat">
                    <div class="b-show-info">
                        <p>
                            Для получения профессионального аттестата, подтверждающий высокую квалификацию специалиста в
                            сфере ценообразования и сметного нормирования в строительстве и именной печати для заверения
                            разработанной и проверенной им сметной документации перейдите по ссылке. Наличие аттестата
                            повышает вероятность получения заказа.
                        </p>
                    </div>
                    <img class="b-wrap__attestat-img" width="24" height="24" src="/img/info-icon-form.png" alt=""/>
                    <a class="block-link-att"
                       href="http://ipap.ru/19-professionalnaya-attestatsiya"
                       target="_blank">Пройти аттестацию</a>

                    <?= $form2->field($user, 'ipap_attestat_id')->textInput() ?>
                </div>

                <?= $form2->field($user, 'price')->textInput([
                    'placeholder' => 'От...'
                ]) ?>

                <?= $form2->field($user, 'experience')->textarea() ?>

                <?= $form2->field($user, 'user_agreement', ['template' => '{input}{label}{hint}{error}'])->checkbox(['class' => 'checkStyle', 'label' => 'я принимаю <a target="_blank" href="/agreement">правила сервиса</a>']) ?>

                <div class="form-group submitRow">
                    <?= Html::submitButton('Зарегистрироваться', ['id' => 'submitBtn', 'class' => 'btn btn-success btn-lg']) ?>
                </div>
            </div>

            <?php
            ActiveForm::end();
        } else {
            ?>

            <div class="alert alert-danger">
                Регистрация анкеты сметчика под учетной записью заказчика невозможна. <a href="/site/logout">Выйти</a>
            </div>
        <?php } ?>
    </div>

</div>