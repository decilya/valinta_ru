<?php

return [
    'bsDependencyEnabled' => false,
    'domen' => 'via-valinta.test',
    'host' => 'https',
    'miniDomen' => 'dsite',

    'dayToFinishNewOrder' => 30,
    'dayToSendMailAboutFinishOrder' => 10,
    'dayOfProlongationForOrder' => 30,

    'remindMessageDivisibleByDays' => 30,

    // 1 - посылает, 0 - не посылает
    'swichForSendMailOfNewOrder' => 1,

    // 1 - регистрация новых заказов включена 0 - выключена
    'switchForRegNewOrder' => 1,

    'mainSystemPhone' => '+7 (812) 655-66-06',

    'enableDev' => true,
    'enableJivosite' => false,
    'enableYandexCounter' => false,

    'defaultSorting' => [
        'filter' => 'date',
        'direction' => 'desc'
    ],

    'searchResultsDefaultLimit' => 7,

    'sortingWeights' => [
        'city' => 100,
        'professions' => 1,
        'normbases' => 1,
        'smetadocs' => 1,
        'normBases' => 1,
        'smetaDocs' => 1
    ],

    /** Например для события типа "Черная пятница" */
    'Event' => [
        'status' => true,
        'link' => 'http://wizardsoft.ru/about/news/1255-chjornaya-pyatnitsa/',
        'img' => '/img/blackF.png'
    ],

    'itemsOnUserIndexPage' => 7,
    'itemsOnRequestIndexPage' => 7,

    //set this to false on production
    'testMail' => false,

    //set this to false on production
    'testAllUsersMode' => false,

    'mailToManagers' => 'via@wizardforum.ru',
    'mailFrom' => 'via@wizardforum.ru',

    /** Настройки отображения некоторых всплывающих сообщений */
    'flashMessageConfig' => [
        // Личный кабинет
        'customerCabinetHeader' => [
            'successCabinetHeader' => [
                'delay' => 15000 // ЗАДЕРЖКА В 15 СЕКУНД
            ],
            'errorCabinetHeader' => [
                'delay' => 20000 // ЗАДЕРЖКА В 20 СЕКУНД
            ]
        ],
    ],

    'messageSubjects' => [
        'mailSubjectManager' => 'ИПАП - VALINTA.RU - НОВАЯ АНКЕТА',
        'mailSubjectClient' => 'ИПАП - VALINTA.RU - РЕГИСТРАЦИЯ',
        'mailRecover' => 'ИПАП - VALINTA.RU - ВОССТАНОВЛЕНИЕ ПАРОЛЯ',
        'mailRecoverPassInstructions' => 'ИПАП - VALINTA.RU - СМЕНА ПАРОЛЯ',
        'mailUserAccepted' => 'ИПАП - VALINTA.RU - АНКЕТА ПОДТВЕРЖДЕНА',
        'mailUserRejected' => 'ИПАП - VALINTA.RU - АНКЕТА ОТКЛОНЕНА',
        'mailRequestSent' => 'ИПАП - VALINTA.RU - ЗАЯВКА',
        'mailSubjectOrderForCustomer' => 'ИПАП - VALINTA.RU - ЗАКАЗ №',
        'mailSubjectOrderForCustomerOrderCloseFromAdmin' => 'ИПАП - VALINTA.RU - ЗАКРЫТИЕ ЗАКАЗА №',
        'mailSubjectCustomerRegistrationManager' => 'ИПАП - VALINTA.RU - НОВЫЙ ПРОФИЛЬ ЗАКАЗЧИКА',
        'mailSubjectCustomerUpdatedManager' =>  'ИПАП - VALINTA.RU - ОБНОВЛЁН ПРОФИЛЬ ЗАКАЗЧИКА',
        'mailSubjectCustomerRegistration' => 'ИПАП - VALINTA.RU - РЕГИСТРАЦИЯ',
        'mailUserReminder' => 'ИПАП - VALINTA.RU - ОБНОВЛЕНИЕ АНКЕТЫ №',
        'mailSubjectCustomerAccepted' => 'ИПАП - VALINTA.RU - ПРОФИЛЬ ЗАКАЗЧИКА ПОДТВЕРЖДЕН',
        'mailSubjectCustomerReject' => 'ИПАП - ЗАКАЗЧИК - ПРОФИЛЬ ЗАКАЗЧИКА ОТКЛОНЁН',
        'mailSubjectUserClosedOrderClosedCustomer' => 'ИПАП - VALINTA.RU - МЕНЕДЖЕРОМ ПОРТАЛА ОТКЛОНЕН ЗАКАЗ №',
        'mailSubjectCustomerNewFeadback' => 'ИПАП - VALINTA.RU - НОВЫЕ ОТКЛИКИ НА ЗАКАЗЫ'
    ],

    'messages' => [
        'msgRegistrationSuccess' => [
            'body' => 'Вы успешно зарегистрировались на сайте бесплатных объявлений для специалистов по сметному делу &laquo;Valinta.ru&raquo;!',
            'status' => 'success',
        ],
        'msgRegistrationFail' => [
            'body' => 'Произошла ошибка при регистрации.',
            'status' => 'danger',
        ],
        'msgRequestSuccess' => [
            'body' => 'Поздравляем! Ваша заявка успешно отправлена! Наши менеджеры с Вами свяжутся!',
            'status' => 'success',
        ],
        'msgRequestFail' => [
            'body' => 'Произошла ошибка при отправке заявки.',
            'status' => 'danger',
        ],
        'msgRecoverLinkSent' => [
            'body' => 'На ваш e-mail отправлено письмо с ссылкой для смены пароля.',
            'status' => 'info',
        ],
        'msgRecoverPasswordSent' => [
            'body' => 'Поздравляем! Вы успешно сменили пароль.',
            'status' => 'success',
        ],
        'msgRecoverLinkSaveFail' => [
            'body' => 'Произошла ошибка при отправке письма для смены пароля.',
            'status' => 'danger',
        ],
        'msgChangePassFail' => [
            'body' => 'Произошла ошибка при смене пароля.',
            'status' => 'danger',
        ],
        'msgUserChangeVisibilityOnShowSuccess' => [
            'body' => 'Вы показали анкету, если анкета подтверждена, она появится в результатах поиска.',
            'status' => 'success',
        ],
        'msgUserChangeVisibilityOnHideSuccess' => [
            'body' => 'Вы скрыли анкету, она убрана из результатов поиска.',
            'status' => 'warning',
        ],
        'msgUserChangeVisibilityFail' => [
            'body' => 'Произошла ошибка при попытке изменения статуса видимости анкеты.',
            'status' => 'danger',
        ],
        'msgUserUpdateSuccess' => [
            'body' => 'Вы успешно обновили анкету.',
            'status' => 'success',
        ],
        'msgUserUpdateFail' => [
            'body' => 'Произошла ошибка при обновлении данных анкеты.',
            'status' => 'danger',
        ],
    ],

    'statusMessages' => [
        'notAcceptedAndVisible' => 'Сметчик хочет: показать',
        'notAcceptedAndNotVisible' => 'Сметчик хочет: скрыть',
        'acceptedAndVisible' => 'Анкета: показана',
        'acceptedAndNotVisible' => 'Анкета: скрыта',
    ],

    'junctionTablesSetup' => [
        [
            'modelName' => 'UserHasProfessions',
            'modelProp' => 'professions',
            'tableCol' => 'profession_id',
        ],
        [
            'modelName' => 'UserHasSmetaDocs',
            'modelProp' => 'smetaDocs',
            'tableCol' => 'smeta_docs_id',
        ],
        [
            'modelName' => 'UserHasNormativeBases',
            'modelProp' => 'normBases',
            'tableCol' => 'normative_bases_id',
        ],
    ],

    'status' => [
        'pending' => 1,
        'accepted' => 2,
        'rejected' => 3
    ],

    'requestStatus' => [
        1 => 'новая',
        2 => 'обработана'
    ],

    'report' => [
        'defaultReportRange' => '7days',
        'defaultDetailLevel' => 'days',
        'customDateStart' => null,
        'customDateEnd' => null
    ],

    'contentType' => [
        'information' => 1,
        'article' => 2
    ],

    'urlToIpapAttestat' =>  "http://ipap.ru/svedeniya-ob-ipap/vydavaemye-dokumenty",

    /** ограничение на количество просмотров контактов Сметчиков в день */
    'limitShowUserContactsCounter' => 10



];