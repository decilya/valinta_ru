<?php
/**
 * @var \app\models\OrderFeadbackUser[] $orderFeadbackUser ;
 * @var string $urlToIpapAttestat
 */
$urlToIpapAttestat = Yii::$app->params['urlToIpapAttestat'];

foreach ($orderFeadbackUser as $item) {
    $item->nowIsVisited();

    if (!isset($item->user->fio)) throw new \yii\web\ForbiddenHttpException('Ошибка'); ?>

    <article class="orderFeadbackUserItem" data-id="<?= $item->id; ?>" data-user="<?= $item->user_id ?>">

        <header class="row mb18">
            <div class="col-lg-9 col-md-9 col-sm-9 noPadding">
                <div class="orderTitle">
                    <h2><?= $item->user->fio; ?></h2>
                </div>
            </div>

            <div class="col-lg-3 col-md-3 col-sm-3 noPadding">
                <div class="atestat">
                    <?php if ($item->user->ipap_attestat_id != null) { ?>
                        <span><b>Аттестат ИПАП: <?= $item->user->ipap_attestat_id; ?></b></span><br>
                        <a target="_blank"
                           href=<?= $urlToIpapAttestat; ?>?attId=<?= $item->user->ipap_attestat_id; ?>#registrySearchForm"
                           class="checkIpap">проверить подлинность
                        </a>
                    <?php } ?>
                </div>
            </div>
        </header>

        <section>
            <div class="row mb18">
                <div class="col-lg-5 col-md-5 col-sm-5 noPadding">
                    <label>Анкета обновлена:</label>
                    <span class="feedbackUpdt">
                    <?= \app\models\Site::getNormalRussianDateByTimeStamp($item->user->date_changed); ?>
                    </span>
                </div>

            </div>

            <div class="row mb18">
                <div class="col-lg-3 col-md-3 col-sm-3 noPadding">
                    <strong>Дата отклика:</strong>
                </div>
                <div class="col-lg-9 col-md-9 col-sm-9 noPadding b-order__left">
                   <span>
                    <?= \app\models\Site::getNormalRussianDateByTimeStamp($item->created_at); ?>
                  </span>
                </div>
            </div>

            <div class="row mb18" style="background: #f2f2f2;">
                <div class="col-lg-3 col-md-3 col-sm-3 noPadding">
                    <strong>Контакты: </strong>
                </div>
                <div class="col-lg-9 col-md-9 col-sm-9 noPadding b-order__left">
                    <span>
                        <?php foreach ($item->user->phones as $itemPhones) { ?>
                            <?php echo $itemPhones['number'] . ', '; ?>
                        <?php } ?> <?php if (!empty($item->user->phones)) { ?> <br/> <?php } ?>
                        <a href="mailto:<?= $item->user->email; ?>"><?= $item->user->email; ?></a>
                     </span>
                </div>
            </div>

            <div class="row mb18">
                <div class="col-lg-3 col-md-3 col-sm-3 noPadding">
                    <strong>Стоимость работ: </strong>
                </div>
                <div class="col-lg-9 col-md-9 col-sm-9 noPadding b-order__left">
                  <span class="price">
                    <?php
                    if ($item->user->price > 0) {
                        echo " от ";
                        echo $item->user->price;
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
                <div class="col-lg-9 col-md-9 col-sm-9 noPadding b-order__left">
                    <div class="dotText ajax oneString">
                  <span class="experience">
                      <?= $item->user->experience; ?>
                  </span>
                    </div>
                </div>
            </div>

            <div class="row mb18">
                <div class="col-lg-3 col-md-3 col-sm-3 noPadding">
                    <strong>Соответствие запросу: </strong>
                </div>
                <div class="col-lg-9 col-md-9 col-sm-9 noPadding b-order__left">
                       <span class="percent
                       <?= \app\models\User::determinePercentColor($percent = \app\models\Site::calcPercentUserByOrder($item->user, $item->order)) ?>">
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
                    foreach ($item->user->professionsNorm as $profession) {
                        echo "<span ";

                        if ($item->order->userPropertyInOrder($profession->title, \app\models\Order::PROPERTY_PROFESSIONS)) {
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
                       foreach ($item->user->normbases as $normbase) {
                           echo "<span ";

                           if ($item->order->userPropertyInOrder($normbase->title, \app\models\Order::PROPERTY_NORM_BASES)) {
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
                       foreach ($item->user->smetadocs as $smetaDoc) {
                           echo "<span ";

                           if ($item->order->userPropertyInOrder($smetaDoc->title, \app\models\Order::PROPERTY_SMETA_DOCS)) {
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
                       <?= $item->user->city->name ?>
                  </span>
                </div>
            </div>
        </section>
        <hr/>
    </article>
<?php } ?>

