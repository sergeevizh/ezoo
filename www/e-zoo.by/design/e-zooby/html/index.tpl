<!DOCTYPE HTML>
<html lang="ru">
<head>
	<meta charset="utf-8"/>
	<base href="{$config->root_url}/"/>
	<title>{$meta_title|escape}{if $current_page_num > 1} | Страница №{$current_page_num}{/if}</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
	<meta name="viewport"
		  content="width=device-width, initial-scale=1, user-scalable=no, maximum-scale=1, minimum-scale=1">
	<meta name="description" content="{$meta_description|escape}{if $current_page_num > 1} Страница №{$current_page_num}{/if}">
	<meta name="keywords" content="{$meta_keywords|escape}">
	{if isset($canonical)}
		<link rel="canonical" href="{$config->root_url}{$canonical}"/>{/if}
	{if isset($prev)}
		<link rel="prev" href="{$config->root_url}{$prev}"/>{/if}
	{if isset($next)}
		<link rel="next" href="{$config->root_url}{$next}"/>{/if}

	<meta property="og:title" content="{if $module == 'ProductView'}{$product->name|escape}{else}{$meta_title|escape}{/if}" />
	<meta property="og:type" content="website" />
	<meta property="og:url" content="{$config->root_url}{url}" />
	<meta property="og:image" content="{if $module == 'ProductView'}{$product->image->filename|resize:330:300}{else}{$config->root_url}/design/e-zooby/images/logo3.png{/if}" />
	{if $module == 'ProductView'}<meta property="og:description" content="{$product->annotation|strip_tags}" />{/if}


	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
	<link rel="icon" href="favicon.ico" type="image/x-icon"/>

	<link href="design/{$settings->theme|escape}/css/fonts.min.css" rel="stylesheet" type="text/css"/>
	<link href="design/{$settings->theme|escape}/css/normalize.min.css" rel="stylesheet" type="text/css"/>
	{if strpos($smarty.server.HTTP_USER_AGENT, "Speed Insights" ) == false}
		<link href="design/{$settings->theme|escape}/js/lib/jquery.fancybox/jquery.fancybox.min.css" rel="stylesheet"
			  type="text/css"/>
		<link href="design/{$settings->theme|escape}/js/lib/jquery.formstyler/jquery.formstyler.min.css"
			  rel="stylesheet" type="text/css"/>
		<link href="design/{$settings->theme|escape}/js/lib/jquery.mCustomScrollbar/jquery.mCustomScrollbar.min.css"
			  rel="stylesheet" type="text/css"/>
		<link href="design/{$settings->theme|escape}/js/lib/jquery.slick/slick.min.css" rel="stylesheet"
			  type="text/css"/>
		<link href="design/{$settings->theme|escape}/js/lib/jquery-ui-1.11.4/jquery-ui.min.css" rel="stylesheet"
			  type="text/css"/>
		<link href="design/{$settings->theme|escape}/js/lib/jquery.carouselTicker/carouselTicker.min.css"
			  rel="stylesheet" type="text/css"/>
	{/if}
