<?php
$fieldsCount++;

$formId = ($modelName == 'Order') ? 'form-order' : 'registrationForm' ;

$this->registerJs("

jQuery('#".$formId."').yiiActiveForm('add', {
    \"id\": \"".strtolower($modelName)."-extraphones-".$fieldsCount."\",
    \"name\": \"".$modelName."[extraPhones][".$fieldsCount."]\",
    \"container\": \".field-".strtolower($modelName)."-extraphones-".$fieldsCount."\",
    \"input\": \"#".strtolower($modelName)."-extraphones-".$fieldsCount."\",
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

$('#".strtolower($modelName)."-extraphones-".$fieldsCount."').inputmasks(mask.optsRU);

", 3);

?>

<div class="form-group field-<?= strtolower($modelName) ?>-extraphones-<?= $fieldsCount ?>" data-number-index="<?= $fieldsCount ?>" data-role="phoneField">
<!--	<label class="control-label" for="--><?//= strtolower($modelName) ?><!---extraphones---><?//= $fieldsCount ?><!--"></label>-->
	<input type="text" id="<?= strtolower($modelName) ?>-extraphones-<?= $fieldsCount ?>" class="form-control" name="<?= $modelName ?>[extraPhones][<?= $fieldsCount ?>]" data-number-index="<?= $fieldsCount ?>" aria-required="true" placeholder="+_(____)__-__-__" aria-invalid="true">
	<span class="numberControl numberRemove" data-role="numberRemove" data-number-index="<?= $fieldsCount ?>"></span>
	<div class="help-block"></div>
</div>