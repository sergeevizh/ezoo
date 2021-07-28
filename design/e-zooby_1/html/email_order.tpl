{* Шаблон письма пользователю о заказе *}
{$subject = "Заказ №`$order->id`" scope=parent}

<div style="margin:0; background-color: #eaebec;" bgcolor="#eaebec">
	<div style="background-color: #eaebec;">
		<div style="width:600px; margin-left: auto; margin-right: auto; font-family: 'Open Sans', Arial, sans-serif; font-size: 13px; line-height: 1.2; color: #000000;">
			<table width="100%" cellspacing="0" cellpadding="38">
				<tr>
					<td style="padding-top: 62px; padding-bottom: 27px;" align="center">
						<a href="{$config->root_url}/" style="color: #6db46d;">
							<img src="{$config->root_url}/design/{$settings->theme|escape}/images/logo.png" height="60" alt="{$settings->site_name|escape}">
						</a>
					</td>
				</tr>
				<tr>
					<td style="padding-top: 71px; padding-bottom: 90px; border-radius: 5px;" bgcolor="#fff">
						<div style="font-size: 24px; line-height: 1.2; margin-bottom: 29px;">Здравствуйте, {$order->name|escape}!</div>
						<div style="font-size: 36px; line-height: 1.2; margin-bottom: 40px;">
							Ваш заказ
							<a href="{$config->root_url}/order/{$order->url}" style="color: #6db46d;">№{$order->id}</a>
							{if $order->status == 0}ждет обработки{elseif $order->status == 1}в обработке{elseif $order->status == 2}выполнен{elseif $order->status == 3}отменен{/if}.
						</div>
						{$total_products = 0}
						{foreach $purchases as $purchase}
							{$total_products = $total_products + $purchase->amount}
						{/foreach}
						<div style="font-size: 24px; line-height: 1.2; margin-bottom: 30px;">Вы заказали {$total_products} {$total_products|plural:'товар':'товаров':'товара'}  на {$order->total_price|convert:$currency->id}&nbsp;{$currency->sign}.</div>


						<table width="100%" cellspacing="0" cellpadding="15" style="margin-bottom: 20px;">
							{foreach $purchases as $purchase}
								{$image = $purchase->product->images[0]}
								<tr>
									<td style="border-bottom: 1px solid #e5e5e5; padding-left: 0;" valign="top">
										{$image = $purchase->product->images[0]}
										{if $image}
											<a href="{$config->root_url}/products/{$purchase->product->url}" style="color: #6db46d;">
												<img src="{$image->filename|resize:100:100}" alt="{$purchase->product_name}">
											</a>
										{/if}
									</td>
									<td style="border-bottom: 1px solid #e5e5e5;" valign="top">
										<div style="font-size: 17px; line-height: 1; margin-bottom: 10px;">
											<a href="{$config->root_url}/products/{$purchase->product->url}" style="color: #6db46d;">{$purchase->product_name}</a>
										</div>
										{*<div style="font-size: 13px; line-height: 1.2; margin-bottom: 10px;">*}
										{*<a href="#" style="color: #666666;">Tonymoly</a>*}
										{*</div>*}
										<div style="font-size: 13px; line-height: 1.2; margin-bottom: 21px;">{$purchase->variant_name}</div>
									</td>
									<td style="border-bottom: 1px solid #e5e5e5; padding-right: 0;" valign="top" align="right">
										<div style="font-size: 17px; line-height: 1.2; margin-bottom: 10px; color: #6db46d;">{$purchase->price|convert:$currency->id}&nbsp;{$currency->sign}</div>
										{*<div style="font-size: 14px; line-height: 1.2; margin-bottom: 24px;"><span style="background: #f9ded3; padding: 0 3px;">−3%</span></div>*}
										<div style="font-size: 14px; line-height: 1.2; margin-bottom: 19px;">{$purchase->amount} {$settings->units}</div>
									</td>
								</tr>
							{/foreach}
						</table>

						{*<div style="font-size: 24px; line-height: 1.2; margin-bottom: 56px; margin-top: 44px;">Следите за заказами <a href="#" style="color: #6db46d;">в личном кабинете</a></div>*}
						<div style="font-size: 17px; line-height: 1.2; margin-bottom: 13px;">Данные заказа:</div>
						<table width="380" cellspacing="0" cellpadding="7">
							<tr>
								<td style="color: #808080; padding-left: 0;" valign="top" width="120">Телефон</td>
								<td valign="top">{$order->phone|escape}</td>
							</tr>
							<tr>
								<td style="color: #808080; padding-left: 0;" valign="top" width="120">Email</td>
								<td valign="top">{$order->email|escape}</td>
							</tr>
							<tr>
								<td style="color: #808080; padding-left: 0;" valign="top" width="120">Дата и время</td>
								<td valign="top">{$order->self_discharge_time|escape}</td>
							</tr>
							{*{if $order->comment}*}
							{*<tr>*}
							{*<td style="color: #808080; padding-left: 0;" valign="top" width="120">Email</td>*}
							{*<td valign="top">{$order->comment|escape|nl2br}</td>*}
							{*</tr>*}
							{*{/if}*}
							{if $delivery}
								<tr>
									<td style="color: #808080; padding-left: 0;" valign="top">Способ доставки</td>
									<td valign="top">{$delivery->name}</td>
								</tr>
							{/if}
							{if $delivery && !$order->separate_delivery}
								<tr>
									<td style="color: #808080;  padding-left: 0;" valign="top">{$delivery->name}</td>
									<td valign="top">{$order->delivery_price|convert:$currency->id}&nbsp;{$currency->sign}</td>
								</tr>
							{/if}
							<tr>
								<td style="color: #808080;  padding-left: 0;" valign="top">Адрес доставки</td>
								<td valign="top">{$order->address|escape}</td>
							</tr>
							{if $payment_method}
								<tr>
									<td style="color: #808080;  padding-left: 0;" valign="top">Способы оплаты</td>
									<td valign="top">{$payment_method->name}</td>
								</tr>
							{/if}
							<tr>
								<td style="color: #808080;  padding-left: 0;" valign="top">Имя и фамилия</td>
								<td valign="top">{$order->name|escape}</td>
							</tr>

							{if $order->discount}
								<tr>
									<td style="color: #808080;  padding-left: 0;" valign="top">
										Скидка
									</td>
									<td valign="top">
										{$order->discount}&nbsp;%
									</td>
								</tr>
							{/if}

							{if $order->coupon_discount>0}
								<tr>
									<td style="color: #808080;  padding-left: 0;" valign="top">
										Купон {$order->coupon_code}
									</td>
									<td valign="top">
										&minus;{$order->coupon_discount}&nbsp;{$currency->sign}
									</td>
								</tr>
							{/if}

						</table>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
				</tr>
			</table>
		</div>
	</div>
</div>