<!--	<link href="design/{$settings->theme|escape}/js/lib/libs.min.css" rel="stylesheet" type="text/css"/>-->
	<meta name="yandex-verification" content="0075d453e22714ef"/>
	<meta name="google-site-verification" content="fw6x1dqL7fxy0DrE7hXrqzyvK5mrSED07rCmb4BTakE" />
	<meta name="google-site-verification" content="mStYj6LFGHIT4DsleR1Dt11qWcTfNGC8PnTBBHP3A1c" />
	<meta content="11e74cdc0b23fc411063e4f8b9d668be" />
	<meta name="cmsmagazine" content="468f70ab91e70c908dc7b4a7dee94232"/>
	<meta name="cmsmagazine" content="11c9463b9bd5970e1f7da9649e12a0c1"/>
	<meta name="it-rating" content="it-rat-c1a5de279a1e954ecc24af76ceb0188e"/>
	<link href="design/{$settings->theme|escape}/css/style.css?04122020" rel="stylesheet" type="text/css"/>
	<link href="design/{$settings->theme|escape}/css/media.css?20112020" rel="stylesheet" type="text/css"/>
	{$colorsTheme = ['default', 'green', 'orange', 'blue']}
	{$colorTheme = 'green'}
	{if $smarty.server.COLOR_THEME && in_array($smarty.server.COLOR_THEME, $colorsTheme)}
		{$colorTheme = $smarty.server.COLOR_THEME}
	{/if}

	<link href="design/{$settings->theme|escape}/css/theme-{$colorTheme}.min.css" rel="stylesheet" type="text/css"/>

	<!--[if lt IE 9]>
		<script src="design/{$settings->theme|escape}/js/html5shiv.min.js" type="text/javascript"></script>
		<script src="design/{$settings->theme|escape}/js/respond.min.js" type="text/javascript"></script>
	<![endif]-->
	<script>
		var echo = false,
				currentHours = {if $smarty.get.currentHours}{$smarty.get.currentHours}{else}{date('H')}{/if};
	</script>

	<meta name="yandex-verification" content="3035ca30538e8aa4"/>
	{literal}
		<!-- Google Tag Manager -->
		<script>(function (w, d, s, l, i) {
				w[l] = w[l] || [];
				w[l].push({
					'gtm.start':
							new Date().getTime(), event: 'gtm.js'
				});
				var f = d.getElementsByTagName(s)[0],
						j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
				j.async = true;
				j.src =
						'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
				f.parentNode.insertBefore(j, f);
			})(window, document, 'script', 'dataLayer', 'GTM-WK6H6MF');</script>
		<!-- End Google Tag Manager -->
		<!-- Global site tag (gtag.js) - Google Analytics -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=UA-125077442-2"></script>
		<script>
			window.dataLayer = window.dataLayer || [];

			function gtag() {
				dataLayer.push(arguments);
			}

			gtag('js', new Date());
			gtag('config', 'UA-125077442-2');
		</script>
	{/literal}
	<script src="//code.jivosite.com/widget.js" jv-id="px2s1mVaRI" async></script>
	{literal}

	<!-- Facebook Pixel Code -->
	<script>
		!function(f,b,e,v,n,t,s)
				{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
				n.callMethod.apply(n,arguments):n.queue.push(arguments)};
				if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
				n.queue=[];t=b.createElement(e);t.async=!0;
				t.src=v;s=b.getElementsByTagName(e)[0];
				s.parentNode.insertBefore(t,s)}(window, document,'script',
				'https://connect.facebook.net/en_US/fbevents.js');
		fbq('init', '1707249842760515');
		fbq('track', 'PageView');
	</script>
	<noscript><img height="1" width="1" style="display:none"
				   src="https://www.facebook.com/tr?id=1707249842760515&ev=PageView&noscript=1"
		/></noscript>
	<!-- End Facebook Pixel Code -->
	{/literal}

	{literal}
		<!-- Facebook Pixel Code -->
		<script>
			!function (f, b, e, v, n, t, s) {
				if (f.fbq) return;
				n = f.fbq = function () {
					n.callMethod ?
							n.callMethod.apply(n, arguments) : n.queue.push(arguments)
				};
				if (!f._fbq) f._fbq = n;
				n.push = n;
				n.loaded = !0;
				n.version = '2.0';
				n.queue = [];
				t = b.createElement(e);
				t.async = !0;
				t.src = v;
				s = b.getElementsByTagName(e)[0];
				s.parentNode.insertBefore(t, s)
			}(window, document, 'script',
					'https://connect.facebook.net/en_US/fbevents.js');
			fbq('init', '2289801434665407');
			fbq('track', 'PageView');
		</script>
		<noscript><img height="1" width="1" style="display:none"
					   src="https://www.facebook.com/tr?id=2289801434665407&ev=PageView&noscript=1"
			/></noscript>
	{/literal}
	{literal}
	<script type="text/javascript">!function(){var t=document.createElement("script");t.type="text/javascript",t.async=!0,t.src="https://vk.com/js/api/openapi.js?168",t.onload=function(){VK.Retargeting.Init("VK-RTRG-483798-a8n5Q"),VK.Retargeting.Hit()},document.head.appendChild(t)}();</script><noscript><img src="https://vk.com/rtrg?p=VK-RTRG-483798-a8n5Q" style="position:fixed; left:-999px;" alt=""/></noscript>
	{/literal}
	{literal}
		<!-- End Facebook Pixel Code -->
		<!-- Global site tag (gtag.js) - Google Ads: 697955346 -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=AW-697955346"></script>
		<script>
			window.dataLayer = window.dataLayer || [];

			function gtag() {
				dataLayer.push(arguments);
			}

			gtag('js', new Date());

			gtag('config', 'AW-697955346');
		</script>
	{/literal}

	{if $module == 'ProductsView' || $module == 'ProductView'}

		{if $product->variants|count >= 1 }

			{foreach $product->variants as $v}

				<script>

					console.log({$v->id});

					gtag('event', 'view_item', {ldelim}
						'send_to': 'AW-697955346',
						'value': {$v->price},
						'items': [{ldelim}
							'id': {$v->id},
							'google_business_vertical': 'custom'
						{rdelim}]
					{rdelim});
				</script>

			{/foreach}

		{/if}

	{/if}

	{if $module != 'ProductView'}
	{literal}
	<script type="application/ld+json">

		{
			"@context": "http://schema.org",
			"@type": "Organization",
			"name": "e-zoo.by",
			"address": {
				"@type": "PostalAddress",
				"addressLocality": "Минск",
				"postalCode": "220073",
				"streetAddress": "ул. Бирюзова, д.4/5, офис 2024"
			},
			"email": "infoezoo.by@gmail.com",
			"telephone": "7755",
			"aggregateRating": {
				"@type": "AggregateRating",
				"ratingValue": "4",
				"ratingCount": "20"
			}
		}

	</script>
	{/literal}
	{else}
	{literal}
	<script type="application/ld+json">

		{
			"@context": "http://schema.org",
			"@type": "Organization",
			"name": "e-zoo.by",
			"address": {
				"@type": "PostalAddress",
				"addressLocality": "Минск",
				"postalCode": "ул. Бирюзова, д.4/5, офис 2024",
				"streetAddress": "ул. Бирюзова, д.4/5, офис 2024"
			},
			"email": "infoezoo.by@gmail.com",
			"telephone": "7755"
		}

	</script>
	{/literal}
	{/if}
	{literal}
	<script>
        (function(d) {
            var s = d.createElement('script');
            s.defer = true;
            s.src = 'https://multisearch.io/plugin/11416';
            if (d.head) d.head.appendChild(s);
        })(document);
	</script>
	{/literal}
