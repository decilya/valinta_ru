<?php
/**
 * Created by PhpStorm.
 * User: decilya
 * Date: 27.02.19
 * Time: 13:12
 *
 * @var  app\models\Order[] $orders ;
 *
 */
foreach ($orders as $order) { ?>
    <article class="orderItem" data-id="<?= $order->id; ?>">
        <header class="row" style="margin-bottom: 0 !important;">
            <div class="col-lg-9 col-md-9 col-sm-9" style="padding-left: 0;">
                <div class="orderTitle">
                    <h2>Заказ №<?= $order->id; ?> - <?= $order->name; ?></h2>
                </div>

                <div class="order-menu">
                    <ul>
                        <li class="editCustomerOrder">
                            <a href="/order/update-customer-order/<?= $order->id; ?>">Редактировать</a>
                        </li>
                        <?php if ($order->published === 1) { ?>
                            <li class="closeCustomerOrder">
                                <a class="modalFormCloseOrder"
                                   data-toggle="modal"
                                   data-target="#myModal"
                                   data-id="<?= $order->id; ?>">
                                    Закрыть
                                </a>
                            </li>
                        <?php } ?>
                        <li class="searchCustomerOrder">
                            <a href="<?= \yii\helpers\Url::to(['customer/recruitment', 'id' => $order->id]) ?>">Подобрать исполнителя</a>
                        </li>
                        <li class="answerCustomerOrder">
                            <a href="/customer/customer-order-feedback/<?= $order->id; ?>">
                                Откликнулись <?php if (count($order->getNewUserFeadback()) > 0) {
                                    echo "(+" . (count($order->getNewUserFeadback())) . ")";
                                } ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-3 col-md-3 col-sm-3" style="padding-right: 0;">
                <div class="orderDate">
                    <div class="orderDate--bl">
                        <h4><?= \app\models\Site::getNormalRussianDateByTimeStamp($order->updated_at); ?></h4>
                    </div>
                </div>

            </div>
        </header>

        <section class="clear">
            <div class="row mb18" class="clear">
                <div class="col-lg-3 col-md-3 col-sm-3 noPadding">
                    <strong>Бюджет: </strong>
                </div>
                <div class="col-lg-9 col-md-9 col-sm-9 noPadding b-order__left">
                    <?php
                    if ($order->price > 0) {
                        echo $order->price;
                        echo " руб.";
                    } else {
                        echo "По договоренности";
                    }
                    ?>
                </div>
            </div>

            <?php
            $professions = $order->getRelatedTitleFrom('professions');
            if (!empty($professions)) {
                ?>

                <div class="row mb18 clearfix">
                    <div class="col-lg-3 col-md-3 col-sm-3 noPadding">
                        <strong>Профессиональная область: </strong>
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-9 noPadding b-order__left">
                        <?php

                        foreach ($professions as $key => $profession) {

                            $f = end($professions);

                            echo "<span class='itemForOval";

                            if (!empty($qp['professions'])) {
                                if (in_array($profession['id'], $qp['professions'])) {
                                    echo " match ";
                                }
                            }

                            echo "''>";
                            echo $profession['title'];

                            if ($profession['id'] != $f['id']) {
                                echo ';';
                            }
                            echo "</span>";
                        }

                        ?>
                    </div>
                </div>

            <?php } ?>

            <?php
            $normBases = $order->getRelatedTitleFrom('normBases');
            if (!empty($normBases)) {
                ?>

                <div class="row mb18">
                    <div class="col-lg-3 col-md-3 col-sm-3 noPadding">
                        <strong>Нормативные базы: </strong>
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-9 noPadding b-order__left">
                        <?php
                        foreach ($normBases as $key => $normBase) {

                            $f = end($normBases);

                            echo "<span class='itemForOval";

                            if (!empty($qp['normBases'])) {
                                if (in_array($normBase['id'], $qp['normBases'])) {
                                    echo " match ";
                                }
                            }

                            echo "''>";
                            echo $normBase['title'];

                            if ($normBase['id'] != $f['id']) {
                                echo ';';
                            }
                            echo "</span>";
                        }
                        ?>
                    </div>
                </div>

            <?php } ?>

            <?php
            $smetaDocs = $order->getRelatedTitleFrom('smetaDocs');
            if (!empty($smetaDocs)) { ?>
                <div class="row mb18">
                    <div class="col-lg-3 col-md-3 col-sm-3 noPadding">
                        <strong>Сметная документация: </strong>
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-9 noPadding b-order__left">
                        <?php
                        foreach ($smetaDocs as $key => $smetaDoc) {

                            $f = end($smetaDocs);

                            echo "<span class='itemForOval";

                            if (!empty($qp['smetaDocs'])) {
                                if (in_array($smetaDoc['id'], $qp['smetaDocs'])) {
                                    echo " match ";
                                }
                            }

                            echo "''>";
                            echo $smetaDoc['title'];

                            if ($smetaDoc['id'] != $f['id']) {
                                echo ';';
                            }
                            echo "</span>";
                        }
                        ?>
                    </div>
                </div>
            <?php } ?>

            <?php if ($order->text != '') { ?>
                <div class="row mb18">
                    <div class="mainTextBlock col-lg-3 col-md-3 col-sm-3 noPadding">
                        <strong style="margin-left: 0;">Содержание заказа: </strong>
                    </div>

                    <div class="col-lg-9 col-md-9 col-sm-9 noPadding b-order__leftb-order__left dotText ajax VvalueRow Eexperience b-text-order noPadding b-order__left" style="overflow: hidden;">
                        <?= $order->text; ?>
                    </div>
                </div>
            <?php } ?>
        </section>

        <hr/>
    </article>
<?php } ?>

