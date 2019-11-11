"use strict";

function CustomerOrdersController() {
    var myObj = this;
    this.pageParams = null;

    this.init = function () {
        // работа меню личного кабинета
        this.cabinetNavLinks(); // показать еще

        this.addItemsEvent(); // читать полностью

        this.readMore(); // закрыть заказ

        this.closeOrder(); // после перехода по "Вернутся к списку" со страницы "Отклики на заказ"

        this.scrollToOrder();
        this.changePasswordSendLinkFor();
        this.sortingUser();
        this.pageParams = window.location.search.replace('?', '').split('&').reduce(function (p, e) {
            var a = e.split('=');
            p[decodeURIComponent(a[0])] = decodeURIComponent(a[1]);
            return p;
        }, {});
    };

    /**  работа меню личного кабинета */
    this.cabinetNavLinks = function () {
        $(document).ready(function () {
            $("header#cabinet li").on('click', function () {
                var link = $(this).children().first('a').attr('href');
                console.log(link);
                window.document.location.href = link;
            });
        });
    };

    /** Показать еще */
    this.addItemsEvent = function () {
        /** Только для страниц __customerCabinet__ | _Текущие заказы_  и __customerCabinetArchive__ | _Архив заказов_ */
        if ($('main').is('#customerCabinet') || $('main').is('#customerCabinetArchive')) {
            var url = $('main').is('#customerCabinet') ? "/customer/order-list" : "/customer/order-archive-list";
            $('#add').on('click', function () {
                var lastOrderId = $('article:last').data('id');
                var countOrders = $('#allOrdersCount').val();
                var countOrdersLasts = $('#countOrdersLasts');
                var addBlock = $('#add');
                var lastDataFinish = $('article:last').data('finishe');
                $.ajax({
                    method: 'POST',
                    url: url,
                    data: {
                        lastOrderId: lastOrderId,
                        countOrdersLasts: $.trim(countOrdersLasts.text()),
                        countOrders: countOrders,
                        lastDataFinish: lastDataFinish
                    },
                    success: function success(data) {
                        $('#orderItems').append(data);
                        var nowOrderItemsCount = $('article.orderItem').length;
                        $('#ordersInList').html(nowOrderItemsCount);
                        var countFooter = countOrders - nowOrderItemsCount;

                        if (countFooter <= 0) {
                            addBlock.hide();
                            countOrdersLasts.hide();
                        } else if (countFooter > 0 && countFooter < 7) {
                            addBlock.show();
                            countOrdersLasts.show();
                            countOrdersLasts.html('' + countFooter);
                        } else {
                            addBlock.show();
                            countOrdersLasts.show();
                            countOrdersLasts.html("7");
                        }

                        myObj.readMore();
                    }
                });
            });
        }

        /** Только для customerCabinetRecruitment | Подбор исполнителя заказа */
        if ($('main').is('#customerCabinetRecruitment')) {
            $('#add').on('click', function () {
                var lastUserId = Number($('article:last').data('id'));
                var countUsers = Number($('#allUsersCount').val());
                var countFeadbackLasts = $('#countUsersLasts');
                var addBlock = $('#add');
                $.ajax({
                    method: 'GET',
                    url: window.document.location.href,
                    data: {
                        id: Number($('#myOrderId').val()),
                        lastUserId: lastUserId,
                        countItem: $('article.userItem').length,
                        // countFeadbackLasts: countFeadbackLasts
                        pageParams: myObj.pageParams
                    },
                    success: function success(data) {
                        $('#recruitmentItems').append(data);
                        var nowUserItemsCount = $('article.userItem').length;
                        $('#userInList').html(nowUserItemsCount);
                        var countFooter = countUsers - nowUserItemsCount;

                        if (countFooter <= 0) {
                            addBlock.hide();
                            countFeadbackLasts.hide();
                        } else if (countFooter > 0 && countFooter < 7) {
                            addBlock.show();
                            countFeadbackLasts.show();
                            countFeadbackLasts.html('' + countFooter);
                        } else {
                            addBlock.show();
                            countFeadbackLasts.show();
                            countFeadbackLasts.html("7");
                        }

                        myObj.readMore();
                        searchFilters.init();
                    }
                });
            });
            /** Только для customerCabinetFeadback | Отклики на заказ */

            if ($('main').is('#customerCabinetFeadback')) {
                alert(777);
                $('#add').on('click', function () {
                    var lastFeadbackId = Number($('article:last').data('id'));
                    var countFeadback = Number($('#allFeadbackCount').val());
                    var countFeadbackLasts = $('#countFeadbackLasts');
                    var addBlock = $('#add');
                    $.ajax({
                        method: 'GET',
                        url: '/customer/customer-order-feedback',
                        data: {
                            id: Number($('#myOrderId').val()),
                            lastFeadbackId: lastFeadbackId
                        },
                        success: function success(data) {
                            $('#feadbackItems').append(data);
                            var nowOrderItemsCount = $('article.orderFeadbackUserItem').length;
                            $('#feadbackInList').html(nowOrderItemsCount);
                            var countFooter = countFeadback - nowOrderItemsCount;

                            if (countFooter <= 0) {
                                addBlock.hide();
                                countFeadbackLasts.hide();
                            } else if (countFooter > 0 && countFooter < 7) {
                                addBlock.show();
                                countFeadbackLasts.show();
                                countFeadbackLasts.html('' + countFooter);
                            } else {
                                addBlock.show();
                                countFeadbackLasts.show();
                                countFeadbackLasts.html("7");
                            }

                            myObj.readMore();
                        }
                    });
                });
            } // Только для customerCabinetFeadback | Отклики на заказ

        }
    };

    /** читай меня полностью */
    this.readMore = function () {
        var exp = $('.dotText.ajax');
        exp.each(function () {
            if ($(this).innerHeight() > 63) {
                $(this).css({
                    overflow: 'hidden',
                    'padding-right': '1%'
                });

                if ($(this).hasClass("oneString")) {
                    $(this).css({
                        height: '22px'
                    });
                } else {
                    $(this).css({
                        height: '22px'
                    });
                }

                $(this).parent().append('<div class="showMoreHolder"><a class="showMore">...читать полностью</a></div>');
            } else {
                $(this).css({
                    'visibility': 'visible'
                });
            }

            if ($(this).hasClass('ajax')) $(this).removeClass('ajax');
        });
        $('.showMore').on('click', function () {
            $(this).parent().parent().children('.dotText').css({
                'height': 'inherit',
                overflow: 'inherit',
                'padding-right': '0'
            });
            $(this).parent().remove();
        });
    };

    /** закрыть заказ*/
    this.closeOrder = function () {
        $('.modalFormCloseOrder').on('click', function () {
            var orderId = $(this).attr('data-id');
            $('#closeOrder').attr('data-id', orderId);
        });
        $("#closeOrder").click(function () {
            var orderId = $(this).attr('data-id');
            var closingReason = $('input[name=answer]:checked').val();
            $.ajax({
                method: "POST",
                url: "/order/close-order-by-customer",
                data: {
                    orderId: orderId,
                    closingReason: closingReason
                },
                dataType: 'json',
                success: function success(data) {
                    $('#ordersInList').text(Number($('#ordersInList').text()) - 1);
                    $('#ordersCount').text(Number($('#ordersCount').text()) - 1);
                    $('#orderIdForAlertClose').html(orderId);
                    $('#myModal .close').click();
                    $('.orderItem[data-id=' + orderId + ']').hide();
                    $('#closeSuccess').show(function () {
                        setTimeout(function () {
                            $('#closeSuccess').hide();
                        }, 5000);
                    });

                    if (!$('#orderItems article.orderItem').is(':visible')) {
                        if (!$('#add').is(':visible')) {
                            if (!$('p').is('#theEnd')) {
                                $('#orderItems').append('<p id="theEnd">На текущий момент у Вас нет действующих заказов.</p>');
                            }
                        }
                    }
                },
                error: function error() {
                    bootbox.alert("При закрытии заказа произошла критическая ошибка.");
                }
            });
        });
    };

    /** Сдвинуть позицию после перехода по "Вернутся к списку" со страницы "Отклики на заказ" и "Подобрать" */
    this.scrollToOrder = function () {
        /** Только для customerCabinet | Текущие заказы */
        if ($('main').is('#customerCabinet')) {
            var goToItem = $('#goToItem').val();

            if (goToItem && Number(goToItem) !== 0) {
                goToItem = Number(goToItem);
                var myEl = $("article.orderItem[data-id='" + goToItem + "']");

                if ($('article').is(".orderItem[data-id='" + goToItem + "']")) {
                    var destination = myEl.offset().top;
                    $('html,body').animate({
                        scrollTop: destination
                    }, 500);
                }
            }
        }
    };

    /** Отправить ссылку на восстановление пароля  */
    this.changePasswordSendLinkFor = function () {
        $('#sendCustomerInstructions').on('click', function (event) {
            var id = $(this).data('id');
            $.ajax({
                url: '/customer/send-customer-instructions',
                method: 'POST',
                data: {
                    id: id
                },
                dataType: 'json',
                success: function success(data) {
                    console.log(data);
                    var messageBlock = $('.messageBlock');
                    messageBlock.html('');
                    messageBlock.append('<div id="w0" class="alert alert-' + data.status + ' fade in" ><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' + data.body + '</div>');
                },
                error: function error() {
                    bootbox.alert('Произошла ошибка');
                }
            });
        });
    };

    /** Сортировка на странице "Подбора исполнителя заказа" */
    this.sortingUser = function () {
        /** Только для customerCabinetRecruitment | Подбор исполнителя заказа */
        if ($('main').is('#customerCabinetRecruitment')) {
            $('.sortingArrow').on('click', function () {
                var filter = $(this).attr('data-results-sort-filter');
                var direction = $(this).attr('data-results-sort-direction');
                var newStr = $('#originUrl').attr('data-url');
                newStr = newStr + '?filter=' + filter + '&direction=' + direction;
                window.document.location.href = newStr;
            });
        }
    };
}

/** ready */
$(function () {
    var myCustomerOrdersController = new CustomerOrdersController();
    myCustomerOrdersController.init();
});
