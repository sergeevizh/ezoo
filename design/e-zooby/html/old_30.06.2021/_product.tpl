<div class="cart js-cart{if $addclass} {$addclass}{/if}{if $product->marketing_offer} marketing-offer{/if}">
	<div class="cart__inner">
		<div class="cart__content">
			<div class="cart__title h3">
				<a href="{$config->root_url}/products/{$product->url}" class="cart__title-link"><span>{$product->name|escape}</span></a>
			</div>

			<!--<span style="display:none;">{$product->name|escape}</span>-->

			{* TODO вывести вкус*}
			{if $product->taste}
			<div class="cart__taste">{$product->taste|escape}</div>
			{/if}
			<div class="cart__meta">
				{if $product->brand}
				<a href="{$config->root_url}/brands/{$product->brand_url}" class="cart__meta-item">{$product->brand|escape}</a>
				{/if}
				{if $product->comments_count > 0}
				<a href="{$config->root_url}/products/{$product->url}#comments" class="comment-link cart__comment-link">{$product->comments_count}</a>
				{/if}
			</div>
			<div class="cart__description">{$product->annotation}</div>
		</div>
		{if $variants === false}{/if}
		<div class="cart__price-field">
			<div class="cart__price-field-content">
				{if $product->variants|count == 0}
				<small>Нет в наличии</small>
				{/if}
				{if $product->price}
				<!--<span class="cart__price">{$product->price|convert} {$currency->sign|escape}</span>-->
				{/if}
				{if $product->compare_price > 0}
				<!--<span class="cart__old-price">{$product->compare_price|convert} {$currency->sign|escape}</span>-->
				{/if}
			</div>
			{if $product->compare_price > 0}
			<!--<div class="cart__price-field-discount">
				<span class="cart__discount">−{ceil(100-$product->price/$product->compare_price*100)}%</span>
			</div>-->
			{/if}

			{*if $variants !== false}

			<div class="show-all-price">Твоя цена</div>

			{/if*}

		</div>

		<div class="cart__image-field">
			{if $product->image}
			<a href="{$config->root_url}/products/{$product->url}" class="cart__image-link" itemscope
			   itemtype="http://schema.org/ImageObject">

				{if $imageLazyLoad}
				<!--<span style="display:none;" src="{$product->image->filename|resize:600:600}"></span>-->
				<meta itemprop="name" content="{$product->name|escape}">
				<img src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
					 data-echo="{$product->image->filename|resize:600:600}" alt="{$product->name|escape}"
					 class="cart__imgae js-cart-image" itemprop="contentUrl">
				<meta itemprop="description" content="{$product->name|escape}">
				{else}
				<meta itemprop="name" content="{$product->name|escape}">
				<img src="{$product->image->filename|resize:600:600}" alt="{$product->name|escape}"
					 class="cart__imgae js-cart-image" itemprop="contentUrl">
				<meta itemprop="description" content="{$product->name|escape}">
				{/if}
			</a>
			{/if}
			{if $product->compare_price > 0}
			<div class="sticker cart__sticker">Акция</div>
			{/if}

		</div>
		{if $product->images|count > 1}
		<div class="cart__imgae-slider-field">
			<div class="cart__imgae-slider js-cart-thumbs">
				{foreach $product->images as $image}
				<div class="cart__imgae-slider-item js-cart-thumb{if $image->id == $product->image->id} is-active{/if}">
					<img src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
						 data-echo="{$image->filename|resize:600:600}" alt="{$product->name|escape}"
						 class="cart__imgae-slider-image js-cart-thumb-img">
				</div>
				{/foreach}
			</div>
		</div>
		{/if}

		{if $variants !== false}
		{if $product->variants|count >= 1 }
		<!--		TODO ветпрепарат-->
		{if !$product->lecense|count >= 1 }
		<div class="product__variant-table product__variant-table-small">
			{*
			<div class="product__variant">
				{if $product->variant->name && $product->variants|count == 1 || $product->variants|count > 1}
				<div class="product__variant-cell">
					<small>Товар</small>
				</div>
				{/if}
				<div class="product__variant-cell">
					<small>Цена, руб.</small>
				</div>
				<div class="product__variant-cell">
					<small>Заказ</small>
				</div>
				<div class="product__variant-cell"></div>
			</div>
			*}
			<div class="line-deliver-name">
				<div class="product__variant-cell product__variant-name for-desktop"></div>
				<div class="product__variant-cell product__variant-price">
					<span class="for-mobile name-variand-mobi"></span>
					<span class="price-span"></span><span
						class="text-deliver-name">online-заказ<br> на доставку</span><span class="text-deliver-name">online-заказ<br> на самовывоз</span>
				</div>
			</div>
			{foreach $product->variants as $v}
			<form action="{$config->root_url}/cart" class="product__variant js-cart-basket-submit"
				  data-variant-name="{$v->name|truncate:10}"
				  data-variant-price-pickup="{if $v->sale['delivery']}{$v->sale['delivery']}{else}{$v->price|convert}{/if}"
				  data-variant-price-delivery="{if $v->sale['pickup']}{$v->sale['pickup']}{else}{$v->price|convert}{/if}">
				<input type="hidden" name="variant" value="{$v->id}">
				<div class="product__variant-cell product__variant-name for-desktop">
					{if $v->name && $product->variants|count == 1 || $product->variants|count > 1}
					{$v->name|truncate:6}
					{/if}
				</div>
				<div
					class="product__variant-cell product__variant-price{if $v->compare_price > 0} product__variant-price-sale{/if}">
					<span class="for-mobile name-variand-mobi">{$v->name|truncate:6}</span>{if $v->compare_price >
					0}{*<span class="product__variant-price-compare">{$v->compare_price|convert}</span>*}{/if}
					<span class="price-span" content="{$product->price}">{$v->price|convert}</span><span
						class="price-span not-background"
						content="{if $v->sale['delivery']}{$v->sale['delivery']}{else}{$v->price|convert}{/if}">{if $v->sale['delivery']}{$v->sale['delivery']}{else}{$v->price|convert}{/if}</span><span
						class="price-span not-background"
						content="{if $v->sale['pickup']}{$v->sale['pickup']}{else}{$v->price|convert}{/if}">{if $v->sale['pickup']}{$v->sale['pickup']}{else}{$v->price|convert}{/if}</span>
					<span style="display:none;">BYN</span>
				</div>
				<div class="product__variant-cell product__variant-amount">
					<div class="count-field js-count-field">
						<span class="count-field__control count-field__control_down js-count-field-down">-</span>
						<input type="number" class="count-field__val js-count-field-val" name="amount" value="1"
							   max="{$v->stock}">
						<span class="count-field__control count-field__control_up js-count-field-up">+</span>
						<span class="for-mobile text-count">шт.</span>
					</div>
				</div>
				<div class="product__variant-cell product__variant-submit">
					<button type="submit" class="btn btn_small basket-btn product__basket-btn js-cart-basket-btn {if $v->supply_dates && $v->supply_dates != '' && $v->stock <=0}basket-btn--yellow{/if}">
					</button>
					{if $v->supply_dates && $v->supply_dates != '' && $v->stock <=0}
					<div class="supply_date">
						Следующая поставка {$v->supply_dates}
					</div>
					{/if}
				</div>
			</form>
			{/foreach}
		</div>
		{/if}
		<!--		TODO end-->
		<!--		TODO start+-->
		{if $product->lecense > 0}


		<div class="product__variant-table product__variant-table-small">
			{foreach $product->variants as $v}
			<form action="{$config->root_url}/cart" class="product__variant js-cart-basket-submit basket__vetpreparat-block"
				  data-variant-name="{$v->name|truncate:10}"
				  data-variant-price-pickup="{if $v->sale['delivery']}{$v->sale['delivery']}{else}{$v->price|convert}{/if}"
				  data-variant-price-delivery="{if $v->sale['pickup']}{$v->sale['pickup']}{else}{$v->price|convert}{/if}">
				<input type="hidden" name="variant" value="{$v->id}">
				<div class="product__variant-cell product__variant-name for-desktop">
					{if $v->name && $product->variants|count == 1 || $product->variants|count > 1}
					{$v->name|truncate:6}
					{/if}
				</div>

				<div class="product__variant-cell product__variant-amount">
					<div class="count-field js-count-field">
						<span class="count-field__control count-field__control_down js-count-field-down">-</span>
						<input type="number" class="count-field__val js-count-field-val" name="amount" value="1"
							   max="{$v->stock}">
						<span class="count-field__control count-field__control_up js-count-field-up">+</span>
						<span class="for-mobile text-count">шт.</span>
					</div>
				</div>
				<div class="product__variant-cell product__variant-submit btn__basket_vetpreparat">

					<button type="submit" class="btn btn_small product__basket-btn js-cart-basket-btn btn__basket-vetpreparat {if $v->supply_dates && $v->supply_dates != '' && $v->stock <=0}basket-btn--yellow{/if}{if $v->supply_dates && $v->stock <=0}basket-btn--yellow{/if}" >Доставка из ветаптеки</button>
					{if $v->supply_dates && $v->supply_dates != '' && $v->stock <=0}
					<div class="supply_date">
						Следующая поставка {$v->supply_dates}
					</div>
					{/if}
				</div>
			</form>
			{/foreach}
		</div>
		{/if}
		<!--TODO end-->

		{else}

		{/if}
		{/if}

		{*
		{if $product->variants|count == 1}
		{$shopping_cart = []}
		{if $smarty.session.shopping_cart}
		{$shopping_cart = (array)array_keys($smarty.session.shopping_cart)}
		{/if}
		<form action="{$config->root_url}/cart" class="js-cart-basket-submit">
			<input type="hidden" name="variant" value="{$product->variant->id}">
			<div class="cart__btn-field">
				<button type="submit"
						class="btn basket-btn cart__btn js-cart-basket-btn{if in_array($product->variant->id, $shopping_cart)} is-active{/if}">
					<span>В корзину</span>
					<span class="in-cart">В корзине</span>
				</button>
			</div>
		</form>
		{elseif $product->variants|count > 1 }
		TODO front-end когда у товара несколколько вариантов

		{else}
		TODO front-end когда товара нету в наличии

		{/if}
		*}
	</div>
</div>










