<?php $link = $host.'/recover' ?>
<div class="mainBlock">
	<h2>Уважаемый(ая) <?= $user->fio ?>!</h2>
	<p>Чтобы сменить пароль, перейдите по ссылке:</p>
	<p><a href="<?= $link ?>"><?= $link ?></a></p>
	<p>Далее, в отобразившейся форме «Восстановление пароля» укажите Ваш логин (e-mail) и нажмите кнопку «Восстановить». Затем, следуйте дальнейшим инструкциям.</p>
	<hr />
	<p class="regards">С уважением,<br />команда Valinta.ru</p>
    <div style='font-size: 14px; text-align: center;'>
        <p style='font-size: 14px; text-align: center;'>Данное письмо создано и отправлено автоматически, не нужно на него отвечать.</p>
    </div>
</div>