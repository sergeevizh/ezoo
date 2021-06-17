{* Шаблон корзины *}

{$meta_title = "Корзина" scope=parent}

{* TODO Паша - в форме карзины нету поля емейл, да и вообще ее надо переделать *}

<section class="section section_bg section_basket">
	{*<p style="text-align: center; color: red;"><b>31.12.2018г и 1.01.2019г</b><br> *}
	{*Интернет-магазин заказы по телефону не принимает.<br> *}
	{*Онлайн-заказы, оформленные в эти дни, будут обработаны и доставлены со 2.01.2019г</p> *}
	<div class="wrapper">

		<div class="breadcrumbs">
			<div class="breadcrumbs__item">
				<a href="{$config->root_url}/" class="breadcrumbs__link">Главная</a>
			</div>
			<div class="breadcrumbs__item">Корзина</div>
		</div>

		<h1 class="page-title">Корзина</h1>
		<section class="basket">
			{if $cart->purchases}
				<form method="post" name="cart" class="js-cart-form">
					<div class="basket__content">
						<div class="basket__list">
							<h3 class="basket__list-title">В корзине {$cart->total_products} {$cart->total_products|plural:'товар':'товаров':'товара'}</h3>
							<div class="p-list">
								{foreach $cart->purchases as $purchase}
									<div class="p-item p-item_dynamic">
										<div class="p-item__inner">
											<div class="p-item__image-block">
												{$image = $purchase->product->images|first}
												{if $image}
													<a class="p-item__image-block-content" href="{$config->root_url}/products/{$purchase->product->url}">
														<img src="{$image->filename|resize:300:300}" alt="{$purchase->product->name|escape}" class="p-item__image">
													</a>
												{/if}
											</div>
											<div class="p-item__content-block">
												<h3 class="p-item__title">
													<a href="{$config->root_url}/products/{$purchase->product->url}">{$purchase->product->name|escape}</a>
													<span class="p-item__title-variant">{$purchase->variant->name|escape}</span>
												</h3>
												<div class="p-item__meta">
													{if $purchase->product->brand}
														<a href="{$config->root_url}/brands/{$purchase->product->brand_url}" class="p-item__meta-link">{$purchase->product->brand|escape}</a>

														{*<span>{$purchase->product->brand}</span>*}
														{*<span>{$purchase->product->brand_id}</span>*}

													{/if}


													{*	<div>	<?php if ($purchase->product->brand_id=$brand_out_discont) { echo $brand_out_discont_metka; }
                                                        else exit; ?></div>*}



												</div>
												<div class="p-item__description">{$purchase->product->annotation}</div>
											</div>
											<div class="p-item__price-block">
												<div class="p-item__price-field">
													{*!!!!!!!! отмена Условия для отображения скидки цены-желтым цветом  к бренду РОЯЛ с его ID='286001' $purchase->product->brand_id != '286001' *}
													<div class="p-item__price-field-content{if $purchase->variant->compare_price > 0} p-item__price-field-content-sale{/if}">
														<span class="p-item__price"><span>{($purchase->variant->price)|convert} {$currency->sign}</span></span>
														{if $purchase->variant->compare_price > 0}
															{*		<span class="p-item__old-price">{($purchase->variant->compare_price)|convert} {$currency->sign}</span>
                                                                *}
														{/if}
													</div>
													{if $purchase->variant->compare_price > 0}
														{*	<div class="p-item__price-field-discount">
                                                                <span class="p-item__discount">−{ceil(100-$purchase->variant->price/$purchase->variant->compare_price*100)}%</span>
                                                            </div>
                                                        *}
													{/if}
												</div>
												<div class="p-item__count-field">
													<div class="count-field js-count-field">
														<span class="count-field__control count-field__control_down js-count-field-down">-</span>
														<input type="text" class="count-field__val js-count-field-val" name="amounts[{$purchase->variant->id}]" value="{$purchase->amount}" onchange="document.cart.submit();">
														<span class="count-field__control count-field__control_up js-count-field-up">+</span>
													</div>
												</div>
											</div>
										</div>
										<div class="p-item__remove-block">
											<a href="{$config->root_url}/cart/remove/{$purchase->variant->id}" class="p-item__remove-link"></a>
										</div>
									</div>
								{/foreach}
							</div>
						</div>
					</div>
					<div class="basket__aside">
						<div class="order-block">
							<div class="order-block__form">
								<div class="order-block__header">
									<div class="order-block__header-inner">
										<div class="order-block__header-row order-block__header-row_lead">
											<div class="order-block__header-title">
												<div class="order-block__header-title-label"><b>Итого</b></div> {$cart->total_products} {$cart->total_products|plural:'товар':'товаров':'товара'}
											</div>
											<div class="order-block__header-description">{$cart->total_without_discount|convert}&nbsp;{$currency->sign}</div>
										</div>

										<div class="clear-border">
											<div></div>
											<div></div>
										</div>


										<div class="order-block__header-row order-block__header-row_second discount-cart-info delivery-row">

											<div class="order-block__header-title">Сумма к оплате
												при<br/>доставке
												курьером {if $deliveries[0]->discount_percent>0}<b>(-{$deliveries[0]->discount_percent}%)</b>{else}<b style="opacity: 0;">(-{$deliveries[0]->discount_percent}%)</b>{/if}</div>
											<div class="order-block__header-description">
												{($deliveries[0]->total_price)|convert}&nbsp;{$currency->sign}
											</div>


											{if $nextdiscount}
												<div class="order-block__header-row order-block__header-row_second get-discount">
													<a href="javascript:void(0)" data-procent="{$nextdiscount.procent}" data-need-coast="{$nextdiscount.sum}" data-procent-week="{$nextdiscount.procent_week}" data-need-coast-week="{$nextdiscount.sum_week}">Получить скидку {$nextdiscount.procent}%</a>

												</div>
											{/if}
										</div>



										<div class="order-block__header-row order-block__header-row_second discount-cart-info">

											<div class="order-block__header-title">Сумма к оплате
												при<br/>самовывозе
												<b>(-30%)</b></div>
											<div class="order-block__header-description">
												{($deliveries[1]->total_price)|convert}&nbsp;{$currency->sign}
											</div>
										</div>



										<div class="order-block__header-row order-block__header-row_second js-discount"{if $cart->total_without_discount == $cart->total_price} style="display: none;"{/if}>

											<div class="order-block__header-title">Ваша скидка</div>
											<div class="order-block__header-description">
												{($cart->total_without_discount-$cart->total_price)|convert}&nbsp;{$currency->sign}
											</div>
										</div>

									</div>
								</div>


								{if $error}
									<div class="error-message">
										{if $error == 'empty_name'}<div class="error-message__item">Введите имя</div>{/if}
										{if $error == 'empty_email'}<div class="error-message__item">Введите email</div>{/if}
										{if $error == 'captcha'}<div class="error-message__item">Капча введена неверно</div>{/if}
									</div>
								{/if}


								<div class="order-block__content">
									<div class="order-block__group">
										<div class="order-block__row js-order-block-row">
											<input type="text" class="order-block__field" placeholder="Имя и фамилия" name="name" value="{$name|escape}" required>
										</div>
										<div class="order-block__row js-order-block-row">
											<input type="text" class="order-block__field" placeholder="Email" name="email" value="{$email|escape}" required>
										</div>
										<div class="order-block__row js-order-block-row">
											<input type="text" class="order-block__field js-input-phone" placeholder="Телефон" name="phone" valu="" required>
										</div>
										{if $deliveries}
											<div class="order-block__row js-order-block-row">
												<div class="order-block__field js-order-block-field">Способ доставки</div>
												<div class="order-block__popup js-order-block-popup">
													<button type="button" class="order-block__popup-close js-order-block-popup-close"></button>
													<div class="order-block__popup-title">Выберите способ доставки</div>
													<div class="order-block__popup-checklist">
														<div class="checklist">
															{foreach $deliveries as $delivery}
																<label class="checklist__row">
																	<input type="radio" data-payments='{$delivery->payments|json_encode}' data-percent='{$delivery->discount_percent}' data-discount-for-order="{($delivery->discount_for_order)|convert}&nbsp;{$currency->sign}" data-total-price="{$delivery->total_price|convert}&nbsp;{$currency->sign}" class="checklist__input js-styler js-change-delivery" name="delivery_id" value="{$delivery->id}" {if $delivery_id==$delivery->id}checked{elseif $delivery@first}required{/if}>
																	<span class="checklist__text">
																{* Сделано *}
																		{$delivery->name|escape}
																		{*$delivery->description*}
																		{*<br>*}
																		{*Стоимость доставки:*}
																		{if $cart->total_price < $delivery->free_from && $delivery->price>0}
																			{$delivery->price|convert}&nbsp;{$currency->sign}
																		{elseif $cart->total_price >= $delivery->free_from}
																			(бесплатно)
																		{/if}
															</span>
																</label>
															{/foreach}
														</div>
													</div>
												</div>
											</div>
										{/if}
										<div class="order-block__row js-order-block-row">
											<input type="text" class="order-block__field" placeholder="Адрес доставки" name="address" value="{$address|escape}"/>
										</div>
										{*

                                        <div class="order-block__row js-order-block-row">
                                            <div class="order-block__field js-order-block-field">Адрес доставки</div>
                                            <div class="order-block__popup js-order-block-popup">
                                                <button type="button" class="order-block__popup-close js-order-block-popup-close"></button>
                                                <div class="order-block__popup-title">Выберите адрес доставки</div>
                                                <div class="order-block__popup-checklist">
                                                    <div class="checklist js-address-checklist">
                                                        <label class="checklist__row">
                                                            <input type="radio" name="delivery_address" class="checklist__input js-styler">
                                                            <span class="checklist__text">г. Минск, ул. Бельского, д. 8, кв. </span>
                                                        </label>
                                                        <label class="checklist__row">
                                                            <input type="radio" name="delivery_address" class="checklist__input js-styler">
                                                            <span class="checklist__text">г. Минск, ул. Дркжбы народов, д. 8, к. 8, кв. 216</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="order-block__row order-block__popup-field">
                                                    <div class="order-block__field js-add-new-address-field">Введите новый адрес</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="order-block__popup order-block__popup_address js-add-new-address-popup" id="add-new-address-popup">
                                            <button type="button" class="order-block__popup-close js-add-new-address-popup-close"></button>
                                            <div class="order-block__popup-title">Новый адресс доставки</div>
                                            <div class="add-new-address js-add-new-address">
                                                <div class="add-new-address__col">
                                                    <div class="form-row">
                                                        <label class="form-label">Область</label>
                                                        <input type="text" name="region" data-preffix="обл.">
                                                    </div>
                                                    <div class="form-row">
                                                        <label class="form-label">Район</label>
                                                        <input type="text" name="area" data-preffix="р.">
                                                    </div>
                                                    <div class="form-row">
                                                        <label class="form-label">Город</label>
                                                        <input type="text" name="city" data-preffix="г." class="required">
                                                    </div>
                                                    <div class="form-row">
                                                        <label class="form-label">Улица</label>
                                                        <input type="text" name="street" data-preffix="ул." class="required">
                                                    </div>
                                                </div>
                                                <div class="add-new-address__col">
                                                    <div class="form-row">
                                                        <label class="form-label">Дом</label>
                                                        <input type="text" name="house" data-preffix="д." class="required">
                                                    </div>
                                                    <div class="form-row">
                                                        <label class="form-label">Корпус</label>
                                                        <input type="text" name="housing" data-preffix="к.">
                                                    </div>
                                                    <div class="form-row">
                                                        <label class="form-label">Этаж</label>
                                                        <input type="text" name="floor" data-preffix="эт.">
                                                    </div>
                                                    <div class="form-row">
                                                        <label class="form-label">Квартира</label>
                                                        <input type="text" name="appartament" data-preffix="кв.">
                                                    </div>
                                                </div>
                                                <div class="add-new-address__footer">
                                                    <button type="button" class="btn btn_large js-add-new-address-btn">Добавить новый адресс</button>
                                                </div>
                                            </div>
                                        </div>
                                        *}
										<div class="order-block__row js-order-block-row">
											<input type="text" class="order-block__field js-timedatepicker" placeholder="Выберите дату" name="self_discharge_time" value="{$self_discharge_time}" required readonly>
										</div>
										<style>
											.js-time option[disabled] {
												display: none;
											}
										</style>


										<div class="order-block__row js-order-block-row">
											<input type="hidden" value="{$discount_hh_week}" name="discount_hh_week"/>
											<input type="hidden" value="{$price_hh_week}" name="price_hh_week"/>
											<input type="hidden" value="{$discount_hh_ends}" name="discount_hh_ends"/>
											<input type="hidden" value="{$price_hh_ends}" name="price_hh_ends"/>

											{$times = [
											10 => '10:00 - 12:00',
											12 => '12:00 - 15:00',
											15 => '15:00 - 17:00',
											17 => '17:00 - 19:00',
											19 => '19:00 - 21:00',
											21 => '21:00 - 23:00'
											]}
											<select name="time" class="order-block__field js-time" required>
												<option value="">Выберите время</option>
												{foreach $times as $key=> $time}
													<option data-key="{$key}" value="{$time}">{$time}</option>
												{/foreach}
											</select>
										</div>
										{* TODO dev *}
										{if $payment_methods}
											<div class="order-block__row js-order-block-row js-payment-methods">
												<div class="order-block__field js-order-block-field">Способы оплаты</div>
												<div class="order-block__popup js-order-block-popup">
													<button type="button" class="order-block__popup-close js-order-block-popup-close"></button>
													<div class="order-block__popup-title">Выберите способ оплаты</div>
													<div class="order-block__popup-checklist">
														<div class="checklist">
															{foreach $payment_methods as $payment_method}
																<label class="checklist__row js-closest">
																	<input type="radio" name="payment_method_id" {if $payment_method_id==$payment_method->id}checked{elseif $payment_method@first}required{/if} value="{$payment_method->id}" class="checklist__input js-styler js-change-payment">
																	<span class="checklist__text">{$payment_method->name}</span>
																</label>
															{/foreach}
														</div>
													</div>
												</div>
											</div>
										{/if}
										<div class="order-block__row">
											<textarea name="comment" class="order-block__field" placeholder="Комментарий к заказу">{$comment|escape}</textarea>
										</div>

										{if $coupon_request}
											<div class="order-block__row js-order-block-row">
												{if $coupon_error}
													<div class="error-message">
														{if $coupon_error == 'invalid'}<div class="error-message__item">Купон недействителен</div>{/if}
													</div>
												{/if}

												<!--<input type="text" class="order-block__field" placeholder="Промо-код, если есть" name="coupon_code" value="{$cart->coupon->code|escape}">
										<div class="apply-coupon-btn-field">
											<button type="button" class="btn btn_small btn_full apply-coupon-btn" name="apply_coupon" onclick="document.cart.submit();">Применить купон</button>
										</div>-->


												{if $cart->coupon->min_order_price>0}
													<div class="coupon-available-info">
														Купон {$cart->coupon->code|escape} действует для заказов от&nbsp;{$cart->coupon->min_order_price|convert}&nbsp;{$currency->sign}
														{if $cart->coupon_discount>0}<span class="coupon-discount-info">(&minus;&nbsp;{$cart->coupon_discount|convert}&nbsp;{$currency->sign})</span>{/if}
													</div>
												{/if}


											</div>
										{/if}
									</div>
									<div class="order-block__price" data-bind-text="total_price">{$cart->total_price|convert}&nbsp;{$currency->sign}</div>
									<button type="submit" name="checkout" value="Подтвердить и заказать" class="btn btn_large order-block__btn">Подтвердить и заказать</button>
									<div class="order-block__note"></div>
								</div>
							</div>
						</div>
					</div>
				</form>
			{else}
				<p>В корзине нет товаров</p>
			{/if}
		</section>
	</div>
</section>

