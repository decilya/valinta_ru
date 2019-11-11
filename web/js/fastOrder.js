/**
 * @summary js для формы создания заказа
 * @description Создание заказа valinta.ru/order/create
 *
 * @author Ilya <ilya.v87v@gmail.com>  a.k.a via (и мне за этот js стыдно, мало того что это не ООП, так еще и... ппц...)
 * @data 19.08.2019
 */

"use strict";

var clickBtn = 0;
var getLoginForm = true;
var myCustomerId = false;
var formValid = false;
var formTimeout = true;
var formTimeoutId = null;

function init() {

    let loginBtn = $('#loginBtn');

    loginBtn.attr('disabled', true);
    loginBtn.on('click', function () {
        return undefined;
    });

    extraPhoneNumbers.init();

    var counterPassError = 0;
    var userAgreement = $('#order-user_agreement');

    if (userAgreement.is(":checked")) {
        $('#submitBtn').attr('disabled', false);
    } else {
        $('#submitBtn').attr('disabled', true);
    }

    userAgreement.on('click', function () {
        if ($('#order-user_agreement').is(":checked")) {
            $('#submitBtn').attr('disabled', false);
        } else {
            $('#submitBtn').attr('disabled', true);
        }
    });

    var fastRegOrderFormLoginInput = $("#fastregorderform-login");
    var fastRegOrderFormPasswordInput = $('#fastregorderform-password');
    var fastRegOrderFormRePasswordInput = $('#fastregorderform-repassword');
    var fastLoginOrderFormLoginInput = $("#fastloginorderform-login");
    var fastLoginOrderFormPasswordInput = $('#fastloginorderform-password');

    fastRegOrderFormLoginInput.focus(function () {
        fastRegOrderFormPasswordInput.attr('disabled', true);
        fastRegOrderFormRePasswordInput.attr('disabled', true);

        fastRegOrderFormLoginInput.on('keydown', function (event) {
            if (event.which == 9) {
                setTimeout(function () {
                    fastRegOrderFormPasswordInput.attr('disabled', false);
                    fastRegOrderFormPasswordInput.focus();
                }, 0);
            }
        });
    });
    fastLoginOrderFormLoginInput.focus(function () {
        fastLoginOrderFormPasswordInput.attr('disabled', true);
        fastLoginOrderFormLoginInput.on('keydown', function (event) {
            if (event.which == 9) {
                setTimeout(function () {
                    fastLoginOrderFormPasswordInput.attr('disabled', false);
                    fastLoginOrderFormPasswordInput.focus();
                }, 0);
            }
        });
    });
    fastRegOrderFormLoginInput.focusout(function () {
        setTimeout(function () {
            enabledInput();
        }, 0);
    });
    fastLoginOrderFormLoginInput.focusout(function () {
        setTimeout(function () {
            enabledInput();
        }, 0);
    });

    function enabledInput() {
        var fastRegOrderFormPasswordInput = $('#fastregorderform-password');
        var fastRegOrderFormRePasswordInput = $('#fastregorderform-repassword');
        var fastLoginOrderFormPasswordInput = $('#fastloginorderform-password');

        if (fastRegOrderFormPasswordInput.length > 0 && $('.rightNow:contains("Адрес электронной почты закреплен за учетной записью сметчика")').length == 0) {
            fastRegOrderFormPasswordInput.attr('disabled', false);
        }

        if (fastRegOrderFormRePasswordInput.length > 0 && $('.rightNow:contains("Адрес электронной почты закреплен за учетной записью сметчика")').length == 0) {
            fastRegOrderFormRePasswordInput.attr('disabled', false);
        }

        if (fastLoginOrderFormPasswordInput.length > 0 && $('.rightNow:contains("Адрес электронной почты закреплен за учетной записью сметчика")').length == 0) {
            fastLoginOrderFormPasswordInput.attr('disabled', false);
        }

        if ($('.rightNow').length > 0) {
            if ($('#fastregorderform-login').length > 0) {
                $('#fastregorderform-login').addClass('rightNow');
            }

            setTimeout(function () {
                setRedErrorForSmet4ik();
            }, 0);

            if ($('#fastregorderform-password').length > 0) {
                $('#fastregorderform-password').attr('disabled', true);
            }

            if ($('#fastregorderform-repassword').length > 0) {
                $('#fastregorderform-repassword').attr('disabled', true);
            }

            if ($('#fastregorderform-password').length > 0) {
                $('#fastregorderform-password').attr('disabled', true);
            }
        }
    }

    $('#fastregorderform-login').on('change', function () {
        if (getLoginForm) {
            getLoginForm = false;

            $('#order-email').val($('#fastregorderform-login').val());
            createForm(this);
        } else {
        }
    });
    $('#fastloginorderform-login').on('change', function () {
        $('#order-email').val($('#fastloginorderform-login').val());
        createForm(this);
    });


    $('#fastloginorderform-password').on('change', function () {
        var password = $('#fastloginorderform-password').val();
        var login = $('#fastloginorderform-login').val();
        var restorePassDiv; //'<div class="myRestorePassDiv"><a class="myRestorePassA" href="/recover?email=' + login + '">Восстановить пароль</a></div>';

        $.ajax({
            url: '/order/check-password-and-login',
            method: 'POST',
            data: {
                password: password,
                login: login
            },
            dataType: 'json',
            success: function success(resultCheckPassword) {
                if (resultCheckPassword) {
                    $("label[for='fastloginorderform-login']").html("Адрес электронной почты (Вход в систему успешно выполнен)");

                    if ($('.regLink').length > 0) {
                        $('#userTopRightBlock').html("<a class='logoutLink' href='/site/logout'>Выйти</a>");
                    }

                    setOrderFormForCustomer(login, 'no');
                    var myRestorePassDiv = $('.myRestorePassDiv');

                    if (myRestorePassDiv.length > 0) {
                        myRestorePassDiv.remove();
                    }

                    $('#fastloginorderform-password').hide();
                    $('#fastloginorderform-login').attr('disabled', true);

                    if ($('#myPassErrorBlock').length > 0) {
                        $('#myPassErrorBlock').remove();
                    }

                    $.ajax({
                        url: '/site/get-ajax-top-menu/',
                        method: 'POST',
                        success: function success(newHeader) {
                            $('#topHeader div').remove();
                            $('#topHeader').append(newHeader);
                            window.location.reload();
                        }
                    });
                } else {
                    counterPassError++;

                    var _myRestorePassDiv = $('.myRestorePassDiv');

                    if (!_myRestorePassDiv.length > 0) {
                        $("label[for='fastloginorderform-password']").append(restorePassDiv);
                    }

                    setTimeout(function () {
                        if (!$('#myPassErrorBlock').length > 0) {
                            $('.field-fastloginorderform-password .help-block').show();
                            $('.field-fastloginorderform-password .help-block').html("<p id='myPassErrorBlock' style='color:#a94442;'>Неправильный пароль</p>");
                            $('#myPassErrorBlock').css('color', '#a94442');
                            $('#fastloginorderform-password').css('border-color', '#a94442');
                            $("label[for='fastloginorderform-password']").css('color', '#a94442');
                        }
                    }, 0);

                    if (counterPassError >= 5) {
                        var inputPass = $('#fastloginorderform-password');
                        inputPass.attr('disabled', true);
                        var counterTmp = 10;
                        repeatPass(counterTmp--); // начать повторы с интервалом 1 сек

                        var timerId = setInterval(function () {
                            repeatPass(counterTmp--);
                        }, 1000); // через 5 сек остановить поmyCustomerвторы

                        setTimeout(function () {
                            clearInterval(timerId);
                            $(".myRepeatInfo").remove();
                            inputPass.attr('disabled', false);
                        }, 10000);
                        counterPassError = 0;
                    }
                }
            },
            error: function error() {
            }
        });
    });
    $('#confirm').on('change', function () {
        if ($('#confirm').prop('checked')) {
            $('#submitBtn').attr('disabled', false);
        } else {
            $('#submitBtn').attr('disabled', true);
        }
    });
    $('#byAgreement').on('change', function () {
        if ($('#byAgreement').prop('checked')) {
            $('#order-price').val('');
            $('#form-order').yiiActiveForm('validateAttribute', 'order-price');
            $('#order-price').attr('disabled', true);
        } else {
            $('#order-price').attr('disabled', false);
            $('#form-order').yiiActiveForm('validateAttribute', 'order-price');
        }
    });
    $('#order-price').on('change', function () {
        if ($(this).val() < 0) {
            $(this).val('0');
        }

        if ($(this).val() == '0') {
            $('#byAgreement').trigger('click');
        }
    });

    if ($('#order-price').val() == '0') {
        $('#byAgreement').trigger('click');
    }

    function repeatPass(i) {
        if ($('.myRepeatInfo').length > 0) {
            $('.myRepeatInfo').remove();
        }

        var str = "<span class='myRepeatInfo'>Повторите попытку через " + i + " сек.</span>";
        $("label[for='fastloginorderform-password']").append(str);
    }

    function validateOrderCustom() {
        if ($('#loggedinorderform-login').val() !== undefined && $('#loggedinorderform-login').val().length > 0) {
            $('<div class="form-group" id="order-emailClone" />').prependTo($('form#form-order'));
            $('#loggedinorderform-login').attr('name', 'Order[email]').prependTo($('#order-emailClone'));
        }
    }

    validateOrderCustom();

    $('#submitBtn').on('click', function () {
        $('#registrationCustomerForm').yiiActiveForm('validateAttribute', 'fastregorderform-login');
        $('#registrationCustomerForm').yiiActiveForm('validateAttribute', 'fastregorderform-password');
        if (formTimeout === true && !formValid) {
            if (!$('#submitBtn').attr('disabled')) {
                $('#submitBtn').attr('disabled', true);

                if ($('#fastregorderform-login').val() !== undefined && $('#fastregorderform-login').val().length > 0) {
                    regCustomer();
                } else {

                    // $('form#form-order').attr('action', '/order/create');
                    // $('form#form-order').submit();
                    // formTimeout = false;

                    $('#submitBtn').attr('disabled', true);

                    if ($('#fastregorderform-login').val() !== undefined && $('#fastregorderform-login').val().length > 0) {
                        regCustomer();
                    } else {
                        formTimeout = false;
                        $('form#form-order').attr('action', '/order/create');
                        $('form#form-order').submit();
                    }
                }
            }
        } else {
            clearTimeout(formTimeoutId);
            formTimeoutId = setTimeout(function () {
                formTimeout = true;
                clearTimeout(formTimeoutId);
            }, 300)
        }
    });

    $('form#form-order').on('beforeSubmit', function (e) {
        e.preventDefault();

        var form = $(this);

        if (form.find('.has-error').length) {
            return false;
        }

        $('#submitBtn').attr('disabled', true);
        formValid = true;

    });

    // $('form#form-order').on('beforeValidate', function () {
    //
    //     $('#submitBtn').attr('disabled', true);
    // });
    //
    $('form#form-order').on('afterValidate', function (e, v, i) {
        var attributes = $(this).data();
        console.log(attributes);
        console.log(i);
        if (i.length === 0) {
            $('#submitBtn').attr('disabled', false);
        }
    });
} // init

