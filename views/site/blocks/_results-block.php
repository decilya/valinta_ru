<?php
/** @var \yii\web\User $user  */

	if(!empty($qp['showfrom'])){
		$resultsOnPage = Yii::$app->params['searchResultsDefaultLimit'] + $qp['showfrom'];
	}else{
		$resultsOnPage = ($resultsTotal < Yii::$app->params['searchResultsDefaultLimit']) ? $resultsTotal : Yii::$app->params['searchResultsDefaultLimit'] ;
	}

	$cnt = 0;
?>

<div class="results-block" data-results-sort-direction="<?= $sortParams['direction'] ?>" data-results-sort-filter="<?= $sortParams['filter'] ?>" data-results-total="<?= $resultsTotal ?>" data-results-onpage="<?= $resultsOnPage ?>"
	data-results-limit="<?= Yii::$app->params['searchResultsDefaultLimit'] ?>">

	<?php if(empty($results)) : ?>
		<div class="container">
			<p class="noResults">На портале не опубликовано ни одной анкеты.</p>
		</div>
	<?php endif; ?>

		<?php if(!empty($results)) : ?>

			<div class="filterRow">

				<div class="container">

					<div class="filter" data-role="dateFilter">
						<label>Сортировать по дате обновления: </label>
						<span class="sortingArrow<?php if($sortParams['filter'] == 'date' && $sortParams['direction'] == 'asc') echo ' active' ?>" data-results-sort-filter="date" data-results-sort-direction="asc">&#11014;</span>
						<span class="sortingArrow<?php if($sortParams['filter'] == 'date' && $sortParams['direction'] == 'desc') echo ' active' ?>" data-results-sort-filter="date" data-results-sort-direction="desc">&#11015;</span>
					</div>

					<div class="filter" data-role="priceFilter">
						<label>Сортировать по стоимости: </label>
						<span class="sortingArrow<?php if($sortParams['filter'] == 'price' && $sortParams['direction'] == 'asc') echo ' active' ?>" data-results-sort-filter="price" data-results-sort-direction="asc">&#11014;</span>
						<span class="sortingArrow<?php if($sortParams['filter'] == 'price' && $sortParams['direction'] == 'desc') echo ' active' ?>" data-results-sort-filter="price" data-results-sort-direction="desc">&#11015;</span>
					</div>
				</div>
			</div>

	<div class="container">

			<?php foreach($results as $item){
				$cnt++;

				echo $this->render('_item-block', [
					'item' => $item,
					'related' => $related,
					'staticDBsContent' => $staticDBsContent,
					'qp' => $qp,
					'matchesPercentArr' => $matchesPercentArr,
					'cnt' => $cnt,
                    'user' => $user
				]);

			} ?>
	</div>
		<?php endif; ?>




</div>
