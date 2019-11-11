<?php

/**
 * @var \app\models\Order $model
 * @var User $user
 */

use app\models\Auth;
use app\models\OrderFeadbackUser;
use app\models\Site;
use yii\helpers\Url;

$this->title = 'Заказ №' . $model->id;
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile(
    '@web/js/orderController.js', [
        'position' => 3
    ]
);
?>
<script>
    $(document).ready(function () {
        $('.showContacts').on('click', function () {
            let orderId = $(this).data('id');
            $(".hiddenText[data-id='" + orderId + "']").show();
            $(this).hide();
        });
    });
</script>

<div class="request-update">

    <div class="messageBlock">

        <div id="orderInfographic" class="b-info__about orderInfographicItem" style="background: #0084ae;">

            <img src="/img/newIcon/order-item.png"/>
            <h1 class="b-order-item__title">ЗАКАЗ №<?= $model->id; ?></h1>
        </div>

        <?php if ((int)$model->published === 0) { ?>

            <div class="roww">
                <div class="container">
                    <div class="alert alert-warning">
                        Заказ №<?= $model->id; ?> закрыт.
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php if ((int)$model->published === 1) { ?>
            <div class="b-order-item">
                <div class="container">

                    <div class="row b-header__order">
                        <div class="col-lg-9 col-md-9 col-sm-9 b-order-item__header-marg">

                            <h2 class="b-order-list__title"><?= $model->name; ?></h2>


                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3">
                    <span class="dataFromOrderView">
                    <?php
                    echo date('d ' . $months[date('n', $model->updated_at)] . ' Y', $model->updated_at);
                    echo " г.";
                    ?>
                    </span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="showContactsBlock">
                            <div class="imgHolder"></div>
                            <div class="linkHolder">
                                <a class="showContacts" data-id="<?= $model->id; ?>">
                                    <?php

                                    /** перенести в котроллер */
                                    $str = 'Откликнуться на заказ';
                                    $strF = 'Открыть контакты';

                                    $item = null;

                                    $auth = isset(Yii::$app->user->identity->id) ? Auth::findOne(['id' => Yii::$app->user->identity->id]) : false;
                                    $item['statusFeadBack'] = (isset($auth->user_id)) ? (($auth) ? Site::canUserGetContactThisOrder($model->id, $auth->user_id, $auth) : null) : null;

                                    echo ($item['statusFeadBack']) ? ($strF) : ($str);

                                    ?>
                                </a>
                            </div>
                        </div>

                        <div class="hiddenText" data-id="<?= $model->id ?>" style="display: none">
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
                                            if (($user->status_id === 2) && ($user->is_visible === 1)) { ?>

                                                <p><strong><?= $model->fio; ?></strong></p>
                                                <p>
                                                    <?php

                                                    foreach ($model->phones as $phone) {
                                                        echo $phone['number'] . ', ';
                                                    }

                                                    ?>
                                                </p>
                                                <p><a href="mailto:<?= $model->email; ?>"><?= $model->email; ?></a></p>

                                            <?php } ?>

                                        <?php } ?>

                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="roww">
                        <strong class="col-lg-3 col-md-3 col-sm-3 noPadding">Бюджет:</strong>

                        <div class="col-lg-9 col-md-9 col-sm-9 b-marg-bot">
                            <?php
                            if ($model->price > 0) {
                                echo " " . $model->price;
                                echo " руб.";
                            } else {
                                echo " По договоренности";
                            }
                            ?>
                        </div>
                    </div>

                    <?php
                    $professions = $model->getRelatedTitleFrom('professions');
                    if (!empty($professions)) { ?>
                        <div class="roww">
                            <strong class="col-lg-3 col-md-3 col-sm-3 noPadding">Профессиональная область:</strong>

                            <div class="col-lg-9 col-md-9 col-sm-9 b-marg-bot">
                                <?php

                                foreach ($professions as $profession) {
                                    echo "<span class='itemForOval'>";
                                    echo $profession['title'];
                                    echo "</span>";

                                }
                                ?>
                            </div>
                        </div>
                    <?php } ?>

                    <?php
                    $normBases = $model->getRelatedTitleFrom('normBases');
                    if (!empty($normBases)) {
                        ?>
                        <div class="roww">
                            <strong class="col-lg-3 col-md-3 col-sm-3 noPadding">Нормативные базы:</strong>
                            <div class="col-lg-9 col-md-9 col-sm-9 b-marg-bot">
                                <?php

                                foreach ($normBases as $normBase) {
                                    echo "<span class='itemForOval'>";
                                    echo $normBase['title'];
                                    echo "</span>";

                                }
                                ?>
                            </div>
                        </div>
                    <?php } ?>

                    <?php
                    $smetaDocs = $model->getRelatedTitleFrom('smetaDocs');
                    if (!empty($smetaDocs)) {
                        ?>
                        <div class="roww">
                            <strong class="col-lg-3 col-md-3 col-sm-3 noPadding">Сметная документация:</strong>
                            <div class="col-lg-9 col-md-9 col-sm-9 b-marg-bot">
                                <?php

                                foreach ($smetaDocs as $smetaDoc) {
                                    echo "<span class='itemForOval'>";
                                    echo $smetaDoc['title'];
                                    echo "</span>";

                                }
                                ?>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="roww">
                        <strong class="col-lg-3 col-md-3 col-sm-3 noPadding">Содержание заказа:</strong>
                        <div class="col-lg-9 col-md-9 col-sm-9 b-marg-bot">
                            <?= $model->text ?>
                        </div>
                    </div>


                </div>
            </div>


        <?php } ?>
    </div>
    <div class="text-center b-order-item__all-orders">
        <a href="<?= Url::to(['order/index']); ?>">Посмотреть другие заказы</a>
    </div>

</div>