function createForm(input) {
    var login = $.trim($(input).val());
    var fastRegOrderFormPasswordInput = $('#fastregorderform-password');
    var fastRegOrderFormRePasswordInput = $('#fastregorderform-repassword');
    var fastLoginOrderFormPasswordInput = $('#fastloginorderform-password');
    var password = fastRegOrderFormPasswordInput.length > 0 ? fastRegOrderFormPasswordInput.val() : fastLoginOrderFormPasswordInput.val();
    var rePassword = fastRegOrderFormRePasswordInput.length > 0 ? fastRegOrderFormRePasswordInput.val() : null;

    if (login !== '' && login !== null) {
        $.ajax({
            url: '/order/check-customer',
            method: 'POST',
            data: {
                login: login
            },
            dataType: 'json',
            success: function success(data) {
                if (data === 1) {
                    setTimeout(function () {
                        // заменим форму на другую
                        $.ajax({
                            url: '/order/get-login-form',
                            method: 'POST',
                            data: {
                                login: login
                            },
                            dataType: 'json',
                            success: function success(newForm) {
                                getLoginForm = true;
                                $('#myLoginForm').html(newForm);
                                setLoginForm(login, password, rePassword);
                                init();
                                $('#registrationCustomerForm div:nth-child(2)').append("<div id='blockForRemember' class='container'>" + "<div id='remember'><a href='/recover?email=" + login + "'>" + "Восстановить пароль" + "</a></div></div>"); // $("label[for='fastloginorderform-password']").hide();
                            },
                            error: function error() {
                                getLoginForm = true;
                                init();
                            }
                        });
                    }, 0); // если 2 - то это сметчик
                } else if (data === 2) {
                    setTimeout(function () {
                        $.ajax({
                            url: '/order/get-reg-form',
                            method: 'POST',
                            dataType: 'json',
                            success: function success(newForm) {
                                getLoginForm = true;
                                $('#myLoginForm').html(newForm);
                                setLoginForm(login, password, rePassword);
                                $('#loginBtn').click();
                                init();
                                setTimeout(function () {
                                    setRedErrorForSmet4ik();
                                }, 0);
                            },
                            error: function error() {
                                getLoginForm = true;
                            }
                        });
                    }, 0);
                } else if (data === false) {
                    //  alert('false');
                    setTimeout(function () {
                        $.ajax({
                            url: '/order/get-reg-form',
                            method: 'POST',
                            data: {
                                login: login
                            },
                            dataType: 'json',
                            success: function success(newForm) {
                                setTimeout(
                                    function () {
                                        getLoginForm = true;
                                        $('#myLoginForm').html(newForm);
                                        setLoginForm(login);
                                        init();


                                        $('.help-block.rightNow').remove();
                                    }, 1000);


                            },
                            error: function error() {
                                getLoginForm = true;
                            }
                        });
                    }, 0);
                }
            },
            error: function error() {
                $.ajax({
                    url: '/order/get-reg-form',
                    method: 'POST',
                    data: {
                        login: login
                    },
                    dataType: 'json',
                    success: function success(newForm) {
                        getLoginForm = true;
                        $('#myLoginForm').html(newForm);
                        setLoginForm(login);
                        init();
                    },
                    error: function error() {
                        getLoginForm = true;
                    }
                });
            }
        });
    }
}

