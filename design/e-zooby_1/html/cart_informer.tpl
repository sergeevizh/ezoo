{* Информера корзины (отдаётся аяксом) *}

{if $cart->total_products>0}
	<a href="./cart/" class="basket-field__link" title="{$cart->total_products} {$cart->total_products|plural:'товар':'товаров':'товара'} на {$cart->total_price|convert} {$currency->sign|escape}">
		<i class="basket-field__icon"></i>
		<span class="basket-field__count js-basket-count">{$cart->total_products}</span>
	</a>
{else}
	<a href="#" class="basket-field__link">
		<i class="basket-field__icon"></i>
		<span class="basket-field__count js-basket-count">0</span>
	</a>
{/if}
