function initOrderAdminList() {
    $('.myTool').tooltip();
    $('[data-toggle="tooltip"]').tooltip({html: true});

    /**
     * Нажали на кнопку подтвердить заказ
     */
    $('.approveBtn').on('click', function (event) {

        let thisLink = this;

        let orderId = $(this).data('id');

        bootbox.dialog({
            title: "Заказ №" + orderId,

            message: "Вы действительно хотите подтвердить правильность заполнения заказа?",

            buttons: {
                success: {
                    label: "Подтвердить",
                    className: "btn-success",
                    callback: function (result) {
                        $.ajax({
                            async: false,
                            url: '/order/approve',
                            method: 'POST',
                            data: {
                                orderId: orderId
                            },
                            success: function (data) {
                                if (data != true) {
                                    bootbox.alert("Произошла ошибка");
                                } else {
                                    $(".closeBtn[data-id=" + orderId + "]").toggle();

                                    let myDivForStatus = $(".statusOfMyOrder[data-id=" + orderId + "]");

                                    myDivForStatus.html("Статус: опубликован (подтверждён)");
                                    myDivForStatus.removeClass('status_public_not_check');
                                    myDivForStatus.addClass('status_public');

                                    $(".approveBtn[data-id=" + orderId + "]").hide();
                                    $(".editLink[data-id=" + orderId + "]").show();

                                    $('.emailAdminListBlock span:contains("(Не подтвержден)")').remove();
                                }

                            },
                            error: function () {
                                location.reload();
                            }
                        });
                    }
                },
                danger: {
                    label: "Отмена",
                    className: "btn-danger--adm"
                }
            }
        });
    });

    /**
     * Нажали на кнопку закрыть заказ
     */
    $('.closeBtn').on('click', function (event) {

        let orderId = $(this).data('id');

        bootbox.dialog({
            title: "Укажите причину закрытия заказа №" + orderId + " и нажмите &laquo;Закрыть&raquo;!",
            message: "<textarea id='reason'></textarea><div id=\"errorDivBlock\"></div>",
            buttons: {
                success: {
                    label: "Закрыть",
                    className: "btn-success",
                    callback: function (result) {

                        var reason = $('#reason').val();

                        if (reason.length < 4) {
                            $('#errorDivBlock').html('<p style=\"color: red\">Причина закрытия не может быть выражена менее чем в 4х символах</p>');

                            return false;
                        }  else {
                            $('#errorDivBlock').html("");
                        }

                        $.ajax({
                            async: false,
                            url: '/order/close',
                            method: 'POST',
                            data: {
                                orderId: orderId,
                                reason: reason
                            },
                            success: function (data) {
                                if (data != true) {
                                    bootbox.alert("Произошла ошибка");
                                } else {
                                    $(".closeBtn[data-id=" + orderId + "]").toggle();

                                    let myDivForStatus = $(".statusOfMyOrder[data-id=" + orderId + "]");
                                    let myReason = reason.substring(0, 50);

                                    myDivForStatus.html("Статус: закрыт (закрыто админом: <span class='myTool' title='" + reason + "'>" + myReason + "</span>)");
                                    myDivForStatus.addClass('status_close');
                                    myDivForStatus.removeClass('status_public');
                                    myDivForStatus.removeClass('status_public_not_check');

                                    $(".approveBtn[data-id=" + orderId + "]").hide();
                                    $(".editLink[data-id=" + orderId + "]").hide();

                                    $('.bootbox-close-button').click();
                                }
                            },
                            error: function () {
                                bootbox.alert("Произошла ошибка");
                            }

                        });
                    }
                },
                danger: {
                    label: "Отмена",
                    className: "btn-danger--adm"
                }
            }
        });
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

    function sendSearchQuery() {

        let searchText = $('#searchText').val();
        let searchId = $('#searchId').val();
        let searchStatus = $('#searchStatus').val();

        $.ajax({
            url: '/order/admin-list',
            method: 'GET',
            data: {
                searchId: searchId,
                searchText: searchText,
                searchStatus: searchStatus
            },
            success: function (data) {
                $('#orderMainAdminList').find('#orderAdminList').remove();
                $('#orderMainAdminList').append(data);
                initOrderAdminList();
            },
            error: function () {
                bootbox.alert("Произошла ошибка");
            }
        });
    }
}

$(document).ready(function () {
    initOrderAdminList();
});