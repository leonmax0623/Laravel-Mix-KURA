$(function () {
    var maskList = $.masksSort($.masksLoad("/public/js/mask/phone-codes.json"), ['#'], /[0-9]|#/, "mask");
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
        onMaskChange: function(maskObj, completed) {
            if (completed) {
                var hint = maskObj.name_ru;
                if (maskObj.desc_ru && maskObj.desc_ru != "") {
                    hint += " (" + maskObj.desc_ru + ")";
                }
            }
            $(this).attr("placeholder", $(this).inputmask("getemptymask"));
        }
    };

    $('#customer_phone').inputmasks(maskOpts);
});