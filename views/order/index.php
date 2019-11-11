<?php

/**
 * @var \app\models\Customer|\app\models\User $user
 */

use yii\bootstrap\Alert;
use yii\jui\DatePicker;

$this->title = 'Заказы';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile(
    '@web/js/readmore.min.js', [
        'position' => 3
    ]
);

$this->registerJsFile(
    '@web/js/orderController.js', [
        'position' => 3
    ]
);

?>
<div id="mainOrdersList">
    <div id="ordersList">

        <div class="row" style="background: #1083ab">
            <div class="container">
                <div class="titleHolder">
                    <img src="/img/glass.png"/>

                    <h2>Поиск заказов на разработку смет</h2>
                </div>

                <span class="resultsTotal">Зарегистрировано заказов: <?= $countOnAllPublicOrder; ?></span>

                <div id="searchForm">
                    <?= $this->render('blocks/_search-block', [
                        'qp' => $qp,
                        'staticDBsContent' => $staticDBsContent
                    ]); ?>
                </div>
            </div>
        </div>
        <div class="filterRow">

            <div class="container">

                <label>Сортировать по дате: </label>
                <span class="sortingPrice <?php if ($priceSort == 'asc') {
                    echo "active";
                } ?>" data-sort="asc">&#11014;</span>
                <span class="sortingPrice <?php if ($priceSort == 'desc') {
                    echo "active";
                } ?>" data-sort="desc">&#11015;</span>

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

        <div class="container" id="resultBlock">
            <?= $contentResults = $this->render('blocks/_result-block', [
                'results' => $results,
                'staticDBsContent' => $staticDBsContent,
                'qp' => $qp,
                'countOnAllPublicOrder' => $countOnAllPublicOrder,
                'dataResultsTotal' => $dataResultsTotal,
                'positionOrderInResultArr' => $positionOrderInResultArr,
                'typeOfUser' => $typeOfUser,
                'user' => $user
            ]); ?>
        </div>

        <div class="row text-center b-order__ajax-pag">
            Показано заказов: <span id="countOfOrdersIntoPage"><?= count($results); ?></span> из <span
                    id="totalCount"><?= $countOnAllPublicOrder; ?></span>
        </div>

        <?php if (count($results) < $countOnAllPublicOrder) { ?>
            <div id="blockOfCounters" class="text-center">
                <a data-block="0" id="showNext" class="b-show-more">Показать еще <span id="counterForShowMore">7</span></a>
            </div>
        <?php } ?>

    </div>
</div>
