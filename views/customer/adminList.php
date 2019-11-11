<?php

use app\assets\BootboxAsset; // js -  bootbox.alert("yo! yo! yo!");
use app\models\Customer;
use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = 'Заказчики';
$this->params['breadcrumbs'][] = $this->title;

BootboxAsset::register($this);

/**
 * @var array $statusArr
 * @var \yii\data\Pagination $pagination
 * @var \yii\data\ActiveDataProvider $dataProvider
 */
$this->registerJsFile('@web/js/adminSearch.js', [
    'depends' => 'yii\web\JqueryAsset'
]);
?>
<style>
    .dopPhones {
        color: #FF0000;
    }

    .container {
        padding-top: 0 !important;
    }
</style>
<script type="text/javascript">

    $(document).ready(function () {

        function adminListInit() {

            $(function () {
                // стилизуем title у элементов
                $('.myTool').tooltip();
                $('[data-toggle="tooltip"]').tooltip({html: true});
            });

            $('#searchText').on('change', function (event) {
                sendSearchQuery();
            });

            $('#searchId').on('change', function (event) {
                sendSearchQuery();
            });

            $('#searchStatus').on('change', function (event) {
                sendSearchQuery();
            });

            $('#sortSelect').on('change', function (event) {
                sendSearchQuery();
            });

            $('#orderAdminList .itemAcceptLink').on('click', function (event) {

                let link = $(this);
                let id = $(this).data('id');
                let realId = $(this).data('real');

                bootbox.dialog({
                    message: "Вы действительно хотите подтвердить правильность заполнения профиля заказчика?",

                    buttons: {
                        success: {
                            label: "Подтвердить",
                            className: "btn-success",
                            callback: function (result) {
                                if (result) {

                                    $.ajax({
                                        url: '/customer/admin-customer-accept',
                                        method: 'GET',
                                        data: {
                                            id: id
                                        },
                                        success: function () {
                                            link.hide();
                                            let statusLink = link.parent().parent().find('.firstRow').find('.status_1');

                                            if (!statusLink.length > 0) {
                                                statusLink = link.parent().parent().find('.firstRow').find('.status_3');
                                            }

                                            statusLink.removeClass('status_1');
                                            statusLink.removeClass('status_3');
                                            statusLink.addClass('status_2');
                                            let textTmp = "№" + realId + " - Подтвержден";
                                            statusLink.text(textTmp);

                                            link.parent().parent().find('.itemRejectLink').show();
                                        },
                                        error: function () {
                                            bootbox.alert("Произошла ошибка");
                                        }
                                    });

                                }
                            }
                        },

                        danger: {
                            label: "Отмена",
                            className: "btn-danger--adm"
                        }
                    }
                });
            }); // itemAcceptLink click

            /**
             * Отклонить Заказчика (интерфейс Админа - /customer/admin-customers-list)
             *
             * @author Ilya <ilya.v87v@gmail.com> a.k.a via a.k.a @decilya
             * @data 19.08.2019
             */
            $('#orderAdminList .itemRejectLink').on('click', function (event) {

                let link = $(this);
                let id = $(this).data('id');
                let realId = $(this).data('real');

                bootbox.dialog({
                    title: "Укажите причину отклонения профиля заказчика №" + realId + " и нажмите «Отклонить»",

                    message: "<textarea maxlength='1000' id='modalTextarea' required='required'></textarea><span class='textareaError'></span>",

                    buttons: {
                        success: {
                            label: "Отклонить",
                            className: "btn-success",
                            callback: function (result) {
                                if (result) {

                                    let textArea = $('#modalTextarea');
                                    let textAreaError = $('.textareaError');
                                    let msg = textArea.val();

                                    textAreaError.text('');

                                    let formIsValid = function () {
                                        return (msg !== '') && (msg.length > 3);
                                    };

                                    let showFormErrors = function () {

                                        textAreaError.text('Причина отклонения не может быть выражена менее чем в 4х символах');
                                        textAreaError.show();

                                        textArea.css({
                                            'border': '1px solid red'
                                        });

                                    };

                                    if (!formIsValid()) {
                                        showFormErrors();

                                        return false;
                                    } else {
                                        textAreaError.text('');

                                        $.ajax({
                                            url: '/customer/admin-customer-reject',
                                            method: 'GET',
                                            data: {
                                                id: id,
                                                msg: msg
                                            },
                                            success: function () {
                                                link.hide();

                                                let statusLink = link.parent().parent().find('.firstRow').find('.status_1');

                                                if (!statusLink.length > 0) {
                                                    statusLink = link.parent().parent().find('.firstRow').find('.status_2');
                                                }

                                                statusLink.removeClass('status_1');
                                                statusLink.removeClass('status_2');
                                                statusLink.addClass('status_3');
                                                let textTmp = "№" + realId + " - отклонён";
                                                statusLink.text(textTmp);

                                                link.parent().parent().find('.itemAcceptLink').show();
                                            },
                                            error: function () {
                                                bootbox.alert("Произошла ошибка");
                                            }
                                        });


                                    }


                                }
                            }
                        },

                        danger: {
                            label: "Отмена",
                            className: "btn-danger--adm"
                        }
                    }
                });

            }); // itemRejectLink click

        }

        adminListInit();

        function sendSearchQuery() {

            let searchText = $.trim($('#searchText').val());
            let searchId = $.trim($('#searchId').val());
            let searchStatus = $.trim($('#searchStatus').val());
            let sortSelect = $.trim($('#sortSelect').val());
            // var save = true;

            $.ajax({
                url: '/customer/admin-customers-list',
                method: 'GET',
                data: {
                    id: searchId,
                    text: searchText,
                    status: searchStatus,
                    sort: sortSelect
                },
                success: function (data) {
                    $('#orderAdminList').find('#customersList').remove();
                    $('#orderAdminList').html("<div id='customersList'></div>");
                    $('#customersList').html($(data).find('#MainAdminList'));

                    adminListInit();

                },
                error: function () {
                    bootbox.alert("Произошла ошибка");
                }

            });
        }


    });
