{get_browsed_products var=browsed_products limit=20}
{if $browsed_products}
	<section class="section additional-catalog-section">
		<div class="section__title additional-catalog-section__title h2">Вы смотрели</div>
		<div class="additional-catalog js-additional-catalog">
			{foreach $browsed_products as $browsed_product}
			<div class="additional-catalog__item">
				{include file='_product.tpl' variants=false product=$browsed_product imageLazyLoad=true}
			</div>
			{/foreach}
		</div>
	</section>
{/if}
