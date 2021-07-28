{$canonical="/user/orders" scope=parent}

{$meta_title = "Архив заказов" scope=parent}
<section class="section section_order">
	<div class="wrapper">
		<h1>Архив заказов</h1>
		<div class="archive">
			<div class="archive__inner">
				{foreach $orders as $order}
					<div class="archive__grid">
						<a href="{$config->root_url}/order/{$order->url}" class="archive__item{if $order->status == 2} archive__item_success{/if}">
							<div class="archive__header">
								<div class="archive__title">
									<span class="archive__title-link">Заказ №{$order->id} </span>
									{if $order->paid == 1}
										<span class="archive__status-icon">
										<span class="archive-status-icon archive-status-icon_success"></span>
									</span>
									{/if}
								</div>
								<div class="archive__date">{$order->date|date}</div>
								<div class="archive__status">
									{if $order->status == 0}В обработке{elseif $order->status == 1}Принят {elseif $order->status == 2}Выполнен{/if}
									{if $order->paid == 1}, оплачен{/if}
									{$order->comment}
									{*Отправлен почтой, трек-код: <b class="cl-highlight archive__track-code">RA038346151FI</b>*}
								</div>
							</div>
							<div class="archive__content">
								{$total_products = 0}
								{api module=orders method=get_purchases var=purchases _=['order_id' => $order->id]}
								{$total_products = $purchases|count}
								{foreach $purchases as $purchase}
									{$total_products = $total_products + $purchase->amount - 1}
								{/foreach}
								{if $total_products > 0}
									<div class="archive__count">{$total_products} {$total_products|plural:'товар':'товаров':'товара'}</div>
								{/if}
								<div class="archive__total">{$order->total_price|convert}&nbsp;{$currency->sign}</div>
							</div>
						</a>
					</div>
				{/foreach}
			</div>
		</div>
	</div>
</section>

