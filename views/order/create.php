<?php

use app\assets\MaskAsset;
use yii\bootstrap\Alert;

/**
 * @var int $isUser
 * @var \app\models\forms\FastLoginOrderForm $fastLoginOrderForm
 * @var \app\models\forms\FastRegOrderForm $fastRegOrderForm
 * @var array $staticDBsContent
 * @var \yii\web\View $this
 * @var \app\models\Customer $customer
 */

$this->registerJsFile('/js/fastOrder.js', ['position' => yii\web\View::POS_END]);

$this->title = 'Разместить заказ';
$this->params['breadcrumbs'][] = ['label' => 'Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

MaskAsset::register($this);
?>

<div id="orderCreate" class="request-create">

    <div class="messageBlock">
        <?php if (Yii::$app->params['switchForRegNewOrder'] == 1) : ?>

            <div id="orderInfographic" class="b-info__about b-orderInfographic">
                <div class="clientsMessageHolder">
                    <div class="container orderCreateFlex">
                        <div class="infoBlock">
                            <img src="/img/newIcon/1.png"/>

                            <p>
                                1. Зарегистрируйте <br/>
                                свой заказ.
                            </p>
                        </div>
                        <div class="infoBlock">
                            <img src="/img/newIcon/2.png"/>

                            <p>
                                2. Дождитесь одобрения <br/>
                                и размещения Вашего заказа.
                            </p>
                        </div>
                        <div class="infoBlock">
                            <img src="/img/newIcon/3d.png"/>
                            <p>
                                3. Выбирайте из предложений <br>
                                сметчиков и выполняйте работу <br>
                                в срок.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        <?php else : ?>
            <div id="orderInfographic" class="b-info__about b-orderInfographic">

                <div class="clientsMessageHolder">
                    <div class="container">
                        <div class="infoBlock">
                            <img src="/img/newIcon/1.png">

                            <p>
                                1. Зарегистрируйте <br/>
                                свой заказ.
                            </p>
                        </div>
                        <div class="infoBlock">
                            <img src="/img/newIcon/2.png">

                            <p>
                                2. Дождитесь одобрения <br/>
                                и размещения Вашего заказа.
                            </p>
                        </div>
                        <div class="infoBlock">
                            <img src="/img/newIcon/3d.png"/>
                            <!-- <p>3. Принимайте заказы<br>и зарабатывайте.</p>-->
                            <p>
                                3. Выбирайте из предложений <br>
                                сметчиков и выполняйте работу <br>
                                в срок.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="container">

        <?php if (Yii::$app->params['switchForRegNewOrder'] == 1) { ?>

            <?php if (($customer !== null) && ($customer->status_id === \app\models\Customer::STATUS_REJECTED['val'])) { ?>
                <div class="alert-warning-wrap"
                     style="width: 100%; height: 100%; display: flex; justify-content: center">
                    <div class="alert alert-warning">Ваша анкета отклонена. Отредактируйте анкету.</div>
                </div>
            <?php } else { ?>

                <h1 class="text-center">Размещение заказа</h1>

                <?php if (!$isUser) { ?>

                    <div id="myLoginForm">
                        <?php if (Yii::$app->user->isGuest) {
                            echo $this->render('/order/forms/_fastRegForm', [
                                'fastRegOrderForm' => $fastRegOrderForm,
                            ]);
                        } else {
                            echo $this->render('/order/forms/_loggedInOrderFormForm', [
                                'fastRegOrderForm' => $fastRegOrderForm,
                            ]);
                        } ?>
                    </div>

                    <div id="myOrderForm">
                        <?= $this->render('/order/forms/_form', [
                            'model' => $model,
                            'staticDBsContent' => $staticDBsContent,
                            'isCreate' => true,
                        ]) ?>

                    </div>

                <?php } else { ?>
                    <div class="alert alert-danger">
                        Размещение заказа под учетной записью сметчика невозможно. <a href="/site/logout">Выйти</a>
                    </div>
                <?php }
            }
        } else { ?>
            <div id="stopRegNewOrder">
                <div class="b-dummy__wrap" style="text-align: center">
                    <img class="b-dummy__img" src="/img/dummy.png" alt="dummy"/>

                    <p class="b-dummy__text" style="margin-bottom: 20px;">
                        «Регистрация заказов временно приостановлена!<br>
                        По всем вопросам, пожалуйста, звоните нам по
                        телефону: <?= Yii::$app->params['mainSystemPhone']; ?>
                    </p>
                </div>
            </div>
        <?php } ?>

    </div>
</div>
