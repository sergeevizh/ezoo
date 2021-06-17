<div class="sidebar-filter-block js-sidebar-filter-block">
	<button type="button" class="sidebar-filter-block__close close js-sidebar-filter-block-close"></button>
	<div class="sidebar-filter-block__content">
		<div class="sidebar-filter">
			<div class="sidebar-filter__header" style="display: none">
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
								<a href="{url page=null _=null type=actions}" class="filter-category__link js-filter-category-link" rel="nofollow">Акции</a>
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

			<form id="filter" action="{$config->root_url}/catalog/{$category->url}" method="get" class="js-filter-form">


				{if $category->brands|count > 1}
					{$brand_selected = []}
					{if $smarty.get.brand}
						{$brand_selected = array_values($smarty.get.brand)}
					{/if}
					<div class="sidebar-filter__section">
						<div class="filter filter_mob-fix js-filter-brands-section">
							<div class="filter__header">
								<span class="filter__title">Бренды</span>
								<span class="filter__header-info js-filter-brands-section-info">{foreach $category->brands as $b}{if in_array($b->id, $brand_selected)}{$b->name|escape}, {/if}{/foreach}</span>
							</div>
							<div class="filter__content">
								<button type="button" class="filter__content-close close"></button>
								<div class="filter__content-inner">
									<div class="filter-check-list js-filter-check-list">
										{foreach $category->brands as $b}

											<div class="filter-check-list__row">
												<div class="check-row">
													<label>
														<input type="checkbox" class="js-styler js-filter-brands-checkbox" name="brand[]" value="{$b->id}" {if in_array($b->id, $brand_selected)} checked{/if}>

														{foreach $sub_cat as $sub name="outer"}
															{assign var="title" value=$sub->name}

															{assign var="b_name" value=$b->name}
															{assign var="b_name_array" value=" "|explode:$b_name}
															{assign var="only_brand" value=$b_name_array[0]}
															{if $title|strpos:$only_brand}
																{assign var="f_brand_link" value=$sub->url}
																{break}
															{else}
																{assign var="f_brand_link" value=""}
															{/if}
														{/foreach}

														{if $f_brand_link != ''}
															<a href="{$config->root_url}/catalog/{$f_brand_link}" onclick="return false;">{$b->name|escape}</a>
														{else}
															<span>{$b->name|escape}</span>
														{/if}

													</label>
												</div>
											</div>
										{/foreach}
									</div>
								</div>
							</div>
						</div>
					</div>
				{/if}

				{if ($prices_ranges->min_price < $prices_ranges->max_price) && $prices_ranges->max_price > 1}

					<div class="sidebar-filter__section">
						<div class="filter filter_mob-fix js-filter-price-section">
							<div class="filter__header">
								<span class="filter__title">Цена<span class="mobile-hidden">, {$currency->sign|escape}</span></span>
								<span class="filter__header-info js-filter-price-section-info">
									от <span class="js-filter-price-section-info-min">{$prices_ranges->min_price|convert:null:false|floor}</span>
									до <span class="js-filter-price-section-info-max">{$prices_ranges->max_price|convert:null:false|ceil}</span> {$currency->sign|escape}
								</span>
							</div>
							<div class="filter__content">
								<button type="button" class="filter__content-close close"></button>
								<div class="filter__content-inner">
									<div class="filter-range js-filter-price">
										<div class="filter-range__val">
											<span class="filter-range__val-min js-filter-price-min"></span>
											<span class="filter-range__val-line">—</span>
											<span class="filter-range__val-max js-filter-price-max"></span>
										</div>
										<div class="filter-range__content">
											<div class="filter-range__main">
												<div class="range js-filter-price-range" data-min="{$prices_ranges->min_price|convert:null:false|floor}" data-max="{$prices_ranges->max_price|convert:null:false|ceil}" data-values="[{if $smarty.get.min_price}{$smarty.get.min_price}{else}{$prices_ranges->min_price|convert:null:false|floor}{/if}, {if $smarty.get.max_price}{$smarty.get.max_price}{else}{$prices_ranges->max_price|convert:null:false|ceil}{/if}]" data-step="1"></div>
											</div>
											<span class="filter-range__content-min">от {$prices_ranges->min_price|convert:null:false|floor}</span>
											<span class="filter-range__content-max">до {$prices_ranges->max_price|convert:null:false|ceil}</span>
										</div>
										<input class="js-filter-input-price-min" type="hidden" name="min_price" value="{if $smarty.get.min_price}{$smarty.get.min_price}{else}{$prices_ranges->min_price|convert:null:false|floor}{/if}">
										<input class="js-filter-input-price-max" type="hidden" name="max_price" value="{if $smarty.get.max_price}{$smarty.get.max_price}{else}{$prices_ranges->max_price|convert:null:false|ceil}{/if}">
									</div>
								</div>
							</div>
						</div>
					</div>
				{/if}
				{if $features}
					{foreach $features as $key=>$f}
						<div class="sidebar-filter__section">
							<div class="filter filter_mob-fix js-filter-capacity-section">
								<div class="filter__header">
									<span class="filter__title" data-feature="{$f->id}">{$f->name}</span>
									<span class="filter__header-info js-filter-capacity-section-info">{$smarty.get.$key}</span>
								</div>
								<div class="filter__content">
									<button type="button" class="filter__content-close close"></button>
									<div class="filter__content-inner">
										<select class="filter-select js-styler js-filter-capacity" name="{$f->id}" {* TODO dev onchange="location = this.value"*}>
											<option value=""{if !$smarty.get.$key} selected{/if}>Все</option>
											{foreach $f->options as $o}
												<option value="{$o->value}"{if $smarty.get.$key == $o->value} selected{/if}>{$o->value|escape}</option>
											{/foreach}
										</select>
									</div>
								</div>
							</div>
						</div>
					{/foreach}
				{/if}

				<a href="#" class="sidebar-filter__tooltip js-sidebar-filter-tooltip">
					{include file="_filter_tooltip.tpl"}
				</a>
				<div class="align-center" style="display: -webkit-box;display: -ms-flexbox;display: flex;-webkit-box-align: center;-ms-flex-align: center;align-items: center;-webkit-box-pack: justify;-ms-flex-pack: justify;justify-content: space-between">
					<button class="btn" type="submit">Подобрать</button>
					<a href="{$config->root_url}/catalog/{$category->url}" rel="nofollow" class="reset">Сбросить</a>
				</div>

			</form>
		</div>
	</div>

	<div class="sidebar-filter-block__footer">
		<a href="#" class="btn btn_border js-sidebar-filter-tooltip">
			{include file="_filter_tooltip.tpl"}
		</a>
	</div>
</div>
