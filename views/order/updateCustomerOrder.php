<?php

/** @var \app\models\Customer $customer */

use yii\helpers\Html;
use app\assets\MaskAsset;
use yii\helpers\Url;

if ($model->published !== 0) {
    $this->title = 'Редактирование заказа №' . $model->id;
} else {
    $this->title = 'Заказ №' . $model->id;
}

$this->params['breadcrumbs'][] = $this->title;

MaskAsset::register($this);
?>

<script>
    $(document).ready(function () {

        if ($('#byAgreement').prop('checked')) {
            $('#order-price').val('');
            $('#order-price').attr('disabled', true);
        }

        $('#byAgreement').on('change', function () {
            if ($('#byAgreement').prop('checked')) {
                $('#order-price').val('');
                $('#form-order').yiiActiveForm('validateAttribute', 'order-price');
                $('#order-price').attr('disabled', true);
            } else {
                $('#order-price').attr('disabled', false);
                $('#form-order').yiiActiveForm('validateAttribute', 'order-price');
            }
        });

        $("#closeOrder").click(function () {
            let orderId = $(this).data("id");
            let closingReason = $('input[name=answer]:checked').val();

            $.ajax({
                method: "POST",
                url: "/order/close-order-by-customer",
                data: {orderId: orderId, closingReason: closingReason},
                dataType: 'json',
                success: function (data) {
                    location.reload();
                },
                error: function () {
                    alert('Ошибка');
                }
            });
        });

        $('#sendLinkToUserFromAdmin').on('click', function (event) {
            let orderId = $(this).data("id");

            $.ajax({
                method: "POST",
                url: "/order/send-link-to-customer-from-admin",
                data: {orderId: orderId},
                dataType: 'json',
                success: function (data) {
                    location.reload();
                },
                error: function () {
                    alert('Ошибка');
                }
            });

        });

    });
</script>

<section id="customerUpdateCabinet">

    <?= $this->render('/customer/blocks/_customerCabinetNav', [
        'customer' => $customer,
        'switchCabinetNav' => null
    ]); ?>

    <div class="request-update">

        <div class="messageBlock ">

            <?php if (!isset(Yii::$app->user->identity->is_admin) || (!Yii::$app->user->identity->is_admin)) { ?>
                <div id="orderInfographic">
                    <?php if (Yii::$app->session->hasFlash('success')): ?>
                        <div class="messageBlock container">
                            <div class="alert alert-success alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button" style="margin-right: 15px;">×</button>
                                <?= Yii::$app->session->getFlash('success') ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="titleHolder">
                        <h2>
                            <?= Html::encode($this->title) ?>
                        </h2>
                    </div>
                </div>
            <?php } else { ?>
                <div class="adminPanel">
                    <div class="roww">
                        <h1>Заказ №<?= $model->id; ?></h1>
                    </div>
                    <div class="roww adminBlockForLink" style="background: #d6d6d6;padding-left:10px; height: 39px;
                padding: 10px">
                        <div class="col-lg-5 col-md-5 col-sm-5 noPadding">
                            <p>Статус заказа:
                                <strong>
                                    <?php
                                    if ($model->published == 1) {
                                        echo " опубликован";
                                        if ($model->checked == 0) {
                                            echo " (требует проверки)";
                                        } else {
                                            echo " (подтверждён)";
                                        }
                                    } else {
                                        echo " закрыт";
                                    }
                                    ?>
                                </strong>
                            </p>
                        </div>

                        <div class="col-lg-4 col-md-4 col-sm-4">
                            <!--                            <a id="sendLinkToUserFromAdmin" data-id="--><?//= $model->id; ?><!--">Отправить ссылку на страницу-->
                            <!--                                редактирования</a>-->
                        </div>


                        <div class="col-lg-3 col-md-3 col-sm-3">

                            <?php

                            $urlTmp = Url::previous();

                            if ((isset($urlTmp)) && (!empty($urlTmp))) { ?>
                                <a href="<?= Yii::$app->params['host'] . '://' . $_SERVER["HTTP_HOST"] . $urlTmp ?>">Вернуться к списку</a>
                            <?php } else { ?>
                                <a href="<?= \yii\helpers\Url::to(['order/admin-list']); ?>">Вернуться к списку</a>
                            <?php } ?>

                        </div>
                    </div>
                </div>
            <?php } ?>

            <div class="wrapp-item-order" style="max-width: 980px; margin: 0 auto; padding: 15px 10px !important">

                    <?php if (!isset(Yii::$app->user->identity->is_admin) || (!Yii::$app->user->identity->is_admin)) { ?>
                    <div class="roww" style="display: flex; justify-content: space-between;">
                        <div class="item-order-stat">
                            <p>Заказ:
                                <strong>
                                    <?php
                                    if ((int)$model->published == 1) {
                                        echo " опубликован";
                                    } else {
                                        echo " закрыт";
                                    }
                                    ?>
                                </strong>
                            </p>
                        </div>

                        <div class="item-order-stat">
                            <p>Дата закрытия: <?= date('d.m.Y', $model->finished_at) ?></p>
                        </div>

                        <!--                    <div class="item-order-stat">-->
                        <!--                        --><?php
                        //                        if ((int)$model->published == 1) {
                        //                            // если перешли по ссылке
                        //                            if ($fromLink) { ?>
                        <!--                                <a data-toggle="modal" data-target="#myModal" id="modalFormCloseOrder"-->
                        <!--                                   data-id="-->
                        <?php //= $model->id; ?><!--" style="cursor: pointer">Закрыть заказ</a>-->
                        <!--                            --><?php //}
                        //                        } ?>
                        <!--                    </div>-->
                    </div>

            </div>
        <?php } ?>
            <div class="container">
                <div class="roww">
                    <?= $this->render('forms/_form', [
                        'model' => $model,
                        'staticDBsContent' => $staticDBsContent,
                        'showEmail' => true
                    ]) ?>
                </div>
            </div>

        </div>
    </div>

    <div id="myModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button class="close" type="button" data-dismiss="modal">×</button>
                    <h4 class="modal-title">Закрытие заказа</h4>
                </div>
                <div class="modal-body">
                    <p>
                        <input id="answer1" checked type="radio" name="answer" value="1">
                        <label for="answer1">Мне помогли сметчики с портала</label>
                    </p>

                    <p>
                        <input id="answer2" type="radio" name="answer" value="2">
                        <label for="answer2">Нашел специалистов в другом месте</label>
                    </p>

                    <p>
                        <input id="answer3" type="radio" name="answer" value="3">
                        <label for="answer3">Другая причина</label>
                    </p>
                </div>
                <div class="modal-footer">
                    <button data-id="<?= $model->id; ?>" id="closeOrder" class="btn btn-success">Закрыть заказ</button>
                    <button class="btn btn-danger" type="button" data-dismiss="modal">Отмена</button>
                </div>
            </div>
        </div>
    </div>

</section>