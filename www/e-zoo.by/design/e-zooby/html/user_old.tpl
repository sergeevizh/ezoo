{* Шаблон страницы зарегистрированного пользователя *}
<section class="section section_order">
	<div class="wrapper">
		<h1>Профиль</h1>

		<div class="account-block">
			<div class="account-block__content">
				<section class="account js-account">
					{if $group->image}
						<div class="account__image-field" style="background-image: url('{$config->groups_images_dir}{$group->image}');">
							<img src="{$config->groups_images_dir}{$group->image}" alt="{$user->group_name|escape}" class="account__image">
						</div>
					{/if}

					<div class="account__content"{if !$group->image} style="margin-left: 0"{/if}>
						<form method="post">


							<div class="account__header">
								<input name="name" type="text" class="editable-input account__title" value="{$name|escape}" disabled>
								{if $user->group_name}
									<input type="text" class="editable-input account__status" value="{$user->group_name|escape}" disabled readonly>
								{/if}
							</div>
							<div class="account__data">
								<input name="phone" type="text" class="editable-input account__data__row" value="{$phone|escape}" disabled placeholder="Номер телефона">
								<input name="email" type="text" class="editable-input account__data__row" value="{$email|escape}" disabled placeholder="Ваш email">
								<input id="password" class="editable-input account__data__row" value="" name="password" type="password" style="display:none;" placeholder="укажите новый пароль">

							</div>
							<div class="account__control">
								<div class="account__control-field">
									<a href="#" class="account__control-link js-change-password" onclick="$('#password').show();return false;" style="display:none;">Изменить пароль</a>
									<a href="#" class="account__control-link js-account-data-edit-link">Изменить данные</a>
								</div>
								<div class="account__control-field">
									<input class="btn js-account-save-input" type="submit" value="Сохранить" style="display: none">
								</div>
								{*<div class="account__control-field"><a href="#password-window" class="account__control-link js-popup-link">Изменить пароль</a></div>*}
							</div>
						</form>
					</div>
				</section>
			</div>
			<div class="account-block__aside">
				{if $user->discount > 0}
				<div class="discount-info">
					<div class="discount-info__val">{$user->discount}%</div>
					<div class="discount-info__content">ваша персональная скидка</div>
				</div>
				{/if}
			</div>
		</div>
		{if $orders}

			<h2>Последние заказы</h2>

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

			{if $count_orders > 6}
				<a href="/user/orders" class="border-link all-archive-link">Архив заказов</a>
			{/if}
		{/if}
	</div>
</section>
