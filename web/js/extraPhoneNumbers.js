var extraPhoneNumbers = {

    limit: 3,

    numbersAmount: undefined,
    addButton: undefined,
    modelName: undefined,

    addingNumberInProgress: false,

    init: function () {

        this.setModelName();

        this.setAddButton();
        this.setNumbersAmount();
        this.checkLimit();
        this.plusButtonEvent();
        this.krestButtonEvent();
        console.log(extraPhoneNumbers.numbersAmount);
        $('[data-role="numberRemove"]').on('click', extraPhoneNumbers.controlEventHandler);
    },

    setModelName: function () {
        this.modelName = $('form[data-role="mainForm"]').attr('data-model-name');
    },

    setAddButton: function () {
        this.addButton = $('[data-role="numberAdd"]');
        this.addControlEvent();
        this.plusButton = $(".js-input-plus");
        this.krestButton = $(".js-input-remove");
    },


    setNumbersAmount: function (val) {
        this.numbersAmount = (val === undefined) ? $('form[data-role="mainForm"]').attr('data-phone-numbers-amount') : val;
    },

    addControlEvent: function () {
        this.addButton.on('click', this.controlEventHandler);
    },

    plusButtonEvent: function () {
        this.plusButton.on('click', this.checkPlusButton);

    },

    krestButtonEvent: function () {
        $(".table-condensed").on('click', '.js-input-remove', this.checkKrestButton);
    },

    checkKrestButton: function () {

        console.log($(this).parents("tbody").children(".multiple-input-list__item").length)

        if ($(this).parents("tbody").children(".multiple-input-list__item").length <= 3) {
            $(".js-input-plus").css('background-image', 'none');
            $(".js-input-plus").css('background-image', 'url("/img/number-add.png")');
        }
    },

    checkPlusButton: function () {
        //console.log($(this).parents("tbody").children(".multiple-input-list__item").length)

        let lengbut = $(this).parents("tbody").children(".multiple-input-list__item").length;

        let plusbut = $(this);

        if (lengbut >= 2) {
            //plusbut.css('background', 'url ("../img/number-disable.png")')
            plusbut.css('background-image', 'none');
            plusbut.css('background-image', 'url("/img/number-disable.png")')
        }
    },

    controlEventHandler: function () {

        switch ($(this).attr('data-role')) {
            case "numberAdd":

                if ($('[data-role="phoneField"]').length >= 3) return;

                if (extraPhoneNumbers.addingNumberInProgress === false) {
                    extraPhoneNumbers.addNumber();
                }
                break;
            case "numberRemove":

                if ($('[data-role="phoneField"]').length === 1) return;

                extraPhoneNumbers.removeNumber($(this));
                break;
            default :
                break;
        }
    },

    checkLimit: function () {
        console.log($('[data-role="phoneField"]').length);
        if ($('[data-role="phoneField"]').length === extraPhoneNumbers.limit) {
            extraPhoneNumbers.addButton.off('click');
            if (!extraPhoneNumbers.addButton.hasClass('numberDisable')) extraPhoneNumbers.addButton.removeClass('numberAdd').addClass('numberDisable');
        } else if (extraPhoneNumbers.numbersAmount <= extraPhoneNumbers.limit) {
            if (extraPhoneNumbers.addButton.hasClass('numberDisable')) extraPhoneNumbers.addButton.removeClass('numberDisable').addClass('numberAdd');
            extraPhoneNumbers.addButton.off('click');
            extraPhoneNumbers.addControlEvent();
        }

    },

    addNumber: function () {

        if ($('[data-role="phoneField"]').length > 3) {
            return;
        }

        extraPhoneNumbers.addingNumberInProgress = true;

        $.ajax({
            url: '/site/add-phone-number',
            method: 'POST',

            data: {
                fieldsCount: extraPhoneNumbers.numbersAmount,
                modelName: extraPhoneNumbers.modelName,
                isAjax: true,
            },

            success: function (data) {

                data = $(data);

                var phoneField = $('[data-role="phoneField"]').last();
                data.insertAfter(phoneField);
                extraPhoneNumbers.setNumbersAmount(data.attr('data-number-index'));
                data.find('[data-role="numberRemove"]').on('click', extraPhoneNumbers.controlEventHandler);
                extraPhoneNumbers.checkLimit();

                extraPhoneNumbers.addingNumberInProgress = false;
            }
        });
    },

    removeNumber: function (obj) {

        var index = obj.attr('data-number-index');

        if (obj.attr('id') === 'order-phone') return;

        if  (index === 1) return;

        $('.form-group[data-number-index="' + index + '"]').remove();
        extraPhoneNumbers.setNumbersAmount(--extraPhoneNumbers.numbersAmount);

        // $('[data-role="phoneField"]').each(function (index) {

        if ($(this).attr('data-number-index') > 1) {

            var actualIndex = index + 1;

            $(this).attr('data-number-index', actualIndex);

            var currentClassStr = $(this).attr('class');

            var newClassStr = currentClassStr.replace(new RegExp('(field-' + extraPhoneNumbers.modelName.toLowerCase() + '-extraphones-\\d+)'), 'field-' + extraPhoneNumbers.modelName.toLowerCase() + '-extraphones-' + actualIndex);

            $(this).attr('class', newClassStr);

            $(this).find('label').attr('for', extraPhoneNumbers.modelName.toLowerCase() + '-extraphones-' + actualIndex);

            $(this).find('input').attr('name', extraPhoneNumbers.modelName + '[extraPhones][' + actualIndex + ']');
            $(this).find('input').attr('id', extraPhoneNumbers.modelName.toLowerCase() + '-extraphones-' + actualIndex);
            $(this).find('input').attr('data-number-index', actualIndex);

            $(this).find('[data-role="numberRemove"]').attr('data-number-index', actualIndex);

        }

        // });

        extraPhoneNumbers.checkLimit();
        extraPhoneNumbers.init();

    }

};

extraPhoneNumbers.init();