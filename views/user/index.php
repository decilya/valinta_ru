<?php

use app\assets\BootboxAsset;
use app\models\Site;
use yii\helpers\Html;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Сметчики';
$this->params['breadcrumbs'][] = $this->title;

//Site::VD($related);
$this->registerJsFile('@web/js/adminSearch.js', [
    'depends' => 'yii\web\JqueryAsset'
]);

BootboxAsset::register($this);

$this->registerJs("
	$('[data-toggle=\"tooltip\"]').tooltip({html: true});
", 3);

$cntPos = $paginator->page * Yii::$app->params['itemsOnUserIndexPage'];

?>
<div data-controller="user" class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="searchBlock">
        <?= $searchBlock ?>
    </div>

    <div class="resultsBlock" data-results-limit="<?= Yii::$app->params['itemsOnUserIndexPage'] ?>">

        <?php if (!empty($model)) : ?>
            <hr/>
            <?php foreach ($model as $item) : ?>
            <?php if ((!isset($item['real_id']) || $item['real_id'] == null)){ continue; } ?>

                <div id="item_<?= $item['real_id'] ?>" class="itemBlock" data-id="<?= $item['real_id'] ?>"
                     data-position="<?= ++$cntPos ?>">

                    <div class="row firstRow">
                        <div class="itemId status_<?= $item['status_id'] ?>">
                            №<?= $item['real_id'] . ' - ' . $status[$item['status_id']]['title'] ?></div>
                        <div class="itemChanged"><strong>Изменена: </strong><?= date('d.m.Y', $item['date_changed']) ?>
                        </div>
                        <div class="itemStatusMsg <?= ((bool)$item['is_visible']) ? 'visible' : 'notVisible' ?>"><?= $item['status_msg'] ?></div>
                        <a class="itemStatLink" href="/report/user/<?= $item['real_id'] ?>"
                           target="_blank">Статистика</a>
                        <a class="itemEditLink" href="/user/update/<?= $item['real_id'] ?>">Редактировать</a>
                    </div>

                    <div class="row secondRow">
                        <div class="itemFio"><strong>ФИО: </strong><?= Html::encode($item['fio']) ?></div>
                        <div class="itemPhone">
                            <strong>Тел.: </strong><?= Html::encode($item['phone']) . $item->createAdditionalNumbersSpan() ?>
                        </div>
                        <div class="itemEmail" style="overflow: hidden">
                            <strong>E-mail: </strong><?= Html::encode($item['email']) ?></div>
                        <div class="itemCity">
                            <strong>Город: </strong><?= (!empty($item['city_id'])) ? $staticDBsContent['cities'][$item['city_id']]['name'] : '-' ?>
                        </div>
                    </div>

                    <?php
                    /** @var \app\models\Auth $auth */
                    $auth = \app\models\Auth::find()->where(['id' => $item['real_id']])->one();
                    $user = \app\models\User::find()
                        ->where(['id' => $auth->user_id])
                        ->with('professions')
                        ->with('smetadocs')
                        ->with('normbases')
                        ->one();
                    ?>

                    <div class="row thirdRow">

                        <div class="itemProfessions">
                            <strong>Профессиональная область: </strong>

                            <?php
                            $professions = $user->getRelatedTitleFrom('professions');
                            if (!empty($professions)) {
                                foreach ($professions as $profession) {
                                    echo $profession['title'];
                                    echo "; ";
                                }
                            } else {
                                echo " -";
                            } ?>

                        </div>

                    </div>

                    <div class="row fourthRow">

                        <div class="itemNormBases">
                            <strong>Нормативные базы: </strong>
                            <?php
                            $professions = $user->getRelatedTitleFrom('normbases');
                            if (!empty($professions)) {
                                foreach ($professions as $profession) {
                                    echo $profession['title'];
                                    echo "; ";
                                }
                            } else {
                                echo " -";
                            } ?>
                        </div>


                    </div>

                    <div class="row fifthRow">

                        <div class="itemSmetaDocs">
                            <strong>Сметная документация: </strong>
                            <?php
                            $professions = $user->getRelatedTitleFrom('smetadocs');
                            if (!empty($professions)) {
                                foreach ($professions as $profession) {
                                    echo $profession['title'];
                                    echo "; ";
                                }
                            } else {
                                echo " -";
                            } ?>
                        </div>
                    </div>

                    <div class="row sixthRow">
                        <div class="itemExperience"><strong>Образование и опыт
                                работы: </strong><?= (!empty($item['experience'])) ? Html::encode($item['experience']) : '-' ?>
                        </div>
                        <div class="itemIpapId"><strong>Номер профессионального аттестата
                                ИПАП: </strong><?= (!empty($item['ipap_attestat_id'])) ? Html::encode($item['ipap_attestat_id']) : '-' ?>
                            &nbsp;&nbsp;<a target="_blank"
                                           href="http://ipap.ru/svedeniya-ob-ipap/vydavaemye-dokumenty?attId=<?= Html::encode($item['ipap_attestat_id']); ?>#registrySearchForm"
                                           class="checkIpap">проверить подлинность</a></div>
                        <div class="itemPrice"><strong>Стоимость
                                от: </strong><?= (!empty($item['price'])) ? Html::encode($item['price']) . '&nbsp;руб.' : '-' ?>
                        </div>

                        <?php if (($item['status_id'] == Yii::$app->params['status']['pending']) || ($item['status_id'] == Yii::$app->params['status']['rejected'])) : ?>

                            <a class="itemAcceptLink" data-id="<?= $item['real_id'] ?>"
                               href="/user/accept-user/<?= $item['real_id'] ?>">Подтвердить</a>

                        <?php endif; ?>

                        <?php if (($item['status_id'] == Yii::$app->params['status']['pending']) || ($item['status_id'] == Yii::$app->params['status']['accepted'])) : ?>

                            <a class="itemRejectLink" data-id="<?= $item['real_id'] ?>"
                               href="/user/reject-user/<?= $item['real_id'] ?>">Отклонить</a>

                        <?php endif; ?>

                    </div>


                </div>
                <hr/>
            <?php endforeach; ?>

        <?php endif; ?>

        <?php if (empty($model)) : ?>


            <p>Ничего не найдено!</p>

        <?php endif; ?>
    </div>

    <div class="paginatorRow">
        <div class="paginator">
            <?php
            echo LinkPager::widget([
                'pagination' => $paginator,
                'lastPageLabel' => true,
                'firstPageLabel' => true
            ]);
            ?>
        </div>
    </div>
</div>
