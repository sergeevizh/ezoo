{* Страница с формой обратной связи *}

{* Канонический адрес страницы *}
{$canonical="/{$page->url}" scope=parent}

<section class="section">
	<div class="wrapper">
		<h1>Самовывоз</h1>

		<div class="contacts">
			<div class="contacts__col">
				<div class="contacts__block-title">Пункты самовывоза</div>
				<p>Сеница 68/8<br> пн-пт с 10:00 до 21:00<br>сб-вс 10:00 до 20:00<br>на МКАД съезд на Слуцкую трассу</p>
				
		</div>

			<div class="contacts__col">
				<div class="contacts__block-title">Колл-центр</div>
				<p>Единый справочный номер 7255</p>
				<p>Пишите: infoezoo.by@gmail.com</p>

				<!--<div class="contacts__block-title">Мы в социальных сетях</div>
				<div class="social contacts__social">
					<a href="#" class="social__link social__link_vk"></a>
					<a href="#" class="social__link social__link_tw"></a>
					<a href="#" class="social__link social__link_fb"></a>
				</div>-->
			</div>

			<div class="contacts__col">
				<div class="contacts__block-title">Юридический адрес и реквизиты</div>
				<p>Частное торговое унитарное предприятие «ЗООХАУЗ»</p>
				<p>г. Минск, ул. Уручская, д. 19, пом. 1, ком. 6<br> УНП 190942323 ОКПО 377725075000</p>
			</div>
		</div>
	</div>
</section>

<div class="wrapper">
	<h2>Пункты самовывоза</h2>

	<div class="contacts-map-header">
		<div class="contacts-map-nav">
			<span class="contacts-map-nav__title">Пункты самовывоза</span>
			<a href="#minsk-shop-map" class="contacts-map-nav__link is-active">в Минске</a>
		</div>
	</div>
</div>

<div class="contacts-map-block">
	<div class="contacts-map" id="minsk-shop-map" style="display: block;"><script type="text/javascript" charset="utf-8" async src="https://api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3A7172f381ba8ee2170c1ba21bc5b836020ede2e6092130f0c9f88d8a2632bf3be&amp;width=100%&amp;height=100%&amp;lang=ru_RU&amp;sourceType=constructor&amp;scroll=false"></script></div>
</div>

<section class="section section_bg">
	<div class="wrapper">
		<div class="add-comments-block" id="add-comments-block">
			<h2 class="add-comments-block__title">Напишите нам</h2>
			{if $message_sent}
				{* TODO frond-end сообщение об отправки *}
				<div class="success-message">
					<p>{$name|escape}, ваше сообщение отправлено.</p>
				</div>
			{else}
				<form class="add-comments-block__form js-validation-form" method="post">
				{if $error}
					<div class="error-message">
						{if $error=='captcha'}
							Неверно введена капча
						{elseif $error=='empty_name'}
							Введите имя
						{elseif $error=='empty_email'}
							Введите email
						{elseif $error=='empty_text'}
							Введите сообщение
						{/if}
					</div>
				{/if}
				<div class="form-row">
					<input placeholder="Имя" value="{$name|escape}" name="name" maxlength="255" type="text" required>
				</div>
				<div class="form-row">
					<input placeholder="Email" value="{$email|escape}" name="email" maxlength="255" type="text" required>
				</div>
				<div class="form-row">
					<textarea name="message" placeholder="Например: Ищу редкий ветеринарный корм. Светлана". name="message" required>{$message|escape}</textarea>
				</div>
				{* TODO оформление каптчи
				<div class="form-row">
					<img src="captcha/image.php?{math equation='rand(10,10000)'}"/>
					<input type="text" name="captcha_code" value="" required/>
				</div>
				*}
				<div class="form-row">
					<button type="submit" class="btn" name="feedback" value="Отправить">Отправить</button>
				</div>
			</form>
			{/if}
		</div>
	</div>
</section>