</head>
<body>
<!-- Google Tag Manager (noscript) -->
<noscript>
	<iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WK6H6MF"
			height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>
<!-- End Google Tag Manager (noscript) -->
<!--[if lt IE 10]>
<div class="browse-happy">
	<div class="wrapper">
		<p class="browse-happy__notice">Мы обнаружили, что вы используете <strong>устаревшую версию</strong> браузера
			Internet Explorer</p>
		<p class="browse-happy__security">Из соображений безопасности этот сайт поддерживает Internet Explorer версии 10
			и выше <br>Кроме того, этот и многие другие сайты могут отображаться <strong>некорректно</strong></p>
		<p class="browse-happy__update">Пожалуйста, обновите свой браузер по этой <a href="http://browsehappy.com/"
																					 rel="nofollow" target="_blank">ссылке</a>
		</p>
		<p class="browse-happy__recommend">(мы рекомендуем <a href="http://www.google.com/chrome" rel="nofollow"
															  target="_blank">Google Chrome</a>)</p>
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
									<a data-page="{$p->id}" href="{$config->root_url}/{$p->url}"
									   class="top-nav__link">{$p->name|escape}</a>
								</li>
							{/if}
						{/foreach}
							<li class="top-nav__item">
								<a data-page="" href="{$config->root_url}/bonus"
									   class="top-nav__link">Бонусы</a>
							</li>
					</ul>
				</div>

				<div class="top-block__contacts">
					<div class="top-contacts">

						<a href="tel:{$phone_shop}" class="top-contacts__phone">
							<div class="h5">Тел: {$phone_shop}</div>
						</a>

						<span class="top-contacts__schedule" style=" margin-right: 2rem;">

								<i class="icon icon-schedule"></i> Пн-Вс 09:00–21:00
							</span>

					</div>

				</div>

				<div class="top-block__account">
					<div class="account-field">

						{* /*regions*/ *}
						<div class="account-map">
							<style>
								#regions-window ul{
									width: 100%;
									columns: 3;
									list-style: none;
								}
								#regions-window .popup-form-row {
									margin-bottom: 0.5rem;
								}

								#regions-window .popup-form-row input[type=radio] {
									margin-right: 15px;
									width: 18px;
									height: 18px;
									vertical-align: middle;
								}

								#regions-window .popup-form-row label {
									cursor: pointer;
								}

								#regions-window .popup-btn-row {
									padding-top: 1.5rem;
								}

								a.a_js-popup-link {
									text-decoration: none;
									color: #40403f;
								}

								#regions-window form{
									display: flex;
									flex-wrap: wrap;
								}

								#regions-window button{
									padding: 0 20px;
								}

								#regions-window.popup-window{
									width: 70rem;
								}

								@media (max-width: 967px){
									#regions-window ul {
										columns: 1;
									}
								}
							</style>
							<div class="hidden">
								<div class="popup-window" id="regions-window">
									<form class="js-validation-form" method="GET">
										<h2>Выберите город: </h2>
{*										{$cities = [*}
{*										'Babruysk'=>'Бобруйск',*}
{*										'Salihorsk'=>'Солигорск',*}
{*										'Minsk'=>'Минск',*}
{*										'Gomel'=>'Гомель',*}
{*										'Brest'=>'Брест',*}
{*										'Vitebsk'=>'Витебск',*}
{*										'Hrodna'=>'Гродно',*}
{*										'Mogilev'=>'Могилев',*}
{*										'Baranovichi'=>'Барановичи',*}
{*										'Polotsk'=>'Полоцк',*}
{*										'Orsha'=>'Орша',*}
{*										'Pinsk'=>'Пинск',*}
{*										'Mazyr'=>'Мозырь',*}
{*										'Borisov'=>'Борисов',*}
{*										'Navapolatsk'=>'Новополоцк',*}
{*										'Lida'=>'Лида',*}
{*										'Other'=>'Моего города нет'*}
{*										]}*}
										<ul>
										{foreach $cities as $city}
											<li class="popup-form-row">
												<label class="form-label"><input data-region_id="{$region_id}"
																				 {if $city=="Минск"}
																				 data-city-region-id="1067"
																				 {else}
																				 {if $regions}
																				 {foreach $regions as $region}
																				 {if $region->short_name|trim==$city|trim}
													data-city-region-id="{$region->id}"
													{/if}
													{/foreach}
													{/if}
													{/if}
																				 type="radio" {if
																				 $region_short_name==$city}checked{/if}
																				 name="short_name"
																				 value="{$city}">{$city}</label>
											</li>
										{/foreach}
										</ul>
										<div class="popup-btn-row">
											<button type="submit" class="btn btn_full">Выбрать</button>
										</div>
									</form>
								</div>
							</div>
						</div>


						{* TODO dev *}
						{if $user}
							<div class="account-field__content">
								<div class="account-field__name">
									<a href="#regions-window" class=" a_js-popup-link js-popup-link" itemscope
									   itemtype="http://schema.org/ImageObject">
										<meta itemprop="name" content="map">
										<img style="width:16px;" src="design/{$settings->theme}/images/map.png"
											 alt="{$meta_title|escape}" itemprop="contentUrl">
										<meta itemprop="description" content="map">
										{if $region_short_name}{$region_short_name}{else}Ваш город{/if}
									</a>
								</div>

								<div class="account-field__name">
									<a href="{$config->root_url}/user"
									   class="account-field__name-link">{$user->name}</a>
								</div>

								<div class="account-field__discount">
										<span class="account-field__discount-link">
										{if $user->discount>0}Ваша скидка &mdash; {$user->discount}%{/if}
										</span>
								</div>

								<div class="account-field__logout">
									<a href="{$config->root_url}/user/logout"
									   class="account-field__logout-link">Выйти</a>
								</div>
							</div>
						{else}
							<ul class="account-field__control">
								<li class="account-field__control-item">
									<a href="#regions-window" class=" a_js-popup-link js-popup-link" itemscope
									   itemtype="http://schema.org/ImageObject">
										<meta itemprop="name" content="map">
										<img style="width:16px;" src="design/{$settings->theme}/images/map.png"
											 alt="{$meta_title|escape}" itemprop="contentUrl">
										<meta itemprop="description" content="map">
										{if $region_short_name}{$region_short_name}{else}Ваш город{/if}
									</a>
								</li>
								<li class="account-field__control-item">
									{* TODO dev оформить Вход в pop-up *}
									{*<a href="#login-window" class="account-field__control-link js-popup-link">Вход</a>*}
									<a href="{$config->root_url}/user/login"
									   class="account-field__control-link">Вход</a>
								</li>
								<li class="account-field__control-item">
									<a href="{$config->root_url}/user/register" class="account-field__control-link">Регистрация</a>
									{* TODO dev оформить регестрацию в pop-up *}
									{*<a href="#registration-window" class="account-field__control-link js-popup-link">Регистрация</a>*}

								</li>
							</ul>
						{/if}
						{* /*/regions*/ *}
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
							<img src="{$config->root_url}/design/{$settings->theme|escape}/images/logo3.png"
								 title="{$settings->site_name|escape}" alt="{$settings->site_name|escape}"
								 class="logo__image">
						</div>
					{else}
						<a href="{$config->root_url}/" class="logo">
							<img src="{$config->root_url}/design/{$settings->theme|escape}/images/logo3.png"
								 title="{$settings->site_name|escape}" alt="{$settings->site_name|escape}"
								 class="logo__image">
						</a>
					{/if}
					<div class="header__mob-phone">
						<div class="top-contacts top-contacts_mob-header">
							<a href="tel:{$phone_shop}" class="top-contacts__phone" itemscope itemtype="http://schema.org/ImageObject">
								<meta itemprop="name" content="phone">
								<img height="20" src="{$config->root_url}/design/{$settings->theme|escape}/images/tel-1.png" itemprop="contentUrl" alt="phone"> {$phone_shop}
								<meta itemprop="description" content="phone">
							</a>
							<!--<span class="top-contacts__schedule">10:00–22:00 / Без выходных</span>-->
						</div>
					</div>
				</div>
				<div class="header__search">
					<button type="button" class="header__search-btn js-search-field-target-btn">Поиск</button>
					<div class="search-field js-search-field">
						<button type="button" class="search-field__close js-search-field-close"></button>
						<script type="application/ld+json">
							{ldelim}
								"@context" : "https://schema.org",
								"@type" : "WebSite",
								"url" : "{$config->root_url}/",
								"potentialAction" : {ldelim}
									"@type" : "SearchAction",
									"target" : "{$config->root_url}/products?keyword={ldelim}search_term{rdelim}",
									"query-input" : "required name=search_term"
								{rdelim}
							{rdelim}


						</script>
						<form class="search-field__form js-validation-form" action="products">
							<input type="text" class="search-field__input js-search-autocomplete"
								   placeholder="Введите точное название товара" name="keyword" value="{$keyword|escape}"
								   required>
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
<div class="chPopUp-custom late-courier-popup">
	<div class="close"></div>
	<div class="inner">
		<form class="late-courier__form" id="late-courier" method="post">
			<div class="popup-input-section" style="margin-bottom: 30px;">Номер Вашего заказа:
				<span>
					<input class="popup-input-section__input order-late__number" type="text" name="lateOrder" placeholder="№ Заказа" style="margin-bottom: 0;"/>
				</span>
			</div>
			<div class="popup-input-section" style="margin-bottom: 30px;">Укажите время, в которое был доставлен заказ:
				<span>
					<input class="popup-input-section__input order-late__time" type="text" name="lateTime" placeholder="Время получения товара" style="margin-bottom: 0;"/>
				</span>
			</div>
			<button type="submit" class="late-courier-btn_style send-late-courier account-field__control-link">Отправить</button>
		</form>
	</div>
