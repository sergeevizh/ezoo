{* Страница регистрации *}

{* Канонический адрес страницы *}
{$canonical="/user/register" scope=parent}
{$meta_title = "Регистрация" scope=parent}

<section class="section">
	<div class="wrapper">
		<h1>Регистрация</h1>

		<br>

		<form class="js-validation-form registration-form" method="post" style="max-width: 400px;margin: 0 auto;">
			{if $error}
				<div class="error-message">
					{if $error == 'empty_name'}<div class="error-message__item">Введите имя</div>
					{elseif $error == 'empty_email'}<div class="error-message__item">Введите email</div>
					{elseif $error == 'empty_password'}<div class="error-message__item">Введите пароль</div>
					{elseif $error == 'user_exists'}<div class="error-message__item">Пользователь с таким email уже зарегистрирован</div>
					{elseif $error == 'police'}<div class="error-message__item">Необходимо согласие на обработку персональных данных</div>
					{elseif $error == 'captcha'}<div class="error-message__item">Неверно введена капча</div>
					{else}{$error}{/if}
				</div>
			{/if}
			<div class="popup-form-row">
				<label class="form-label">Имя</label>
				<input type="text" name="name" class="input-border" value="{$name|escape}" maxlength="255" required>
			</div>
			<div class="popup-form-row">
				<label class="form-label">E-mail</label>
				<input type="email" name="email" class="input-border" required value="{$email|escape}" maxlength="255">
			</div>
			<div class="popup-form-row">
				<label class="form-label">Адрес</label>
				<input type="address" name="address" class="input-border" required value="{$address|escape}" maxlength="255">
			</div>
			<div class="popup-form-row">
				<label class="form-label">Пароль</label>
				<input type="password" name="password" class="input-border" required>
			</div>
			<div class="check-row">
				<label>
					<input type="checkbox" checked="checked" class="js-styler js-filter-brands-checkbox" name="brand" style="position: absolute; z-index: -1; opacity: 0; margin: 0px; padding: 0px;" required>
					<span class="check-row__text">Вы даёте согласие на обработку персональных данных.</span>
				</label>
			</div>
			{* TODO Паша - отказываемся ?
			<div class="popup-form-row">
				<label class="form-label">Повторить пароль</label>
				<input type="password" name="re-password" class="input-border" required>
			</div>
			*}
			<div class="popup-btn-row">
				<button type="submit" class="btn btn_full" name="register" value="Регистрация">Регистрация</button>
			</div>
		</form>
	</div>
</section>
