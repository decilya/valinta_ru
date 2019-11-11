
<style>

	table tr td:first-child{
		text-align:right;
		font-weight: bold;
	}
	table tr td:last-child{
		padding-left:10px;
	}
</style>

<h2>Уважаемый менеджер!</h2>
<p>На портале «Valinta.ru» обновлен заказ №<?= $order->id ?>.</p>

<table cellpadding="5">
	<tr>
		<th>
			Данные заказа:
		</th>
	</tr>
	<tr>
		<td>№ заказа:</td><td><?= $order->id ?></td>
	</tr>
	<tr>
		<td>Дата/Время обновления:</td><td><?= date('d.m.Y H:i:s',$order->updated_at) ?></td>
	</tr>
	<tr>
		<td>Название заказа:</td><td><?= $order->name ?></td>
	</tr>
	<tr>
		<td>ФИО:</td><td><?= $order->fio ?></td>
	</tr>
	<tr>
		<td>Телефон:</td><td><?= $order->phone ?><?php if(!empty($order->extraPhones)) foreach($order->extraPhones as $phone) if(!empty($phone)) echo ", ".$phone; ?></td>
	</tr>
	<tr>
		<td>E-mail:</td><td><?= $order->email ?></td>
	</tr>
	<tr>
		<td>Профессиональная область: </td>
		<td>
			<?php
				$cnt = 1;

				foreach($order->professions as $k => $v){

					$comma = ($cnt !== count($order->professions)) ? ', ' : '' ;

					echo "<span>".$staticDBsContent['professions'][$v]['title'].$comma."</span>";

					$cnt++;
				}
			?>
		</td>
	</tr>

	<?php if(!empty($order->smetaDocs)) : ?>

		<tr>
			<td>Сметные документы: </td>
			<td>
				<?php
				$cnt = 1;

				foreach($order->smetaDocs as $k => $v){

					$comma = ($cnt !== count($order->smetaDocs)) ? ', ' : '' ;

					echo "<span>".$staticDBsContent['smetaDocs'][$v]['title'].$comma."</span>";

					$cnt++;
				}
				?>
			</td>
		</tr>

	<?php endif; ?>

	<?php if(!empty($order->normBases)) : ?>

		<tr>
			<td>Нормативные базы: </td>
			<td>
				<?php
				$cnt = 1;

				foreach($order->normBases as $k => $v){

					$comma = ($cnt !== count($order->normBases)) ? ', ' : '' ;

					echo "<span>".$staticDBsContent['normBases'][$v]['title'].$comma."</span>";

					$cnt++;
				}
				?>
			</td>
		</tr>

	<?php endif; ?>

	<tr>
		<td>Содержание заказа:</td><td><?= $order->text ?></td>
	</tr>

	<tr>
		<td>Бюджет (руб.):</td><td><?= (!empty($order->price)) ? $order->price : 'по договоренности' ?></td>
	</tr>
</table>
<hr />
<p class="regards">С уважением,<br />команда Valinta.ru</p>
<div style='font-size: 14px; text-align: center;'>
    <p style='font-size: 14px; text-align: center;'>Данное письмо создано и отправлено автоматически, не нужно на него отвечать.</p>
</div>