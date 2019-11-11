var mask = {

    cityOrder: '',

    maskOpts: {
        inputmask: {
            definitions: {
                '#': {
                    validator: "[0-9]",
                    cardinality: 1
                }
            },
            //clearIncomplete: true,
            showMaskOnHover: false,
            autoUnmask: true
        },
        match: /[0-9]/,
        replace: '#',
        list: $.masksSort($.masksLoad("/libs/mask/phone-codes.json"), ['#'], /[0-9]|#/, "mask"),
        listKey: "mask",
        onMaskChange: function (maskObj, determined) {
            if (determined) {
                var hint = maskObj.name_ru;
                if (maskObj.desc_ru && maskObj.desc_ru != "") {
                    hint += "(" + maskObj.desc_ru + ")";
                }
                $("#user-cityfinal").val(hint);
            } else {
                $("#user-cityfinal").val("");
            }
            $(this).attr("placeholder", $(this).inputmask("getemptymask"));
        }
    },

    optsRU: {
        inputmask: {
            definitions: {
                '#': {
                    validator: "[0-9]",
                    cardinality: 1
                }
            },

            //clearIncomplete: true,
            showMaskOnHover: false,
            autoUnmask: false
        },
        match: /[0-9]/,
        replace: '#',
        list: $.masksSort($.masksLoad("/libs/mask/phones-ru.json"), ['#'], /[0-9]|#/, "mask"),
        listKey: "mask",
        onMaskChange: function (maskObj, determined) {

            var inputCity = $('#user-city');

            $(this).attr("placeholder", $(this).inputmask("getemptymask"));
        }
    },

    init: function () {
        $('#order-phone').inputmasks(mask.optsRU);
        $('#user-phone').inputmasks(mask.optsRU);
        $('#request-phone').inputmasks(mask.optsRU);
        $('#order-phone').inputmasks(mask.optsRU);
    }
};

$(document).ready(function () {
    mask.init();
});