</div>
<footer class="footer">
	<div class="wrapper">
		<div class="footer__content">
			<div class="footer__col footer__col_1">
				<ul class="footer-nav">
					{foreach $pages as $p}
						{* Выводим только страницы из первого меню *}
						{if $p->menu_id == 1 || $p->menu_id == 3 }
							<li class="footer-nav__item{if $page && $page->id == $p->id}{/if}">
								<a data-page="{$p->id}" href="{$config->root_url}/{$p->url}"
								   class="footer-nav__link">{$p->name|escape}</a>
							</li>
						{/if}
					{/foreach}
					<li class="footer-nav__item"><a href="/brands" class="footer-nav__link">Бренды</a></li>
				</ul>
			</div>

			<div class="footer__col footer__col_2">
				<a href="tel:{$phone_shop}">{$phone_shop} Звоните!</a><br>
				Пн-Вс:&nbsp;09:00-&nbsp;21:00
				<div class="social contacts__social">
					<a href="https://vk.com/ezooby" class="social__link social__link_vk"></a>
					<a href="https://m.facebook.com/e-zooby-Интернет-зоомагазин-709671376046323/"
					   class="social__link social__link_fb"></a>
					<a href="https://www.instagram.com/ezoo.by/" class="social__link social__link_tw"></a>
					<a href="https://ok.ru/group/59858431377442" class="social__link social__link_ok"></a>
					<a href="https://play.google.com/store/apps/details?id=by.ezoo.android" class="social__link social__link_pm"></a>
				</div>
			</div>

			<div class="footer__col footer__col_2">

				Частное предприятие «ЗООХАУЗ»<br>
				УНП 190942323
				<br>г. Минск, ул. Бирюзова, д.4/5, офис 2024
				<br>Дата регистрации в Торговом реестре РБ: 13.04.2018
				<br><a href="https://e-zoo.by/dogovor-publichnoj-oferty" class="link">
					Договор публичной оферты
					</a>
				<input type="hidden" id="test" value="region_short_name :{$region_short_name} and cities: {$cities[$dadata_city]} and dadata_city: {$dadata_city}">
			</div>

			<div class="footer__col footer__col_4">
				<div class="social">
					<img src="{$config->root_url}/design/{$settings->theme|escape}/images/logo1-1.png" alt="logo">
					<img src="{$config->root_url}/design/{$settings->theme|escape}/images/logo-2.png" alt="logo">
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
<input type="hidden" name="currentTime" value="{$smarty.now|date_format:'%H:%M:%S'}">

