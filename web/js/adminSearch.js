"use strict";

var adminSearch = {
    inputId: $('input[name="searchId"]'),
    inputText: $('input[name="searchText"]'),
    selectStatus: $('select[name="searchStatus"]'),
    selectUserStatus: $('select[name="searchUserStatus"]'),
    selectSort: $('#sortSelect'),
    paginatorPage: undefined,
    controller: undefined,
    filtersAll: undefined,
    init: function init() {
        this.filtersAll = this.inputId.add(this.inputText).add(this.selectStatus).add(this.selectUserStatus).add(this.selectSort);
        this.checkPageInUrl();
        this.controller = $('div[data-controller]');
        this.onChange();
        this.acceptUserEvent();
        this.rejectUserEvent();
        this.savePageNum();
    },
    determineAnchors: function determineAnchors(obj) {
        var items = $('.itemBlock');
        var arr = [];
        var matchStr = '';
        items.each(function () {
            arr.push($(this).attr('data-id') * 1);
        });

        if (location['search'].search(/page=(\d+)/) > -1) {
            var matches = location['search'].match(/page=(\d+)/);
            matchStr = '_' + matches[1];
        } else {
            matchStr = '';
        }

        var currentVal = arr.indexOf(obj.parents('.itemBlock').attr('data-id') * 1);
        var anchorVal = currentVal != 0 ? arr[--currentVal] : arr[++currentVal];
        return '/' + anchorVal + matchStr;
    },
    acceptUserEvent: function acceptUserEvent() {
        if (document.URL.search(/user\/index/) !== -1) {
            $('.itemAcceptLink').on('click', function (event) {
                event.preventDefault();
                var anchor = adminSearch.determineAnchors($(this));
                var url = window.location.protocol + '//' + document.location.host + $(this).attr('href') + anchor;
                bootbox.dialog({
                    message: "Вы действительно хотите подтвердить правильность заполнения анкеты №" + $(this).attr('data-id') + "?",
                    buttons: {
                        success: {
                            label: "Подтвердить",
                            className: "btn-success",
                            callback: function callback(result) {
                                if (result) {
                                    document.location.href = url;
                                }
                            }
                        },
                        danger: {
                            label: "Отмена",
                            className: "btn-danger--adm"
                        }
                    }
                });
            });
        }
    },
    rejectUserEvent: function rejectUserEvent() {
        if (document.URL.search(/user\/index/) !== -1) {
            $('.itemRejectLink').on('click', function (event) {
                event.preventDefault();
                var anchor = adminSearch.determineAnchors($(this));
                var url = window.location.protocol + '//' + document.location.host + $(this).attr('href') + anchor;
                var id = $(this).attr('data-id');
                bootbox.dialog({
                    title: "Укажите причину отклонения анкеты №" + $(this).attr('data-id') + " и нажмите &laquo;Отклонить&raquo;!",
                    message: "<textarea maxlength='1000' id='modalTextarea' required='required'></textarea><span class='textareaError'></span>",
                    buttons: {
                        success: {
                            label: "Отклонить",
                            className: "btn-success",
                            callback: function callback(result) {
                                var textarea = $('#modalTextarea');
                                var textareaError = $('.textareaError');
                                textareaError.text('');

                                var formIsValid = function formIsValid() {
                                    return ((textarea.val() !== '') && (textarea.val().length > 3));
                                };

                                var showFormErrors = function showFormErrors() {
                                    textareaError.text('Причина отклонения не может быть выражена менее чем в 4х символах');
                                    textareaError.show();
                                    textarea.css({
                                        'border': '1px solid red'
                                    });
                                };

                                var formOk = function formOk() {
                                    textareaError.hide();
                                    textarea.css({
                                        'border': 'inherit'
                                    });
                                };

                                if (!formIsValid()) {
                                    showFormErrors();
                                    return false;
                                } else {
                                    textareaError.text('');
                                    $.ajax({
                                        async: false,
                                        url: window.location.protocol + '//' + document.location.host + '/user/save-reject-msg',
                                        method: 'POST',
                                        data: JSON.stringify({
                                            id: id,
                                            msg: textarea.val()
                                        }),
                                        success: function success(data) {
                                            if (data === '1') {
                                                document.location.href = url;
                                            } else {
                                                textareaError.text('Произошла ошибка при записи сообщения в БД');
                                                textareaError.show();
                                                return false;
                                            }
                                        },
                                        error: function error() {
                                            textareaError.text('Произошла ошибка при записи сообщения в БД');
                                            textareaError.show();
                                            return false;
                                        }
                                    });
                                    return false;
                                }
                            }
                        },
                        danger: {
                            label: "Отмена",
                            className: "btn-danger--adm"
                        }
                    }
                });
            });
        }
    },
    checkPageInUrl: function checkPageInUrl() {
        if (document['location']['search'].search(/page=\d+/) > -1) {
            var page = document['location']['search'].match(/page=(\d+)/);
            adminSearch.paginatorPage = page[1] * 1;
        }
    },
    onChange: function onChange() {
        this.filtersAll.on('change', function () {
            if ($('h1').text() === 'Сметчики') {
                var id = adminSearch.inputId.val() * 1 > 0 ? 'id=' + adminSearch.inputId.val() + '&' : '';
                var text = adminSearch.inputText.val() !== '' ? 'text=' + adminSearch.inputText.val() + '&' : '';
                var status = adminSearch.selectStatus.val() * 1 > 0 && adminSearch.selectStatus.length ? 'status=' + adminSearch.selectStatus.val() + '&' : '';
                var userStatus = adminSearch.selectUserStatus.val() * 1 > 0 && adminSearch.selectUserStatus.length ? 'userStatus=' + adminSearch.selectUserStatus.val() + '&' : '';
                var sort = 'sort=' + adminSearch.selectSort.val();
                var controller = adminSearch.controller.attr('data-controller');
                var q = id !== '' || text !== '' || status !== '' || userStatus !== '' || sort !== '' ? '?' : '';
                var url = window.location.protocol + '//' + document.location.host + '/' + controller + '/index' + q + id + text + status + userStatus + sort;
                var newUrl = '';

                if (url.charAt(url.length - 1) == '&') {
                    newUrl = url.slice(0, -1);
                }

                document.location.href = newUrl !== '' ? newUrl : url;
            }
        }); //IE 10 fix

        if (window['navigator']['userAgent'].search(/(MSIE [0-9]{1,2}\.[0-9]?)|(Trident\/[4-7]\.0)/) !== -1) {
            this.filtersAll.keydown(function (e) {
                if (e.which == 13) $(this).trigger('change');
            });
        }
    },
    savePageNum: function savePageNum() {
        $('.itemEditLink').on('click', function (event) {
            event.preventDefault();
            var match = window['location']['search'].match(/page=\d+/);
            var anchor = '#item_' + $(this).parents('.itemBlock').attr('data-id');
            sessionStorage.setItem('urlPagePart', match !== null ? match[0] + anchor : 'page=1' + anchor);
            window.location.href = window.location.protocol + '//' + location.host + $(this).attr('href');
        });
    }
};
adminSearch.init();