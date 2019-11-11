let resultBlockController = {

        init: function () {
            this.showContactsEvent();
            this.showNextEvent();
            this.experienceValueCheck();
            this.setValOfCounter();
        },

        showContactsEvent: function () {
            $('.showContacts').on('click', function () {

                    const myThisLink = $(this);
                    const userTypeInput = $('#typeOfUser');

                    /** 0 - гость, 1 - сметчик, 2 - заказчик */
                    let typeOfUser = Number(userTypeInput.val());

                    let orderId = $(this).data('id');
                    $(".hiddenText[data-id='" + orderId + "']").show();
                    $(this).hide();

                    /** realId of user (authId)*/
                    let userId = userTypeInput.data('user_id');

                    if (typeOfUser === 1) {

                        // Подтвержена ли анкеты и скрыта ли она

                        $.ajax({
                                url: '/user/get-user',
                                method: 'POST',
                                dataType: 'json',
                                data: {
                                    authId: userId,
                                },
                                success: function (arr) {

                                    if ((arr['status_id'] === 2) && (arr['is_visible'] === 1)) {
                                        $.ajax({
                                            url: '/order/user-answer-order',
                                            method: 'POST',
                                            dataType: 'json',
                                            data: {
                                                userId: userId,
                                                orderId: orderId,
                                            },
                                            success: function (data) {

                                                if (data === "new") {

                                                    myThisLink.parent().append("<div class='row alert alert-success'><div class='col-lg-11 col-md-11 col-sm-11'>" +
                                                        "Заказчику отправлено уведомление о вашем отклике." +
                                                        "</div><div class='closeOrderCustomerFeedBack col-lg-1 col-md-1 col-sm-1' style='cursor: pointer;text-align: right;'>x</div></div>");

                                                }

                                                resultBlockController.closeOrderCustomerFeedBack();
                                            },
                                        });
                                    } else if ((arr['status_id'] === 2) && (arr['is_visible'] === 0)) {
                                        myThisLink.parent().append("<div class='row alert alert-warning' style='margin-top: 0;'><div class='col-lg-11 col-md-11 col-sm-11'>" +
                                            "Ваша анкета скрыта. Чтобы откликнуться на заказ, анкета должна быть опубликована." +
                                            "</div><div class='closeOrderCustomerFeedBack col-lg-1 col-md-1 col-sm-1' style='cursor: pointer'>x</div></div>");

                                        resultBlockController.closeOrderCustomerFeedBack();
                                    } else if (arr['status_id'] === 3) {
                                        myThisLink.parent().append("<div class='row alert alert-warning' style='margin-top: 0;'><div class='col-lg-11 col-md-11 col-sm-11'>" +
                                            "Ваша анкета отклонена. Отредактируйте анкету." +
                                            "</div><div class='closeOrderCustomerFeedBack col-lg-1 col-md-1 col-sm-1' style='cursor: pointer'>x</div></div>");

                                        resultBlockController.closeOrderCustomerFeedBack();

                                    } else {
                                        myThisLink.parent().append("<div class='row alert alert-warning' style='margin-top: 0;'><div class='col-lg-11 col-md-11 col-sm-11'>" +
                                            "Ваша анкета проверяется. Чтобы откликнуться на заказ, анкета должна быть подтверждена и опубликована." +
                                            "</div><div class='closeOrderCustomerFeedBack col-lg-1 col-md-1 col-sm-1' style='cursor: pointer'>x</div></div>");

                                        resultBlockController.closeOrderCustomerFeedBack();
                                    }


                                },
                            }
                        );

                    } // typeOfUser === 1


                    // заказчик, страница "Подбор исполнителя заказа"
                    if (typeOfUser === 2) {
                        /** Только для customerCabinetRecruitment | Подбор исполнителя заказа */
                        if ($('main').is('#customerCabinetRecruitment')) {


                        }
                    } // typeOfUser === 2


                }
            )
            ;
        },

        closeOrderCustomerFeedBack: function () {
            $('.closeOrderCustomerFeedBack').on('click', function () {
                $(this).parent().hide();
            });
        },

        showNextEvent: function () {
            $('#showNext').on('click', function () {

                let block = $('#showNext').attr('data-block');

                if (block == '0') {

                    $('#showNext').attr('data-block', '1');

                    let priceSort = $('.sortingPrice.active').data('sort');

                    let professionsFilter = [];
                    let normBasesFilter = [];
                    let smetaDocsFilter = [];

                    let positionOrderInResultArr = $('.itemRow').last().data('position');
                    positionOrderInResultArr--;

                    $("option:selected", "#order-professions").each(function (el, i) {
                        let b = i.value;
                        professionsFilter.push(b);
                    });

                    $("option:selected", "#order-normbases").each(function (el, i) {
                        let b = i.value;
                        normBasesFilter.push(b);
                    });

                    $("option:selected", "#order-smetadocs").each(function (el, i) {
                        let b = i.value;
                        smetaDocsFilter.push(b);
                    });

                    $.ajax({
                        url: '/order/index',
                        method: 'POST',
                        data: {
                            priceSort: priceSort,
                            dataResultsTotal: positionOrderInResultArr,
                            professionsFilter: professionsFilter,
                            normBasesFilter: normBasesFilter,
                            smetaDocsFilter: smetaDocsFilter,
                            positionOrderInResultArr: positionOrderInResultArr
                        },
                        success: function (data) {

                            $('#resultBlock').append(data);

                            resultBlockController.showContactsEvent();
                            resultBlockController.experienceValueCheck();
                            resultBlockController.setValOfCounter();

                            $('#showNext').attr('data-block', '0');

                        },
                        error: function () {
                            bootbox.alert("Произошла ошибка");
                            $('#showNext').attr('data-block', '0');
                        }

                    });
                }
            });
        }
        ,

        experienceValueCheck: function () {

            let exp = $('.dotText.ajax');

            exp.each(function () {

                if ($(this).innerHeight() > 63) {

                    $(this).css({
                        height: '44px',
                        overflow: 'hidden',
                        'padding-right': '1%'
                    });

                    $(this).parent().append('<div class="showMoreHolder"><a class="showMore">...читать полностью</a></div>');

                } else {
                    $(this).css({'visibility': 'visible'});
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
        }
        ,

        setValOfCounter: function () {
            let positionOrderInResultArr = $('.itemRow').last().data('position');
            $('#countOfOrdersIntoPage').text(positionOrderInResultArr);

            let totalCount = $('#totalCount').text();

            if (positionOrderInResultArr >= totalCount) {
                $('#blockOfCounters').hide();
            } else {
                let tmp = totalCount - positionOrderInResultArr;

                if (tmp < 7) {
                    $("#counterForShowMore").text(tmp);
                }

            }


        }
    }
;

$(document).ready(function () {
    resultBlockController.init();

    $('#order-professions').on('change', function () {
        sendSearchFilter();
    });

    $('#order-normbases').on('change', function () {
        sendSearchFilter();
    });

    $('#order-smetadocs').on('change', function () {
        sendSearchFilter();
    });

    $('.sortingPrice').on('click', function () {
        let priceSort = $(this).data('sort');

        $('.sortingPrice').removeClass('active');

        $(this).addClass('active');

        sendSearchFilter(priceSort);
    });

    function sendSearchFilter(priceSort) {
        let professionsFilter = [];
        let normBasesFilter = [];
        let smetaDocsFilter = [];

        $("option:selected", "#order-professions").each(function (el, i) {
            let b = i.value;
            professionsFilter.push(b);
        });

        $("option:selected", "#order-normbases").each(function (el, i) {
            let b = i.value;
            normBasesFilter.push(b);
        });

        $("option:selected", "#order-smetadocs").each(function (el, i) {
            let b = i.value;
            smetaDocsFilter.push(b);
        });

        $.ajax({
            url: '/order/index',
            method: 'POST',
            data: {
                professionsFilter: professionsFilter,
                normBasesFilter: normBasesFilter,
                smetaDocsFilter: smetaDocsFilter,
                priceSort: priceSort
            },
            success: function (data) {

                $('#counterForShowMore').text('7');
                $('#blockOfCounters').show();

                let resultBlock = $('#resultBlock');

                resultBlock.empty();
                resultBlock.append(data);
                resultBlockController.init();

            },
            error: function () {
                bootbox.alert("Произошла ошибка");
            }

        });
    }
});
