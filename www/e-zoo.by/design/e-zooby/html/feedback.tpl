{* Страница с формой обратной связи *}

{* Канонический адрес страницы *}
{$canonical="/{$page->url}" scope=parent}

<section class="section">
	<div class="wrapper">
		<h1>Контакты</h1>

		<div class="contacts">
			<div class="contacts__col">
				<div class="contacts__block-title">Пункты самовывоза</div>
				<p>• Сеница 68/11, Минск, МКАД,<br>съезд на Слуцкую трассу<br>Координаты на Яндекс Картах 53.835383, 27.527388</p>
				<p>• ТЦ "Яркий" Большое Стиклево, 126<br></p>
				<p>Пн-Вс с 10:00 до 21:00</p>
				<p>• ТЦ "ALL House", Долгиновский тр-т 188</p>
				<p>• ул.Минская, 109-1, Бобруйск<br></p>
				<p>• пр-т. Победы, 14, Гомель<br></p>
				<p>• ул.Полиграфистов 2а, Гродно<br></p>
				<p>• ул. Советских Пограничников, д.42, пом.1, Брест<br></p>
				<p>Пн-Вс с 09:00 до 21:00</p>

             <p> <br> <br> <p>
		</div>

			<div class="contacts__col">
				<div class="contacts__block-title">Колл-центр</div>
				<p><a href="tel:{$phone}">Звоните: 7255 {$phone}</a></p>
				<p><a href="tel:80447313414">А1 80447313414</a></p>
				<p><a href="tel:80336531456">МТС 80336531456</a></p>
				<p><a href="tel:80256007842">Life 80256007842</a></p>
{*				<p>Единый справочный номер 7255</p>*}

				<p>Пишите: infoezoo.by@gmail.com</p>
				<p>Написать руководителю: pm@zoomarket.by</p>

				<div class="contacts__block-title"><br><br>Мы в социальных сетях</div>
				<div class="social contacts__social">
					<a href="https://vk.com/ezooby" class="social__link social__link_vk"></a>
					<a href="https://www.facebook.com/ezoo.by/" class="social__link social__link_fb"></a>
                    <a href="https://www.instagram.com/ezoo.by/" class="social__link social__link_tw"></a>
                    <a href="https://ok.ru/group/59858431377442" class="social__link social__link_ok"></a>
                    <a href="https://play.google.com/store/apps/details?id=by.ezoo.android" class="social__link social__link_pm"></a>
				</div>

			</div
			<div class="contacts__col">
				<div class="contacts__block-title">Юридический адрес и реквизиты</div>
				<p>Частное торговое унитарное предприятие «ЗООХАУЗ»</p>
				<p>220073, г. Минск, ул. Бирюзова, д.4/5, офис 2024<br><br> УНП 190942323<br> ОКПО 377725075000</p>
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
			<h2 class="add-comments-block__title">Напишите ваш отзыв или предложение, и мы обязательно с вами свяжемся!</h2>
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
					<div class="g-recaptcha" data-sitekey="6Lcxgb8UAAAAACu24hMG5je1TfPKIc_lFOhMgvN_"></div>
				</div>
				<div class="form-row">
					<button type="submit" class="btn" name="feedback" value="Отправить">Отправить</button>
				</div>
			</form>
				<script src='https://www.google.com/recaptcha/api.js'></script>
			{/if}
		</div>
	</div>
</section>
