<?php
/* @var $this yii\web\View */

use app\models\Site;
use yii\web\View;

$this->registerJsFile('/libs/chart/Chart.bundle.min.js');

$this->registerJsFile('/js/chartFilters.js', [
	'depends' => 'yii\web\JqueryAsset'
]);
?>
<div class="report-all report" data-days="<?= $reports['days'] ?>" data-clicks="<?= $reports['clicks'] ?>" data-holidays="<?= $reports['holidays'] ?>" data-filter="<?= Yii::$app->params['report']['defaultReportRange'] ?>" data-detail="<?= Yii::$app->params['report']['defaultDetailLevel'] ?>" data-chart="true" data-start="<?= $reports['dateStart'] ?>" data-end="<?= $reports['dateEnd'] ?>">

	<div class="container">

		<div class="chartMenuHolder">
			<ul>
				<li>
					<a href="/report/all">Просмотр контактов</a>
				</li>
			</ul>
		</div>

		<div class="chartHolder">
			<h2>Просмотр контактов по всем анкетам</h2>

			<?= $this->render('blocks/_filter-block', [
				'reports' => $reports
			]); ?>
		</div>

	</div>

</div>
