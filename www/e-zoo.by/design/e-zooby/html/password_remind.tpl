{* Письмо пользователю для восстановления пароля *}

{* Канонический адрес страницы *}
{$canonical="/user/password_remind" scope=parent}
{$meta_title = "Напоминание пароля" scope=parent}

<section class="section">
	<div class="wrapper">

		{if $email_sent}
			<h1>Вам отправлено письмо</h1>
			<br>
			<p>На {$email|escape} отправлено письмо для восстановления пароля.</p>
		{else}
			<h1>Напоминание пароля</h1>
			<br>
			{if $error}
				<div class="error-message">
					{if $error == 'user_not_found'}<div class="error-message__item">Пользователь не найден</div>
					{else}<div class="error-message__item">{$error}</div>{/if}
				</div>
			{/if}

			<form class="js-validation-form" method="post" style="max-width: 400px;margin: 0 auto;">
				<div class="popup-form-row">
					<label class="form-label">Введите email</label>
					<input type="email" name="email" class="input-border" required value="{$email|escape}" maxlength="255">
				</div>
				<div class="popup-btn-row">
					<button type="submit" class="btn btn_full" value="Вспомнить">Вспомнить</button>

				</div>
			</form>
		{/if}

	</div>
</section>



