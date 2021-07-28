	<div class="cart js-cart{if $addclass} {$addclass}{/if}">
		<div class="cart__inner">
			<div class="cart__content">
				<h3 class="cart__title">
					<a href="{$config->root_url}/products/{$product->url}" class="cart__title-link">{$product->name|escape}</a>
				</h3>
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
				</div>

			<div class="cart__image-field">
				{if $product->image}
					<a href="{$config->root_url}/products/{$product->url}" class="cart__image-link">
						{if $imageLazyLoad}
							<img src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" data-echo="{$product->image->filename|resize:600:600}" alt="{$product->name|escape}" class="cart__imgae js-cart-image">
						{else}
							<img src="{$product->image->filename|resize:600:600}" alt="{$product->name|escape}" class="cart__imgae js-cart-image">
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
								<img src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" data-echo="{$image->filename|resize:600:600}" alt="{$product->name|escape}" class="cart__imgae-slider-image js-cart-thumb-img">
							</div>
						{/foreach}
					</div>
				</div>
			{/if}

			{if $variants !== false}
				{if $product->variants|count >= 1 }
					<div class="product__variant-table product__variant-table-small">
						{*<div class="product__variant">
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
						</div>*}
						{foreach $product->variants as $v}
							<form action="{$config->root_url}/cart" class="product__variant js-cart-basket-submit" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
								<input type="hidden" name="variant" value="{$v->id}">
								{if $v->name && $product->variants|count == 1 || $product->variants|count > 1}
									<div class="product__variant-cell product__variant-name">
										{$v->name|truncate:10}
									</div>
								{/if}
								<div class="product__variant-cell product__variant-price{if $v->compare_price > 0} product__variant-price-sale{/if}">
									{if $v->compare_price > 0}{*<span class="product__variant-price-compare">{$v->compare_price|convert}</span>*}{/if}
									<span itemprop="price" content="{$product->price}">{$v->price|convert} {$currency->sign|escape}</span>
								</div>
								<div class="product__variant-cell product__variant-amount">
									<div class="count-field js-count-field">
										<span class="count-field__control count-field__control_down js-count-field-down">-</span>
										<input type="text" class="count-field__val js-count-field-val" name="amount" value="1" max="{$v->stock}">
										<span class="count-field__control count-field__control_up js-count-field-up">+</span>
									</div>
								</div>
								<div class="product__variant-cell product__variant-submit">
									<button type="submit" class="btn btn_small basket-btn product__basket-btn js-cart-basket-btn"></button>
								</div>
							</form>
						{/foreach}
					</div>
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
						<button type="submit" class="btn basket-btn cart__btn js-cart-basket-btn{if in_array($product->variant->id, $shopping_cart)} is-active{/if}">
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
