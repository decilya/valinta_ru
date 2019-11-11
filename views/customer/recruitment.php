<?php
/**
 * @var \app\models\Order $order
 * @var \app\models\User[] $users
 * @var int $countUsers
 * @var \app\models\Customer $customer
 * @var array $sortParams
 * @var array $orderFeadbackUserForThisCustomerArr
 *
 */

$this->registerJsFile('@web/js/customerOrdersController.js', ['depends' => 'yii\web\JqueryAsset']);

$this->registerJsFile('@web/js/userSearchFilters.js', ['depends' => 'yii\web\JqueryAsset']);

use yii\helpers\Url; ?>

<?= $this->render('blocks/_customerCabinetNav', [
    'customer' => $customer,
    'switchCabinetNav' => 1
]); ?>

<main id="customerCabinetRecruitment" class="container">

    <section id="additionallyVariables" hidden="hidden">
        <input id="allUsersCount" value="<?= $countUsers; ?>" hidden="hidden">
        <input id="myOrderId" value="<?= $order->id; ?>" hidden="hidden">
    </section>

    <div id="infoBlock"></div>
    <section id="currentOrderRecruitment">

        <article class="orderItem" data-id="<?= $order->id; ?>">

            <input id="originUrl"
                   data-url="<?= "//" . $_SERVER["HTTP_HOST"] . Url::to(['customer/recruitment', 'id' => $order->id]); ?>"
                   hidden>

            <div class="row margRecruit">

                <div class="col-lg-3 col-md-3 col-sm-3 noPadding">
                    <div class="order-menu">
                        <ul>
                            <li class="backToOrderList">
                                <a href="/customer/order-list/?goToItem=<?= $order->id; ?>">Вернутся к списку</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-7 col-md-7 col-sm-7 noPadding">
                    <div class="feedbackTitle">
                        <h2>Подбор исполнителя заказа</h2>
                    </div>
                </div>
            </div>

            <div class="row headerSelectText">
                <span>ВАШ ЗАКАЗ:</span>
            </div>
            <hr>
            <section class="row">
                <header class="mb18">
                    <div class="col-lg-9 col-md-9 col-sm-9 noPadding">
                        <div class="orderTitle">
                            <h2>Заказ №<?= $order->id; ?> - <?= $order->name; ?></h2>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-3 col-sm-3 noPadding">
                        <div class="orderDate">
                            <div class="orderDate--bl">
                                <h4><?= \app\models\Site::getNormalRussianDateByTimeStamp($order->updated_at); ?></h4>
                            </div>
                        </div>
                    </div>
                </header>

                <div class="row ">
                    <?php if (isset($order->price)) { ?>
                        <div class="row mb18 mt30">
                            <div class="col-lg-3 col-md-3 col-sm-3 noPadding">
                                <label><strong>Бюджет:</strong></label>
                            </div>
                            <div class="col-lg-9 col-md-9 col-sm-9 noPadding b-order__left">
                            <span class="price">
                                <?php
                                if ($order->price > 0) {
                                    echo $order->price;
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
                    $professions = $order->getRelatedTitleFrom('professions');
                    if (!empty($professions)) { ?>
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
                        <div class="row">
                            <div class="mainTextBlock col-lg-3 col-md-3 col-sm-3 noPadding">
                                <strong style="margin-left: 0;">Содержание заказа: </strong>
                            </div>
                            <div class="col-lg-9 col-md-9 col-sm-9 noPadding dotText ajax VvalueRow Eexperience b-text-order noPadding b-order__left"
                                 style="overflow: hidden;">
                                <?= $order->text; ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </section>
            <hr/>
            <section class="recruitment">
                <div class="row headerSelectText">
                    ВАМ ПОДХОДЯТ:
                </div>
                <div class="row blockNav">


                    <div class="filterRow">

                        <div class="container">

                            <div class="filter col-md-4 col-lg-4 col-sm-4 noPadding" data-role="dateFilter">
                                <label>Сортировать по дате обновления: </label>
                                <span class="sortingArrow<?php if ($sortParams['filter'] == 'date' && $sortParams['direction'] == 'asc') echo ' active' ?>"
                                      data-results-sort-filter="date" data-results-sort-direction="asc">&#11014;</span>
                                <span class="sortingArrow<?php if ($sortParams['filter'] == 'date' && $sortParams['direction'] == 'desc') echo ' active' ?>"
                                      data-results-sort-filter="date" data-results-sort-direction="desc">&#11015;</span>
                            </div>

                            <div class="filter col-md-4 col-lg-4 col-sm-4 noPadding" data-role="priceFilter">
                                <label>Сортировать по стоимости: </label>
                                <span class="sortingArrow<?php if ($sortParams['filter'] == 'price' && $sortParams['direction'] == 'asc') echo ' active' ?>"
                                      data-results-sort-filter="price" data-results-sort-direction="asc">&#11014;</span>
                                <span class="sortingArrow<?php if ($sortParams['filter'] == 'price' && $sortParams['direction'] == 'desc') echo ' active' ?>"
                                      data-results-sort-filter="price"
                                      data-results-sort-direction="desc">&#11015;</span>
                            </div>
                        </div>
                    </div>

                </div>
                <div id="recruitmentItems">
                    <?= $this->render('blocks/_recruitmentItems', [
                        'users' => $users,
                        'order' => $order,
                        'orderFeadbackUserForThisCustomerArr' => $orderFeadbackUserForThisCustomerArr
                    ]); ?>
                </div>
            </section>

            <div class="row text-center">
                <p>Показано сметичков: <span id="userInList"><?php if ($countUsers < 7) {
                            echo $countUsers;
                        } else { ?> 7 <?php } ?> </span> из <span
                            id="countUsers"><?= $countUsers; ?></span></p>
                <?php
                $count = $countUsers - 7;

                if ($count > 0) { ?>
                    <a id="add">Показать еще
                        <span id="countUsersLasts">
                        <?php
                        if ($count >= 7) { ?>7<?php } elseif ($count > 0) {
                            echo $count;
                        } ?>
                        </span>
                    </a>
                <?php } ?>
            </div>

        </article>
    </section>
</main>