<?php

use \app\models\OrderFeadbackUser;

/** @var app\models\Customer $customer ; */

if (isset(Yii::$app->user->identity)) { ?>

    <div id="statisticsCustomerBlock" class="alert alert-info">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-4" style="padding: 0;">
                <div class="alertWrap">
                    <p>Просмотрено контактов за день:</p>
                </div>
            </div>

            <div class="col-lg-8 col-md-8 col-sm-8">
                <?= \app\models\ShowUserContactsCounter::getCustomerCounterForDayByAuthId(Yii::$app->user->identity->id) ?>
                /<?= Yii::$app->params['limitShowUserContactsCounter'] ?>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-4" style="padding: 0;">
                <div class="alertWrap">
                    <p>Количество размещенных заказов:</p>
                </div>
            </div>

            <div class="col-lg-8 col-md-8 col-sm-8">
                <?= \app\models\Order::find()->where(['auth_id' => Yii::$app->user->identity->id])->count(); ?>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-4" style="padding: 0;">
                <div class="alertWrap">
                    <p>Общее число откликов на заказы:</p>
                </div>

            </div>

            <div class="col-lg-8 col-md-8 col-sm-8">
                <?= OrderFeadbackUser::find()
                    ->where(['customer_id' => $customer->id])
                    ->innerJoin('users', 'order_feadback_user.user_id = users.id')
                    ->andWhere(['users.status_id' => 2])
                    ->andWhere(['users.is_visible' => 1])
                    ->count();
                ?>
                <?php
                $new = OrderFeadbackUser::find()
                    ->where(['customer_id' => $customer->id])
                    ->andWhere(['new' => 1])
                    ->innerJoin('order', 'order.id=order_feadback_user.order_id')
                    ->andWhere(['order.published' => 1])
                    ->innerJoin('users', 'order_feadback_user.user_id = users.id')
                    ->andWhere(['users.status_id' => 2])
                    ->andWhere(['users.is_visible' => 1])
                    ->count();

                if ($new > 0) { ?>
                    /<span style="color: #009900;">(+<?= $new ?>)</span>
                <?php } ?>

            </div>
        </div>
    </div>

<?php } ?>