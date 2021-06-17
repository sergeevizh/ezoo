{* Страница входа пользователя *}

{* Канонический адрес страницы *}
{$canonical="/user/login" scope=parent}
{$meta_title = "Вход" scope=parent}

<section class="section">
	<div class="wrapper">
		<h1>Вход</h1>

		<br>

		<form class="js-validation-form" method="post" style="max-width: 400px;margin: 0 auto;">
			{if $error}
				<div class="error-message">
					{if $error == 'login_incorrect'}<div class="error-message__item">Неверный логин или пароль</div>
					{elseif $error == 'user_disabled'}<div class="error-message__item">Ваш аккаунт еще не активирован.</div>
					{else}{$error}{/if}
				</div>
			{/if}

			<div class="popup-form-row">
				<label class="form-label">Email</label>
				<input type="email" name="email" class="input-border" required value="{$email|escape}" maxlength="255">
			</div>
			<div class="popup-form-row">
				<label class="form-label">Пароль (<a href="{$config->root_url}/user/password_remind">востановить</a>)</label>
				<input type="password" name="password" class="input-border" required>
			</div>
			<div class="popup-btn-row">
				<button type="submit" class="btn btn_full" name="login" value="Войти">Вход</button>

			</div>
		</form>
	</div>
</section>
