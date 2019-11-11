<?php

$this->registerJsFile('/libs/chart/Chart.bundle.min.js');

$this->registerJsFile('/js/chartFilters.js', [
	'depends' => 'yii\web\JqueryAsset'
]);
?>

<div class="report-user report" data-days="<?= $reports['days'] ?>" data-clicks="<?= $reports['clicks'] ?>" data-holidays="<?= $reports['holidays'] ?>" data-filter="<?= Yii::$app->params['report']['defaultReportRange'] ?>" data-detail="<?= Yii::$app->params['report']['defaultDetailLevel'] ?>" data-chart="true" data-start="<?= $reports['dateStart'] ?>" data-end="<?= $reports['dateEnd'] ?>" data-user-id="<?= $user->id ?>">

	<div class="container">

		<div class="chartMenuHolder">
			<ul>
				<li>
					<a href="/report/user/<?= $user->real_id ?>">Просмотр контактов</a>
				</li>
			</ul>
		</div>

		<div class="chartHolder">
			<h2>Статистика по анкете №<?= $user->real_id ?><span> - <?= $user->fio ?></span></h2>

			<?= $this->render('blocks/_filter-block', [
				'reports' => $reports
			]); ?>
		</div>

	</div>

</div>