{api module=db method=query _="SELECT * FROM __categories WHERE visible_is_main=1 AND visible=1 ORDER BY position"}
{api module=db method=results var="main_categories"}
{if $main_categories}
<section class="section section_bg main-catalog-section for-desktop">
	<div class="wrapper">
		<div class="main-catalog js-main-catalog">

			<div class="main-catalog__grid">
				<a href="catalog/koshki-suhoj-korm" class="main-catalog__item main-catalog__item_bg" data-echo-background="design/{$settings->theme|escape}/images/catalog1-11.png">
					<div class="main-catalog__item-content">
						<div class="main-catalog__item-title">
							<p><span>Сухой корм для кошек</span></p>
						</div>
                     <div class="main-catalog__item-count"><span>Огромный выбор</span></div>
					</div>
				</a>
			</div>

			<div class="main-catalog__grid main-catalog__grid_hrz">
				<div class="main-catalog__grid main-catalog__grid_small">
					<a href="catalog/koshki-igrushki" class="main-catalog__item main-catalog__item_small main-catalog__item_bg" data-echo-background="design/{$settings->theme|escape}/images/catalog1-222.png">
						<div class="main-catalog__item-content">
							<div class="main-catalog__item-title">
								<p><span>Игрушки</span></p>
							</div>
							<div class="main-catalog__item-count"><span>и дразнилки</span></div>
						</div>
					</a>
				</div>

				<div class="main-catalog__grid main-catalog__grid_small">
					<a href="catalog/sobaki-igrushki" class="main-catalog__item main-catalog__item_small main-catalog__item_bg" data-echo-background="design/{$settings->theme|escape}/images/catalog1-333.png">
						<div class="main-catalog__item-content">
							<div class="main-catalog__item-title">
								<p><span>Развлеченье для собак</span></p>
							</div>
                         <div class="main-catalog__item-count"><span>Игрушки</span></div>
						</div>
					</a>
				</div>
			</div>

			<div class="main-catalog__grid main-catalog__grid_right">
				<a href="catalog/sobaki-suhie-korma" class="main-catalog__item main-catalog__item_bg" data-echo-background="design/{$settings->theme|escape}/images/catalog1-111.png">
					<div class="main-catalog__item-content">
						<div class="main-catalog__item-title">
							<p><span>Сухие корма для собак</span></p>
						</div>
						<div class="main-catalog__item-count"><span>То что нужно верному другу!</span></div>
					</div>
				</a>
			</div>

			<div class="main-catalog__grid main-catalog__grid_hrz">
				<a href="catalog/koshki-transportirovka-perenoski" class="main-catalog__item main-catalog__item_hrz">
					<div class="main-catalog__item-content">
						<div class="main-catalog__item-title">
							<p><span>Переноски</span></p>
						</div>
						<div class="main-catalog__item-count"><span>Практично и удобно!</span></div>
					</div>
					<div class="main-catalog__item-image-field">
						<img src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" data-echo="design/{$settings->theme|escape}/images/catalog1-5555.png" alt="" class="main-catalog__item-image">
					</div>
				</a>
			</div>
		</div>
	</div>
</section>
{/if}
