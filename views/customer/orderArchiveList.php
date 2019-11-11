<?php

use  app\models\Order;

/**
 *  Личный кабинет - АРХИВ ЗАКАЗОВ (customer/order-archive-list/)
 *
 *
 * @var Order[] $orders
 * @var \app\models\Customer $customer
 * @var int $switchCabinetNav
 * @var int $allOrdersInArchiveCount
 * @var Order $order
 * @var \app\models\OrderFeadbackUser $orderFeadbackUser
 **
 */

$this->registerJsFile('@web/js/customerOrdersController.js', ['depends' => 'yii\web\JqueryAsset']);

?>

<section id="customerArchiveCabinet">

    <?= $this->render('blocks/_customerCabinetNav', [
        'customer' => $customer,
        'switchCabinetNav' => 2
    ]); ?>

    <section id="additionallyVariables" hidden="hidden">
        <input id="allOrdersCount" value="<?= $allOrdersInArchiveCount; ?>" hidden="hidden">
    </section>

    <main id="customerCabinetArchive" class="container" style="min-height: 280px; position: relative">
        <div id="infoBlock">
            <div id="closeSuccess" class="alert alert-warning" hidden>
                Ваш заказ №<span id="orderIdForAlertClose"></span> успешно закрыт!
            </div>
        </div>
        <section id="currentOrders" class="row">
            <div id="orderItems">
                <?= $this->render('blocks/_orderArchiveItems', [
                    'orders' => $orders,
                    'count' => $count
                ]); ?>
                <?php if ($allOrdersInArchiveCount === 0) { ?>
                    <p>На текущий момент у Вас нет заказов в архиве.</p>
                <?php } ?>
            </div>
        </section>
        <section class="clear" style="position: absolute; bottom: 0; margin: 0 auto; left: 0; right: 0;">
            <div>
                <div class="row text-center">
                    <p>
                        Показано заказов: <span id="ordersInList"><?php
                            if ($allOrdersInArchiveCount < 7) {
                                echo $allOrdersInArchiveCount;
                            } else { ?> <?= count($orders); ?><?php } ?>
                    </span> из <span id="ordersCount"><?= $allOrdersInArchiveCount; ?></span>
                    </p>
                    <?php
                    $count = $allOrdersInArchiveCount - count($orders);

                    if ($count > 0) { ?>

                        <a id="add">Показать еще <span id="countOrdersLasts">
                        <?php
                        $count = $allOrdersInArchiveCount - 7;
                        if ($count >= 7) { ?>7<?php } elseif ($count > 0) {
                            echo $count;
                        } ?>
                        </span></a>

                    <?php } ?>
                </div>
            </div>
        </section>
    </main>

    <div id="myModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">

            </div>
        </div>
    </div>

</section>