{if $settings->hide_welcome}
	<div id="hidden-mess">

	</div>
{else}
	<div class="chPopUp-custom" id="welcome">
		<div class="close"></div>
		<div class="inner">
			<div class="need-coast">{$settings->welcome}</div>
		</div>
	</div>
{/if}
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

	<script src="design/{$settings->theme|escape}/js/lib/jquery/jquery-1.11.1.min.js" type="text/javascript"></script>
	<script src="design/{$settings->theme|escape}/js/jquery.fancybox.min.js" type="text/javascript"></script>
	<script src="design/{$settings->theme|escape}/js/jquery.formstyler.min.js" type="text/javascript"></script>
	<script src="design/{$settings->theme|escape}/js/lib/jquery.inputmask/jquery.inputmask.bundle.js"
			type="text/javascript"></script>
	<script src="design/{$settings->theme|escape}/js/lib/jquery.mCustomScrollbar/jquery.mCustomScrollbar.concat.min.js"
			type="text/javascript"></script>
	<script src="design/{$settings->theme|escape}/js/lib/jquery.placeholder/jquery.placeholder.min.js"
			type="text/javascript"></script>
	<script src="design/{$settings->theme|escape}/js/lib/jquery.slick/slick.min.js" type="text/javascript"></script>
	<script src="design/{$settings->theme|escape}/js/lib/jquery.sticky-kit/jquery.sticky-kit.min.js"
			type="text/javascript"></script>
	<script src="design/{$settings->theme|escape}/js/lib/jquery.validate/jquery.validate.min.js"
			type="text/javascript"></script>
	<script src="design/{$settings->theme|escape}/js/lib/jquery-ui-1.11.4/jquery-ui.min.js"
			type="text/javascript"></script>
	<script src="design/{$settings->theme|escape}/js/lib/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js"
			type="text/javascript"></script>
	<script src="design/{$settings->theme|escape}/js/lib/jquery.carouselTicker/jquery.carouselTicker.min.js"
			type="text/javascript"></script>
	<script src="design/{$settings->theme|escape}/js/lib/jquery.autocomplete/jquery.autocomplete.min.js"
			type="text/javascript"></script>
	<script src="design/{$settings->theme|escape}/js/lib/air-datepicker/datepicker.min.js"
			type="text/javascript"></script>
	<link href="design/{$settings->theme|escape}/js/lib/air-datepicker/datepicker.css" rel="stylesheet"
		  type="text/css"/>
