/**
 * JS для страницы  /rcsc/list/n, где n - id пользователя Rcsc
 *
 * @author Ilya <ilya.v87v@gmail.com>
 * @data 25.09.2019
 */
$(document).ready(function () {
    function checkData() {
        let start_at = $('#rcscrequestssearch-start_at').val();
        let finish_at = $('#rcscrequestssearch-finish_at').val();

        if ((start_at !== '') && (finish_at !== '')) {

            var parts = finish_at.split('.');
            let myDate2 = new Date(parts[2], parts[1] - 1, parts[0]);

            parts = start_at.split('.');
            let myDate1 = new Date(parts[2], parts[1] - 1, parts[0]);

            if ((myDate1 != null) && (myDate2 != null)) {

                if (myDate2 < myDate1) {
                    $('#rcscrequestssearch-start_at').val(finish_at);
                    $('#rcscrequestssearch-finish_at').val(start_at);
                }

                return true;
            }
        } else if ((start_at === '') && (finish_at === '')) {
            return true;
        }

        return false;
    }

    function initBlockRcsc() {
        $('#searchId').on('change', function () {
            searchRcsc();
        });

        $('#rcscrequestssearch-start_at').on('change', function () {
            if (checkData()) {
                searchRcsc();
            }
        });

        $('#rcscrequestssearch-finish_at').on('change', function () {
            if (checkData()) {
                searchRcsc();
            }
        });
    }

    initBlockRcsc();

    function searchRcsc() {
        var searchId = $('#searchId').val();
        var start_at = $('#rcscrequestssearch-start_at').val();
        var finish_at = $('#rcscrequestssearch-finish_at').val();

        $.ajax({
            url: '/rcsc/list/' + $('#rcscId').val(),
            method: 'GET',
            data: {
                searchId: searchId,
                start_at: start_at,
                finish_at: finish_at
            },
            success: function (data) {
                $('#rcscList').find('#mainResultsBlock').remove();
                $('.childBlockRcsc').append($(data).find('#mainResultsBlock'));

                $('#rcscrequestssearch-start_at').val(start_at);
                $('#rcscrequestssearch-finish_at').val(finish_at);
                $('#searchId').val(searchId);


                jQuery.fn.kvDatepicker.dates = {};
                jQuery && jQuery.pjax && (jQuery.pjax.defaults.maxCacheLength = 0);
                if (jQuery('#rcscrequestssearch-start_at').data('kvDatepicker')) {
                    jQuery('#rcscrequestssearch-start_at').kvDatepicker('destroy');
                }
                jQuery('#rcscrequestssearch-start_at-kvdate').kvDatepicker(kvDatepicker_6347c7ac);

                initDPRemove('rcscrequestssearch-start_at');
                initDPAddon('rcscrequestssearch-start_at');
                if (jQuery('#rcscrequestssearch-finish_at').data('kvDatepicker')) {
                    jQuery('#rcscrequestssearch-finish_at').kvDatepicker('destroy');
                }
                jQuery('#rcscrequestssearch-finish_at-kvdate').kvDatepicker(kvDatepicker_6347c7ac);

                initDPRemove('rcscrequestssearch-finish_at');
                initDPAddon('rcscrequestssearch-finish_at');
                jQuery('#w0').yiiActiveForm([], []);

                yii.confirm = function (message, ok, cancel) {
                    bootbox.confirm(message, function (result) {
                        if (result) {
                            !ok || ok();
                        } else {
                            !cancel || cancel();
                        }
                    });
                };

                initBlockRcsc();

            },
            error: function () {
                bootbox.alert("Произошла ошибка");
            }
        });
    }
});