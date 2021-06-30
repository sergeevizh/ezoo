{* Информера корзины (отдаётся аяксом) *}

{if $cart->total_products>0}
	<a href="./cart/" class="basket-field__link" title="{$cart->total_products} {$cart->total_products|plural:'товар':'товаров':'товара'} на {if $cart->bonus_price}{$cart->bonus_price|convert} {else}{$cart->total_price|convert} {/if}{$currency->sign|escape}">
		<i class="basket-field__icon"></i>
		<span class="basket-field__count js-basket-count" style="display: none">{$cart->total_products}</span>
		<span class="count-sum-cart">{$cart->total_products} {$cart->total_products|plural:'товар':'товаров':'товара'} на {if $deliveries}{if $deliveries[0]->bonus_price}{($deliveries[0]->bonus_price)|convert}{else}{($deliveries[0]->total_price)|convert}{/if}{else}{$cart->total_price|convert}{/if} {$currency->sign|escape}</span>
	</a>
{else}
	<a href="./cart/" class="basket-field__link">
		<i class="basket-field__icon"></i>
		<span class="basket-field__count js-basket-count">0</span>
	</a>
{/if}