function setLoginForm(login, password, rePassword) {
    var fastRegOrderFormLogin1 = $('#fastregorderform-login');
    var fastLoginOrderForm1 = $('#fastloginorderform-login');

    if (fastRegOrderFormLogin1.length > 0) {
        fastRegOrderFormLogin1.val(login);
    } else if (fastLoginOrderForm1.length > 0) {
        fastLoginOrderForm1.val(login);
        fastLoginOrderForm1.parent().css('color', 'green');
        fastLoginOrderForm1.css('border-color', 'green');
    }

    var fastRegOrderFormPasswordInput = $('#fastregorderform-password');
    var fastRegOrderFormRePasswordInput = $('#fastregorderform-repassword');
    var fastLoginOrderFormPasswordInput = $('#fastloginorderform-password');

    if (fastRegOrderFormPasswordInput.length > 0) {
        fastRegOrderFormPasswordInput.val(password);
    }

    if (fastRegOrderFormRePasswordInput.length > 0) {
        fastRegOrderFormRePasswordInput.val(rePassword);
    }

    if (fastLoginOrderFormPasswordInput.length > 0) {
        fastLoginOrderFormPasswordInput.val(password);
    }
}

function setOrderFormForCustomer(login, go) {
    var rawData = {
        name: $('#order-name').val(),
        fio: $('#order-fio').val(),
        phone1: $('#order-phone').val(),
        phone2: $("input").is("#order-extraphones-2") ? $('#order-extraphones-2').val() : null,
        phone3: $("input").is("#order-extraphones-3") ? $('#order-extraphones-2').val() : null,
        byAgreement: $('#byAgreement').val(),
        price: $('#order-price').val(),
        text: $('#order-text').val(),
        professions: $('#order-professions').val(),
        normBases: $('#order-normbases').val(),
        smetaDocs: $('#order-smetadocs').val()
    };

    if ($('#byAgreement').prop('checked')) {
        rawData['price'] = 0;
    }

    $.ajax({
        url: '/order/get-order-for-customer',
        method: 'POST',
        data: {
            login: login,
            rawData: rawData
        },
        dataType: 'json',
        success: function success(order) {
            $('#myOrderForm').html(order);
            init();
            $('form#form-order').attr('action', '/order/create');

            if (go === 'go') {
                if (!$('#order-user_agreement').prop("checked")) {
                    $('#order-user_agreement').click();
                }

                if (rawData['price'] === 0) {
                    if (!$('#byAgreement').prop('checked')) {
                        $('#order-price').val(0);
                        $('#byAgreement').click();
                    }
                }

                $('form#form-order').submit();
            }
        },
        error: function error() {
        }
    });
}