</script>
<div id="MainAdminList">
    <div id="orderAdminList" data-controller="order" class="user-index">

        <div id="customersList">

            <div class="headerBlock">
                <h1><?= Html::encode($this->title) ?></h1>
            </div>

            <div id="searchBlock" class="roww">
                <div class="searchIdBlock" style="position: relative">
                    <span class="b-num">№</span>
                    <input id="searchId"
                           name="searchId"
                           type="text"
                           placeholder=""
                           value="<?php if (isset($search['id'])) {
                               if (!empty($search['id'])) {
                                   echo $search['id'];
                               }
                           } ?>"
                           style="max-width: 100%"
                    />
                </div>
                <div class="searchTextBlock">
                    <span>Текст</span>
                    <input name="searchText"
                           id="searchText"
                           value="<?php if (isset($search['text'])) {
                               if (!empty($search['text'])) {
                                   echo $search['text'];
                               }
                           } ?>"
                           type="text" placeholder="ФИО, E-mail, Телефон"/>
                </div>

                <div class="searchUserStatusBlock">
                    <span>Статус</span>
                    <select id="searchStatus" name="search['status']">
                        <option value="0" <?php if (isset($search['status'])) {
                            if ($search['status'] == 0) echo "selected";
                        } ?>>
                            Все
                        </option>
                        <option value="1" <?php if (isset($search['status'])) {
                            if ($search['status'] == 1) echo "selected";
                        } ?>>
                            Требует проверки
                        </option>

                        <option value="3" <?php if (isset($search['status'])) {
                            if ($search['status'] == 3) echo "selected";
                        } ?>>
                            Отклонён
                        </option>

                        <option value="2" <?php if (isset($search['status'])) {
                            if ($search['status'] == 2) echo "selected";
                        } ?>>
                            Подтверждён
                        </option>
                    </select>
                </div>

                <div class="sortBlockCustomer">
                    <label for="sortSelect">Сортировка по</label>
                    <select id="sortSelect" name="sortUser">
                        <option value="0" <?php if (($sortUser == null) && ($sortUser == 0)) { ?>selected <?php } ?>>
                            Статус + изменение
                        </option>
                        <option value="1" <?php if ($sortUser == 1) { ?>selected <?php } ?>>Номеру по убыванию</option>
                        <option value="2" <?php if ($sortUser == 2) { ?>selected <?php } ?>>Номеру по возрастанию
                        </option>
                    </select>
                </div>

            </div>

            <div class="resultsBlock" data-results-limit="">
                <hr/>
                <?php if (!empty($customers)) { ?>

                    <?php /** @var Customer $item */
                    foreach ($customers as $item) {
                        if ((!isset($item['real_id']) || $item['real_id'] == null)){ continue; }
                        ?>

                        <div id="item_<?= $item['id'] ?>" class="itemBlock" data-id="<?= $item['id'] ?>"
                             data-position="">

                            <div class="row firstRow">
                                <div class="itemId status_<?= $item['status_id'] ?>">

                                    №<?= $item->real_id . ' - ' . $statusArr[$item['status_id']]['realTitle'] ?>
                                </div>
                                <div class="itemChanged">
                                    <strong>Изменен: </strong><?= date('d.m.Y', $item['created_at']) ?>
                                </div>
                                <a class="itemEditLink"
                                   href="/customer/update/<?= $item['real_id'] ?>">Редактировать</a>
                            </div>

                            <div data-id="<?= $item['id']; ?>"
                                 class="col-lg-8 col-md-8 col-sm-8 statusOfMyOrder <?php if ($item['status_id'] == $item::STATUS_CONFIRMED['val']) {
                                     echo "status_public";
                                 } else if ($item['status_id'] == $item::STATUS_REQUIRES_VERIFICATION['val']) {
                                     echo "_not_check";
                                 } else {
                                     echo "status_close";
                                 }
                                 ?>">
                            </div>

                            <div class="row">
                                <div>
                                    <strong>E-mail:</strong> <?= Html::encode($item['email']) ?>
                                </div>

                                <div>
                                    <strong>Тел.:</strong> <?php

                                    if (isset($item->phones[0])) echo $item->phones[0]->phone;

                                    if (($countPhones = count($item->phones)) > 1) {
                                        $countPhones = --$countPhones;
                                        $str = '';
                                        foreach ($item->phones as $itemPhone) {
                                            $str .= $itemPhone->phone . ' ';
                                        }
                                        echo " <span class='dopPhones' title='$str'>(+$countPhones)</span>";
                                    }

                                    ?>
                                </div>

                                <div>
                                    <strong>ФИО:</strong> <?= Html::encode($item['name']); ?>
                                </div>
                            </div>

                            <div class="row sixthRow">

                                <a class="itemAcceptLink" data-id="<?= $item['id'] ?>"
                                   data-real="<?= $item['real_id'] ?>"
                                    <?php if (!(($item['status_id'] == Yii::$app->params['status']['pending']) || ($item['status_id'] == Yii::$app->params['status']['rejected']) || ($item['status_id'] === Customer::STATUS_REJECTED['val']))) { ?>
                                        hidden <?php } ?>
                                >Подтвердить</a>

                                <a class="itemRejectLink" data-id="<?= $item['id'] ?>"
                                   data-real="<?= $item['real_id'] ?>"
                                    <?php if (!(($item['status_id'] == Yii::$app->params['status']['pending']) || ($item['status_id'] == Yii::$app->params['status']['accepted']))) { ?>
                                        hidden <?php } ?> >Отклонить</a>

                            </div>
                        </div>

                        <hr/>
                    <?php } ?>

                <?php } else { ?>
                    <p>Ничего не найдено!</p>
                <?php } ?>
            </div>

            <div class="paginatorRow">
                <div class="paginator">
                    <?php
                    echo LinkPager::widget([
                        'pagination' => $dataProvider->pagination,
                        'lastPageLabel' => true,
                        'firstPageLabel' => true
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
</div>