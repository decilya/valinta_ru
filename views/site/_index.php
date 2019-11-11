<?php

/**
 * @var \app\models\Customer|\app\models\User $user
 */

$contentResults = $this->render('blocks/_results-block', [
	'results' => $results,
	'staticDBsContent' => $staticDBsContent,
	'resultsTotal' => $resultsTotal,
	'sortParams' => $sortParams,
	'related' => $related,
	'qp' => $qp,
	'matchesPercentArr' => $matchesPercentArr,
    'user' => $user
]);

if(!Yii::$app->request->isAjax){

	$contentSearch = $this->render('blocks/_search-block', [
		'cityIdArr' => $cityIdArr,
		'staticDBsContent' => $staticDBsContent,
		'qp' => $qp,
		'resultsTotal' => $resultsTotal,
	]);

	echo $this->render('index', [
		'contentSearch' => $contentSearch,
		'contentResults' => (!empty($contentResults) ? $contentResults : null ),
	]);

}else{

	$arr = [];

	$arr['html'] = 	$this->render('index', [
		'contentResults' => (!empty($contentResults) ? $contentResults : null ),
	]);

	$arr['currentSearchVals'] = $qp;

	$arr['sortFilter'] = $qp['sortFilter'];
	$arr['sortDirection'] = $qp['sortDirection'];

	echo json_encode($arr);

	exit;
}

