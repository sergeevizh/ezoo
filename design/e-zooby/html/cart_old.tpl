{* Шаблон корзины *}

{$meta_title = "Корзина" scope=parent}

{* TODO Паша - в форме карзины нету поля емейл, да и вообще ее надо переделать *}


<section class="section section_bg section_basket">
	{*<p style="text-align: center; color: red;"><b>31.12.2018г и 1.01.2019г</b><br> *}
		{*Интернет-магазин заказы по телефону не принимает.<br> *}
		{*Онлайн-заказы, оформленные в эти дни, будут обработаны и доставлены со 2.01.2019г</p> *}
	<div class="wrapper">

		<div class="breadcrumbs">
			<div class="breadcrumbs__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
				<a href="{$config->root_url}/" class="breadcrumbs__link" itemprop="item">
					<span itemprop="name">Главная</span>
				</a>
				<meta itemprop="position" content="1"/>
			</div>
			<div class="breadcrumbs__item">Корзина</div>
		</div>
		<h1 class="page-title">Корзина</h1>
		<section class="basket">
			{if $cart->purchases}
			<div class="line-sale-text" style="display: none">Для получения скидки <span class="percent"></span>
				добавьте товаров на <span class="summ"></span> руб.
			</div>
			<script>
                console.log({$product_sale_price})
                var product_sale_price = JSON.parse('{$product_sale_price}');
			</script>
			<form method="post" name="cart" class="js-cart-form">
				<input type="hidden" id="suggestion_courier" name="suggestion_courier" value="0">
				<div class="basket__content">
					<div class="basket__list">
						{if $user}
						<button class="w-show-checkboxes" type="button">
							Выбрать товары для заказа
						</button>
						{/if}
						<h3 class="basket__list-title">В
							корзине {$cart->total_products} {$cart->total_products|plural:'товар':'товаров':'товара'}</h3>
						<div class="p-list">
							{foreach $cart->purchases as $purchase}
							<div class="p-item p-item_dynamic">
								<div class="p-item__category" style="display: none">
									{$purchase->category}
								</div>
								{if $user}
								<label for="checkbox-{$purchase->variant->id}"
									   class="w-check-label {if $purchase->check == 1}checked{/if}"
									   data-checkbox="{$purchase->variant->id}"></label>
								{/if}
								<div class="p-item__inner">
									<div class="p-item__image-block">
										{$image = $purchase->product->images|first}
										{if $image}
										<a class="p-item__image-block-content"
										   href="{$config->root_url}/products/{$purchase->product->url}"
										   itemscope
										   itemtype="http://schema.org/ImageObject">
											<meta itemprop="name" content="{$product->name|escape}">
											<img src="{$image->filename|resize:300:300}"
												 alt="{$purchase->product->name|escape}"
												 class="p-item__image" itemprop="contentUrl"
												 alt="{$product->name|escape}">
											<meta itemprop="description" content="{$product->name|escape}">
										</a>
										{/if}
									</div>
									<div class="p-item__content-block">
										<h3 class="p-item__title">
											<a href="{$config->root_url}/products/{$purchase->product->url}">{$purchase->product->name|escape}</a>
											<div class="p-item__meta">
												{if $purchase->product->brand}
												<a href="{$config->root_url}/brands/{$purchase->product->brand_url}"
												   class="p-item__meta-link">{$purchase->product->brand|escape}</a>
												{*<span>{$purchase->product->brand}</span>*}
												{*<span>{$purchase->product->brand_id}</span>*}
												{/if}
												{*	<div>	<?php if ($purchase->product->brand_id=$brand_out_discont) { echo $brand_out_discont_metka; }
													else exit; ?></div>*}
											</div>
											<span class="p-item__title-variant">{$purchase->variant->name|escape}</span>
										</h3>
										<div class="p-item__description">{$purchase->product->annotation}</div>
									</div>
									<!--									TODO start +-->
									<div class="p-item__price-block{if !$purchase->variant->compare_price && $purchase->check==1} dinamic_price{/if}"
										 data-id-product="{$purchase->variant->id}">
										<div class="p-item__price-field bottom-padd-10">
											<div class="p-item__price-field-content{if $purchase->variant->compare_price > 0} p-item__price-field-content-sale{/if}">
												<div class="p-item__price">
													<div class="left-price">Цена:</div>

													<div class="right-price default-price">{($purchase->variant->price)|convert} {$currency->sign}</div>
												</div>
											</div>
										</div>
										{if !$purchase->variant->compare_price}
										<div class="p-item__price-field">
											<div class="p-item__price-field-content">
												<div class="p-item__price sale-price-product">
													<div class="left-price">Со скидкой:</div>
													<div class="sale_value right-price">{($purchase->variant->price)|convert} {$currency->sign}</div>
												</div>
												<div class="panel-percent">
													<div class="sale-percent">Моя скидка:-%</div>
												</div>
											</div>
										</div>
										{/if}
										<div class="p-item__count-field bottom-padd-10">
											<div class="left-price">Количество:</div>
											<div class="right-price-count count-field js-count-field">
												<span class="count-field__control count-field__control_down js-count-field-down">-</span>
												<input type="text"
													   class="count-field__val js-count-field-val"
													   name="amounts[{$purchase->variant->id}]"
													   value="{$purchase->amount}"
													   {* onchange="document.cart.submit();" *}
													   defaultVal="{$purchase->amount}"
													   data-max="{if $purchase->variant->stock ==0}1000{else}{$purchase->variant->stock}{/if}"
												>
												<span class="count-field__control count-field__control_up js-count-field-up">+</span>
											</div>
										</div>
										<div class="p-item__price-field">
											<div class="p-item__price-field-content">
												<div class="p-item__price">
													<div class="left-price"><strong>Сумма:</strong></div>
													<div class="final-summ right-price">{($purchase->variant->price*$purchase->amount)|convert} {$currency->sign}</div>
												</div>
											</div>
										</div>
									</div>


								</div>
								<div class="p-item__remove-block">
									<a href="{$config->root_url}/cart/remove/{$purchase->variant->id}"
									   class="p-item__remove-link"></a>
								</div>
							</div>
							{/foreach}
							<!--							TODO end-->

						</div>
					</div>
				</div>
				<div class="basket__aside">
					<div class="order-block">
						<div class="order-block__form">
							{if $city}
							<div class="block-city order-block__row">
								<select id="current-city-name" name="city" class="order-block__field js-city"
										required>
									<option value=>Выберите город</option>
									{*<option value="0" data-city-min-1="15" data-city-min-2="328">Минск</option>*}
									{foreach $city as $city_item}
									<option value="{$city_item['city_id']}"
											data-region_id="{$city_item['region_id']}"
											{if ($city_item['delivery'][1]['active']=='1')}data-active-deliver-1="1"
									data-city-min-1="{$city_item['delivery'][1]['city_min']}"{/if}
									{if ($city_item['delivery'][2]['active']=='1')}data-active-deliver-2="1" data-city-min-2="{$city_item['delivery'][2]['city_min']}"{/if}>{$city_item['city_name']}</option>
									{/foreach}
								</select>
							</div>
							{/if}
							<div class="order-block__header" style="display: none">
								<div class="order-block__header-inner">
									{if $deliveries}
									<div class="checklist deliver_modify">
										<script>
                                            var date_sale = new Array();
										</script>
										{foreach $deliveries as $delivery}
										<script>
                                            date_sale[{$delivery->id}] = new Array();
                                            {foreach $date_sale[$delivery->id] as $date_sale_item}
                                            {if $date_sale_item}
                                            date_sale[{$delivery->id}].push([{$date_sale_item[0]},{$date_sale_item[1]},{$date_sale_item[2]-1},{$date_sale_item[3]},{$date_sale_item[4]},{$date_sale_item[5]}]);
                                            {/if}
                                                {/foreach}
										</script>
										<label class="checklist__row data-id-deliver-{$delivery->id}">
											<input type="radio"
												   data-payments='{$delivery->payments|json_encode}'
												   data-percent='{$delivery->discount_percent}'
												   data-discount-for-order="{($delivery->discount_for_order)|convert}&nbsp;{$currency->sign}"
												   data-discount-for-order-not="{($delivery->discount_for_order)|convert}"
												   data-total-price-check="{$delivery->total_price|convert}"
												   data-total-price="{$delivery->total_price|convert}&nbsp;{$currency->sign}"
												   data-price-sum="{$cart->total_without_discount|convert}"
												   class="checklist__input js-styler js-change-delivery"
												   name="delivery_id" value="{$delivery->id}"
												   {if $delivery->id==1}checked{elseif $delivery@first}required{/if}
											{foreach from=$delivery->sale_city key=key_city_id item=sale_city}
											data-city-percent-{$key_city_id}='{$sale_city['discount_percent']}'
											data-city-total_price-{$key_city_id}='{$sale_city['total_price']}'
											data-city-sale-{$key_city_id}='{$sale_city['sale']}'
											{/foreach}
											data-next-percent="{$delivery->next_percent}"
											data-next-price="{$delivery->next_price}"
											>
											<span class="checklist__text">
															{if $delivery->id == 2}
																<div class="order-block__header-row order-block__header-row_second discount-cart-info">
																	<div class="order-block__header-title">Самовывоз {*<b></b>*}</div>
																	<div class="order-block__header-description">
																		{($delivery->total_price)|convert}&nbsp;{$currency->sign}
																	</div>
																</div>

