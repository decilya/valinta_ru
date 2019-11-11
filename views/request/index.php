<?php

use app\assets\BootboxAsset;
use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = 'Заявки';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile('@web/js/adminSearch.js', [
    'depends' => 'yii\web\JqueryAsset'
]);

BootboxAsset::register($this);

?>
    <script>
        "use strict";

        var requestIndex = {
            init: function init() {
                var buttonsEdit = $('.requestCommentEdit');
                var buttonsSave = $('.requestCommentSave');
                var buttonsCancel = $('.requestCommentCancel');
                var buttonsChangeStatus = $('.changeRequestStatus');
                var btnStill = $('.btn_still');
                buttonsEdit.on('click', this.editEvent);
                buttonsSave.on('click', this.saveEvent);
                buttonsCancel.on('click', this.cancelEvent);
                buttonsChangeStatus.on('click', this.changeStatusEvent);
                this.addStill();
                this.modalCloseWrap();
            },
            modalCloseWrap: function modalCloseWrap() {
                $(".modal_wrap").click(function () {
                    $(".modal_wrap").hide();
                    $(".still_modal").hide();
                });
            },
            addStill: function addStill() {
                $(".text_bases").each(function (index) {
                    if ($(this).text().length > 285) {
                        var numb = $(this).attr('data-num');
                        var r = Math.random().toString(36).substring(7);
                        $(this).parent().append('<button class="btn_st btn_still__' + r + '">еще...</button>');
                        $('.btn_still__' + r).on('click', function () {
                            requestIndex.stillEvent($('.btn_still__' + r));
                        });

                        if (!$(this).parent().is(".still_modal")) {
                            $(this).parent().append('<div class="still_modal"><button class="still_modal_close"><strong>X</strong></button><h3 style="text-align: center;">Заказанные базы заявки №' + numb + '</h3><div class="still_modal_text"></div> </div>');
                        }

                        $('.still_modal_close').on('click', function () {
                            //$(this).parent().hide();
                            $('.still_modal').hide();
                            $(".modal_wrap").hide();
                        });
                    }
                });
            },
            stillEvent: function stillEvent(button) {
                $(".modal_wrap").show();
                $(button).parent().children(".still_modal").show();
                var txt = $(button).parent().children('.text_bases').text();
                $(button).parent().children(".still_modal").children('.still_modal_text').text(txt);
            },
            editEvent: function editEvent() {
                $(this).siblings('button').show();
                $(this).hide(); //$(this).add($(this).siblings('button')).toggleClass('hidden');

                $(this).parent().siblings('textarea').removeAttr('disabled');
            },
            saveEvent: function saveEvent(event) {
                event.preventDefault();
                var id = $(this).attr('data-id');
                var textarea = $(this).parent().siblings('textarea');
                var buttons = $(this).add($(this).siblings('button'));
                $.ajax({
                    url: window.location.protocol + '//' + location.host + '/request/comment-save/' + id,
                    method: 'post',
                    data: JSON.stringify(textarea.val()),
                    dataType: 'text',
                    success: function success(data) {
                        if (data) {
                            textarea.attr('disabled', 'disabled');
                            buttons.show();
                            buttons.parent().find('.requestCommentSave').hide();
                            buttons.parent().find('.requestCommentCancel').hide();
                        }
                    }
                });
            },
            cancelEvent: function cancelEvent(event) {
                event.preventDefault();
                var id = $(this).attr('data-id');
                var textarea = $(this).parent().siblings('textarea');
                var buttons = $(this).add($(this).siblings('button'));
                $.ajax({
                    url: window.location.protocol + '//' + location.host + '/request/comment-cancel/' + id,
                    method: 'post',
                    dataType: 'text',
                    success: function success(data) {
                        textarea.val(data).attr('disabled', 'disabled');
                        buttons.parent().find('.requestCommentSave').hide();
                        buttons.parent().find('.requestCommentCancel').hide();
                        buttons.parent().find('.requestCommentEdit').show();
                    }
                });
            },
            changeStatusEvent: function changeStatusEvent(event) {
                event.preventDefault();

                var id = $(this).attr('data-id');
                var link = $(this);

                bootbox.dialog({
                    message: "Вы действительно хотите подтвердить заявку №" + $(this).attr('data-id') + "?",
                    buttons: {
                        success: {
                            label: "Подтвердить",
                            className: "btn-success",
                            callback: function callback(result) {
                                if (result) {
                                    $.ajax({
                                        method: 'post',
                                        url: window.location.protocol + '//' + location.host + '/request/confirm/' + id,
                                        dataType: 'text',
                                        success: function success(data) {
                                            if (data != 0) {
                                                link.parent().siblings('.itemStatus').find('span.text').text('обработана');
                                                link.remove();
                                                $('textarea[data-id="' + id + '"]').val(data);
                                            }
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
            }
        };
        $(document).ready(function () {
            function initRequestAdmin() {
                $('#searchText').on('change', function (event) {
                    sendSearchQuery();
                });
                $('#searchId').on('change', function (event) {
                    sendSearchQuery();
                });
                $('#searchStatusSelect').on('change', function (event) {
                    sendSearchQuery();
                });
            }

            requestIndex.init();
            initRequestAdmin();

            function sendSearchQuery() {
                
                var searchText = $('#searchText').val();
                var searchId = $('#searchId').val();
                var searchStatus = $('#searchStatusSelect').val();

                $.ajax({
                    url: '/request/index',
                    method: 'GET',
                    data: {
                        id: searchId,
                        text: searchText,
                        status: searchStatus
                    },
                    success: function success(data) {

                        $('#orderMainAdminList').find('.request-index').remove();
                        $('#orderMainAdminList').html($(data).find('.request-index'));

                        initRequestAdmin();

                        $(".text_bases").each(function (index) {
                            if ($(this).text().length > 285) {
                                var numb = $(this).attr('data-num');
                                var r = Math.random().toString(36).substring(7);
                                $(this).parent().append('<button class="btn_st btn_still__' + r + '">еще...</button>');
                                $('.btn_still__' + r).on('click', function () {
                                    requestIndex.stillEvent($('.btn_still__' + r));
                                });
                                $(this).parent().append("<div class='still_modal'><button class='still_modal_close'><strong>X</strong></button><h3 style='text-align: center;'>Заказанные базы заявки №' + numb + '</h3><div class='still_modal_text'></div> </div>");
                                $('.still_modal_close').on('click', function () {
                                    $(this).parent().hide();
                                    $(".modal_wrap").hide();
                                });
                            }
                        });

                        setTimeout(function () {
                            console.log('start');
                            requestIndex.init();
                        }, 500);
                    },
                    error: function error() {
                        bootbox.alert("Произошла ошибка");
                        initRequestAdmin();
                    }
                });
            }
        });
    </script>

    <div id="orderMainAdminList">
        <div data-controller="request" class="request-index">

            <h1><?= Html::encode($this->title) ?></h1>

            <div class="searchBlock">

                <?= $searchBlock ?>

            </div>

            <div class="resultsBlock" data-results-limit="<?= Yii::$app->params['itemsOnRequestIndexPage'] ?>">

                <?php if (!empty($model)) : ?>
                    <hr/>

                    <?php foreach ($model as $item) : ?>

                        <div class="itemBlock mrg_5_b" data-id="<?= $item['id'] ?>">

                            <div class="itemBlock__wrap--1">
                                <div class="itemId mrg_5_b"><strong>№<?= $item['id'] ?></strong></div>
                                <div class="itemPhone mrg_5_b" style="height: 35px">
                                    <strong>Тел: </strong><?= Html::encode(strip_tags(trim($item['phone']))) ?></div>
                                <div class="item mrg_5_b"><strong>ИНН: </strong><span
                                            class="text"><?= Html::encode($item['inn']); ?></span></div>

                            </div>
                            <div class="itemBlock__wrap--2">
                                <div class="itemDateCreated mrg_5_b">
                                    <strong>Оформлена: </strong><?= date('d.m.Y H:i:s', $item['date_created']) ?></div>
                                <div class="statusWrap mrg_5_b">
                                    <div class="itemStatus" style="height: 35px">
                                        <strong>Статус: </strong>
                                        <span class="text"><?= Yii::$app->params['requestStatus'][$item['status_value']] ?></span>
                                    </div>
                                    <?= (!empty($item['status_value'] == 1)) ? '<div class="itemStatusChangeLink"><button data-id="' . $item['id'] . '" class="changeRequestStatus btn-small btn-info">Обработать</button></div>' : '' ?>
                                </div>

                                <div class="accessWrap mrg_5_b">
                                    <div class="item">
                                        <strong>Доступ с: </strong>
                                        <span class="text"><?= date('d.m.Y', $item['desired_date']) ?></span>
                                    </div>
                                    <div class="item">
                                        <strong>Дней: </strong>
                                        <span class="text"><?= Html::encode($item['access_days']); ?></span>
                                    </div>
                                </div>

                            </div>
                            <div class="itemBlock__wrap--3 ">
                                <div class="itemFio mrg_5_b" style="overflow: hidden">
                                    <strong>ФИО: </strong><?= Html::encode(strip_tags(trim($item['fio']))) ?></div>
                                <div class="itemEmail mrg_5_b" style="height: 35px; overflow: hidden">
                                    <strong>E-mail: </strong><?= Html::encode(strip_tags(trim($item['email']))) ?>
                                </div>
                                <div class="item mrg_5_b">
                                    <strong>Стоимость: </strong>
                                    <span class="text"><?= Html::encode($item['cost']); ?>р.</span>
                                </div>

                            </div>
                            <div class="itemBlock__wrap--4">
                                <div class="itemComment">
                            <textarea data-id="<?= $item['id'] ?>"
                                      disabled><?= Html::encode(strip_tags(trim($item['comment']))) ?></textarea>
                                    <div class="itemComment_block">
                                        <button class="requestCommentEdit btn-small btn-warning">Редактировать</button>

                                        <button class="requestCommentSave btn-small btn-success "
                                                data-id="<?= $item['id'] ?>" hidden>
                                            Сохранить
                                        </button>
                                        <button class="requestCommentCancel btn-small btn-danger--adm"
                                                data-id="<?= $item['id'] ?>" hidden>
                                            Отмена
                                        </button>
                                    </div>


                                </div>
                            </div>

                        </div>
                        <div class="item">
                            <strong>Базы: </strong>
                            <span class="text_bases" data-num="<?= $item['id'] ?>">
                            <?php
                            $order = \app\models\Request::find()->with('databases')->where(['id' => $item['id']])->one();
                            ?>
                                <?php if (isset($order->databases)) { ?>
                                    <span><?php foreach ($order->databases as $database) {
                                            echo Html::encode($database->name);
                                            echo "; ";
                                        } ?></span>
                                <?php } ?>
                        </span>
                        </div>
                        <hr/>
                    <?php endforeach; ?>

                <?php endif; ?>


                <?php if (empty($model)) : ?>

                    <p>Ничего не найдено.</p>

                <?php endif; ?>

            </div>

            <div class="paginatorRow">
                <div class="paginator">
                    <?php
                    echo LinkPager::widget([
                        'pagination' => $paginator,
                        'lastPageLabel' => true,
                        'firstPageLabel' => true
                    ]);
                    ?>
                </div>
            </div>
            <div class="modal_wrap" style="display: none"></div>

        </div>
    </div>

<?php

$this->registerJsFile('@web/js/requestIndex.js', [
    'depends' => 'yii\web\JqueryAsset'
], 3);

