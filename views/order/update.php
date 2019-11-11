<?php

use kartik\alert\Alert;
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

<div class="request-update">

    <?php
    $successFlashMessage = Yii::$app->session->getFlash('success');
    $errorFlashMessage = Yii::$app->session->getFlash('error');

    if ($successFlashMessage) {
        echo Alert::widget([
            'type' => Alert::TYPE_SUCCESS,
            'icon' => 'glyphicon glyphicon-ok-sign',
            'body' => $successFlashMessage,
            'showSeparator' => true,
            'delay' => 8000
        ]);
    }

    if ($errorFlashMessage) {
        echo Alert::widget([
            'type' => Alert::TYPE_DANGER,
            'icon' => 'glyphicon glyphicon-remove-sign',
            'body' => $errorFlashMessage,
            'showSeparator' => true,
            'delay' => 9000
        ]);
    }
    ?>

    <div class="messageBlock">

        <?php if (!isset(Yii::$app->user->identity->is_admin) || (!Yii::$app->user->identity->is_admin)) { ?>
            <div id="orderInfographic">

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

                    </div>


                    <div class="col-lg-3 col-md-3 col-sm-3">

                        <?php

                        $urlTmp = Url::previous();

                        if ((isset($urlTmp)) && (!empty($urlTmp))) { ?>
                            <a href="<?php if (!empty($_SERVER['HTTPS'])) {
                                echo "https://";
                            } else {
                                echo "http://";
                            }
                            echo $_SERVER["HTTP_HOST"] . $urlTmp ?>">Вернуться к списку</a>
                        <?php } else { ?>
                            <a href="<?= \yii\helpers\Url::to(['order/admin-list']); ?>">Вернуться к списку</a>
                        <?php } ?>

                    </div>
                </div>
            </div>
        <?php } ?>

        <div class="wrapp-item-order">
            <div class="container">
                <?php if (!isset(Yii::$app->user->identity->is_admin) || (!Yii::$app->user->identity->is_admin)) { ?>
                <div class="roww">
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
                        <p>Дата закрытия: <strong><?= date('d.m.Y', $model->finished_at) ?></strong></p>
                    </div>

                    <div class="item-order-stat">
                        <?php
                        if ((int)$model->published == 1) {
                            // если перешли по ссылке
                            if ($fromLink) { ?>
                                <a data-toggle="modal" data-target="#myModal" id="modalFormCloseOrder"
                                   data-id="<?= $model->id; ?>" style="cursor: pointer">Закрыть заказ</a>
                            <?php }
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
        <div class="container">
            <div class="roww">
                <?= $this->render('/order/forms/_form', [
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