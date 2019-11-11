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

<h2>Уважаемый менеджер!</h2>
<p>На сайте бесплатных объявлений для специалистов сметного дела «Valinta.ru» создан новый профиль заказчика.</p>
<p><b>Данные профиля:</b></p>
<table cellpadding="5">

    <tr>
        <td>№ профиля:</td>
        <td><?= $customer->real_id; ?></td>
    </tr>
    <tr>
        <td>Дата/Время создания:</td>
        <td><?= date('d.m.Y H:i:s', $customer->created_at) ?></td>
    </tr>
    <tr>
        <td>ФИО:</td>
        <td><?= $customer->name ?></td>
    </tr>
    <tr>
        <td>E-mail:</td>
        <td><?= $customer->email ?></td>
    </tr>

    <?php

    $customerPhonesRows = false;

    if (isset($customer->customerPhones)) {
        foreach ($customer->customerPhones as $customerPhone) {

            if ($customerPhone) {
                $customerPhonesRows = true; ?>

                <tr>
                    <td>Телефон:</td>
                    <td>
                        <?= $customerPhone; ?>
                    </td>
                </tr>
                <?php
            }
        }
    }

    // Старая реализация, вдруг осталось где так
    if (!$customerPhonesRows) {

        $customer = \app\models\Customer::find()->with('customerPhones')->where(['email' => $customer->email])->one();

        $realCustomerPhones = $customer->getRelatedRecords();

        if (isset($realCustomerPhones['customerPhones'])) {
            foreach ($realCustomerPhones['customerPhones'] as $customerPhone) { ?>

                <tr>
                    <td>Телефон:</td>
                    <td>
                        <?= $customerPhone->phone; ?>
                    </td>
                </tr>

                <?php

            }
        }

    }


    ?>

</table>
<hr/>
<p class="regards">С уважением,<br/>команда Valinta.ru</p>