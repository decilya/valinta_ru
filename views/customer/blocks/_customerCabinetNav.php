<?php
/**
 * Created by PhpStorm.
 *
 * User: decilya
 * Date: 25.02.19
 * Time: 16:03
 *
 * @var \app\models\Customer $customer
 * @var int $switchCabinetNav
 *
 */

use kartik\alert\Alert;

?>

<header id="cabinet">
    <div class="titleHolder">
        <div class="container">
            <h1>Личный кабинет №<?= !empty($customer->real_id) ? $customer->real_id : 0; ?></h1>
        </div>
    </div>
    <div class="titleBottom">
        <div class="container">
            <nav>
                <ul>
                    <li class="<?php if ($switchCabinetNav === 1) { ?>active<?php } ?>">
                        <a href="/customer/order-list/">Текущие заказы</a>
                    </li>
                    <li class="<?php if ($switchCabinetNav === 2) { ?>active<?php } ?>">
                        <a href="/customer/order-archive-list/">Архив заказов</a>
                    </li>
                    <li class="<?php if ($switchCabinetNav === 3) { ?>active<?php } ?>">
                        <a href="/customer/update/<?= Yii::$app->user->identity->id; ?>">Мой профиль</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</header>

<input id="siteDomen" data-domen="<?= Yii::$app->params['domen']; ?>" hidden />
<input id="siteHost" data-host="<?= Yii::$app->params['host']; ?>" hidden />

<div class="infoBlockForCabinetHeader container">
    <?php

    $successFlashMessageCabinet = Yii::$app->session->getFlash('successCabinetHeader');
    $errorFlashMessageCabinet = Yii::$app->session->getFlash('errorCabinetHeader');

    if ($successFlashMessageCabinet) {
        echo Alert::widget([
            'type' => Alert::TYPE_SUCCESS,
            'icon' => 'glyphicon glyphicon-ok-sign',
            'body' => $successFlashMessageCabinet,
            'showSeparator' => true,
            'delay' => Yii::$app->params['flashMessageConfig']['customerCabinetHeader']['successCabinetHeader']['delay']
        ]);
    }

    if ($errorFlashMessageCabinet) {
        echo Alert::widget([
            'type' => Alert::TYPE_DANGER,
            'icon' => 'glyphicon glyphicon-remove-sign',
            'body' => $errorFlashMessageCabinet,
            'showSeparator' => true,
            'delay' => Yii::$app->params['flashMessageConfig']['customerCabinetHeader']['errorCabinetHeader']['delay']
        ]);
    }
    ?>
</div>