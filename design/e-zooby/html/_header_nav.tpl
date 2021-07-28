{if $categories}
<div class="header__nav js-header-nav">
	<div class="wrapper">
		<div class="header__nav-content">
			<span class="nav-overlay nav-overlay_left js-nav-left"></span>
			<span class="nav-overlay nav-overlay_right js-nav-right"></span>
			<nav class="nav js-nav">
				<ul class="nav__list js-nav-list" itemscope="itemscope" itemtype="http://www.schema.org/SiteNavigationElement">
					<li class="nav__item nav__item_mobile">
						<a href="#" class="nav__link js-subnav-link"  itemprop="url"><span class="nav__link-text" itemprop="name">Каталог</span></a>
					</li>
					{foreach $categories as $c}
						{if $c->visible}
							<li class="nav__item">
								<a href="{$config->root_url}/catalog/{$c->url}" itemprop="url" class="nav__link" data-category="{$c->id}"{if $c->subcategories} data-dropdown="{$c->id}"{/if}><span itemprop="name">{$c->name|escape}</span></a>
							</li>
						{/if}
					{/foreach}
				</ul>
			</nav>
		</div>
		<div class="subnav-block js-subnav-block">
			<div class="subnav-block__header">
				<div class="subnav-block__title">Каталог</div>
				<button class="subnav-block__close js-subnav-block-close"></button>
			</div>
			<div class="subnav-block__content">
			{foreach $categories as $c}
				{if $c->visible && $c->subcategories}
					<div class="subnav-block__item" data-dropdown="{$c->id}">
						<div class="wrapper">
								<div class="subnav-block__item-title">
									<a href="{$config->root_url}/catalog/{$c->url}" class="subnav-block__item-title-link">{$c->name|escape}</a>
								</div>
							<div class="subnav">
								{$colom = 4}
								{$max_colom = ceil(count($c->subcategories)/$colom)}

								{foreach array_chunk($c->subcategories, $max_colom, true) as $subnav}

									<div class="subnav__col">
										{foreach $subnav as $sub}
										<div class="subnav__nav">
											<div class="subnav__nav-title">
												<a href="{$config->root_url}/catalog/{$sub->url}" class="subnav__nav-title-link">{$sub->name|escape}</a>
											</div>
										</div>
										{/foreach}
									</div>
								{/foreach}
								</div>
							</div>
						</div>
					{else}
						<div class="subnav-block__item">
							<div class="wrapper">
								<div class="subnav-block__item-title">
									<a href="{$config->root_url}/catalog/{$c->url}" class="subnav-block__item-title-link">{$c->name|escape}</a>
							</div>
						</div>
					</div>
				    {/if}
				{/foreach}
            </div>
        </div>
    </div>
</div>
{/if}
