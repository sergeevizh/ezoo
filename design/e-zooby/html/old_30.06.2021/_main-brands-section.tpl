{get_brands var=all_brands visible_is_main=1}
{if $all_brands}
	<section class="section section_bg main-brands-section for-desktop">
		<div class="wrapper">
			<div class="section__title main-brands-section__title h2">Бренды</div>
			<div class="slider-brands">
						{foreach $all_brands as $b}
							<div class="main-brand__item" itemscope
								 itemtype="http://schema.org/ImageObject">
								<a href="{$config->root_url}/brands/{$b->url}" class="main-brands__link" title="{$b->name|escape}">
									<meta itemprop="name" content="{$b->name|escape}">
								<img src="../{$config->brands_images_dir}{$b->image|escape}" itemprop="contentUrl" alt="{$b->name|escape}"></a>
								<meta itemprop="description" content="{$b->name|escape}">
							</div>
						{/foreach}
				</div>
		</div>
	</section>
{/if}
