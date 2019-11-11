var userUpdate = {

    link: $('#sendInstructions'),

    messageBlock: $('.messageBlock'),

    init: function () {
        this.addLinkEvent();
        this.backToListPagePart();
    },

    addLinkEvent: function () {


        $('#sendInstructions').on('click', function (event) {

            event.preventDefault();

            var href = $(this).attr('data-href');

            $.ajax({
                url: window.location.protocol + '//' + document.location.host + href,
                method: 'get',
                dataType: 'json',
                isAjax: true,
                success: function (data) {
                    $('.messageBlock').html('');
                    $('.messageBlock').html('<div id="w0" class="alert alert-' + data.status + ' fade in" ><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' + data.body + '</div>');
                },
            });


        });
    },

    backToListPagePart: function () {

        if ((document['referrer'].search(/user\/index/) !== -1) ||
            (document['referrer'].search(/customer\/admin-customers-list/) !== -1) ||
            (-1 < document['referrer'].indexOf('user/update'))
        ) {
            var href = $('.backToIndex').attr('href');

            if (sessionStorage.getItem('urlPagePart')) {

                sessionStorage.removeItem('urlPagePartReload');

                href += '?' + sessionStorage.getItem('urlPagePart');

                $('.backToIndex').attr('href', href);

                sessionStorage.setItem('urlPagePartReload', '?' + sessionStorage.getItem('urlPagePart'));

                sessionStorage.removeItem('urlPagePart');

            } else if (sessionStorage.getItem('urlPagePartReload') && !sessionStorage.getItem('urlPagePart')) {

                href += sessionStorage.getItem('urlPagePartReload');

                $('.backToIndex').attr('href', href);
            }

        } else {
            sessionStorage.removeItem('urlPagePart');
            sessionStorage.removeItem('urlPagePartReload');
        }
    }
};

$(function () {
    userUpdate.init();

    // Личный кабинет Заказчика №xxx (и сам юзверь и редактирование имярека админом)
    if (document.location.href.indexOf('customer/update') > -1) {
        if ($("tbody").children(".multiple-input-list__item").length >= 3) {
            $(".js-input-plus").css('background-image', 'url("/img/number-disable.png")')
        }

        for (let i = 0; i <= $('.list-cell__customerPhones input').length; i++) {
            $('#customer-customerphones-' + i).inputmasks(mask.optsRU);
        }

        ///////////

        $('#customerAgreementBtn').on('change', function(){
            if ($(this).prop('checked')){
                $('#submitBtn').prop('disabled', false);
            } else {
                $('#submitBtn').prop('disabled', true);
            }
        });

        $('.multiple-input-list__btn').on('click', function () {
            setTimeout(function () {
                $('.multiple-input-list input.form-control[type=text]').each(function(i,elem) {
                    $(this).inputmasks(optsRU);
                });
            }, 0);
        });
    }

});
