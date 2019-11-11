<?php
/**
 * @var \app\models\Customer $customer
 */
?>

<style>
    table tr td:first-child {
        text-align: right;
        font-weight: bold;
    }

    table tr td:last-child {
        padding-left: 10px;
    }
</style>

<div class="mainBlock">
    <div class="header">
        <div class="contactsBlock">
            <p>Телефон: <?= Yii::$app->params['mainSystemPhone'] ?></p>
            <p>E-mail: <a href="mailto:<?= Yii::$app->params['mailFrom']; ?>"><?= Yii::$app->params['mailFrom']; ?></a>
            </p>
        </div>
    </div>
    <h2 style="font-family: Arial, sans-serif; font-weight: 400;background-color: #1083ab;padding: 10px 0;color: #FFF; text-align: center;">Уважаемый(ая) <?= $customer->name; ?>!</h2>
    <p style="font-family: Arial, sans-serif;">С радостью сообщаем, что вы зарегистрированы на сайте бесплатных объявлений для специалистов по сметному делу
        «Valinta.ru». Ваш профиль заказчика проходит проверку нашими менеджерами. Внести необходимые изменения в свой
        профиль вы всегда можете в Личном кабинете заказчика (ЛК).</p>
    <p>Параметры доступа в ЛК:</p>

    <table cellpadding="5">
        <tr>
            <td>Логин:</td>
            <td><?= $customer->email; ?></td>
        </tr>
    </table>

    <hr/>
    <p class="regards" style="font-family: Arial, sans-serif;font-size: 16px;font-style: italic;text-align: right;">С уважением,<br/>команда Valinta.ru</p>
</div>
