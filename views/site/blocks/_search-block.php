<?php

use kartik\select2\Select2;
use yii\helpers\ArrayHelper;

$js = "	function matchStart (term, text) {
	if (text.toUpperCase().indexOf(term.toUpperCase()) == 0) {
		return true;
	}

	return false;
	}

	$.fn.select2.amd.require(['select2/compat/matcher'], function (oldMatcher) {
	$('#user-city').select2({
		language: 'ru',
		matcher: oldMatcher(matchStart),
		allowClear: true,
		placeholder: 'Выберите город ...'
	}).on('select2:unselecting', function() {
   		 $(this).data('unselecting', true);
	}).on('select2:opening', function(e) {
    if ($(this).data('unselecting')) {
        $(this).removeData('unselecting');
        e.preventDefault();
    }
	});
	});

//	$('#user-professions, #user-normbases, #user-smetadocs').on('select2:unselect', function(event){
//		searchFilters.inputHeightCheck(event)
//	});

";

$this->registerJS($js, 3);

//\app\models\Site::VD($cityIdArr);
?>

<div class="search-block">

	<div class="container">


		<div class="titleHolder">
			<img src="/img/glass.png" />
			<h2>Поиск специалистов для разработки смет</h2>
		</div>
		<span class="resultsTotal">Зарегистрировано сметчиков: <?= $resultsTotal ?></span>



		<div class="filtersHolder">

				<div class="filtersRow">
				<div class="labelHolder">
					<div class="tableDiv">
						<label class="control-label">Профессиональная область</label>
					</div>
				</div>
				<div class="filterHolder">
					<?= Select2::widget([
						'language' => 'ru',
						'name' => 'select2-filterProfessions',
						'id' => 'user-professions',

						'value' => !empty($qp['professions']) ? $qp['professions'] : '',
						'options' => [
							'placeholder' => 'Выберите...',
							'multiple' => true,
						],
						'data' => ArrayHelper::map($staticDBsContent['professions'], 'id', 'title'),
						'showToggleAll' => false,
						'toggleAllSettings' => [
							'selectLabel' => 'Выбрать всё',
							'unselectLabel' => 'Убрать всё'
						]
//						'pluginEvents' => [
//							"select2:select" => "function(){
//								searchFilters.inputHeightCheck(event);
//							}",
//						]
					]) ?>
				</div>
			</div>

				<div class="filtersRow">
					<div class="labelHolder">
						<div class="tableDiv">
							<label class="control-label">Нормативные базы</label>
						</div>
					</div>
					<div class="filterHolder">
						<?= Select2::widget([
							'language' => 'ru',
							'name' => 'select2-filterNormBases',
							'id' => 'user-normbases',

							'value' => !empty($qp['normbases']) ? $qp['normbases'] : '',
							'options' => [
								'placeholder' => 'Выберите...',
								'multiple' => true,
							],
							'data' => ArrayHelper::map($staticDBsContent['normBases'], 'id', 'title'),
							'showToggleAll' => false,
							'toggleAllSettings' => [
								'selectLabel' => 'Выбрать всё',
								'unselectLabel' => 'Убрать всё'
							]
//							'pluginEvents' => [
//								"select2:select" => "function(){
//								searchFilters.inputHeightCheck(event);
//							}"
//							]
						]) ?>
					</div>
				</div>

				<div class="filtersRow">
					<div class="labelHolder">
						<div class="tableDiv">
							<label class="control-label">Сметная документация</label>
						</div>
					</div>
					<div class="filterHolder">
						<?= Select2::widget([
							'language' => 'ru',
							'name' => 'select2-filterSmetaDocs',
							'id' => 'user-smetadocs',

							'value' => !empty($qp['smetadocs']) ? $qp['smetadocs'] : '',
							'options' => [
								'placeholder' => 'Выберите...',
								'multiple' => true,
							],
							'data' => ArrayHelper::map($staticDBsContent['smetaDocs'], 'id', 'title'),
							'showToggleAll' => false,
							'toggleAllSettings' => [
								'selectLabel' => 'Выбрать всё',
								'unselectLabel' => 'Убрать всё'
							]
//							'pluginEvents' => [
//								"select2:select" => "function(){
//								searchFilters.inputHeightCheck(event);
//							}"
//							]
						]) ?>
					</div>
				</div>

				<div class="filtersRow">
					<div class="labelHolder">
						<div class="tableDiv">
							<label class="control-label">Город</label>
						</div>
					</div>
					<div class="filterHolder">
						<select id="user-city" class="form-control select2-hidden-accessible" data-placeholder="Выберите...">
							<option></option>
							<?php
								if(!empty($cityIdArr)){
									foreach($staticDBsContent['cities'] as $item){
										if(in_array($item['id'], $cityIdArr)){
											echo "<option value=".$item['id'].">".$item['name']."</option>";
											unset($cityIdArr[$item['id']]);
										}
									}
								}
							?>
						</select>
					</div>
				</div>
			<a id="dropFilters" href="/">Сбросить фильтр</a>
		</div>



	</div>

</div>