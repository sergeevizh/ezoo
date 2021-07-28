<!DOCTYPE HTML>
<html lang="ru">
<head>
	<meta charset="utf-8" />
	<base href="{$config->root_url}/"/>
	<title>{$meta_title|escape}</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no, maximum-scale=1, minimum-scale=1">
	<meta name="description" content="{$meta_description|escape}">
	<meta name="keywords"    content="{$meta_keywords|escape}">
	{if isset($canonical)}<link rel="canonical" href="{$config->root_url}{$canonical}"/>{/if}
	{if isset($prev)}<link rel="prev" href="{$config->root_url}{$prev}"/>{/if}
	{if isset($next)}<link rel="next" href="{$config->root_url}{$next}"/>{/if}
	<meta name="cmsmagazine" content="468f70ab91e70c908dc7b4a7dee94232">
	<meta name="it-rating" content="it-rat-c1a5de279a1e954ecc24af76ceb0188e">
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<link rel="icon" href="favicon.ico" type="image/x-icon" />

	<link href="design/{$settings->theme|escape}/css/fonts.min.css" rel="stylesheet" type="text/css" />
	<link href="design/{$settings->theme|escape}/css/normalize.min.css" rel="stylesheet" type="text/css" />
	{if strpos($smarty.server.HTTP_USER_AGENT, "Speed Insights" ) == false}
		<link href="design/{$settings->theme|escape}/js/lib/jquery.fancybox/jquery.fancybox.min.css" rel="stylesheet" type="text/css" />
		<link href="design/{$settings->theme|escape}/js/lib/jquery.formstyler/jquery.formstyler.min.css" rel="stylesheet" type="text/css" />
		<link href="design/{$settings->theme|escape}/js/lib/jquery.mCustomScrollbar/jquery.mCustomScrollbar.min.css" rel="stylesheet" type="text/css" />
		<link href="design/{$settings->theme|escape}/js/lib/jquery.slick/slick.min.css" rel="stylesheet" type="text/css" />
		<link href="design/{$settings->theme|escape}/js/lib/jquery-ui-1.11.4/jquery-ui.min.css" rel="stylesheet" type="text/css" />
		<link href="design/{$settings->theme|escape}/js/lib/jquery.carouselTicker/carouselTicker.min.css" rel="stylesheet" type="text/css" />
	{/if}
	<meta name="cmsmagazine" content="468f70ab91e70c908dc7b4a7dee94232"/>
	<meta name="it-rating" content="it-rat-c1a5de279a1e954ecc24af76ceb0188e"/>
	<link href="design/{$settings->theme|escape}/css/style.css?08052018" rel="stylesheet" type="text/css" />
	<link href="design/{$settings->theme|escape}/css/media.css?08052018" rel="stylesheet" type="text/css" />
	{$colorsTheme = ['default', 'green', 'orange', 'blue']}
	{$colorTheme = 'green'}
	{if $smarty.server.COLOR_THEME && in_array($smarty.server.COLOR_THEME, $colorsTheme)}
		{$colorTheme = $smarty.server.COLOR_THEME}
	{/if}

	<link href="design/{$settings->theme|escape}/css/theme-{$colorTheme}.min.css" rel="stylesheet" type="text/css" />

	<!--[if lt IE 9]>
		<script src="design/{$settings->theme|escape}/js/html5shiv.min.js" type="text/javascript"></script>
		<script src="design/{$settings->theme|escape}/js/respond.min.js" type="text/javascript"></script>
	<![endif]-->
	<script>
		var echo = false;
	</script>
</head>


