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
{elseif $page}
	{$canonical="/"|cat:$page->url scope=parent}
{else}
	{$canonical="/products" scope=parent}
{/if}
<section class="section section_bg section_catalog section-add"
		 {if $brand}
		 {if $brand->background}
	style="background: url(../{$config->brands_background_images_dir}{$brand->background}) repeat;"
	{elseif $brand->color}
	style="background-color: #{$brand->color}"
	{/if}
	{elseif $category}
	{if $category->background}
	style="background: url(../{$config->categories_background_images_dir}{$category->background}) repeat;"
	{elseif $category->color}
	style="background-color: #{$category->color}"
	{/if}
	{/if}
	>
	<div class="catalog-header-mob">
		<div class="catalog-header-mob__field">
			{if $category->subcategories}
			<button type="button" class="catalog-header-mob__link a js-sidebar-nav-block-link"><span class="catalog-header-mob__link-text">{$category->name|escape}</span></button>
			{elseif $category->path[count($category->path)-2]}
				<a href="{$config->root_url}/catalog/{$category->path[count($category->path)-2]->url}" class="catalog-header-mob__link catalog-header-mob__link_back">
					<span class="catalog-header-mob__link-text">{$category->path[count($category->path)-2]->name}</span>
				</a>
			{elseif $category->path.0}
				<button type="button" class="catalog-header-mob__link catalog-header-mob__link_back a js-sidebar-nav-block-link"><span class="catalog-header-mob__link-text">{$category->path.0->name|escape}</span></button>
			{elseif $results_categories}
				<button type="button" class="catalog-header-mob__link catalog-header-mob__link_back a js-sidebar-nav-block-link">
					<span class="catalog-header-mob__link-text">Категории</span>
				</button>
			{/if}
		</div>
		{if !($keyword && !$products)}
		<div class="catalog-header-mob__field">
			<button type="button" class="catalog-header-mob__link a js-sidebar-filter-block-link">
				<span class="catalog-header-mob__link-text">Сортировать</span>
			</button>
		</div>
		{/if}
	</div>

	<div class="wrapper">
		<div class="page-header">
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
				<h1 class="page-title search-title">{if $products}Результаты поиска по {$keyword|escape}{else}Результатов по Вашему запросу не найдено. Позвоните нам! Мы
						поможем подобрать нужный товар
						<a href="tel:7255" style="text-decoration: underline">7255</a>{/if}</h1>
			{elseif $page}
				<h1 class="page-title">{$page->name|escape}</h1>
			{else}
				<h1 class="page-title">{if $category->h1_head} {$category->h1_head|escape} {else} {$category->name|escape} {/if}{if $brand->h1_head} {$brand->h1_head|escape} {else} {$brand->name|escape} {/if}</h1>
			{/if}
		</div>
		<div class="page-wrapper js-page-wrapper">
			<div class="page-sidebar js-page-sidebar">
				{$category_sidebar = false}
				{$category_status = ''}
				{if $category->subcategories}
					{$category_sidebar = $category}
				{elseif $category->path[count($category->path)-2]->subcategories}
					{$category_sidebar = $category->path[count($category->path)-2]}
					{$category_status = ' not_category'}
				{elseif $category->path.0->subcategories}
					{$category_sidebar = $category->path.0}
					{$category_status = ' not_category'}
				{/if}


				{if $category_sidebar}
					<div class="sidebar-nav-block js-sidebar-nav-block">
						<button type="button" class="sidebar-nav-block__close close js-sidebar-nav-block-close"></button>
						<div class="sidebar-nav-block__content{$category_status}">
							<div class="sidebar-nav">
								<div class="sidebar-nav__header">
									<span class="sidebar-nav__text"><a href="{$config->root_url}/catalog/{$category_sidebar->url}" class="prev_cat_url">
										{$category_sidebar->name|escape}</span>{if isset($category_sidebar->products_count)}<span class="sidebar-nav__count">{$category_sidebar->products_count|escape}</a>
									</span>
									{/if}
								</div>

