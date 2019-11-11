<?php

use yii\helpers\Html;
use app\models\Order;
use yii\helpers\Url;

/**
 * @var array $item
 * @var array $months
 * @var \app\models\Customer|\app\models\User $user
 */

$phoneArr = [];

foreach ($item['phones'] as $phone) {
    $phoneArr[] = $phone['number'];
}
?>

<div class="itemRow" data-position="<?php if (isset($positionOrderInResultArr)) {
    echo $positionOrderInResultArr;
} else {
    echo 1;
} ?>">
    <div class="nameHolder">

        <div class="roww clearfix">
            <div class="col-lg-9 col-md-9 col-sm-9 noPadding">
                <h3 class="b-order-list__title">
                    <a href="<?= Url::to(['order/view', 'id' => $item['id']]); ?>"><?= Html::encode($item['name']); ?></a>
                </h3>
            </div>

            <div class="col-lg-3 col-md-3 col-sm-3 noPadding">
                <h3 class="b-order-list__title-date">
                    <strong>
                        <?= date('d ' . $months[date('n', $item['updated_at'])] . ' Y', $item['updated_at']) . " г."; ?>
                    </strong>
                </h3>
            </div>
        </div>

        <div class="showContactsBlock">
            <div class="imgHolder"></div>
            <div class="linkHolder">
                <a class="showContacts" data-id="<?= $item['id']; ?>">
                    <?php

                    $str = 'Откликнуться на заказ';
                    $strF = 'Открыть контакты';

                    echo ($item['statusFeadBack']) ? ($strF) : ($str);

                    ?>
                </a>
            </div>
        </div>

    </div>

    <div class="hiddenText" data-id="<?= $item['id']; ?>" style="display: none">
        <div class="ipapHolder">
            <?php
            $auth = false;
            if (!Yii::$app->user->isGuest) {
                /** @var \app\models\Auth $auth */
                $auth = \app\models\Auth::findOne(['id' => Yii::$app->user->identity->id]);
            }
            ?>

            <?php if (Yii::$app->user->isGuest) { ?>
                <a href="/register">Зарегистрируйтесь</a> в качестве сметчика и <a href="/login">войдите</a> на сайт.
            <?php } elseif (\app\models\Auth::getUserType() === \app\models\Auth::TYPE_CUSTOMER) { ?>
                <a href="/register">Зарегистрируйтесь</a> в качестве сметчика.
            <?php } else {

                if (\app\models\Auth::getUserType() === \app\models\Auth::TYPE_USER) {

                    if ($user !== null) {
                        if (($user['status_id'] === 2) && ($user['is_visible'] === 1)) { ?>

                            <p><strong><?= $item['fio']; ?></strong></p>
                            <p>
                                <?php foreach ($phoneArr as $tel) {
                                    echo $tel . ', ';
                                } ?>
                            </p>
                            <p><a href="mailto:<?= $item['email']; ?>"><?= $item['email']; ?></a></p>

                        <?php } ?>

                    <?php } ?>

                <?php } ?>
            <?php } ?>
        </div>
    </div>

</div>

<div class="secondRow b-item__list-order">

    <?php if (isset($item['price'])) { ?>

        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-3 noPadding">
                <label><strong>Бюджет:</strong></label>
            </div>
            <div class="col-lg-9 col-md-9 col-sm-9 noPadding b-order__left">
                <span class="price">
                    <?php
                    if ($item['price'] > 0) {
                        echo $item['price'];
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
    $order = Order::find()->where(['id' => $item['id']])
        ->with('professions')
        ->with('smetaDocs')
        ->with('normBases')
        ->one();
    ?>

    <?php
    $professions = $order->getRelatedTitleFrom('professions');
    if (!empty($professions)) {
        ?>

        <div class="row clearfix">
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

        <div class="row">
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
        <div class="row">
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

    <?php if ($item['text'] != '') { ?>
        <div class="row">
            <div class="mainTextBlock col-lg-3 col-md-3 col-sm-3 noPadding">
                <strong>Содержание заказа: </strong>
            </div>

            <div class="col-lg-9 col-md-9 col-sm-9 noPadding dotText ajax VvalueRow Eexperience b-text-order noPadding b-order__left" style="overflow: hidden;">
                <?= $item['text']; ?>
            </div>
        </div>
    <?php } ?>

</div>


<hr/>
