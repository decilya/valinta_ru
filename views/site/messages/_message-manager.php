<?php /** @var \app\models\User $user */?>
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
<p>На сайте бесплатных объявлений для специалистов сметного дела «Valinta.ru» размещена новая анкета.</p>

<table cellpadding="5">
	<tr>
		<th>
			Данные анкеты:
		</th>
	</tr>
	<tr>
		<td>№ Анкеты:</td><td><?= $user->real_id ?></td>
	</tr>
	<tr>
		<td>Дата/Время создания:</td><td><?= date('d.m.Y H:i:s',$user->date_created) ?></td>
	</tr>
	<tr>
		<td>ФИО:</td><td><?= $user->fio ?></td>
	</tr>
	<tr>
		<td>Телефон:</td><td><?= $user->phone ?><?php if(!empty($user->extraPhones)) foreach($user->extraPhones as $phone) if(!empty($phone)) echo ", ".$phone; ?></td>
	</tr>
	<tr>
		<td>E-mail:</td><td><?= $user->email ?></td>
	</tr>
	<tr>
		<td>Профессиональная область: </td>
		<td>
			<?php
				$cnt = 1;

				foreach($user->professions as $k => $v){

					$comma = ($cnt !== count($user->professions)) ? ', ' : '' ;

					echo "<span>".$staticDBsContent['professions'][$v]['title'].$comma."</span>";

					$cnt++;
				}
			?>
		</td>
	</tr>

	<?php if(!empty($user->smetaDocs)) : ?>

		<tr>
			<td>Сметные документы: </td>
			<td>
				<?php
				$cnt = 1;

				foreach($user->smetaDocs as $k => $v){

					$comma = ($cnt !== count($user->smetaDocs)) ? ', ' : '' ;

					echo "<span>".$staticDBsContent['smetaDocs'][$v]['title'].$comma."</span>";

					$cnt++;
				}
				?>
			</td>
		</tr>

	<?php endif; ?>

	<?php if(!empty($user->normBases)) : ?>

		<tr>
			<td>Нормативные базы: </td>
			<td>
				<?php
				$cnt = 1;

				foreach($user->normBases as $k => $v){

					$comma = ($cnt !== count($user->normBases)) ? ', ' : '' ;

					echo "<span>".$staticDBsContent['normBases'][$v]['title'].$comma."</span>";

					$cnt++;
				}
				?>
			</td>
		</tr>

	<?php endif; ?>
</table>
<hr />
<p class="regards">С уважением,<br />команда Valinta.ru</p>
<div style='font-size: 14px; text-align: center;'>
    <p style='font-size: 14px; text-align: center;'>Данное письмо создано и отправлено автоматически, не нужно на него отвечать.</p>
</div>
