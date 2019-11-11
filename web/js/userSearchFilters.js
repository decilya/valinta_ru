var searchFilters = {

    resultsLimit: undefined,
    resultsOnPage: undefined,
    resultsTotal: undefined,

    sortFilter: undefined,
    sortDirection: undefined,

    queryString: '',

    sortingPriceLink: $('.sortingArrow'),

    dropFiltersLink: $('#dropFilters'),

    siteIndex: $('.site-index'),

    showContactsLinks: undefined,

    filterProfessions: $('#user-professions'),
    filterNormBases: $('#user-normbases'),
    filterSmetaDocs: $('#user-smetadocs'),
    filterCity: $('#user-city'),

    filtersAll: null,

    init: function () {
        this.filtersAll = this.filterProfessions.add(this.filterNormBases).add(this.filterSmetaDocs).add(this.filterCity);
        this.sortFilter = this.siteIndex.find('.results-block').attr('data-results-sort-filter');
        this.sortDirection = this.siteIndex.find('.results-block').attr('data-results-sort-direction');

        this.setQueryString(this.filtersAll);
        this.onChange();
        this.onSorting();

        this.setResultsOnPage();
        this.setResultsTotal();
        this.setResultsLimit();
        this.setShowContactsLinks();

        this.checkResults();

        this.loadContactsEvent();

        this.experienceValueCheck(false);
    },

    setResultsLimit: function () {
        this.resultsLimit = this.siteIndex.find('.results-block').attr('data-results-limit') * 1;
    },

    setResultsOnPage: function () {
        this.resultsOnPage = this.siteIndex.find('.results-block').attr('data-results-onpage') * 1;
    },

    setResultsTotal: function () {
        this.resultsTotal = this.siteIndex.find('.results-block').attr('data-results-total') * 1;
    },

    setShowContactsLinks: function () {
        this.showContactsLinks = $('.showContacts');
    },

    setQueryString: function (obj) {

        var qs = '?';

        obj.each(function (index) {

            var val = ($(this).val()) ? $(this).val() : '';

            qs += $(this).attr('id').replace(/^[a-zA-Z0-9]+-/, '') + '=' + val;

            if (++index !== searchFilters.filtersAll.length) qs += '&';

        });

        qs += '&sortFilter=' + searchFilters.sortFilter + '&sortDirection=' + searchFilters.sortDirection;

        searchFilters.queryString = qs;
    },

    loadMoreEvent: function () {
        $('#loadMore').on('click', this.loadMore);
    },

    loadContactsEvent: function () {
        this.showContactsLinks.on('click', this.loadContacts);
    },

    remainingResultsCount: function () {

        if ((this.resultsOnPage + this.resultsLimit) > this.resultsTotal) {
            return this.resultsTotal - this.resultsOnPage;
        } else {
            return this.resultsLimit;
        }
    },

    checkResults: function () {

        if (this.resultsOnPage < this.resultsTotal) {
            this.siteIndex.find('.results-block > .container').append('<div class="loadMoreHolder"><p class="countInfo">Показано сметчиков: ' + this.resultsOnPage + '  из ' + this.resultsTotal + '</p><a id="loadMore">показать еще ' + this.remainingResultsCount() + '</a></div>');
            this.loadMoreEvent();
        } else {
            this.siteIndex.find('.results-block > .container').append('<div class="loadMoreHolder"><p class="countInfo">Показано сметчиков: ' + this.resultsOnPage + ' из ' + this.resultsTotal + '</p></div>');
        }

    },

    loadMore: function () {

        searchFilters.setQueryString(searchFilters.filtersAll);

        $.ajax({
            method: 'POST',
            url: window.location.protocol + '//' + location.host + '/more-results',
            data: JSON.stringify({
                'start': searchFilters.resultsOnPage,
                'query': searchFilters.queryString
            }),
            dataType: 'json',

            success: function (data) {
                if (data.error == true) {
                    document.location.href = window.location.protocol + '//' + location.host + '/search' + searchFilters.queryString + '&showfrom=' + searchFilters.resultsOnPage;
                }

                $('#loadMore').off('click').parent().remove();

                searchFilters.resultsOnPage += data.count;

                searchFilters.siteIndex.find('.results-block > .container').append(data.html);

                searchFilters.setShowContactsLinks();

                searchFilters.checkResults();

                searchFilters.loadContactsEvent();

                searchFilters.experienceValueCheck(true);

            },
            error: function () {
                $('#loadMore').off('click').parent().remove();
                searchFilters.siteIndex.find('.results-block > .container').append('<p style="color:red;">Произошла ошибка при загрузке дополнительных результатов поиска.<br />Обновите страницу и попробуйте еще раз.</p>');
            }
        });
    },

    loadContacts: function () {

        /** 0 - гость, 1 - сметчик, 2 - заказчик */
        let typeOfUser = Number($('#typeOfUser').val());
        let holderDiv = $(this).parents('.linkHolder');

        // если заказчик
        if (typeOfUser === 2) {

            let userId = $(this).parents('.itemRow').attr('data-id');
            let authId = $('#typeOfUser').attr('data-user_id');

            $.ajax({
                method: 'POST',
                dataType: 'json',
                url: '/site/get-customer-status',
                data: {
                    'authId': authId
                },
                success: function (customerStatus) {

                    if (customerStatus === 3) {
                        holderDiv.html('').append('<div class="alert alert-danger" style="color: #8a6d3b; background-color: #fcf8e3;border-color: #faebcc;">Ваша анкета отклонена. Отредактируйте анкету.</div>');

                    } else {
                        $.ajax({
                            method: 'POST',
                            url: window.location.protocol + '//' + location.host + '/get-contacts',
                            data: JSON.stringify({
                                'id': userId
                            }),
                            dataType: 'json',

                            success: function (myData) {

                                /** realId of user (authId)*/
                                let customerRealId = $('#typeOfUser').data('user_id');

                                // Проверим не привысил ли заказчик лимит просмотров
                                $.ajax({
                                    url: '/customer/check-show-limit',
                                    method: 'POST',
                                    dataType: 'json',
                                    data: {
                                        customer_real_id: customerRealId,
                                        userId: userId
                                    },
                                    success: function (data) {

                                        if (data['status']) {
                                            // Нужно для вывода тел. в столбик
                                            let arrTel = myData.phone.split(', ');

                                            let outline = '';

                                            for (let i = 0; i < arrTel.length; i++) {
                                                outline += arrTel[i] + '<br>';
                                            }

                                            holderDiv.html('').append(
                                                '<div class="contactsBlock"><p>' + outline + '</p><p>' + myData.email + '</p></div>'
                                            );

                                            $.ajax({
                                                url: '/customer/inc-counter',
                                                method: 'POST',
                                                dataType: 'json',
                                                data: {
                                                    customer_real_id: customerRealId,
                                                    user_id: userId,
                                                },
                                                success: function (data) {
                                                    //
                                                },

                                            });
                                        } else {
                                            holderDiv.html('').append("<div class='contactsBlock'><div class='alert alert-warning'>" + data['text'] + "</div></div>");
                                        }
                                    },
                                    error: function () {
                                        holderDiv.html('').append('<div class="alert alert-danger">Ошибка при опредлении лимита просмотра контактов!</div>');
                                    }
                                });


                            },

                            error: function () {
                                holderDiv.html('').append('<div class="contactsBlock"><p style="color:red;">Произошла ошибка при загрузке дополнительных результатов поиска.<br />Обновите страницу и попробуйте еще раз.</p></div>');
                            }
                        });
                    }

                }

            });


            // если гость
        } else if (typeOfUser === 0) {
            holderDiv.html('').append('<div class="contactsBlock"><p><a href="/customer/customer-registration">Зарегистрируйтесь</a> в качестве заказчика и <a href="site/login">войдите</a> на сайт.</p></div>');

            // если все другие, а именно - СМЕТЧИК
        } else {
            holderDiv.html('').append('<div class="contactsBlock"><p><a href="/customer/customer-registration">Зарегистрируйтесь</a> на портале в качестве заказчика.</p></div>');
        }
    },

    onChange: function (on) {

        //if(on === undefined) console.log('change on');
        //if(on === false) console.log('change off');

        if (on === undefined) {
            this.filtersAll.on('change', function () {

                searchFilters.setQueryString(searchFilters.filtersAll);

                searchFilters.sendAjax();

            });
        } else if (on === false) {

            this.filtersAll.off('change');
        }
    },

    onSorting: function () {
        $('.sortingArrow').on('click', function () {

            if (!$(this).hasClass('active')) {
                searchFilters.sortFilter = $(this).attr('data-results-sort-filter');
                searchFilters.sortDirection = $(this).attr('data-results-sort-direction');

                searchFilters.setQueryString(searchFilters.filtersAll);
                searchFilters.sendAjax();
            }
        });
    },

    sendAjax: function () {
        $.ajax({
            method: 'GET',
            url: window.location.protocol + '//' + document.location.host + '/search' + searchFilters.queryString,
            dataType: 'json',

            success: function (data) {
                searchFilters.manageAjaxResponse(data);
            }
        });
    },

    manageAjaxResponse: function (data) {

        searchFilters.siteIndex.find('.results-block').remove('');

        searchFilters.siteIndex.append(data.html);

        //searchFilters.changeDropFiltersLink(data.sortprice);

        searchFilters.filtersAll = searchFilters.filterProfessions.add(searchFilters.filterNormBases).add(searchFilters.filterSmetaDocs).add(searchFilters.filterCity);
        searchFilters.sortFilter = searchFilters.siteIndex.find('.results-block').attr('data-results-sort-filter');
        searchFilters.sortDirection = searchFilters.siteIndex.find('.results-block').attr('data-results-sort-direction');

        searchFilters.setQueryString(searchFilters.filtersAll);

        searchFilters.onSorting();

        searchFilters.setResultsOnPage();
        searchFilters.setResultsTotal();
        searchFilters.setResultsLimit();
        searchFilters.setShowContactsLinks();

        searchFilters.checkResults();

        searchFilters.loadContactsEvent();

        searchFilters.experienceValueCheck(true);

    },

    changeDropFiltersLink: function (sort) {
        var url = "/search?sortprice=" + sort;
        searchFilters.dropFiltersLink.attr('href', url);
    },

    experienceValueCheck: function (ajax) {

        var exp = (ajax === false) ? $('.valueRow.experience') : $('.valueRow.experience.ajax');

        exp.each(function () {

            console.log($(this).innerHeight())

            if ($(this).innerHeight() > 22) {

                $(this).css({
                    height: '18px',
                    overflow: 'hidden',
                    'padding-right': '20%',
                    'visibility': 'visible'
                }).append('<div class="showMoreHolder"><span class="showMore">...читать полностью</span></div>');

            } else {
                $(this).css({'visibility': 'visible'});
            }

            if ($(this).hasClass('ajax')) $(this).removeClass('ajax');

        });

        $('.showMore').on('click', function () {
            $(this).parents('.experience').css({
                'height': 'inherit',
                overflow: 'inherit',
                'padding-right': '0'
            });

            $(this).parent().remove();
        });

    }
};

searchFilters.init();