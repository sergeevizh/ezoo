{get_featured_products var=featured_products limit=20 sort=rand}
{if $featured_products}
	<section class="section hits-section section-add">
		<div class="wrapper">
			<h2 class="section__title hits-section__title">Хиты продаж</h2>
		</div>

		<div class="hits">
			{$featured_products_first_three = array_slice($featured_products, 0, 3)}
			{if $featured_products_first_three}
				<div class="hits__main js-hits-main for-desktop">
					{foreach $featured_products_first_three as $featured_product_first_three}
					<div class="hits__main-item{if $featured_product_first_three@first} hits__main-item_prev{elseif $featured_product_first_three@last} hits__main-item_next{/if}">
						{include file="_product.tpl" variants=false addclass="cart_hit" product=$featured_product_first_three imageLazyLoad=true}
					</div>
					{/foreach}
				</div>

				<div class="hits__main js-hits-main-mobile for-mobile">
					{foreach $featured_products_first_three as $featured_product_first_three}
						<div class="actions__item">
						{include file="_product.tpl" variants=true addclass="cart_large" product=$featured_product_first_three imageLazyLoad=true}
					</div>
					{/foreach}
				</div>
			{/if}

			{if $featured_products|count > 3}
				{$featured_products = array_splice($featured_products, 3)}
				<div class="hits__slider js-hits-slider for-desktop">
					<ul class="hits__slider-list">
						{foreach $featured_products as $featured_product}
							<li class="hits__slider-item">
								{include file="_product.tpl" variants=false addclass="cart_small" product=$featured_product imageLazyLoad=true}
							</li>
						{/foreach}
					</ul>
				</div>
			{/if}
		</div>
	</section>
{/if}