<!--	<script src="design/{$settings->theme|escape}/js/lib/libs.min.js" type="text/javascript"></script>-->
	<script src="design/{$settings->theme|escape}/js/filter.js?2" type="text/javascript"></script>
	<script src="design/{$settings->theme|escape}/js/scripts.js?12032021" type="text/javascript"></script>
<script>
    var region_id = '';
    $('.zoom').fancybox({
        openEffect  : "fade",
        closeEffect : "fade",
        type : "image"
    });
</script>
{if $region_id}
<script>
    var region_id = '?region_id={$region_id}';
</script>
{/if}
{literal}
<script>
    $(".js-search-autocomplete").autocomplete({
        serviceUrl:'ajax/search_products.php'+region_id,
        minChars:1,
        noCache: false,
        onSelect: function(){
            $(".js-search-autocomplete").closest('form').submit();
        },
        formatResult: function(suggestion, currentValue){
            var reEscape = new RegExp('(\\' + ['/', '.', '*', '+', '?', '|', '(', ')', '[', ']', '{', '}', '\\'].join('|\\') + ')', 'g');
            var pattern = '(' + currentValue.replace(reEscape, '\\$1') + ')';
            return (suggestion.data.image?"<img align=absmiddle src='"+suggestion.data.image+"'> ":'') + suggestion.value.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>');
        }
    });