<body>
	<!--[if lt IE 10]>
	<div class="browse-happy">
		<div class="wrapper">
			<p class="browse-happy__notice">Мы обнаружили, что вы используете <strong>устаревшую версию</strong> браузера Internet Explorer</p>
			<p class="browse-happy__security">Из соображений безопасности этот сайт поддерживает Internet Explorer версии 10 и выше <br>Кроме того, этот и многие другие сайты могут отображаться <strong>некорректно</strong></p>
			<p class="browse-happy__update">Пожалуйста, обновите свой браузер по этой <a href="http://browsehappy.com/" rel="nofollow" target="_blank">ссылке</a></p>
			<p class="browse-happy__recommend">(мы рекомендуем <a href="http://www.google.com/chrome" rel="nofollow" target="_blank">Google Chrome</a>)</p>
		</div>
	</div>
	<![endif]-->

	<header class="header">
		<div class="header__top top-block js-top-block">
			<div class="wrapper">
				<div class="top-block__content">
					<div class="top-block__nav">
						<ul class="top-nav">
							{foreach $pages as $p}
								{* Выводим только страницы из первого меню *}
								{if $p->menu_id == 1}
									<li class="top-nav__item{if $page && $page->id == $p->id}{/if}">
										<a data-page="{$p->id}" href="{$config->root_url}/{$p->url}" class="top-nav__link">{$p->name|escape}</a>
									</li>
								{/if}
							{/foreach}
						</ul>
					</div>

					<div class="top-block__contacts">
						<div class="top-contacts">
                         <a href="tel:+7255" class="top-contacts__phone"> <h5>тел: 7255</h5> </a>
							<span class="top-contacts__schedule">
								<i class="icon icon-schedule"></i> пн-пт 10:00–21:00 сб-вс 10:00-20:00, без выходных
							</span>
						</div>
					</div>

					<div class="top-block__account">
						<div class="account-field">
							{* TODO dev *}
							{if $user}
								<div class="account-field__content">
									<div class="account-field__name">
										<a href="{$config->root_url}/user" class="account-field__name-link">{$user->name}</a>
									</div>

									<div class="account-field__discount">
										<span class="account-field__discount-link">
										{if $user->discount>0}Ваша скидка &mdash; {$user->discount}%{/if}
										</span>
									</div>

									<div class="account-field__logout">
										<a href="{$config->root_url}/user/logout" class="account-field__logout-link">Выйти</a>
									</div>
								</div>
							{else}
								<ul class="account-field__control">
									<li class="account-field__control-item">
										{* TODO dev оформить Вход в pop-up *}
										{*<a href="#login-window" class="account-field__control-link js-popup-link">Вход</a>*}
										<a href="{$config->root_url}/user/login" class="account-field__control-link">Вход</a>
									</li>
									<li class="account-field__control-item">
										<a href="{$config->root_url}/user/register" class="account-field__control-link">Регистрация</a>
										{* TODO dev оформить регестрацию в pop-up *}
										{*<a href="#registration-window" class="account-field__control-link js-popup-link">Регистрация</a>*}
									</li>
								</ul>
							{/if}
						</div>
					</div>
				</div>
			</div>
			<button class="top-block__close js-top-block-close"></button>
		</div>

		<div class="header__content">
			<div class="wrapper">
				<div class="header__content-inner">
					<div class="header__top-block-target">
						<button class="top-block__target js-top-block-target"></button>
					</div>
					<div class="header__logo">
						{if $module == 'MainView'}
							<div class="logo">
								<img src="{$config->root_url}/design/{$settings->theme|escape}/images/logo3.png" title="{$settings->site_name|escape}" alt="{$settings->site_name|escape}" class="logo__image">
							</div>
						{else}
							<a href="{$config->root_url}/" class="logo">
								<img src="{$config->root_url}/design/{$settings->theme|escape}/images/logo3.png" title="{$settings->site_name|escape}" alt="{$settings->site_name|escape}" class="logo__image">
							</a>
						{/if}
						<div class="header__mob-phone">
							<div class="top-contacts top-contacts_mob-header">
								<a href="tel:+7255" class="top-contacts__phone"><img  height="20px" src="{$config->root_url}/design/{$settings->theme|escape}/images/tel-1.png"> 7255</a>
								<!--<span class="top-contacts__schedule">10:00–22:00, без выходных</span>-->
							</div>
						</div>
					</div>
					<div class="header__search">
						<button type="button" class="header__search-btn js-search-field-target-btn">Поиск</button>
						<div class="search-field js-search-field">
							<button type="button" class="search-field__close js-search-field-close"></button>
							<script type="application/ld+json">
							/*<![CDATA[*/
							{
								"@context" : "http://schema.org",
								"@type" : "WebSite",
								"url" : "{$config->root_url}/",
								"potentialAction" : {
									"@type" : "SearchAction",
									"target" : "{$config->root_url}/products?keyword={literal}{search_term}{/literal}",
									"query-input" : "required name=search_term"
								}
							}
							/*]]>*/
							</script>
							<form class="search-field__form js-validation-form" action="products">
								<input type="text" class="search-field__input js-search-autocomplete" placeholder="Введите название товара" name="keyword" value="{$keyword|escape}" required>
								<button type="submit" class="search-field__btn"></button>
							</form>
						</div>
					</div>
					<div class="header__basket">
						<div class="basket-field js-basket-field">
							{include file='cart_informer.tpl'}
						</div>
					</div>
				</div>
			</div>
		</div>
		{include file="_header_nav.tpl"}

	</header>

	{$content}
	<div class="top-mob">
		<button type="button" class="top-mob__link a js-top-link">Наверх</button>
	</div>
	<footer class="footer">
		<div class="wrapper">
			<div class="footer__content">
				<div class="footer__col footer__col_1">
					<ul class="footer-nav">
						{foreach $pages as $p}
							{* Выводим только страницы из первого меню *}
							{if $p->menu_id == 1}
								<li class="footer-nav__item{if $page && $page->id == $p->id}{/if}">
									<a data-page="{$p->id}" href="{$config->root_url}/{$p->url}" class="footer-nav__link">{$p->name|escape}</a>
								</li>
							{/if}
						{/foreach}
					</ul>
				</div>

				<div class="footer__col footer__col_2">
					<a href="tel:+7255">+7255 единый справочный номер</a><br>
				пн-пт: &nbsp;10:00-&nbsp;21:00; сб-вс:10:00-20:00
				</div>

				<div class="footer__col footer__col_3">
					ЧТУП «ЗООХАУЗ»<br>
					УНП 190942323<br>
				</div>

				<div class="footer__col footer__col_4">
					<div class="social">
                    	<img src="{$config->root_url}/design/{$settings->theme|escape}/images/logo1-1.png">
						<img src="{$config->root_url}/design/{$settings->theme|escape}/images/logo-2.png">
					</div>
				</div>
			</div>
		</div>
	</footer>

	<div class="popup-window" id="login-window">
		<form class="js-validation-form">
			<div class="popup-form-row">
				<label class="form-label">Логин</label>
				<input type="text" name="login" class="input-border" required>
			</div>
			<div class="popup-form-row">
				<label class="form-label">Пароль</label>
				<input type="password" name="password" class="input-border" required>
			</div>
			<div class="popup-btn-row">
				<button type="submit" class="btn btn_full">Вход</button>
			</div>
		</form>
	</div>

