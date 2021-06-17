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
	<div class="catalog-header-mob">
		<div class="catalog-header-mob__field">
			{if $category->subcategories}
			<button type="button" class="catalog-header-mob__link a js-sidebar-nav-block-link"><span class="catalog-header-mob__link-text">{$category->name|escape}</span></button>
			{elseif $category->path[count($category->path)-2]}
				<button type="button" class="catalog-header-mob__link catalog-header-mob__link_back a js-sidebar-nav-block-link"><span class="catalog-header-mob__link-text">{$category->path[count($category->path)-2]->name}</span></button>
			{elseif $category->path.0}
				<button type="button" class="catalog-header-mob__link catalog-header-mob__link_back a js-sidebar-nav-block-link"><span class="catalog-header-mob__link-text">{$category->path.0->name|escape}</span></button>
			{elseif $results_categories}
				<button type="button" class="catalog-header-mob__link catalog-header-mob__link_back a js-sidebar-nav-block-link">
					<span class="catalog-header-mob__link-text">Категории</span>
				</button>
			{/if}
		</div>
		<div class="catalog-header-mob__field">
			<button type="button" class="catalog-header-mob__link a js-sidebar-filter-block-link">
				<span class="catalog-header-mob__link-text">Сортировать</span>
			</button>
		</div>
	</div>

	<div class="wrapper">
		<div class="page-header">
			<div class="breadcrumbs" itemscope="" itemtype="http://schema.org/BreadcrumbList">
				{if $category}
					{foreach $category->path as $cat}
						<div class="breadcrumbs__item">
							{if $cat@last && !$brand}
								{$cat->name|escape}
							{else}
								<a class="breadcrumbs__link" href="{$config->root_url}/catalog/{$cat->url}" itemscope="" itemprop="itemListElement" itemtype="http://schema.org/ListItem">
									<span itemprop="name">{$cat->name|escape}</span>
								</a>
							{/if}
						</div>
					{/foreach}
					{if $brand}
						<div class="breadcrumbs__item">{$brand->name|escape}</div>
					{/if}
				{elseif $brand}
					<div class="breadcrumbs__item">
						<a class="breadcrumbs__link" href="{$config->root_url}/brands" itemscope="" itemprop="itemListElement" itemtype="http://schema.org/ListItem">
							<span itemprop="name">Бренды</span>
						</a>
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
				<h1 class="page-title">{$category->name|escape} {$brand->name|escape}</h1>
			{/if}
		</div>

		<div class="page-wrapper js-page-wrapper">
			<div class="page-sidebar js-page-sidebar">
				{$category_sidebar = false}

				{if $category->subcategories}
					{$category_sidebar = $category}
				{elseif $category->path[count($category->path)-2]->subcategories}
					{$category_sidebar = $category->path[count($category->path)-2]}
				{elseif $category->path.0->subcategories}
					{$category_sidebar = $category->path.0}
				{/if}


				{if $category_sidebar}
					<div class="sidebar-nav-block js-sidebar-nav-block">
						<button type="button" class="sidebar-nav-block__close close js-sidebar-nav-block-close"></button>
						<div class="sidebar-nav-block__content">
							<div class="sidebar-nav">
								<div class="sidebar-nav__header">
									<span class="sidebar-nav__text">{$category_sidebar->name|escape}</span>{if isset($category_sidebar->products_count)}<span class="sidebar-nav__count">{$category_sidebar->products_count|escape}</span>
									{/if}
								</div>

								<ul class="sidebar-nav__list">
									{foreach $category_sidebar->subcategories as $s}
										{if $s->visible}
										<li class="sidebar-nav__list-item">
											<a href="{$config->root_url}/catalog/{$s->url}" class="sidebar-nav__list-link">
												<span class="sidebar-nav__text">{$s->name|escape}</span>{if isset($s->products_count)}<span class="sidebar-nav__count">{$s->products_count|escape}</span>
												{/if}
											</a>
										</li>
										{/if}
									{/foreach}
								</ul>

							</div>
						</div>
					</div>
				{elseif $results_categories}
					<div class="sidebar-nav-block js-sidebar-nav-block">
						<button type="button" class="sidebar-nav-block__close close js-sidebar-nav-block-close"></button>
						<div class="sidebar-nav-block__content">
							<div class="sidebar-nav">
								<ul class="sidebar-nav__list">
									{foreach $results_categories as $s}
										{$url = "{$config->root_url}/catalog/{$s->url}"}

										<li class="sidebar-nav__list-item">
											<a href="{$url}" class="sidebar-nav__list-link">
												<span class="sidebar-nav__text">{$s->name|escape}</span>{if isset($s->products_count)}<span class="sidebar-nav__count">{$s->products_count|escape}</span>
												{/if}
											</a>
										</li>
									{/foreach}
								</ul>

							</div>
						</div>
					</div>
				{/if}

				{if $features || $category->brands|count > 1}
					{include file="_filter.tpl"}
				{/if}
			</div>

			<div class="page-content">
				<div class="page-content__inner">
					<div class="catalog-header">
						{*<div class="catalog-header__view-control">
							<div class="catalog-view-control">
								<button class="catalog-view-control__link catalog-view-control__link_grid js-catalog-view-grid is-active"></button>
								<button class="catalog-view-control__link catalog-view-control__link_hrz js-catalog-view-hrz"></button>
								<button class="catalog-view-control__link catalog-view-control__link_list js-catalog-view-list"></button>
							</div>
						</div>*}
					</div>

					<div class="catalog js-catalog">
						{foreach $products as $product}
						<div class="catalog__item js-catalog-item">
							{include file="_product.tpl" imageLazyLoad=true}
						</div>
						{/foreach}
					</div>

					{include file='pagination.tpl'}

					{include file='_browsed_products.tpl'}

					{if $current_page_num==1}
						{if $page}
							{$page->body}
						{elseif $category}
							{$category->description}
						{elseif $brand}
							{$brand->description}
						{/if}
					{/if}

				</div>
			</div>
		</div>
	</div>
</section>
