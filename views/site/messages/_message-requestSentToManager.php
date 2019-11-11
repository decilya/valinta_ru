<?php
/**
 *@var  \app\models\Request $request
 */
?>
<div class="mainBlock">

	<h2>Уважаемый &laquo;Менеджер&raquo;!</h2>
	<p>На сайте бесплатных объявлений для специалистов сметного дела «Valinta.ru» сформирована новая заявка №<?= $request->id ?> на доступ к ПП SmetaWIZARD.</p>

	<p>Параметры заявки:</p>
	<ul>
		<li><strong>Как вас зовут: </strong><?= $request->fio ?></li>
		<li><strong>E-mail: </strong><?= $request->email ?></li>
		<li><strong>Мобильный телефон: </strong><?= $request->phone ?></li>
        <?php if ($request->inn  != ''){ ?>
        <li><strong>ИНН: </strong><?= $request->inn ?></li>
        <?php } ?>
        <li><strong>Нормативные базы: </strong><?php

            /** @var \app\models\Database $item */
            foreach ($request->databases as $item){
                echo $item->name; echo "; ";
            }

            ?></li>
        <li><strong>Количество дней доступа: </strong><?= $request->access_days; ?></li>
        <li><strong>Желаемая дата доступа: </strong><?= date('d-m-Y', $request->desired_date); ?></li>
        <li><strong>Стоимость заказа: </strong><?= $request->cost; ?></li>

        <?php if ($request->comment != '') { ?>
        <li><strong>Запрос на добавление баз: </strong><?= $request->comment; ?></li>
        <?php } ?>
	</ul>

	<hr />
	<p>С уважением,	<br />команда Valinta.ru</p>
    <div style='font-size: 14px; text-align: center;'>
        <p style='font-size: 14px; text-align: center;'>Данное письмо создано и отправлено автоматически, не нужно на него отвечать.</p>
    </div>
</div>