function regCustomer() {

    let form = $(this);
    if (form.find('.has-error').length) {
        return undefined;
    }

    setTimeout(function () {
        var login = $('#fastregorderform-login').val();
        var name = $('#order-fio').val();
        var password = $('#fastregorderform-password').val();
        var rePassword = $('#fastregorderform-repassword').val();
        var phone1 = $('#order-phone').val();
        var phone2 = $('#order-extraphones-2');
        var phone3 = $('#order-extraphones-3');
        phone2 = phone2.length > 0 ? phone2.val() : null;
        phone3 = phone3.length > 0 ? phone3.val() : null;
        var phones = {
            phone1: phone1,
            phone2: phone2,
            phone3: phone3
        };

        if (login != null && name != null && password != null && rePassword != null) {
            if (password == rePassword) {
                $.ajax({
                    url: '/order/reg-customer',
                    method: 'POST',
                    data: {
                        name: name,
                        password: password,
                        login: login,
                        phones: phones
                    },
                    dataType: 'json',
                    success: function success(newCustomer) {
                        if (newCustomer !== false) {
                            if (myCustomerId === false) {
                                myCustomerId = newCustomer;
                            }

                            setOrderFormForCustomer(login, 'go');
                        }

                        if (newCustomer === false && myCustomerId !== false) {
                            $('form#form-order').submit();
                        }

                        init();
                    },
                    error: function error() {

                        init();
                    }
                });
            } else {
                // bootbox.alert("Введеные пароли не совпадают");
                init();
            }
        } else {
            init();
        }
    }, 0);
}

function setRedErrorForSmet4ik() {
    setTimeout(function () {
        var tmp = $('.field-fastregorderform-login');
        var tmp1 = $('.field-fastregorderform-login .help-block');
        tmp1.html('Адрес электронной почты закреплен за учетной записью сметчика. Чтобы разместить заказ, используйте другую почту');
        tmp1.addClass('rightNow');
        tmp.addClass('rightNow');
        $('#fastregorderform-login').addClass('rightNow');
        $('#fastregorderform-password').attr('disabled', true);
        $('#fastregorderform-repassword').attr('disabled', true);
        tmp.css('color', '#a94442');
        tmp.css('border-color', '#a94442');
        tmp1.css('color', '#a94442');
        $('.rightNow label').css('color', '#a94442');
        init();
    }, 666);
}

init();