{*								<ul class="sidebar-nav__list">
									{foreach $category_sidebar->subcategories as $s}
									{if $s->visible && $s->hide!=true}
										<li class="sidebar-nav__list-item">
											<a href="{$config->root_url}/catalog/{$s->url}" class="sidebar-nav__list-link">
												<span class="sidebar-nav__text">{$s->name|escape}</span></a>{if isset($s->products_count)}<span class="sidebar-nav__count">{$s->products_count|escape}</span>
												{/if}
										</li>
										{/if}
									{/foreach}
								</ul>*}
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
										{if $s->visible}
										<li class="sidebar-nav__list-item">
											<a href="{$url}" class="sidebar-nav__list-link">
												<span class="sidebar-nav__text">{if $s->h1}{$s->h1|escape}{else}{$s->name|escape}{/if}</span>{if isset($s->products_count)}<span class="sidebar-nav__count">{$s->products_count|escape}</span>
												{/if}
											</a>
										</li>
										{/if}
									{/foreach}
								</ul>

							</div>
						</div>
					</div>
				{/if}


					{include file="_filter.tpl"}
			</div>

			<div class="page-content">
				{if $category->subcategories}
				<div class="categories">
					{foreach $category->subcategories as $s}
					{if $s->visible && !$s->hide}
					<div class="category-block three-line">
						<div class="link-cat">
							<a href="{$config->root_url}/catalog/{$s->url}"  class="bg-image">
								{if $s->image}
								<img src="../{$config->categories_images_dir}{$s->image}" alt="{$s->name|escape}">
								{else}
								<img src="../files/uploads/logo-mobile.png" alt="{$s->name|escape}">
								{/if}
							</a>
							<div class="name-cat">
								<a href="{$config->root_url}/catalog/{$s->url}" class="name">{$s->name|escape}</a>{if isset($s->products_count)}<span class="count_product_cat">({$s->products_count|escape})</span>{/if}
							</div>
						</div>
					</div>
					{/if}
					{/foreach}
				</div>
				{/if}
				<div class="page-content__inner">
					<div class="catalog-header">
						<div class="sidebar-filter__header top-line-custom-lv-2">
							{if $category}
							<div class="sidebar-filter__section sidebar-filter__section_category"><!--noindex-->
								<div class="filter-category js-filter-category">
									{if $type == 'featured'}
									<button class="filter-category__link js-filter-category-link is-active">Хиты</button>
									{else}
									<a href="{url page=null _=null type=featured}" class="filter-category__link js-filter-category-link" rel="nofollow">Хиты</a>
									{/if}
									{if $type == 'actions'}
									<button class="filter-category__link js-filter-category-link is-active">Акции</button>
									{else}
									<a href="{url page=null _=null type=actions}" class="filter-category__link js-filter-category-link filter-category__link--actions" rel="nofollow">Акции</a>
									{/if}
									{if !$type}
									<button class="filter-category__link js-filter-category-link is-active">Все</button>
									{else}
									<a href="{$config->root_url}/catalog/{$category->url}" class="filter-category__link js-filter-category-link">Все</a>
									{/if}
								</div><!--/noindex-->
							</div>
							{/if}

							<div class="sidebar-filter__section sidebar-filter__section_sort">
								<div class="filter filter_mob-fix js-filter-sort-section">

									{$sorts = [
									'position'=>'по популярности',
									'budget'=>'по рейтингу',
									'name'=>'по названию',
									'cheap'=>'по цене, сначала дешевые',
									'expensive'=>'по цене, сначала дорогие'
									]}

									<div class="filter__header">
										<span class="filter__title">Сортировать</span>
										<span class="filter__header-info js-filter-sort-section-info">{$sorts[$sort]}</span>
									</div>

									<div class="filter__content">
										<button type="button" class="filter__content-close close"></button>
										<div class="filter__content-inner">
											<div class="filter-sort js-filter-sort">
												<div class="filter-sort__title">{$sorts[$sort]}</div>
												<div class="filter-sort__dropdown">
													{foreach $sorts as $key=>$name}
													<div class="filter-sort__dropdown-item">
														<a href="{url sort=$key page=null}" class="filter-sort__dropdown-link{if $sort == $key} is-active{/if}">{$name}</a>
													</div>
													{/foreach}
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						{*<div class="catalog-header__view-control">
							<div class="catalog-view-control">
								<button class="catalog-view-control__link catalog-view-control__link_grid js-catalog-view-grid is-active"></button>
								<button class="catalog-view-control__link catalog-view-control__link_hrz js-catalog-view-hrz"></button>
								<button class="catalog-view-control__link catalog-view-control__link_list js-catalog-view-list"></button>
							</div>
						</div>*}
						{if $current_page_num==1}

						{if $brand}
						{if $brand->banner}
						<img src="../{$config->brands_images_dir}{$brand->banner}" alt="{$brand->name}"
							 style="max-width: 100%"/>
						{/if}

						{$brand->description}
						{elseif $category}
						{if $category->banner}
						<img src="../{$config->categories_banners_images_dir}{$category->banner}"
							 alt="{$category->name}" style="max-width: 100%"/>
						{/if}
						{/if}
						{/if}
					</div>

					<div class="catalog js-catalog">
						{foreach $products as $product}
						<div class="catalog__item js-catalog-item">
							{include file="_product.tpl" imageLazyLoad=true}
						</div>
						{/foreach}
					</div>

					{include file='pagination.tpl'}

					{if $current_page_num==1}
						{if $page}
							{$page->body}
						{elseif $category}
							{$category->description}
						{/if}
					{/if}

					{include file='_browsed_products.tpl'}

				</div>
			</div>
		</div>
	</div>
</section>
