
<section class="brand-main" style="background-image: url('design/{$settings->theme|escape}/images/temp/brand-bg-1.jpg');">
	<div class="brand-main__content">
		<div class="brand-main__inner">
			<div class="wrapper">
				<h1 class="brand-main__title">Работаем с&nbsp;товарами 178&nbsp;марок</h1>
			</div>
		</div>
	</div>
</section>


<section class="section section_bg main-catalog-section">
	<div class="wrapper">
		<div class="main-catalog js-main-catalog-large">
			<div class="main-catalog__grid_large">
				<div class="main-catalog__grid main-catalog__grid_hrz">
					<div class="main-catalog__grid main-catalog__grid_small">
						<a href="#" class="main-catalog__item main-catalog__item_small">
							<div class="main-catalog__item-content">
								<div class="main-catalog__item-title">
									<p><span>Fiskars</span></p>
								</div>
								<div class="main-catalog__item-count"><span>1800 товаров</span></div>
							</div>
							<div class="main-catalog__item-image-field main-catalog__item-image-field_full-height"><img src="design/{$settings->theme|escape}/images/temp/main-catalog-6.jpg" alt="" class="main-catalog__item-image"></div>
							<img src="design/{$settings->theme|escape}/images/temp/logo-1.png" alt="" class="main-catalog__logo">
						</a>
					</div>

					<div class="main-catalog__grid main-catalog__grid_small">
						<a href="#" class="main-catalog__item main-catalog__item_small">
							<div class="main-catalog__item-content">
								<div class="main-catalog__item-title">
									<p><span>Gorenje</span></p>
								</div>
								<div class="main-catalog__item-count"><span>600 товаров</span></div>
							</div>
							<div class="main-catalog__item-image-field"><img src="design/{$settings->theme|escape}/images/temp/main-catalog-7.jpg" alt="" class="main-catalog__item-image"></div>
							<img src="design/{$settings->theme|escape}/images/temp/logo-2.png" alt="" class="main-catalog__logo">
						</a>
					</div>
				</div>

				<div class="main-catalog__grid main-catalog__grid_hrz">
					<a href="#" class="main-catalog__item main-catalog__item_hrz">
						<div class="main-catalog__item-content">
							<div class="main-catalog__item-title">
								<p><span>Electrolux</span></p>
							</div>
							<div class="main-catalog__item-count"><span>1450 товаров</span></div>
						</div>
						<div class="main-catalog__item-image-field">
							<img src="design/{$settings->theme|escape}/images/temp/main-catalog-8.jpg" alt="" class="main-catalog__item-image">
						</div>
						<img src="design/{$settings->theme|escape}/images/temp/logo-3.png" alt="" class="main-catalog__logo">
					</a>
				</div>
			</div>

			<div class="main-catalog__grid_large">
				<div class="main-catalog__item main-catalog__item_large">
					<div class="main-catalog__item-content">
						{get_brands var=new_brands label_new=1}
						{if $new_brands}
							{$max_colom = ceil(count($new_brands)/3)}
							<div class="main-catalog__brands-list-title">Новые бренды</div>
							<div class="main-catalog__brands-list">
								{foreach array_chunk($new_brands, $max_colom, true) as $brands_coloms}
								<div class="main-catalog__brands-list-col">
									{foreach $brands_coloms as $bn}
									<div class="main-catalog__brands-list-item">
										<a href="{$config->root_url}/brands/{$bn->url}" class="main-catalog__brands-list-link">{$bn->name|escape}</a>
									</div>
									{/foreach}
								</div>
								{/foreach}
							</div>
						{/if}
					</div>
					<div class="main-catalog__item-image-field">
						<img src="design/{$settings->theme|escape}/images/temp/main-catalog-9.jpg" alt="" class="main-catalog__item-image">
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="section">
	<div class="wrapper">
		<div class="brands-block-header">
			<div class="brands-block-nav">
				<a href="#eng" class="brands-block-nav__link{* is-active*} js-target-link">Eng</a>
				<a href="#rus" class="brands-block-nav__link js-target-link">Rus</a>
			</div>
		</div>
		{$eng_target = false}
		{$rus_target = false}
		<div class="brands-block">
			{foreach $alf_brands as $alf => $brands}
				{if !$eng_target && preg_match ('/[a-zA-Z]/', $alf)}
					<div class="brands-block__section" id="eng">
					{$eng_target = true}
				{elseif !$rus_target && preg_match ('/[а-яА-я]/', $alf)}
					<div class="brands-block__section" id="rus">
					{$rus_target = true}
				{else}
					<div class="brands-block__section">
				{/if}

					<div class="brands-block__header">
						<div class="brands-block__letter">{$alf|escape}</div>
					</div>
					<div class="brands-block__content">
						<ul class="brands-block__list">
							{foreach $brands as $b}
							<li class="brands-block__item">
								<a href="{$config->root_url}/brands/{$b->url}" class="brands-block__link">{$b->name|escape}</a>
								{if $b->label_new} <span class="sticker">NEW</span>{/if}
							</li>
							{/foreach}
						</ul>
					</div>
				</div>
			{/foreach}
		</div>
	</div>
</section>
