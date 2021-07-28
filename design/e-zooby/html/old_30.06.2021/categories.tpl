{* Список товаров *}

{* Канонический адрес страницы *}
{if $category && $brand}
	{$canonical="/catalog/{$category->url}/{$brand->url}" scope=parent}
{elseif $category}
	{$canonical="/catalog/{$category->url}" scope=parent}
{elseif $brand}
	{$canonical="/brands/{$brand->url}" scope=parent}
{elseif $keyword}
	{$canonical="/products?keyword={$keyword|escape}" scope=parent}
{else}
	{$canonical="/products" scope=parent}
{/if}
<section class="section section_bg section_catalog section-add">
	<div class="wrapper">
		<div>
			<div class="breadcrumbs" itemscope="" itemtype="https://schema.org/BreadcrumbList">
				<div class="breadcrumbs__item" itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem">
					<a href="{$config->root_url}/" class="breadcrumbs__link" itemprop="item">
						<span itemprop="name">Главная</span>
					</a>
					<meta itemprop="position" content="1" />{$itemBread = 2}
				</div>
				{if $category}
					{foreach $category->path as $cat}
						{if $cat@last && !$brand}
							<div class="breadcrumbs__item">
								{if $cat->h1_head} {$cat->h1_head|escape} {else} {$cat->name|escape} {/if}
							</div>
						{else}
						<div class="breadcrumbs__item" itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem">

								<a class="breadcrumbs__link" href="{$config->root_url}/catalog/{$cat->url}" itemprop="item">
									<span itemprop="name">{$cat->name|escape}</span>
								</a>
								<meta itemprop="position" content="{$itemBread}" />{$itemBread = $itemBread+1}

						</div>
						{/if}
					{/foreach}
					{if $brand}
						<div class="breadcrumbs__item">{$brand->name|escape}</div>
					{/if}
				{elseif $brand}
					<div class="breadcrumbs__item" itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem">
						<a class="breadcrumbs__link" href="{$config->root_url}/brands" itemprop="item">
							<span itemprop="name">Бренды</span>
						</a>
						<meta itemprop="position" content="{$itemBread}" />{$itemBread = $itemBread+1}
					</div>
					<div class="breadcrumbs__item">{$brand->name|escape}</div>
				{elseif $keyword}
					<div class="breadcrumbs__item">Поиск</div>
				{/if}
			</div>
			{if $keyword}
				<h1 class="page-title">Поиск {$keyword|escape}</h1>
			{elseif $page}
				<h1 class="page-title">{$page->name|escape}</h1>
			{else}
				<h1 class="page-title">{if $category->h1_head} {$category->h1_head|escape} {else} {$category->name|escape} {/if} {$brand->name|escape}</h1>
			{/if}
		</div>

		<div class="page-wrapper">
			{$category_sidebar = false}
			{if $category->subcategories}
				{$category_sidebar = $category}
			{elseif $category->path[count($category->path)-2]->subcategories}
				{$category_sidebar = $category->path[count($category->path)-2]}
			{elseif $category->path.0->subcategories}
				{$category_sidebar = $category->path.0}
			{/if}
			{if $category_sidebar}
				<div class="home_page_prev_area"><a href="{$config->root_url}/">Другие разделы</a></div>
			<div class="categories">
				{foreach $category_sidebar->subcategories as $s}
				{if $s->visible}
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
								<span class="name">{$s->name|escape}</span>{if isset($s->products_count)}<span class="count_product_cat">({$s->products_count|escape})</span>
							</div>
							{/if}
						</a>
				</div>
				{/if}
				{/foreach}
			</div>
				{if $current_page_num==1}
					{if $page}
						{$page->body}
					{elseif $category}
						{$category->description}
					{elseif $brand}
						{$brand->description}
					{/if}
				{/if}
			{/if}
		</div>
	</div>
</section>
