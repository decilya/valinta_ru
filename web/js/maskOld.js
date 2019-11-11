var maskList = $.masksSort($.masksLoad("/libs/mask/phone-codes.json"), ['#'], /[0-9]|#/, "mask");
var maskOpts = {
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
    list: maskList,
    listKey: "mask",
    onMaskChange: function(maskObj, determined) {
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
};
var cityOrder = '';
var listRU = $.masksSort($.masksLoad("/libs/mask/phones-ru.json"), ['#'], /[0-9]|#/, "mask");
var optsRU = {
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
    list: listRU,
    listKey: "mask",
    onMaskChange: function(maskObj, determined) {

        var inputCity = $('#user-city');

        $(this).attr("placeholder", $(this).inputmask("getemptymask"));
    }
};