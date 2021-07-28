{* Список записей блога *}

{* Канонический адрес страницы *}
{$canonical="/blog" scope=parent}

<section class="section">
	<div class="wrapper">
		<div class="news-page">
			<div class="news-page__header">
				<h1 class="news-page__title">{$page->name}</h1>
			</div>

			<div class="news-page__sidebar">

				<div class="date-nav-block">
					{if $years}
					<div class="date-nav-block__item date-nav-block__item_year">
						<div class="date-nav js-date-nav">
							<div class="date-nav__title js-date-nav-title">{$year}</div>
							<div class="date-nav__content js-date-nav-content">
								<div class="date-nav__list">
									{foreach $years as $y}
									<div class="date-nav__list-item">
										<a href="{url page=null month=null year=$y}" class="date-nav__link js-date-nav-link{if $year == $y} is-active{/if}">
											<span>{$y}</span>
										</a>
									</div>
									{/foreach}
								</div>
							</div>
						</div>
					</div>
					{/if}
					{if $months}
					<div class="date-nav-block__item date-nav-block__item_month">
						<div class="date-nav js-date-nav">
							<div class="date-nav__title js-date-nav-title">Январь </div>
							<div class="date-nav__content js-date-nav-content">
								<div class="date-nav__list">
									{foreach array_chunk($months, 3, true) as $months_chunk}
									<div class="date-nav__list-col">
										{foreach $months_chunk as $key => $m}
											<div class="date-nav__list-item">
												<a href="{url page=null month=$m}" class="date-nav__link js-date-nav-link{if $month == $m} is-active{/if}">
													<span>{"01-$m-2000"|date_format:"%m":"":"rus"}</span>
												</a>
											</div>
										{/foreach}
									</div>
									{/foreach}
								</div>
							</div>
						</div>
					</div>
					{/if}
				</div>
			</div>

			<div class="news-page__content">
				{foreach $posts as $post}
				<section class="news-excerpt">
					{if $post->image}
					<div class="news-excerpt__image-field">
						<a href="{$config->root_url}/blog/{$post->url}" itemscope
						   itemtype="http://schema.org/ImageObject">
							<meta itemprop="name" content="{$post->name|escape}">
							<img src="{$config->root_url}/{$config->blog_images_dir}{$post->image}" alt="{$post->name|escape}" class="news-excerpt__image" itemprop="contentUrl">
							<meta itemprop="description" content="{$post->annotation}">
						</a>
					</div>
					{/if}
					<div class="news-excerpt__content"{if !$post->image} style="margin-left: 0" {/if}>
						<div class="news-excerpt__date">{$post->date|date_format:"%m %d, %Y":"":"rus"}</div>
						<h2 class="news-excerpt__title">
							<a href="blog/{$post->url}" class="news-excerpt__title-link">{$post->name|escape}</a>
						</h2>
						<div class="news-excerpt__description">
							{$post->annotation}
						</div>
					</div>
				</section>
				{/foreach}
			</div>
		</div>
	</div>
</section>
