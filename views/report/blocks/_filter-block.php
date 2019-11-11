<?php
use app\models\Report;
use app\models\Site;
use kartik\daterange\DateRangePicker;
use yii\helpers\Html;

$range = (Yii::$app->params['report']['defaultReportRange'] !== 'custom') ? Report::determineRangeDates(Yii::$app->params['report']['defaultReportRange']) : null;

$dateStart = (Yii::$app->params['report']['defaultReportRange'] !== 'custom') ? date('d.m.Y', $range['dateStart']) : Yii::$app->params['report']['customDateStart'];
$dateEnd = (Yii::$app->params['report']['defaultReportRange'] !== 'custom') ? date('d.m.Y', $range['dateEnd']) : Yii::$app->params['report']['customDateEnd'];

$filter = Yii::$app->params['report']['defaultReportRange'];

$this->registerJs("
	window.helperObj = {
		dayNames: [".$reports['daysNames']."],
		ips: [".$reports['ips']."]
	};

	window.addEventListener('keydown', function(e) {
      if (e.keyIdentifier == 'U+000A' || e.keyIdentifier == 'Enter' || e.keyCode == 13) {
        if (e.target.nodeName === 'INPUT' && e.target.type !== 'textarea') {
          e.preventDefault();
          return false;
        }
      }
    }, true);
");

?>
<div id="filterBlock">
  <div class="filterBlock-btn-wrap clearfix">
	<button data-event="changeRange" data-range="7days" class="<?= ($filter == '7days') ? 'active' : 'default' ?>">7 дней</button>

	<button data-event="changeRange" data-range="30days" class="<?= ($filter == '30days') ? 'active' : 'default' ?>">30 дней</button>

	<button data-event="changeRange" data-range="90days" class="<?= ($filter == '90days') ? 'active' : 'default' ?>">90 дней</button>

	<button data-event="changeRange" data-range="365days" class="<?= ($filter == '365days') ? 'active' : 'default' ?>">365 дней</button>
</div>
	<?php

	$addon = <<< HTML
	<span class="input-group-addon">
		<i class="glyphicon glyphicon-calendar"></i>
	</span>
HTML;
	echo '<div class="input-group drp-container clearfix">';
	echo $addon . DateRangePicker::widget([
			'id' => 'dateRangePicker',
			'language' => 'ru',
			'name'=>'date_range_1',
			'value'=> $dateStart.'  -  '.$dateEnd,
			'convertFormat'=>true,
			'useWithAddon'=>true,
			'options' => [
				'class' => ($filter == 'custom') ? 'form-control active' : 'form-control default',
				'readonly' => 'true',
//				'disabled' => 'disabled',
				'data-range' => 'custom'
			],
			'pluginOptions'=>[
				'startDate' => $dateStart,
				'endDate' => $dateEnd,
				'maxDate' => date('d.m.Y'),
//				'singleDatePicker' => true,
				'locale'=>[
					'format'=>'d.m.Y',
					'separator'=>'  -  ',
					'applyLabel' => 'Показать',
					'cancelLabel' => 'Отменить',
					'monthNames' => [
						'Январь',
						'Февраль',
						'Март',
						'Апрель',
						'Май',
						'Июнь',
						'Июль',
						'Август',
						'Сентябрь',
						'Октябрь',
						'Ноябрь',
						'Декабрь',
					]
				],
				'opens'=>'right',
				'linkedCalendars' => false
//				'autoApply' => true
			],
			'pluginEvents' => [
				'apply.daterangepicker' => "function(){

					var startDate, endDate, startDateObj, endDateObj;

					startDate = $.trim($('#dateRangePicker').val().split('-')[0]);
					endDate = $.trim($('#dateRangePicker').val().split('-')[1]);

					console.log(startDate);
					console.log(endDate);

					if(startDate !== '' && endDate !== ''){

						var startDayArr = startDate.split('.');
						var endDayArr = endDate.split('.');

						console.log(startDayArr);
						console.log(endDayArr);

						console.log(new Date(startDayArr[2],startDayArr[1]*1 - 1,startDayArr[0]));
						console.log(new Date(endDayArr[2],endDayArr[1]*1 - 1,endDayArr[0]));

						startDateObj = new Date(startDayArr[2],startDayArr[1]*1 - 1,startDayArr[0]);
						endDateObj = new Date(endDayArr[2],endDayArr[1]*1 - 1,endDayArr[0]);

						console.log(startDateObj.getTime());
						console.log(endDateObj.getTime());
						console.log(new Date().getTime());
						console.log(endDateObj.getTime() <= new Date().getTime());

						if((endDateObj.getTime() >= startDateObj.getTime()) && (endDateObj.getTime() <= new Date().getTime())){

							var startValue = startDate;
							var endValue = endDate;

							console.log(1);

						}else{

							var nowDateObj = new Date();

							console.log(nowDateObj);

							var dateVal = new String(nowDateObj.getDate());
							var monthVal = new String((nowDateObj.getMonth()*1 + 1));

							console.log(dateVal.length);
							console.log(monthVal.length);

							var nowDateDate = (dateVal.length == 1) ? '0' + dateVal : dateVal ;
							var nowDateMonth = (monthVal.length == 1) ? '0' + monthVal : monthVal ;

							var nowDateStr = nowDateDate + '.' + nowDateMonth + '.' + nowDateObj.getFullYear();

							console.log(nowDateStr);

							var startValue = nowDateStr;
							var endValue = nowDateStr;

							console.log(2);
						}

							console.log(startValue);
							console.log(endValue);

							chartFilters.dateStart = startValue;
							chartFilters.dateEnd = endValue;

							chartFilters.dateRangePicker.attr('value',chartFilters.dateStart + '  -  ' + chartFilters.dateEnd);

							chartFilters.activeFilter = 'custom';
							chartFilters.detailLevel = 'days';
							chartFilters.changeDetailElement.find('option').removeAttr('selected').filter('option[value=\"1\"]').attr('selected', 'selected');

							chartFilters.changeRangeButtons.removeClass('active').addClass('default');

							chartFilters.dateRangePicker.removeClass('default').addClass('active');



							chartFilters.changeRange($(this));

					}

				}",

				'show.daterangepicker' => "function(){

							$('[name=\"daterangepicker_start\"]').val(chartFilters.dateStart).trigger('change');
							$('[name=\"daterangepicker_end\"]').val(chartFilters.dateEnd).trigger('change');

				}"
			]
		]);
	echo "</div>";

	//TODO make options hide on start, depending on detail level
	echo Html::dropDownList('detail', Yii::$app->params['report']['defaultDetailLevel'], [
		'days' => 'Детализация по дням',
		'weeks' => 'Детализация по неделям',
		'months' => 'Детализация по месяцам'
	]);

	?>



</div>
<div class="clearfix"></div>