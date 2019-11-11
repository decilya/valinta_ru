<?php
/**
 * Created by PhpStorm.
 * User: decilya
 * Date: 27.02.19
 * Time: 13:12
 *
 * @var  app\models\Order[] $orders ;
 *
 */

foreach ($orders

         as $order) { ?>
    <article class="orderItem" data-id="<?= $order->id; ?>" data-finishe="<?= $order->finished_at ?>">

        <header class="row">
            <div class="col-lg-9 col-md-9 col-sm-9 noPadding">
                <div class="orderTitle">
                    <h2>Заказ №<?= $order->id; ?> - <?= $order->name; ?></h2>
                </div>

                <div class="order-menu">
                    <ul>
                        <li class="addCustomerOrder">
                            <a href="/order/create/?cloneOrderId=<?= $order->id; ?>">Создать новый заказ по
                                шаблону</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-3 col-md-3 col-sm-3 noPadding">
                <div class="orderDate">
                    <div class="archiveDate--bl">
                        <p><span>Открыт:</span><?= \app\models\Site::getNormalRussianDateByTimeStamp($order->created_at); ?></p>
                        <p><span>Закрыт:</span><?= \app\models\Site::getNormalRussianDateByTimeStamp($order->finished_at) ?></p>
                    </div>
                </div>
            </div>
        </header>

        <section class="clear">

            <div class="row mb18  clear" style="background-color: #F2F2F2; padding: 9px 0;">
                <div class="col-lg-3 col-md-3 col-sm-3 noPadding">
                    <strong>Причина закрытия:</strong>
                </div>
                <div class="col-lg-9 col-md-9 col-sm-9 noPadding b-order__left">
                    <?php

                    if ($order->getShutterOrderIsCustomerOrUserOrManager() == \app\models\Auth::STRING_CUSTOMER) {
                        echo "Закрыт заказчиком: ";
                    } elseif ($order->closing_reason == \app\models\Order::CLOSING_REASON_ADMIN){
                        echo "Закрыт менеджером: ";
                    }

                    echo $order->closing_reason_text;

                    echo $order->getClosingReasonTextByReasonId();
                    ?>
                </div>
            </div>

            <div class="row mb18 clear">
                <div class="col-lg-3 col-md-3 col-sm-3 noPadding">
                    <strong>Бюджет: </strong>
                </div>
                <div class="col-lg-9 col-md-9 col-sm-9 noPadding b-order__left">
                    <?php
                    if ($order->price > 0) {
                        echo $order->price;
                        echo " руб.";
                    } else {
                        echo "По договоренности";
                    }
                    ?>
                </div>
            </div>

            <?php
            $professions = $order->getRelatedTitleFrom('professions');
            if (!empty($professions)) {
                ?>

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
                <div class="row mb18">
                    <div class="mainTextBlock col-lg-3 col-md-3 col-sm-3 noPadding">
                        <strong style="margin-left: 0;">Содержание заказа: </strong>
                    </div>

                    <div class="col-lg-9 col-md-9 col-sm-9 noPadding b-order__left dotText ajax VvalueRow Eexperience b-text-order noPadding" style="overflow: hidden">
                        <?= $order->text; ?>
                    </div>
                </div>
            <?php } ?>
        </section>

        <hr/>
    </article>
<?php } ?>

