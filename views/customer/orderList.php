<?php

use  app\models\Order;

/**
 *  Личный кабинет - ТЕКУЩИЕ ЗАКАЗЫ (customer/order-list/)
 * =============================================
 **
 * Created by PhpStorm.
 * User: decilya
 * Date: 22.02.19
 * Time: 17:13
 *
 * @var Order[] $orders
 * @var \app\models\Customer $customer
 * @var int $switchCabinetNav
 * @var int $allOrdersCount
 * @var int|null $goToItem
 */

$this->registerJsFile('@web/js/customerOrdersController.js', ['depends' => 'yii\web\JqueryAsset']);
?>

<section id="customerOrderCabinet">

    <?= $this->render('blocks/_customerCabinetNav', [
        'customer' => $customer,
        'switchCabinetNav' => 1
    ]); ?>

    <section id="additionallyVariables" hidden="hidden">
        <input id="allOrdersCount" value="<?= $allOrdersCount; ?>" hidden="hidden">
        <input id="goToItem" value="<?= $goToItem ?>">
    </section>

    <main id="customerCabinet" class="container" style="min-height: 280px; position: relative">
        <div id="infoBlock">
            <div id="closeSuccess" class="alert alert-success" hidden>
                Ваш заказ №<span id="orderIdForAlertClose"></span> успешно закрыт!
            </div>
        </div>
        <section id="currentOrders" class="row">
            <div id="orderItems">
                <?= $this->render('blocks/_orderItems', [
                    'orders' => $orders
                ]); ?>
                <?php if ($allOrdersCount === 0) { ?>
                    <p>На текущий момент у Вас нет действующих заказов.</p>
                <?php } ?>
            </div>
        </section>

        <section class="clear" style="position: absolute; bottom: 0; margin: 0 auto; left: 0; right: 0;">
            <div>
                <div class="row text-center">
                    <p>
                        Показано заказов: <span id="ordersInList"><?php
                            if ($allOrdersCount < 7) {
                                echo $allOrdersCount;
                            } else { ?> <?= count($orders); ?><?php } ?>
                    </span> из <span id="ordersCount"><?= $allOrdersCount; ?></span>
                    </p>
                    <?php
                    $count = $allOrdersCount - count($orders);

                    if ($count > 0) { ?>

                        <a id="add">Показать еще
                            <span id="countOrdersLasts">
                        <?php
                        $count = $allOrdersCount - 7;
                        if ($count >= 7) { ?>7<?php } elseif ($count > 0) {
                            echo $count;
                        } ?>
                        </span>
                        </a>

                    <?php } ?>
                </div>
            </div>
        </section>
    </main>

    <div id="myModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Закрытие заказа</h4>
                    <button class="close" type="button" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body">
                    <p>
                        <input id="answer1" class="modal-radio" checked type="radio" name="answer" value="1">
                        <label for="answer1">Мне помогли сметчики с портала</label>
                    </p>

                    <p>
                        <input id="answer2" class="modal-radio" type="radio" name="answer" value="2">
                        <label for="answer2">Нашел специалистов в другом месте</label>
                    </p>

                    <p>
                        <input id="answer3" class="modal-radio" type="radio" name="answer" value="3">
                        <label for="answer3">Другая причина</label>
                    </p>
                </div>
                <div class="modal-footer">
                    <button data-id="" id="closeOrder" class="btn btn-closeOrder">Закрыть заказ</button>
                    <button class="btn btn-close-modal" type="button" data-dismiss="modal">Отмена</button>
                </div>
            </div>
        </div>
    </div>

</section>
