<?php

/**
 * @var Rcsc $rcsc
 * @var int $rcscId
 * @var $dataProvider yii\data\ActiveDataProvider
 *
 * @author Ilya <ilya.v87v@gmail.com>
 * @data 25.09.2019
 */

use app\models\Database;
use app\models\Order;
use app\models\Rcsc;
use kartik\date\DatePicker;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;

$this->title = 'Заявки';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile('/js/rcscList.js', [
    'depends' => 'yii\web\JqueryAsset'
]);
?>
<div id="rcscList">
    <div id="rcscAdminList" data-controller="rcsc" class="container">
        <input id="rcscId" value="<?= $rcscId ?>" hidden="hidden">

        <h2 style="margin-bottom: 20px">Ваши базы:</h2>
        <?php
        if (!empty($rcsc->databases)) {
            /** @var Database $database */
            foreach ($rcsc->databases as $database) {
                echo "<span class='dbItem'>$database->name<span>";
                echo "; ";
            }
        } else {
            echo "<span>У вас еще не добавлено баз данных!</span>";
        }
        ?>
        <div class="mainBlockRcsc">
            <div class="childBlockRcsc">


                <div id="mainResultsBlock" class="container" style="padding-left: 0px !important;">
                    <div class="resultsBlock" data-results-limit=""
                         style="padding-left: 0px !important; padding-top: 0px !important;">
                        <h2 style="margin-top: 20px">Заявки</h2>
                        <div class="row">
                            <?php $form = ActiveForm::begin(); ?>
                            <div id="searchBlock" class="row">
                                <div class="search_row" style="display: flex; align-items: center">
                                    <div class="searchIdBlock" style="position: relative; margin-right: 50px">
                                        <span class="b-num">№</span>
                                        <input id="searchId"
                                               name="searchId"
                                               type="text"
                                               placeholder=""
                                               value="<?php if (isset($params['searchId'])) {
                                                   if (!empty($params['searchId'])) {
                                                       echo $params['searchId'];
                                                   }
                                               } ?>"
                                               style="width: 50px"
                                        />
                                    </div>
                                    <div class="searchDataBlock" style="display: flex; align-items: center">
                                        <span style="margin-right: 20px">Оформлена</span>
                                        <?= $form->field($rcscRequestsSearch, 'start_at')->widget(DatePicker::className(), [
                                            'options' => [
                                                'value' => $params['start_at'] ? $params['start_at'] : ''
                                            ],
                                            'pluginOptions' => [
                                                'autoclose' => TRUE,
                                                'format' => 'dd.mm.yyyy',
                                            ]
                                        ])
                                        ?>
                                        <span style="margin-right: 10px; margin-left: 10px">-</span>
                                        <?= $form->field($rcscRequestsSearch, 'finish_at')->widget(DatePicker::className(), [
                                            'options' => [
                                                'value' => ($params['finish_at']) ? $params['finish_at'] : ''
                                            ],
                                            'pluginOptions' => [
                                                'autoclose' => TRUE,
                                                'format' => 'dd.mm.yyyy',
                                            ]
                                        ]);
                                        ?>

                                        <?php ActiveForm::end(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <?php if (!empty($results)) { ?>

                            <?php foreach ($results

                                           as $item) { ?>

                                <div id="item_<?= $item['id'] ?>" class="row itemBlock" data-id="<?= $item['id'] ?>"
                                     data-position="">

                                    <div class="row">
                                        <div class="col-lg-1 col-md-1 col-sm-1 noPadding">
                                            <strong>№</strong><?= $item['id'] ?>
                                        </div>

                                        <div class="col-lg-3 col-md-3 col-sm-3 noPadding">
                                            <strong>Оформлена:</strong> <?= date('d.m.Y', $item['date_created']) ?>
                                        </div>

                                        <div class="col-lg-2 col-md-2 col-sm-2 noPadding">
                                            <strong>Статус:</strong>
                                            <?= Yii::$app->params['requestStatus'][$item['status_value']]; ?>
                                        </div>

                                        <div class="col-lg-1 col-md-1 col-sm-1 noPadding">
                                            <strong>Дней:</strong>
                                            <?= $item['access_days']; ?>
                                        </div>

                                        <div class="col-lg-5 col-md-5 col-sm-5 noPadding">
                                            <strong>ФИО:</strong>
                                            <?= $item['fio']; ?>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 noPadding" style="margin-top: 20px">
                                            <strong style="margin-right: 20px">Примечание:</strong><?= $item['comment'] ?>
                                        </div>
                                    </div>

                                </div>
                                <hr/>


                            <?php }
                        } else { ?>
                            <p>Заявок не найдено.</p>
                        <?php } ?>

                    </div>
                    <?php if (!empty($results)) { ?>
                        <div class="paginatorRow">
                            <div class="paginator">
                                <?php
                                if (!empty($dataProvider)) {
                                    echo LinkPager::widget([
                                        'pagination' => $dataProvider->pagination,
                                        'lastPageLabel' => true,
                                        'firstPageLabel' => true
                                    ]);
                                }
                                ?>
                            </div>
                        </div>
                    <?php } ?>

                </div>
            </div>
        </div>
    </div>
</div>

