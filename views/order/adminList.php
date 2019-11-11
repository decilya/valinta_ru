<?php

use app\assets\BootboxAsset;
use app\models\Site;
use yii\helpers\Html;
use yii\widgets\LinkPager;
use app\models\Order;
use yii\helpers\Url;

/** @var Order $order */

$this->title = 'Заказы';
$this->params['breadcrumbs'][] = $this->title;

BootboxAsset::register($this);

/** @var  \yii\debug\models\timeline\DataProvider $dataProvider */
$this->registerJsFile('@web/js/orderAdminList.js', [
    'depends' => 'yii\web\JqueryAsset',
    'position' => \yii\web\View::POS_END
]);
?>

<div id="orderMainAdminList">
    <div id="orderAdminList" data-controller="order" class="order-admin-list">

        <h1><?= Html::encode($this->title) ?></h1>

        <div id="searchBlock" class="roww">
            <div class="searchIdBlock" style="position: relative">
                <span class="b-num">№</span>
                <input id="searchId"
                       name="searchId"
                       type="text"
                       placeholder=""
                       value="<?php if (isset($searchId)) {
                           if (!empty($searchId)) {
                               echo $searchId;
                           }
                       } ?>"
                       style="max-width: 100%"
                />
            </div>

            <div class="searchTextBlock">
                <span>Текст</span>
                <input name="searchText"
                       id="searchText"
                       value="<?php if (isset($searchText)) {
                           if (!empty($searchText)) {
                               echo $searchText;
                           }
                       } ?>"
                       type="text" placeholder="ФИО, E-mail, Телефон"/>
            </div>

            <div class="searchUserStatusBlock">
                <span>Статус</span>
                <select id="searchStatus" name="searchStatus">
                    <option value="0" <?php if (isset($searchStatus)) {
                        if ($searchStatus == 0) echo "selected";
                    } ?>>
                        все
                    </option>
                    <option value="1" <?php if (isset($searchStatus)) {
                        if ($searchStatus == 1) echo "selected";
                    } ?>>
                        (Опубликован) требует проверки
                    </option>
                    <option value="2" <?php if (isset($searchStatus)) {
                        if ($searchStatus == 2) echo "selected";
                    } ?>>
                        (Опубликован) подтверждён
                    </option>
                    <option value="3" <?php if (isset($searchStatus)) {
                        if ($searchStatus == 3) echo "selected";
                    } ?>>
                        Закрыт
                    </option>
                </select>
            </div>

        </div>


        <div class="resultsBlock" data-results-limit="">
            <hr/>
            <?php if (!empty($results)) { ?>

            <?php foreach ($results

            as $item) {

            $order = Order::find()->where(['id' => $item['id']])
                ->with('professions')
                ->with('smetaDocs')
                ->with('normBases')
                ->one();
            ?>

            <div id="item_<?= $item['id'] ?>" class="itemBlock" data-id="<?= $item['id'] ?>" data-position="">

                <div class="row">
                    <div class="col-lg-1 col-md-1 col-sm-1 noPadding">
                        <strong>№<?= $item['id'] ?></strong>
                    </div>

                    <div class="col-lg-2 col-md-2 col-sm-2">
                        <strong>
                            <?php if ($item['published'] == 1) {
                                echo "Изменен";
                            } else {
                                echo "Закрыт";
                            }
                            ?>: </strong>
                        <?php if ($item['published'] == 1) {
                            echo date('d.m.Y', $item['updated_at']);
                        } else {
                            echo date('d.m.Y', $item['finished_at']);
                        }
                        ?>
                    </div>

                    <div data-id="<?= $item['id']; ?>" class="col-lg-8 col-md-8 col-sm-8 statusOfMyOrder
                            <?php
                    // В зависимости от статуса добавим в див класс для отображения цвета
                    if ($item['published'] == 1) {
                        echo "status_public";
                        if ($item['checked'] == 0) {
                            echo "_not_check";
                        }
                    } else {
                        echo "status_close";
                    }
                    ?>
                        ">
                        Статус:
                        <?php
                        if ($item['published'] == 1) {
                            echo " опубликован";
                            if ($item['checked'] == 0) {
                                echo " (требует проверки)";
                            } else {
                                echo " (подтверждён)";
                            }
                        } else {
                            echo " закрыт";

                            if (($item['closing_reason'] == Order::CLOSING_REASON_OUR)) {
                                echo " (помогли сметчики с портала)";
                            } elseif (($item['closing_reason'] == Order::CLOSING_REASON_ANOTHER)) {
                                echo " (нашел специалистов в другом месте )";
                            } elseif (($item['closing_reason'] == Order::CLOSING_REASON_OTHER)) {
                                echo " (другая причина)";
                            } elseif (($item['closing_reason'] == Order::CLOSING_REASON_TIME)) {
                                echo " (закрыто по истечению времени)";
                            } elseif (($item['closing_reason'] == Order::CLOSING_REASON_ADMIN)) {
                                echo " (закрыто админом: ";
                                echo "<span class='myTool' title='" . $item['closing_reason_text'] . "'>";

                                $text = htmlspecialchars($item['closing_reason_text']);
                                mb_regex_encoding('UTF-8');
                                mb_internal_encoding("UTF-8");
                                $text = preg_split('/(?<!^)(?!$)/u', $text);

                                $i = 0;
                                foreach ($text as $ch) {
                                    $i++;
                                    echo $ch;
                                    if ($i >= 50) break;
                                }

                                echo "</span>";
                                echo ")";
                            }
                        }
                        ?>
                    </div>

                    <div class="col-lg-1 col-md-1 col-sm-1 b-admin__edit">
                        <?php if ($item['published'] == 1) { ?>
                            <a class="editLink" data-id="<?= $item['id']; ?>"
                               href="<?= Url::to(['order/update', 'id' => $item['id']]); ?>">Редактировать</a>
                        <?php } ?>
                    </div>
                </div>

                <div class="row">
                    <strong><?= $item['name']; ?></strong>
                </div>

                <div class="row">
                    <div class="emailAdminListBlock">
                        <strong>E-mail:</strong> <?= Html::encode($item['email']) ?>

                        <?php

                        if (isset($item->customer) && (!empty($item->customer))) {
                            if ($item->customer->status_id === \app\models\Customer::STATUS_REQUIRES_VERIFICATION['val']) {
                                echo "<span style='color: #0066FF'>(Не подтвержден)</span>";
                            } ?>

                            <a href="<?= Url::to(['customer/admin-customers-list', 'text' => trim($item['email'])]); ?>">Перейти
                                к профилю</a>

                        <?php } ?>

                    </div>
                    <div class="">
                        <strong>ФИО:</strong> <?= Html::encode($item['fio']); ?>
                    </div>
                    <div class="">
                        <strong>Тел.:</strong> <?= Html::encode($item['phone']) . $item->createAdditionalNumbersSpan() ?>
                    </div>

                </div>

                <div class="row">

                    <div class="itemProfessions">
                        <strong>Профессиональная область: </strong>
                        <?php
                        $professions = $order->getRelatedTitleFrom('professions');
                        if (!empty($professions)) {
                            foreach ($professions as $profession) {
                                echo $profession['title'];
                                echo "; ";
                            }
                        } else {
                            echo " -";
                        }
                        ?>
                    </div>

                </div>

                <div class="row">

                    <div class="itemNormBases">
                        <strong>Нормативные базы: </strong>
                        <?php
                        $normBases = $order->getRelatedTitleFrom('normBases');
                        if (!empty($normBases)) {
                            foreach ($normBases as $normBase) {
                                echo $normBase['title'];
                                echo "; ";
                            }
                        } else {
                            echo " -";
                        }
                        ?>
                    </div>

                </div>

                <div class="row">

                    <div class="itemSmetaDocs">
                        <strong>Сметная документация: </strong>
                        <?php
                        $smetaDocs = $order->getRelatedTitleFrom('smetaDocs');
                        if (!empty($smetaDocs)) {
                            foreach ($smetaDocs as $smetaDoc) {
                                echo $smetaDoc['title'];
                                echo "; ";
                            }
                        } else {
                            echo " -";
                        }
                        ?>
                    </div>

                </div>

                <div class="row" style="overflow: hidden;">
                    <strong>Содержание заказа: </strong> <?= $item['text']; ?>
                </div>

                <div class="row">
                    <div style="float: left;margin-bottom: 15px;">
                        <strong>Бюджет (руб): </strong>
                        <?php
                        if ($item['price'] != 0) {
                            echo $item['price'];
                        } else {
                            echo "По договоренности";
                        }
                        ?>
                    </div>

                    <style>
                        .b-admin__resolve-success {
                            float: right;
                            text-align: right;
                            padding-right: 23px;
                        }

                        .b-admin__res-suc_item1 {
                            width: 150px;
                        }

                        .b-admin__res-suc_item2 {
                            width: 150px;
                            margin-right: 2px;
                        }
                    </style>

                    <div class="b-admin__resolve-success">


                        <div class="col-lg-1 col-md-1 col-sm-1 noPadding b-admin__res-suc_item1">
                            <?php if (($order->checked == 0) && ($item['published'] == 1)) { ?>
                                <?php if (isset($item->customer) && (!empty($item->customer))) {
                                    //   if ($item->customer->status_id !== \app\models\Customer::STATUS_CONFIRMED['val']) { ?>
                                    <a data-id="<?= $item['id']; ?>" class="approveBtn">Подтвердить</a>
                                    <?php //}
                                } ?>
                            <?php } ?>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1 noPadding  b-admin__res-suc_item2">
                            <?php if ($item['published'] == 1) {
                                // if ($item->customer->status_id !== \app\models\Customer::STATUS_REJECTED['val']) { ?>
                                <a data-id="<?= $item['id']; ?>" class="closeBtn">Закрыть</a>
                                <?php // }
                            }?>
                        </div>

                    </div>

                </div>
                <hr/>
                <?php } ?>

                <?php } else { ?>

                    <p>Ничего не найдено.</p>

                <?php } ?>
            </div>

            <div class="paginatorRow">
                <div class="paginator">
                    <?=
                    LinkPager::widget([
                        'pagination' => $dataProvider->pagination,
                        'lastPageLabel' => true,
                        'firstPageLabel' => true
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>