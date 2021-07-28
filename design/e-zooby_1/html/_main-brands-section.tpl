{get_brands var=all_brands visible_is_main=1}
{if $all_brands}
	{$max_colom = ceil(count($all_brands)/2)}
	<section class="section section_bg main-brands-section">
		<div class="wrapper">
			<h2 class="section__title main-brands-section__title">Бренды</h2>
			<div class="main-brands">
			{foreach array_chunk($all_brands, $max_colom, true) as $brands_coloms}
				<div class="main-brands__group">
					{foreach array_chunk($brands_coloms, ceil($max_colom/2), true) as $brands_colom}
					<ul class="main-brands__list">
						{foreach $brands_colom as $b}
							<li class="main-brands__item">
								<a href="{$config->root_url}/brands/{$b->url}" class="main-brands__link">{$b->name|escape}</a>
								{if $b->label_new} <span class="sticker main-brands__sticker">NEW</span>{/if}
							</li>
						{/foreach}
						{if $brands_coloms@index == 1 && $brands_colom@index == 1}
							<li class="main-brands__item">
								<a href="{$config->root_url}/brands" class="main-brands__link main-brands__link_highlight">Все бренды</a>
							</li>
						{/if}
					</ul>
					{/foreach}
				</div>
			{/foreach}

			</div>
		</div>
	</section>
{/if}