</script>
{/literal}
<style>
	.btn_border.js-sidebar-filter-tooltip br {
		content: "";
	}

	@media only screen and (max-width: 767px) {
		.filter-check-list {
			display: block;
			width: auto;
			padding: 0;
			border-radius: 0;
			background: none;
			max-height: 100%;
			overflow: initial;
		}

		.sidebar-filter [type=submit], .submit-link {
			display: none;
		}
	}

	.hits__main-item .cart.is-open .cart__imgae-slider-field {
		display: none;
	}

</style>
	{literal}<!-- Yandex.Metrika counter -->
	<script type="text/javascript">
		(function (m, e, t, r, i, k, a) {
			m[i] = m[i] || function () {
				(m[i].a = m[i].a || []).push(arguments)
			};
			m[i].l = 1 * new Date();
			k = e.createElement(t), a = e.getElementsByTagName(t)[0], k.async = 1, k.src = r, a.parentNode.insertBefore(k, a)
		})
		(window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

		ym(50329477, "init", {
			clickmap: true,
			trackLinks: true,
			accurateTrackBounce: true,
			webvisor: true,
			ecommerce: "dataLayer"
		});
	</script>
	<noscript>
		<div><img src="https://mc.yandex.ru/watch/50329477" style="position:absolute; left:-9999px;" alt=""/></div>
	</noscript>
	<!-- /Yandex.Metrika counter -->{/literal}
{if isset($add_comments_block_form)}
{literal}
	<script>
		gtag('event', 'send', {'event_category': 'write_us'});
		setTimeout(function () {
			yaCounter50329477.reachGoal('write_us');
		}, 3000);
	</script>
{/literal}
{/if}
{if isset($registration_form)}
{literal}
	<script>
		gtag('event', 'send', {'event_category': 'register'});
		setTimeout(function () {
			yaCounter50329477.reachGoal('register');
		}, 3000);
	</script>
{/literal}
{/if}
<div id="deliver_message" style="display:none">
	<div class="close_deliver"></div>
	<div class="title-text">Закажите до <span class="time_do_dev"></span>,</div>
	<div>получите до <span class="time_to_dev"></span></div>
	<div class="image-dev"></div>
</div>
{if $settings->manager_contacts_text}
	<div id="manager-contacts">
		<div>
			<span>{$settings->manager_contacts_text|escape}</span>
			<div class="links">
				{if ($settings->manager_contacts_phone_1 && $settings->manager_contacts_operator_1)}
					<a href="{$settings->manager_contacts_phone_1|escape}">{$settings->manager_contacts_operator_1|escape}</a>
				{/if}
				{if ($settings->manager_contacts_phone_2 && $settings->manager_contacts_operator_2)}
					<a href="{$settings->manager_contacts_phone_2|escape}">{$settings->manager_contacts_operator_2|escape}</a>
				{/if}
				{if ($settings->manager_contacts_chat && $settings->manager_contacts_chat_text)}
					<a href="{$settings->manager_contacts_chat|escape}">{$settings->manager_contacts_chat_text|escape}</a>
				{/if}
			</div>

		</div>
	</div>
{/if}
{* /* region */ *}
<input type="hidden" id="test" value="region_short_name :{$region_short_name} and cities: {$cities[$dadata_city]}">
{if !$region_short_name && {$cities[$dadata_city]}}
	<script>
		setTimeout(function () {
			popupOpen('#your_city-window');
		}, 300);
	</script>
	<div class="popup-window" id="your_city-window" style="width: 310px;padding: 26px 20px;">
		<form class="js-validation-form tex-center center" style="text-align: center;">
			<h4>Ваш город {$cities[$dadata_city]}?</h4>
			<input type="hidden" name="short_name" value="{$cities[$dadata_city]}">
			<button type="submit" class="btn ">Да</button>
			<button onclick="popupClose();popupOpen('#regions-window');return false;" class="btn ">НЕТ</button>
		</form>
	</div>
	{* /* /region */ *}
{/if}
<style>
	a.link{
		color: black;
	}
</style>
</body>
</html>
