{* Информера корзины (отдаётся аяксом) *}

{if $cart->total_products>0}
	<a href="./cart/" class="basket-field__link" title="{$cart->total_products} {$cart->total_products|plural:'товар':'товаров':'товара'} на {$cart->total_price|convert} {$currency->sign|escape}">
		<i class="basket-field__icon"></i>
		<span class="basket-field__count js-basket-count" style="display: none">{$cart->total_products}</span>
		<span class="count-sum-cart">{$cart->total_products} {$cart->total_products|plural:'товар':'товаров':'товара'} на {if $deliveries} {($deliveries[0]->total_price)|convert} {else}{$cart->total_price|convert}{/if} {$currency->sign|escape}</span>
	</a>
{else}
	<a href="./cart/" class="basket-field__link">
		<i class="basket-field__icon"></i>
		<span class="basket-field__count js-basket-count">0</span>
	</a>
{/if}