{else}

																<div class="order-block__header-row order-block__header-row_second discount-cart-info delivery-row">
																	<div class="order-block__header-title">Доставка курьером {*{if $delivery->discount_percent>0}<b>(-{$delivery->discount_percent}%)</b>{else}<b></b>{/if}*}</div>
																	<div class="order-block__header-description">
																		{($delivery->total_price)|convert}&nbsp;{$currency->sign}
																	</div>
																	{if $nextdiscount}
																		<div class="order-block__header-row order-block__header-row_second get-discount"
																			 style="display: none">
																			<a href="javascript:void(0)"
																			   data-procent="{$nextdiscount.procent}"
																			   data-need-coast="{$nextdiscount.sum}"
																			   data-procent-week="{$nextdiscount.procent_week}"
																			   data-need-coast-week="{$nextdiscount.sum_week}">Получить скидку {$nextdiscount.procent}%</a>
																		</div>
																	{/if}
																</div>
															{/if}
															</span>
										</label>
										{/foreach}
									</div>
									{/if}
								</div>
							</div>
							<div class="bascet-inormation-block">
								<div class="summ-price-block">
									<div class="summ-count-all row-table">
										<div class="col-table">Товаров:</div>
										<div class="col-table value">{$cart->total_products} шт</div>
									</div>
									<div class="summ-price-all row-table">
										<div class="col-table">На сумму:</div>
										<div class="col-table value">{$cart->total_without_discount|convert}
											&nbsp;{$currency->sign}</div>
									</div>
									<div class="js-discount row-table">
										<div class="col-table">Скидка</div>
										<div class="col-table value">
											{($cart->total_without_discount-$cart->total_price)|convert}
											&nbsp;{$currency->sign}
										</div>
									</div>
								</div>
								<div class="text-basket-summ">Итого к оплате</div>
								<div class="order-block__price"
									 data-bind-text="total_price">{$deliveries[0]->total_price|convert}
									&nbsp;{$currency->sign}</div>
							</div>

							{if $error}
							<div class="error-message">
								{if $error == 'empty_name'}
								<div class="error-message__item">Введите имя</div>
								{/if}
								{if $error == 'empty_email'}
								<div class="error-message__item">Введите email</div>
								{/if}
								{if $error == 'captcha'}
								<div class="error-message__item">Капча введена неверно</div>
								{/if}
							</div>
							{/if}


							<div class="order-block__content">
								<div class="order-block__group" style="display:none">
									{*<div class="order-block__row js-order-block-row">
										<input type="text" class="order-block__field" placeholder="Имя и фамилия" name="name" value="{$name|escape}" required>
									</div>
									<div class="order-block__row js-order-block-row">
										<input type="text" class="order-block__field" placeholder="Email" name="email" value="{$email|escape}" required>
									</div>*}
									<div class="order-block__row js-order-block-row">
										<input type="text" class="order-block__field js-input-phone"
											   placeholder="Телефон*" name="phone" value="{$phone|escape}" required>
									</div>
									<div class="order-block__row js-order-block-row">

										<div class="y-map-search-field">
											<input type="text" name="yamap_input" id="suggestion" value="{$address}" prev-val="{$address|escape}"
												   placeholder="Адрес доставки*" required autocomplete="off"/>
											<span class="reset-y-map-input"></span>
										</div>
										<button type="button" class="button-check-address" id="button">Проверить
										</button>
										<p id="notice" class="notice"></p>
										<div id="map"></div>
										<div id="message"></div>
									</div>
									<div class="order-block__row js-order-block-row">
										<input type="text" class="order-block__field js-input-flat"
											   placeholder="Квартира №*" name="flat_num" value="{$flat_num}" required>
									</div>
									{if time() > 1624611600 && time() < 1624629600}
									<div class="order-block__row js-order-block-row" style="text-align:center;">
										<input type="checkbox" id="express" name="express" value="1"/>
										<label for="express" style="font-size: 16px;">Доставка за 1 час</label>
									</div>
									{/if}
									{if $city}
									<div class="order-block__row js-order-block-row city_area_block">
										<select name="city_area" class="order-block__field city_area">
											<option value="">Пункт самовывоза*</option>
											{foreach $city_minsk as $city_minsk_area}
											<option value="{$city_minsk_area->id}"
													data-id-city="0">{$city_minsk_area->name_area}</option>
											{/foreach}
											{foreach $city as $city_item}
											{foreach $city_item['city_area'] as $city_area}
											{if ($city_item['delivery'][2]['active']=='1')}
											<option value="{$city_area->id}"
													data-id-city="{$city_area->shipping_city_id}">{$city_area->name_area}</option>
											{/if}
											{/foreach}
											{/foreach}
										</select>
										<div class="city_area_text"></div>
									</div>
									{/if}

									<div class="order-block__row js-order-block-row">
										<input type="text" class="order-block__field js-timedatepicker"
											   placeholder="Выберите дату*" name="self_discharge_time"
											   value="{$self_discharge_time}" required readonly>
									</div>
									<style>
										.js-time option[disabled] {
											display: none;
										}
									</style>

									{if !$hide_time}
									<div class="order-block__row js-order-block-row">
										<input type="hidden" value="{$discount_hh_week}"
											   name="discount_hh_week"/>
										<input type="hidden" value="{$price_hh_week}" name="price_hh_week"/>
										<input type="hidden" value="{$discount_hh_ends}"
											   name="discount_hh_ends"/>
										<input type="hidden" value="{$price_hh_ends}" name="price_hh_ends"/>

										{$times = [
										10 => '10:00 - 12:00',
										12 => '12:00 - 14:00',
										15 => '14:00 - 16:00',
										17 => '16:00 - 18:00',
										19 => '18:00 - 20:00',
										20 => '20:00 - 22:00',
										21 => '22:00 - 23:00'
										]}
										<select name="time" class="order-block__field js-time" required>
											<option value="">Выберите время*</option>
											{foreach $times as $key=> $time}
											<option data-key="{$key}" value="{$time}">{$time}</option>
											{/foreach}
										</select>
									</div>
									{/if}
									{* TODO dev *}
									{if $payment_methods}
									<div class="order-block__row js-order-block-row js-payment-methods">
										<div class="order-block__field js-order-block-field">Способы оплаты
										</div>
										<div class="order-block__popup js-order-block-popup">
											<button type="button"
													class="order-block__popup-close js-order-block-popup-close"></button>
											<div class="order-block__popup-title">Выберите способ оплаты</div>
											<div class="order-block__popup-checklist">
												<div class="checklist">
													{foreach $payment_methods as $payment_method}
													<label class="checklist__row js-closest">
														<input type="radio" name="payment_method_id"
															   {if $payment_method_id==$payment_method->id}checked{elseif $payment_method@first}{/if}
														value="{$payment_method->id}"
														class="checklist__input js-styler js-change-payment">
														<span class="checklist__text">{$payment_method->name}</span>
													</label>
													{/foreach}
												</div>
											</div>
										</div>
									</div>
									{/if}
									<div class="order-block__row">
											<textarea name="comment" class="order-block__field"
													  placeholder="Комментарий к заказу">{$comment|escape}</textarea>
									</div>
									<div class="order-block__row">
										<input type="text" class="order-block__field" placeholder="Промокод"
											   name="promo" value="" aria-required="false">
									</div>
									<div class="order-block__row c-cart-row">
										<input type="checkbox" name="mailing" id="mailing" checked><label
											for="mailing" class="c-label">Подписаться на новости и
											скидки</label>
									</div>
									<div class="order-block__row c-cart-row"
										 style="flex-wrap: wrap; justify-content: space-between;text-align: center">
										<input type="checkbox" name="call_back" id="call_back" value="1"
											   style="display: none;">Хотите, чтобы Вам перезвонил оператор
										интернет-магазина?
										<button type="button" id="call_back_yes" class="btn btn_full"
												style="width: calc(50% - 20px);">Да
										</button>
										<button type="button" id="call_back_no" class="btn btn_full"
												style="width: calc(50% - 20px);">Нет
										</button>
									</div>
									{if $coupon_request}
									<div class="order-block__row js-order-block-row">
										{if $coupon_error}
										<div class="error-message">
											{if $coupon_error == 'invalid'}
											<div class="error-message__item">Купон недействителен</div>
											{/if}
										</div>
										{/if}

										<!--<input type="text" class="order-block__field" placeholder="Промо-код, если есть" name="coupon_code" value="{$cart->coupon->code|escape}">
						<div class="apply-coupon-btn-field">
							<button type="button" class="btn btn_small btn_full apply-coupon-btn" name="apply_coupon" onclick="document.cart.submit();">Применить купон</button>
						</div>-->


										{if $cart->coupon->min_order_price>0}
										<div class="coupon-available-info">
											Купон {$cart->coupon->code|escape} действует для заказов от&nbsp;{$cart->coupon->min_order_price|convert}
											&nbsp;{$currency->sign}
											{if $cart->coupon_discount>0}<span class="coupon-discount-info">
															(&minus;&nbsp;{$cart->coupon_discount|convert}&nbsp;{$currency->sign})</span>{/if}
										</div>
										{/if}


									</div>
									{/if}
								</div>
								<input type="hidden" name="press_check" value="">
								<button type="submit" name="checkout" value="Оформить заказ"
										class="btn btn_large order-block__btn" style="display:none">Оформить заказ
								</button>
								<div class="btn btn_large" id="cart_show_information_block" style="width: 100%;">
									Оформить заказ
								</div>
								<div class="order-block__note"></div>
							</div>
						</div>
					</div>
				</div>
			</form>
			{foreach $cart->purchases as $purchase}
			<form class="w-form-check" style="display: none" id="{$purchase->variant->id}" method="post">
				<input type="hidden" name="check" value="1">
				<input type="hidden" name="variant_id" value="{$purchase->variant->id}">
				<input type="checkbox" name="checkbox" id="checkbox-{$purchase->variant->id}" value="1"
					   {if $purchase->check == 1}checked{/if}>
			</form>
			{/foreach}
			{else}
			<p>В корзине нет товаров</p>
			{/if}
			{if $user || $history}
			<div class="user-history">
				<div class="basket__content">
					<div class="basket__list">
						<h3>
							История покупок
						</h3>
						<div class="p-list">
							{foreach $history as $purchase}
							<div class="p-item p-item_dynamic">
								<form class="history-item__add" method="post">
									<input type="hidden" name="variant" value="{$purchase['variant']->id}">
									<input type="hidden" name="history" value="1">
									<input type="checkbox" value="1" id="h-checkbox-{$purchase['variant']->id}"
										   style="display: none" class="js-w-checkbox">
									<label for="h-checkbox-{$purchase['variant']->id}"
										   class="w-check-label w-check-label--history"></label>
								</form>
								<div class="p-item__inner">

									<div class="p-item__image-block">
										{$image = $purchase['images']|first}
										{if $image}
										<a class="p-item__image-block-content"
										   href="{$config->root_url}/products/{$purchase['product']->url}"
										   itemscope
										   itemtype="http://schema.org/ImageObject">
											<meta itemprop="name" content="{$product->name|escape}">
											<img src="{$image->filename|resize:300:300}"
												 alt="{$purchase['product']->name|escape}"
												 class="p-item__image" itemprop="contentUrl">
											<meta itemprop="description" content="{$product->name|escape}">
										</a>
										{/if}
									</div>
									<div class="p-item__content-block">
										<h3 class="p-item__title">
											<a href="{$config->root_url}/products/{$purchase['product']->url}">{$purchase['product']->name|escape}</a>
											<div class="p-item__meta">
												{if $purchase['brand']}
												<a href="{$config->root_url}/brands/{$purchase['product']->brand_url}"
												   class="p-item__meta-link">{$purchase['brand']->name|escape}</a>
												{*<span>{$purchase['brand']}</span>*}
												{*<span>{$purchase['brand']->brand_id}</span>*}

												{/if}


												{*
												<div>    <?php if ($purchase->product->brand_id = $brand_out_discont) {
														echo $brand_out_discont_metka;
													} else exit; ?></div>
												*}
											</div>
											<span class="p-item__title-variant">{$purchase['variant']->name|escape}</span>
										</h3>
										<div class="p-item__description">{$purchase['product']->annotation}</div>
									</div>
									<div
										class="p-item__price-block"
										data-id-product="{$purchase['product']->id}">
										<div class="p-item__price-field bottom-padd-10">
											<div
												class="p-item__price-field-content{if $purchase['variant']->compare_price > 0} p-item__price-field-content-sale{/if}">
												<div class="p-item__price">
													<div class="left-price">Цена:</div>
													<div class="right-price default-price">
														{($purchase['variant']->price)|convert} {$currency->sign}
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							{/foreach}
						</div>
					</div>
				</div>

			</div>
			{/if}
		</section>
		{if $featured_products}
		<section class="similiar-catalog-section similiar-catalog-section--cart"
				 style="margin-top: 100px; background: #ffc300">
			<div class="wrapper">
				<div class="similiar-catalog-section__title h2"
					 style="margin-left: 20px; text-align: center; margin-bottom: 50px;">Мы подобрали эти товары
					специально для вас!
				</div>
			</div>
			<div class="similiar-catalog js-similiar-catalog">
				<ul class="similiar-catalog__list">
					{foreach $featured_products as $featured_product}
					<li class="similiar-catalog__item">
						{include file="_product_cart.tpl" product=$featured_product}
					</li>
					{/foreach}
				</ul>
			</div>
		</section>
		{/if}

	</div>
</section>
<input type="hidden" name="dateandtime" value="{$date_and_time}">
{foreach $cart->purchases as $purchase}
{if $purchase->product->pickup}
<div class="chPopUp-custom" id="vetpreparaty">
	<div class="close"></div>
	<div class="inner">
		<div class="need-coast">{$vetpreparaty}</div>
	</div>
	<div class="continue">Продолжить покупки</div>
</div>
{break}
{/if}
{/foreach}

<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;coordorder=longlat&amp;apikey=06121448-377d-4258-b280-81672bfc97b6"
		type="text/javascript"></script>
<script src="https://yandex.st/jquery/2.2.3/jquery.min.js" type="text/javascript"></script>
<script>
$( document ).ready(function() {
	$("#express").click(function(){
		if($("#express").prop('checked')){
			$('.js-time').val('10:00 - 12:00');
			$('.js-time').css('display','none');
		}else{
			$('.js-time').css('display','block');
			$('.js-time').val('');
		}
	});
	
	$('[name="self_discharge_time"]').click(function(){
		setInterval(function(){
			if($("#express").prop('checked')){
				$('.js-time').val('22:00 - 23:00');	
				}
			},2000);
	});
});
</script>

