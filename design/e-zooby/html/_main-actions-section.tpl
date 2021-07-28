{get_discounted_products var=discounted_products limit=4}
{if $discounted_products}
	<section class="section actions-section">
		<div class="wrapper">
			<h2 class="section__title actions-section__title">Акции</h2>
		</div>
		<div class="actions">
			<div class="wrapper">
				<div class="actions__content js-actions-content">
					{foreach $discounted_products as $discounted_product}
						<div class="actions__item">
							{include file="_product.tpl" addclass="cart_large" product=$discounted_product imageLazyLoad=true}
						</div>
					{/foreach}
				</div>
			</div>
			{api module='products' method='count_products' _=['visible'=>1, 'discounted'=>1] var=discounted_count_products}
			{if $discounted_count_products > 4}
				{* TODO frond-end http://c2n.me/3EiWZvf *}
				<div class="actions__overlay-block">
					<a href="{$config->root_url}/actions" class="actions__overlay"><span class="actions__overlay-content">Еще {$discounted_count_products-4} {($discounted_count_products-4)|plural:'товар':'товаров':'товара'}</span></a>
				</div>
			{/if}
		</div>
	</section>
{/if}