{*	<div class="popup-window" id="registration-window">
		<form class="js-validation-form">
			<div class="popup-form-row">
				<label class="form-label">Логин</label>
				<input type="text" name="login" class="input-border" required>
			</div>
			<div class="popup-form-row">
				<label class="form-label">E-mail</label>
				<input type="email" name="email" class="input-border" required>
			</div>
			<div class="popup-form-row">
				<label class="form-label">Пароль</label>
				<input type="password" name="password" class="input-border" required>
			</div>
			<div class="popup-form-row">
				<label class="form-label">Повторить пароль</label>
				<input type="password" name="re-password" class="input-border" required>
			</div>
			<div class="popup-btn-row">
				<button type="submit" class="btn btn_full">Регистрация</button>
			</div>
		</form>
	</div>*}

	<!-- <div class="popup-window" id="basket-success-window">
        <h3 style="align-center">Товар добавлен в корзину.</h3>
        <br>

        <a href="#" class="btn btn_full" style="margin-bottom: 1rem;">Перейти в корзину</a>
        <button type="button" class="btn btn_full js-popup-close">Закрыть</button>
    </div> -->

{*	<div class="popup-window" id="password-window">
		<form class="js-validation-form">
			<div class="popup-form-row">
				<label class="form-label">Старый пароль</label>
				<input type="text" name="password" class="input-border" value="123Реоf876" required>
			</div>
			<div class="popup-form-row">
				<label class="form-label">Новый Пароль</label>
				<input type="text" name="new-password" class="input-border" required>
			</div>
			<div class="popup-btn-row">
				<button type="submit" class="btn btn_full">Сохранить</button>
			</div>
		</form>
	</div>*}
	<script type="text/javascript">
		{fetch file="{$config->root_dir}/design/{$settings->theme}/js/echo.min.js"}
		echo.init({
			offset: 100,
			throttle: 250,
			debounce: false,
			unload: false,
			callback: function (element, op) {
				//console.log(element, 'has been', op + 'ed')
			}
		});
	</script>
	<div class="mobile-test"></div>
	{if strpos($smarty.server.HTTP_USER_AGENT, "Speed Insights" ) == false}
		<script src="design/{$settings->theme|escape}/js/lib/jquery/jquery-1.11.1.min.js" type="text/javascript"></script>
		<script src="design/{$settings->theme|escape}/js/jquery.fancybox.min.js" type="text/javascript"></script>
		<script src="design/{$settings->theme|escape}/js/jquery.formstyler.min.js" type="text/javascript"></script>
		<script src="design/{$settings->theme|escape}/js/lib/jquery.inputmask/jquery.inputmask.bundle.js" type="text/javascript"></script>
		<script src="design/{$settings->theme|escape}/js/lib/jquery.mCustomScrollbar/jquery.mCustomScrollbar.concat.min.js" type="text/javascript"></script>
		<script src="design/{$settings->theme|escape}/js/lib/jquery.placeholder/jquery.placeholder.min.js" type="text/javascript"></script>
		<script src="design/{$settings->theme|escape}/js/lib/jquery.slick/slick.min.js" type="text/javascript"></script>
		<script src="design/{$settings->theme|escape}/js/lib/jquery.sticky-kit/jquery.sticky-kit.min.js" type="text/javascript"></script>
		<script src="design/{$settings->theme|escape}/js/lib/jquery.validate/jquery.validate.min.js" type="text/javascript"></script>
		<script src="design/{$settings->theme|escape}/js/lib/jquery-ui-1.11.4/jquery-ui.min.js" type="text/javascript"></script>
		<script src="design/{$settings->theme|escape}/js/lib/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js" type="text/javascript"></script>
		<script src="design/{$settings->theme|escape}/js/lib/jquery.carouselTicker/jquery.carouselTicker.min.js" type="text/javascript"></script>
		<script src="design/{$settings->theme|escape}/js/lib/jquery.autocomplete/jquery.autocomplete.min.js" type="text/javascript"></script>
		<script src="design/{$settings->theme|escape}/js/lib/air-datepicker/datepicker.min.js" type="text/javascript"></script>
		<link href="design/{$settings->theme|escape}/js/lib/air-datepicker/datepicker.css" rel="stylesheet" type="text/css" />
		<script src="design/{$settings->theme|escape}/js/filter.js" type="text/javascript"></script>
		<script src="design/{$settings->theme|escape}/js/scripts.js?14062018" type="text/javascript"></script>
	{/if}
	<style>
		.btn_border.js-sidebar-filter-tooltip br{
			content: "";
		}
		@media only screen and (max-width : 767px) {
			.filter-check-list {
				display: block;
				width: auto;
				padding: 0;
				border-radius: 0;
				background: none;
				max-height: 100%;
				overflow: initial;
			}
			.sidebar-filter [type=submit] {
				display: none;
			}
		}

		.hits__main-item .cart.is-open .cart__imgae-slider-field {
			display: none;
		}

	</style>
{literal}<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
    (function (d, w, c) {
        (w[c] = w[c] || []).push(function() {
            try {
                w.yaCounter49155352 = new Ya.Metrika2({
                    id:49155352,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true,
                    webvisor:true
                });
            } catch(e) { }
        });

        var n = d.getElementsByTagName("script")[0],
            s = d.createElement("script"),
            f = function () { n.parentNode.insertBefore(s, n); };
        s.type = "text/javascript";
        s.async = true;
        s.src = "https://mc.yandex.ru/metrika/tag.js";

        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f, false);
        } else { f(); }
    })(document, window, "yandex_metrika_callbacks2");
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/49155352" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->{/literal}
</body>
</html>
