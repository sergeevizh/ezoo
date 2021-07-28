{* Страница заказа *}

{$meta_title = "Ваш заказ №`$order->id`" scope=parent}

<section class="section">
	<div class="wrapper">
		<section class="order">
			<div class="order__header">
				<div class="order__header-top">
					<h1 class="order__title">Ваш заказ №{$order->id}
					{if $order->status == 0}принят{/if}
					{if $order->status == 1}принят{elseif $order->status == 2}выполнен{/if}</h1>
                    {if $order->paid == 1}
						<span class="order__status-icon">
							<span class="order-status-icon order-status-icon_success"></span>
						</span>
					{/if}
				</div>

				<div class="order__header-bottom">

					{$total_products = $purchases|count}
					{foreach $purchases as $purchase}
						{$total_products = $total_products + $purchase->amount - 1}
					{/foreach}

					<div class="order__header-info">
						<div class="order__header-count">
							<div class="order__count"><span class="order__count-label">Итого</span> {$total_products} {$total_products|plural:'товар':'товаров':'товара'}</div>
						</div>
						<div class="order__header-total">
							<div class="order__total">{$order->total_price|convert}&nbsp;{$currency->sign}</div>
						</div>
					</div>
				</div>
			</div>

			<div class="order__content">
				<div class="order__list">
					{*<div class="p-list">
						{foreach $purchases as $purchase}
						<div class="p-item">
							<div class="p-item__inner">
								<div class="p-item__image-block">
									<a href="{$config->root_url}/products/{$purchase->product->url}">
										{$image = $purchase->product->images|first}
										{if $image}
										<img src="{$image->filename|resize:300:300}" alt="{$purchase->product_name|escape}" class="p-item__image">
										{/if}
									</a>
								</div>
								<div class="p-item__content-block">
									<h3 class="p-item__title">
										<a href="{$config->root_url}/products/{$purchase->product->url}">{$purchase->product_name|escape}</a>
										<span class="p-item__title-variant">{$purchase->variant_name|escape}</span>
										{if 0 && $order->paid && $purchase->variant->attachment}
											<a class="download_attachment" href="order/{$order->url}/{$purchase->variant->attachment}">скачать файл</a>
										{/if}
									</h3>

									<div class="p-item__meta">
										{if $purchase->product->brand}
											<a href="{$config->root_url}/brands/{$purchase->product->brand_url}" class="p-item__meta-link">{$purchase->product->brand|escape}</a>
										{/if}
									</div>
									<div class="p-item__description">{$purchase->product->annotation}</div>
								</div>
								<div class="p-item__price-block">
									<div class="p-item__price-field">
										<div class="p-item__price-field-content">
											<span class="p-item__price">{($purchase->price)|convert}&nbsp;{$currency->sign}</span>
										</div>
									</div>
									<div class="p-item__count-field">{$purchase->amount}&nbsp;{$settings->units}
									</div>
								</div>
							</div>
						</div>
						{/foreach}
					</div>*}
				</div>
				<div class="order__data-list">
					{if $order->name}
						<div class="order__data-list-row">
							<div class="order__data-list-title">Имя</div>
							<div class="order__data-list-description">{$order->name|escape}</div>
						</div>
					{/if}
					{if $order->email}
						<div class="order__data-list-row">
							<div class="order__data-list-title">Email</div>
							<div class="order__data-list-description">{$order->email|escape}</div>
						</div>
					{/if}
					{if $order->phone}
						<div class="order__data-list-row">
							<div class="order__data-list-title">Телефон</div>
							<div class="order__data-list-description">{$order->phone|escape}</div>
						</div>
					{/if}
					{if $order->address}
						<div class="order__data-list-row">
							<div class="order__data-list-title">Адрес доставки</div>
							<div class="order__data-list-description">{$order->address|escape}</div>
						</div>
					{/if}
					{if $order->self_discharge_time}
						<div class="order__data-list-row">
							<div class="order__data-list-title">Дата и время</div>
							<div class="order__data-list-description">{$order->self_discharge_time|escape}</div>
						</div>
					{/if}
					{if $order->comment}
						<div class="order__data-list-row">
							<div class="order__data-list-title">Комментарий</div>
							<div class="order__data-list-description">{$order->comment|escape|nl2br}</div>
						</div>
					{/if}
					{if $payment_method}
						<div class="order__data-list-row">
							<div class="order__data-list-title">Оплата</div>
							<div class="order__data-list-description">{$payment_method->name|escape}</div>
						</div>
					{/if}
				</div>
			</div>
			<div class="clearfix"></div>


			{if !$order->paid}
				{* Выбор способа оплаты *}
				{if $payment_methods && !$payment_method && $order->total_price>0}
					<form method="post">
						<h2>Выберите способ оплаты</h2>
						<div class="checklist">


							{foreach $payment_methods as $payment_method}
								<label class="checklist__row">
									<input type="radio" class="checklist__input js-styler" name="payment_method_id" value="{$payment_method->id}" {if $payment_method@first}checked{/if}>
									<span class="checklist__text">
										{$payment_method->name}, к оплате {$order->total_price|convert:$payment_method->currency_id}&nbsp;{$all_currencies[$payment_method->currency_id]->sign}
										<div class="description">
											{$payment_method->description}
										</div>
									</span>
								</label>

							{/foreach}
						</div>
						<input type='submit' class="btn basket-btn" value='Закончить заказ'>
					</form>

					{* Выбраный способ оплаты *}
				{elseif $payment_method}

					<h3>Способ оплаты &mdash; {$payment_method->name}</h3>
						<form method="post">
							<input class="btn btn_border" type="submit" name="reset_payment_method" value='Выбрать другой способ оплаты'>
						</form>

					<p>
						{$payment_method->description}
					</p>
					<h2>
						К оплате {$order->total_price|convert:$payment_method->currency_id}&nbsp;{$all_currencies[$payment_method->currency_id]->sign}
					</h2>

					<div class="js-add-class-btn">
						{* Форма оплаты, генерируется модулем оплаты *}
						{checkout_form order_id=$order->id module=$payment_method->module}
						<script>
							document.addEventListener("DOMContentLoaded", function() {
								document.querySelector('.js-add-class-btn [type=submit]').classList.add('btn', 'btn_large')
							});
						</script>
					</div>

				{/if}

			{/if}



		</section>
	</div>
</section>
