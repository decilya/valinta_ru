<?php

use  app\models\Order;

/**
 * Личный кабинет - ОТЗЫВЫ
 *
 *
 * @var Order[] $orders
 * @var \app\models\Customer $customer
 * @var int $switchCabinetNav
 * @var int $countOrderFeadbackUser
 * @var Order $order
 * @var \app\models\OrderFeadbackUser $orderFeadbackUser
 * @var null|int $goToItem
 *
 */

$this->registerJsFile('@web/js/customerOrdersController.js', ['depends' => 'yii\web\JqueryAsset']);
?>
<script>
    $(document).ready(function () {
        let goToItem = $('#goToItem').val();
        
        if ((goToItem) && (Number(goToItem) !== 0)) {
            goToItem = Number(goToItem);

            let myEl = $("article.orderFeadbackUserItem[data-user='" + goToItem + "']");

            if ($('article').is(".orderFeadbackUserItem[data-user='" + goToItem + "']")) {

                let destination = myEl.offset().top - 150;

                $('html,body').animate({scrollTop: destination}, 500)
            }
        }
    });
</script>

<section>

    <?= $this->render('blocks/_customerCabinetNav', [
        'customer' => $customer,
        'switchCabinetNav' => 1
    ]); ?>

    <section id="additionallyVariables" hidden="hidden">
        <input id="allFeadbackCount" value="<?= $countOrderFeadbackUser; ?>" hidden="hidden">
        <input id="myOrderId" value="<?= $order->id; ?>" hidden="hidden">
        <input id="goToItem" value="<?= $goToItem ?>">
    </section>

    <main id="customerCabinetFeadback" class="container">
        <div id="infoBlock"></div>
        <article id="currentOrderFeadback">

            <article class="orderItem" data-id="<?= $order->id; ?>">

                <div class="row margFeedBack">

                    <div class="col-lg-4 col-md-4 col-sm-4 noPadding">
                        <div class="order-menu">
                            <ul>
                                <li class="backToOrderList">
                                    <a href="/customer/order-list/?goToItem=<?= $order->id; ?>">Вернуться к
                                        списку</a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-lg-7 col-md-7 col-sm-7">
                        <div class="feedbackTitle">
                            <h2>Отклики на заказ</h2>
                        </div>
                    </div>
                </div>


                <div class="row headerSelectText">
                    ВАШ ЗАКАЗ:
                </div>

                <hr style="margin-bottom: 0.5em;">

                <section class="row">
                    <header>
                        <div class="col-lg-9 col-md-9 col-sm-9 noPadding">
                            <div class="orderTitle">
                                <h2>Заказ №<?= $order->id; ?> - <?= $order->name; ?></h2>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-3 noPadding">
                            <div class="orderDate">
                                <div class="orderDate--bl">
                                    <h4><?= \app\models\Site::getNormalRussianDateByTimeStamp($order->updated_at); ?></h4>
                                </div>
                            </div>
                        </div>
                    </header>

                    <div class="row">
                        <?php if (isset($order->price)) { ?>
                            <div class="row mb18 mt30">
                                <div class="col-lg-3 col-md-3 col-sm-3 noPadding">
                                    <label><strong>Бюджет:</strong></label>
                                </div>
                                <div class="col-lg-9 col-md-9 col-sm-9 noPadding b-order__left">
                            <span class="price">
                                <?php
                                if ($order->price > 0) {
                                    echo $order->price;
                                    echo " руб.";
                                } else {
                                    echo "По договоренности";
                                }
                                ?>
                            </span>
                                </div>
                            </div>
                        <?php } ?>

                        <?php
                        $professions = $order->getRelatedTitleFrom('professions');
                        if (!empty($professions)) { ?>
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
                            <div class="row">
                                <div class="mainTextBlock col-lg-3 col-md-3 col-sm-3 noPadding">
                                    <strong style="margin-left: 0;">Содержание заказа: </strong>
                                </div>

                                <div class="col-lg-9 col-md-9 col-sm-9 noPadding dotText ajax VvalueRow Eexperience b-text-order noPadding b-order__left">
                                    <?= $order->text; ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </section>
                <hr/>
                <section class="feadback">
                    <div class="row headerSelectText mb18">
                        ОТКЛИКНУЛИСЬ:
                    </div>
                    <div id="feadbackItems">
                        <?= $this->render('blocks/_userFeadbackItem', [
                            'orderFeadbackUser' => $orderFeadbackUser,
                            'order' => $order
                        ]); ?>
                    </div>
                </section>

                <div class="row text-center">
                    <p>Показано откликов: <span id="feadbackInList"><?php if ($countOrderFeadbackUser < 7) {
                                echo $countOrderFeadbackUser;
                            } else { ?> 7 <?php } ?> </span> из <span
                                id="countOrderFeadbackUser"><?= $countOrderFeadbackUser; ?></span></p>
                    <?php
                    $count = $countOrderFeadbackUser - 7;

                    if ($count > 0) { ?>
                        <a id="add">Показать еще
                            <span id="countFeadbackLasts">
                        <?php
                        if ($count >= 7) { ?>7<?php } elseif ($count > 0) {
                            echo $count;
                        } ?>
                        </span>
                        </a>
                    <?php } ?>
                </div>
            </article>
        </article>
    </main>

</section>