{* Шаблон текстовой страницы *}

{* Канонический адрес страницы *}
{$canonical="/{$page->url}" scope=parent}
<section class="section">
	<div class="wrapper">
		<div class="breadcrumbs">
			<div class="breadcrumbs__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
				<a href="{$config->root_url}/" class="breadcrumbs__link" itemprop="item">
					<span itemprop="name">Главная</span>
				</a>
				<meta itemprop="position" content="1" />
			</div>
			<div class="breadcrumbs__item">{$page->header|escape}</div>
		</div>
		<h1 data-page="{$page->id}">{$page->header|escape}</h1>
		<div class="page-wrapper">
			{if $categories_pages_cat}
				<div class="categories">
					{foreach $categories_pages_cat as $s}
						{if $s->visible  && $s->visible_cat_page}
							<div class="category-block">
								<a href="{$config->root_url}/catalog/{$s->url}" class="link-cat">
									<div class="bg-image">
										{if $s->image}
											<img src="../{$config->categories_images_dir}{$s->image}" alt="{$s->name|escape}">
										{else}
											<img src="../files/uploads/logo-mobile.png" alt="logo">
										{/if}
									</div>
									<div class="name-cat">
										<span class="name">{if $s->name_cat_page}{$s->name_cat_page|escape}{else}{$s->name|escape}{/if}</span>{if isset($s->products_count)}<span class="count_product_cat">({$s->products_count|escape})</span>
									</div>
									{/if}
								</a>
							</div>
						{/if}
					{/foreach}
				</div>
			{/if}
		</div>
		{$page->body}
	</div>
</section>
