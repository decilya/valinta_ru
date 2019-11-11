<?php

use app\assets\MaskAsset;
use app\models\Site;
use app\models\User;
use kartik\select2\Select2;
use unclead\multipleinput\MultipleInput;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */

$extraPhonesCount = $model->extraPhones !== null ? count($model->extraPhones) : 0;

//\app\models\Site::VD((bool)$is_user, false);
$js = "	function matchStart (term, text) {
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
		placeholder: 'Выберите город ...',
	}).on('select2:unselecting', function() {
  	  $(this).data('unselecting', true);
	}).on('select2:opening', function(e) {
		if ($(this).data('unselecting')) {
			$(this).removeData('unselecting');
			e.preventDefault();
		}
	});
	});

	jQuery('#registrationForm').on('afterInit', function(e){
			for(var i = 2; i < (2 + ".$extraPhonesCount.") ;i++){
					jQuery('#registrationForm').yiiActiveForm('add', {
    \"id\": \"user-extraphones-\" + i,
    \"name\": \"User[extraPhones][\" + i + \"]\",
    \"container\": \".field-user-extraphones-\" + i,
    \"input\": \"#user-extraphones-\" + i,
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

$('#user-extraphones-' + i).inputmasks(mask.optsRU);

			}
	});
	";

$this->registerJs($js, 3);

$this->registerJsFile(Yii::getAlias('@web')."/js/extraPhoneNumbers.js");

MaskAsset::register($this);
?>
<script>
    $(document).ready(function () {
        let x = 0;
        $('.multiple-input-list__btn').on('click', function () {
            setTimeout(
                function () {
                    x++;

                    for (let i = 0; i <= x; i++) {
                        $('#user-phone-' + i).inputmasks(mask.optsRU);
                    }

                }, 0
            );
        });
    });
</script>

<div class="user-form">

    <?php $form = ActiveForm::begin([
		'enableAjaxValidation' => true,
		'options' => [
			'name' => 'registrationForm',
			'id' => 'registrationForm',
			'data-role' => 'mainForm',
			'data-model-name' => $model->formName(),
			'data-phone-numbers-amount' => $extraPhonesCount + 1
		]
	]); ?>

    <?= $form->field($model, 'fio')->textInput() ?>

    <?= $form->field($model, 'email')->textInput() ?>

    <?= $form->field($model, 'phone', [
        'template' => '{label}{input}<span data-role="numberAdd" class="numberControl '.((!empty($model->extraPhones) && ((count($model->extraPhones) + 1) < User::USER_PHONE_NUMBERS_LIMIT)) ? 'numberAdd' : 'numberDisable' ).'"></span>{error}',
        'options' => [
            'data-number-index' => 1,
            'data-role' => 'phoneField',
            'class' => 'form-group'
        ]
    ])->textInput([
        'data-number-index' => 1
    ])->label("Мобильный телефон") ?>

    <?php
    if(!empty($model->extraPhones)){
        foreach ($model->extraPhones as $key => $number) {
            echo $form->field($model, 'extraPhones['.$key.']', [
                'template' => '{label}{input}<span data-role="numberRemove" class="numberControl numberRemove" data-number-index="'.$key.'"></span>{error}',
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

    <?= $form->field($model, 'experience')->textarea(['rows' => 6]) ?>

	<div class="form-group field-user-city_id">
		<label class="control-label" for="user-city_id">Город</label>

		<select id="user-city_id" class="form-control" name="User[city_id]">

			<option></option>

			<?php foreach($staticDBsContent['cities'] as $item) : ?>

				<option data-text="<?= $item['name'] ?>" <?= ($item['id'] == $model->city_id) ? ' selected="selected"' : ' ' ?> value="<?= $item['id'] ?>"><?= $item['name'] ?></option>

			<?php endforeach; ?>

		</select>

		<div class="help-block"></div>
	</div>

	<?= $form->field($model, 'professions')->widget(Select2::classname(), [
		'language' => 'ru',
		'options' => [
			'placeholder' => 'Выберите профессиональную область',
			'multiple' => true,
			'options' => Site::addDataTextAttributeToSelect2Options($staticDBsContent['professions'])
		],
		'data' => ArrayHelper::map($staticDBsContent['professions'], 'id', 'title'),
		'showToggleAll' => false
	]) ?>

	<?= $form->field($model, 'smetaDocs')->widget(Select2::classname(), [
		'language' => 'ru',
		'options' => [
			'placeholder' => 'Выберите сметную документацию',
			'multiple' => true,
			'options' => Site::addDataTextAttributeToSelect2Options($staticDBsContent['smetaDocs'])
		],
		'data' => ArrayHelper::map($staticDBsContent['smetaDocs'], 'id', 'title'),
		'showToggleAll' => false
	]) ?>

	<?= $form->field($model, 'normBases')->widget(Select2::classname(), [
		'language' => 'ru',
		'options' => [
			'placeholder' => 'Выберите нормативные базы',
			'multiple' => true,
			'options' => Site::addDataTextAttributeToSelect2Options($staticDBsContent['normBases'])
		],
		'data' => ArrayHelper::map($staticDBsContent['normBases'], 'id', 'title'),
		'showToggleAll' => false
	]) ?>

	<?= $form->field($model, 'ipap_attestat_id', [
		'template' => "{label}&nbsp;&nbsp;<a target='_blank' href='http://ipap.ru/svedeniya-ob-ipap/vydavaemye-dokumenty?attId=".$model->ipap_attestat_id."#registrySearchForm' class='checkIpap'>проверить подлинность</a>\n{input}\n{hint}\n{error}"
	])->textInput()->label((bool)$is_user ? null : 'Номер профессионального аттестата ИПАП') ?>

    <?= $form->field($model, 'price')->textInput() ?>

	<div class="form-group submitRow update-user-btn-top">
        <div class="update-user-btn">
          <span class="update-btn__star">&#042;</span>
          <?= Html::submitButton('Обновить', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>


</div>
