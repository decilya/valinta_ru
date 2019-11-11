<?php
/**
 *
 * @var \app\models\User[] $users
 * @var \app\models\Order $order
 * @var string $urlToIpapAttestat
 * @var array $orderFeadbackUserForThisCustomerArr
 *
 */

use app\models\Order;
use app\models\User;
use yii\helpers\Url;

$urlToIpapAttestat = Yii::$app->params['urlToIpapAttestat'];

foreach ($users as $user) { ?>
    <article class="userItem" data-id="<?= $user->id; ?>">

        <div class="itemRow" data-id="<?= $user->id; ?>">

            <header class="row mb18">
                <div class="col-lg-9 col-md-9 col-sm-9 noPadding">
                    <div class="orderTitle">
                        <h2><?= $user->fio; ?></h2>
                    </div>
                </div>

                <div class="col-lg-3 col-md-3 col-sm-3 noPadding">
                    <div class="atestat">
                        <?php if ($user->ipap_attestat_id != null) { ?>
                            <span><b>Аттестат ИПАП: <?= $user->ipap_attestat_id; ?></b></span><br>
                            <a target="_blank"
                               href=<?= $urlToIpapAttestat; ?>?attId=<?= $user->ipap_attestat_id; ?>#registrySearchForm"
                               class="checkIpap">проверить подлинность
                            </a>
                        <?php } ?>
                    </div>
                </div>
            </header>

            <section>
                <div class="row mb18">
                    <div class="col-lg-3 col-md-3 col-sm-3 noPadding">
                        <label>Анкета обновлена:</label>
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-9 noPadding b-order__left">
                        <span>
                        <?= \app\models\Site::getNormalRussianDateByTimeStamp($user->date_changed); ?>
                        </span>
                    </div>
                </div>

                <!--Если сметчик не откликался на заказ кастомера, то отобразим кнопку  -->
                <?php if (!in_array($user->id, $orderFeadbackUserForThisCustomerArr)) { ?>
                    <div class="showContactsBlock">
                        <div class="imgHolder"></div>
                        <div class="linkHolder">
                            <a class="showContacts">
                                Открыть контакты
                            </a>
                        </div>
                    </div>

                    <!--ну а если сметчик все же откликался, то просто сразу выводим контакты  -->
                <?php } else { ?>

                    <div class="row mb18" style="background: #f2f2f2;">
                        <div class="col-lg-3 col-md-3 col-sm-3 noPadding">
                            <strong>Контакты: </strong>
                        </div>
                        <div class="col-lg-9 col-md-9 col-sm-9 noPadding b-order__left">
                    <span>
                        <?php foreach ($user->phones as $itemPhones) { ?>
                            <?php echo $itemPhones['number'] . ', '; ?>
                        <?php } ?> <?php if (!empty($user->phones)) { ?> <br/> <?php } ?>
                        <a href="mailto:<?= $user->email; ?>"><?= $user->email; ?></a>
                     </span>
                        </div>
                    </div>

                <?php } ?>


                <div class="row mb18">
                    <div class="col-lg-3 col-md-3 col-sm-3 noPadding">
                        <strong>Стоимость работ: </strong>
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-9 noPadding b-order__left">
                        <span class="price">
                        <?php
                        if ($user->price > 0) {
                            echo " от ";
                            echo $user->price;
                            echo " руб.";
                        } else {
                            echo "По договоренности";
                        }
                        ?>
                        </span>
                    </div>
                </div>

                <div class="row mb18">
                    <div class="col-lg-3 col-md-3 col-sm-3 noPadding">
                        <strong>Образование и опыт работы: </strong>
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-9 noPadding b-order__left" style="overflow: hidden;">
                        <div class="dotText ajax oneString">
                  <span class="experience">
                      <?= $user->experience; ?>
                  </span>
                        </div>
                    </div>
                </div>

                <div class="row mb18">
                    <div class="col-lg-3 col-md-3 col-sm-3 noPadding">
                        <strong>Соответствие запросу: </strong>
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-9 noPadding b-order__left">
                        <span class="percent <?= \app\models\User::determinePercentColor($percent = \app\models\Site::calcPercentUserByOrder($user, $order)) ?>">
                             <?= $percent ?>%
                       </span>
                    </div>
                </div>

                <div class="row mb18">
                    <div class="col-lg-3 col-md-3 col-sm-3 noPadding">
                        <strong>Профессиональная область: </strong>
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-9 noPadding b-order__left">
                        <?php
                        foreach ($user->professionsNorm as $profession) {
                            echo "<span ";

                            if ($order->userPropertyInOrder($profession->title, \app\models\Order::PROPERTY_PROFESSIONS)) {
                                echo " class='select' ";
                            }

                            echo ">";

                            echo $profession->title;
                            echo "</span>; ";
                        }
                        ?>
                    </div>
                </div>

                <div class="row mb18">
                    <div class="col-lg-3 col-md-3 col-sm-3 noPadding">
                        <strong>Нормативные базы: </strong>
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-9 noPadding b-order__left">
                   <span>
                       <?php
                       foreach ($user->normbases as $normbase) {
                           echo "<span ";

                           if ($order->userPropertyInOrder($normbase->title, \app\models\Order::PROPERTY_NORM_BASES)) {
                               echo " class='select' ";
                           }

                           echo ">";

                           echo $normbase->title;
                           echo "</span>; ";
                       }
                       ?>
                  </span>
                    </div>
                </div>

                <div class="row mb18">
                    <div class="col-lg-3 col-md-3 col-sm-3 noPadding">
                        <strong>Сметная документация: </strong>
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-9 noPadding b-order__left">
                   <span>
                       <?php
                       foreach ($user->smetadocs as $smetaDoc) {
                           echo "<span ";

                           if ($order->userPropertyInOrder($smetaDoc->title, \app\models\Order::PROPERTY_SMETA_DOCS)) {
                               echo " class='select' ";
                           }

                           echo ">";

                           echo $smetaDoc->title;
                           echo "</span>; ";
                       }
                       ?>
                  </span>
                    </div>
                </div>

                <div class="row mb18">
                    <div class="col-lg-3 col-md-3 col-sm-3 noPadding">
                        <strong>Город: </strong>
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-9 noPadding b-order__left">
                   <span class="city">
                       <?= $user->city->name ?>
                  </span>
                    </div>
                </div>
            </section>
            <hr/>
        </div>
    </article>
<?php } ?>
