{* Страница товара *}

{* Канонический адрес страницы *}
{$canonical="/products/{$product->url}" scope=parent}


<section class="section section_bg section_product" itemscope itemtype="https://schema.org/Product">

	{if $categories}
		<div class="category-nav-block js-category-nav-block">
			<button type="button" class="category-nav-block__close close js-category-nav-block-close"></button>
			<div class="category-nav-block__content">
				<div class="category-nav">
					<ul class="category-nav__list">
						{foreach $categories as $c}
							{if $c->visible}
								<li class="category-nav__list-item">
									<a href="{$config->root_url}/catalog/{$c->url}" class="category-nav__list-link">{$c->name|escape}</a>
								</li>
							{/if}
						{/foreach}
					</ul>
				</div>
			</div>
		</div>
	{/if}


	<div class="breadcrumbs-wrapper">
		<div class="wrapper">
			<div class="breadcrumbs" itemscope itemtype="https://schema.org/BreadcrumbList">
				<div class="breadcrumbs__item hidden" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
					<a href="{$config->root_url}/" class="breadcrumbs__link" itemprop="item">
						<span itemprop="name">Главная</span>
					</a>
				</div>
				{foreach $category->path as $cat}
					<div class="breadcrumbs__item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
						<a href="{$config->root_url}/catalog/{$cat->url}" class="breadcrumbs__link" itemprop="item">
							<span itemprop="name">{$cat->name|escape}</span>
						</a>
					</div>
				{/foreach}
				<div class="breadcrumbs__item">{$product->name|escape}</div>
			</div>
		</div>
	</div>

	<div class="catalog-header-mob">
		<div class="catalog-header-mob__field">
			<a href="{$config->root_url}/catalog/{$cat->url}" class="catalog-header-mob__link catalog-header-mob__link_back">
				<span class="catalog-header-mob__link-text">{$cat->name|escape}</span>
			</a>
		</div>

		<div class="catalog-header-mob__field">
			<button type="button" class="catalog-header-mob__link a js-category-nav-block-link">
				<span class="catalog-header-mob__link-text">Все разделы</span>
			</button>
		</div>

	</div>

	<section class="product-section">
		<div class="wrapper">
			<div class="product js-product">
				<div class="product__image-field js-product-image-field">
					<div class="product-image js-product-image">
						<div class="product-image__main js-product-image-main">
							{foreach $product->images as $i=>$image}
								<div class="product-image__main-item">
									<img src="{$image->filename|resize:600:600}" alt="{$product->name|escape}" class="product-image__main-img">
								</div>
							{/foreach}
						</div>
						<div class="product-image__thumbs js-product-image-thumbs">
							{foreach $product->images as $i=>$image}
								<div class="product-image__thumbs-item">

									<img src="{$image->filename|resize:600:600}" alt="{$product->name|escape}" class="product-image__thumbs-img">
								</div>
							{/foreach}
						</div>
					</div>
				</div>

				<div class="product__content">
					<div class="product__content-inner">
						<div class="product__header">
							<h1 data-product="{$product->id}" itemprop="name" class="product__title">{$product->name|escape}</h1>
							{* TODO Паша - проверить что бы в верстке логотип бренда адаптировался через css (ужимался и отцентровыволся )*}
							{if $brand->image}
								<div class="product__logo-field">
									<a href="{$config->root_url}/brands/{$brand->url}" class="product__logo-link">
										<img src="{$config->brands_images_dir}{$brand->image}" alt="{$brand->name|escape}" class="product__logo">
									</a>
								</div>
							{/if}
						</div>
						<div class="product__meta">
							{* TODO Паша - зачем выводить бренд и ссылку если выше у нас выводится логотип бренда и ссылка *}
							{if $brand}
								<a itemprop="brand" href="{$config->root_url}/brands/{$brand->url}" class="product__meta-item">
									{$brand->name|escape}
								</a>
							{/if}
							{if $comments|count > 0 && $product->rating}

								<div class="product__meta-rating" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
									<meta itemprop="bestRating" content="5">
									<meta itemprop="ratingValue" content="{$product->rating}">
									<meta itemprop="reviewCount" content="{$comments|count}">

									<div class="b-rating">
										<div class="b-rating__inner">
											{section name=rating loop=6 max=5 step=-1}
												<span class="b-rating__field{if $smarty.section.rating.index == ceil($product->rating)} is-active{/if}">
												<span class="b-rating__star">{$smarty.section.rating.index}</span>
											</span>
											{/section}
										</div>
									</div>
								</div>

								<button type="button" data-href="#comments-section" class="product__comment-link js-target-link">
									<span class="product__comment-icon">{$comments|count}</span>
									<span class="product__comment-text">{count($comments)|plural:'отзыв':'отзывов':'отзыва'}</span>
								</button>
							{else}
								<button type="button" data-href="#comments-section" class="product__add-comment-link js-target-link">Написать отзыв</button>
							{/if}
						</div>

						{* TODO Паша - TODO REMOVE удалить Js и css
						{if $product->variants|count > 1 }
							<div class="product__variants js-product-variants-block">
								<div class="product__variants-main product-variants js-product-variants-block-main">
									<div class="product-variants__item">
										<span class="product-variants__item-main">
											<span class="product-variants__img-field"><img src="http://cosmet.dev.rlab.pro/files/products/shampun-dlja-brjunetok-tigi-catwalk-fashionista-brunette-shampoo-250079-20160602114805.600x600.jpg?dbb0521339887831c046cb52bb8c0b11" alt="" class="product-variants__img"></span>
											<span class="product-variants__price-field">
												<span class="product-variants__price">402 руб.</span>
											</span>
											<span class="product-variants__content">Каштановых или темно-русых</span>
										</span>
									</div>
								</div>

								<div class="product__variants-mob-title">Еще 8 вариантов</div>

								<div class="product__variants-content js-product-variants-block-content">
									<div class="product-variants">
										<label class="product-variants__item">
											<input type="radio" name="product-variant" class="product-variants__input">
											<span class="product-variants__item-main">
												<span class="product-variants__img-field"><img src="http://cosmet.dev.rlab.pro/files/products/shampun-dlja-brjunetok-tigi-catwalk-fashionista-brunette-shampoo-250079-20160602114805.600x600.jpg?dbb0521339887831c046cb52bb8c0b11" alt="" class="product-variants__img"></span>
												<span class="product-variants__price-field">
													<span class="product-variants__price">402 руб.</span>
												</span>
												<span class="product-variants__content">1 Каштановых или темно-русых</span>
											</span>
										</label>

										<label class="product-variants__item">
											<input type="radio" name="product-variant" class="product-variants__input">
											<span class="product-variants__item-main">
												<span class="product-variants__img-field"><img src="http://cosmet.dev.rlab.pro/files/products/shampun-dlja-brjunetok-tigi-catwalk-fashionista-brunette-shampoo-250079-20160602114805-1.600x600.jpg?fac3ec427a922118566fa0e3e4dc237b" alt="" class="product-variants__img"></span>
												<span class="product-variants__price-field">
													<span class="product-variants__price">403 руб.</span>
												</span>
												<span class="product-variants__content">2 Каштановых или темно-русых</span>
											</span>
										</label>

										<label class="product-variants__item">
											<input type="radio" name="product-variant" class="product-variants__input">
											<span class="product-variants__item-main">
												<span class="product-variants__img-field"><img src="http://cosmet.dev.rlab.pro/files/products/shampun-dlja-brjunetok-tigi-catwalk-fashionista-brunette-shampoo-250079-20160701163831.600x600.jpg?637336320c4e8b0aaf9f5a6e5ed0871c" alt="" class="product-variants__img"></span>
												<span class="product-variants__price-field">
													<span class="product-variants__price">404 руб.</span>
												</span>
												<span class="product-variants__content">3 Каштановых или темно-русых</span>
											</span>
										</label>

										<label class="product-variants__item">
											<input type="radio" name="product-variant" class="product-variants__input">
											<span class="product-variants__item-main">
												<span class="product-variants__img-field"><img src="http://cosmet.dev.rlab.pro/files/products/shampun-dlja-brjunetok-tigi-catwalk-fashionista-brunette-shampoo-250079-20160602114805.600x600.jpg?dbb0521339887831c046cb52bb8c0b11" alt="" class="product-variants__img"></span>
												<span class="product-variants__price-field">
													<span class="product-variants__price">402 руб.</span>
												</span>
												<span class="product-variants__content">4 Каштановых или темно-русых</span>
											</span>
										</label>

										<label class="product-variants__item">
											<input type="radio" name="product-variant" class="product-variants__input">
											<span class="product-variants__item-main">
												<span class="product-variants__img-field"><img src="http://cosmet.dev.rlab.pro/files/products/shampun-dlja-brjunetok-tigi-catwalk-fashionista-brunette-shampoo-250079-20160602114805-1.600x600.jpg?fac3ec427a922118566fa0e3e4dc237b" alt="" class="product-variants__img"></span>
												<span class="product-variants__price-field">
													<span class="product-variants__price">403 руб.</span>
												</span>
												<span class="product-variants__content">5 Каштановых или темно-русых</span>
											</span>
										</label>

										<label class="product-variants__item">
											<input type="radio" name="product-variant" class="product-variants__input">
											<span class="product-variants__item-main">
												<span class="product-variants__img-field"><img src="http://cosmet.dev.rlab.pro/files/products/shampun-dlja-brjunetok-tigi-catwalk-fashionista-brunette-shampoo-250079-20160701163831.600x600.jpg?637336320c4e8b0aaf9f5a6e5ed0871c" alt="" class="product-variants__img"></span>
												<span class="product-variants__price-field">
													<span class="product-variants__price">404 руб.</span>
												</span>
												<span class="product-variants__content">6 Каштановых или темно-русых</span>
											</span>
										</label>

										<label class="product-variants__item">
											<input type="radio" name="product-variant" class="product-variants__input">
											<span class="product-variants__item-main">
												<span class="product-variants__img-field"><img src="http://cosmet.dev.rlab.pro/files/products/shampun-dlja-brjunetok-tigi-catwalk-fashionista-brunette-shampoo-250079-20160602114805.600x600.jpg?dbb0521339887831c046cb52bb8c0b11" alt="" class="product-variants__img"></span>
												<span class="product-variants__price-field">
													<span class="product-variants__price">402 руб.</span>
												</span>
												<span class="product-variants__content">7 Каштановых или темно-русых</span>
											</span>
										</label>

										<label class="product-variants__item">
											<input type="radio" name="product-variant" class="product-variants__input">
											<span class="product-variants__item-main">
												<span class="product-variants__img-field"><img src="http://cosmet.dev.rlab.pro/files/products/shampun-dlja-brjunetok-tigi-catwalk-fashionista-brunette-shampoo-250079-20160602114805-1.600x600.jpg?fac3ec427a922118566fa0e3e4dc237b" alt="" class="product-variants__img"></span>
												<span class="product-variants__price-field">
													<span class="product-variants__price">403 руб.</span>
												</span>
												<span class="product-variants__content">8 Каштановых или темно-русых</span>
											</span>
										</label>
									</div>
								</div>

								<div class="product__variants-title">
									<button type="button" class="a product__variants-title-link js-product-variants-block-title-link"><span>Еще 8 вариантов</span><span class="is-open">Скрыть</span></button>
								</div>
							</div>
						{/if}
						*}


						{if $product->variants|count >= 1 }
							<div class="product__variant-table" >
								<div class="product__variant">
									{if $product->variant->name && $product->variants|count == 1 || $product->variants|count > 1}
										<div class="product__variant-cell">
											<small>Товар</small>
										</div>
									{/if}
									<div class="product__variant-cell">
										<small>Цена, {$currency->sign|escape}.</small>
									</div>
									<div class="product__variant-cell">
										<small>Заказ</small>
									</div>
									<div class="product__variant-cell"></div>
									<div class="product__variant-cell">
										<small>Твоя цена</small>
									</div>
									<div class="product__variant-cell">
										<small>Цена при самовывозе</small>
									</div>
								</div>
								{assign var=i value=0}
								{foreach $product->variants as $v}
									<form action="{$config->root_url}/cart" class="product__variant js-cart-basket-submit" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
										<input type="hidden" name="variant" value="{$v->id}">
										{if $v->name && $product->variants|count == 1 || $product->variants|count > 1}
											<div class="product__variant-cell product__variant-name">
												{$v->name}
											</div>
										{/if}
										<div class="product__variant-cell product__variant-price{if $v->compare_price > 0} product__variant-price-sale{/if}">
											{if $v->compare_price > 0}{*<span class="product__variant-price-compare">{$v->compare_price|convert}</span>*}{/if}
											<span itemprop="price" content="{$product->price}">{$v->price|convert}</span>
																				<span itemprop="priceCurrency" style="display:none;">BYN</span>

										</div>
										<div class="product__variant-cell product__variant-amount">
											<div class="count-field js-count-field">
												<span class="count-field__control count-field__control_down js-count-field-down">-</span>
												<input type="text" class="count-field__val js-count-field-val" name="amount" value="1" max="{$v->stock}">
												<span class="count-field__control count-field__control_up js-count-field-up">+</span>
											</div>
										</div>
										<div class="product__variant-cell product__variant-submit">
											<button type="submit" class="btn btn_small basket-btn product__basket-btn js-product-basket-btn {*{if in_array($product->variant->id, $shopping_cart)} is-active{/if}*}"></button>
										</div>

										{if $v->sale}
											<div class="product__variant-cell product_sales">
												{if $v->sale['delivery']}
													<b>{$v->sale['delivery']} руб.</b>
												{else}
													<b>{$v->price|convert}  руб.</b>
												{/if}
											</div>
											<div class="product__variant-cell product_sales">
												{if $v->sale['pickup']}
													<b>{$v->sale['pickup']} руб.</b>
												{else}
													<b>{$v->price|convert}  руб.</b>
												{/if}
											</div>
										{else}
											<div class="product__variant-cell product_sales">

													<b>{$v->price|convert}  руб.</b>

											</div>
											<div class="product__variant-cell product_sales">

													<b>{$v->price|convert}  руб.</b>

											</div>
										{/if}


									</form>
								{/foreach}
							</div>
						{else}
							{* TODO Паша - что да как *}
							<div class="product__btn-field">
								{*<button type="button" class="btn btn_blue request-btn product__request-btn">Оставить заявку</button>*}
								<div class="product__available-info h2">Нет в наличии</div>
							</div>
						{/if}




						{*
						<div class="product__price-field">
							<div class="product__price-field-content">
								{if $product->price}
									<span class="product__price" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
										<span class="js-price-change" itemprop="price" content="{$product->price}">{$product->price|convert}</span>
										<span class="currency" itemprop="priceCurrency" content="{$currency->code|escape}">{$currency->sign|escape}</span>
										{if $product->variant->infinity}
											<link itemprop="availability" href="http://schema.org/InStock">
										{elseif $product->variant->stock > 0}
											<link itemprop="availability" href="http://schema.org/InStock">
										{else}
											<link itemprop="availability" href="http://schema.org/SoldOut">
										{/if}

									</span>
								{/if}

								<span class="product__old-price js-compare-price-change{if $product->compare_price < 1} hidden{/if}">{$product->compare_price|convert} {$currency->sign|escape}</span>

							</div>

							<div class="product__price-field-discount js-discount-change{if $product->compare_price < 1} hidden{/if}">
								<span class="product__discount">−{ceil(100-$product->price/$product->compare_price*100)}%</span>
							</div>

						</div>

						{if $product->variants|count >= 1 }

							<form action="{$config->root_url}/cart" class="js-cart-basket-submit">
								{$shopping_cart = []}
								{if isset($smarty.session.shopping_cart)}
									{$shopping_cart = (array)array_keys($smarty.session.shopping_cart)}
								{/if}
								{if $product->variants|count > 1 }
									<select class="js-product-variants-select" name="variant">
										{foreach $product->variants as $v}
											{$inCart = 0}
											{if in_array($v->id, $shopping_cart)}
												{$inCart = 1}
											{/if}
											<option value="{$v->id}" data-price="{$v->price|convert}" data-stock="{$v->stock}"{if $v->compare_price > 0} data-compare-price="{$v->compare_price|convert} {$currency->sign|escape}" data-discount="−{ceil(100-$v->price/$v->compare_price*100)}%"{/if}  data-in-cart="{$inCart}"{if $product->variant->id == $v->id} selected{/if}>
												{$v->name}
											</option>
										{/foreach}
									</select>
								{else}
									<input type="hidden" name="variant" value="{$product->variant->id}">
								{/if}

								<div class="product__btn-field">
									<button type="submit" class="btn basket-btn product__basket-btn js-product-basket-btn{if in_array($product->variant->id, $shopping_cart)} is-active{/if}">
										<span>В корзину</span>
										<span class="in-cart">В корзине</span>
									</button>
								</div>
							</form>
						{else}
							<div class="product__btn-field">
								<div class="product__available-info h2">Нет в наличии</div>
							</div>
						{/if}
						*}
{*карты оплат*}
						
					{*	<p  style="margin-bottom: 5px; color: #666; font-size: 75%;">Карты рассрочки</p>*}
						<div class="product__variant"><small>Карты рассрочки</small></div>
						<div class="product__variant-table">
				<img  height="40px" src="{$config->root_url}/design/{$settings->theme|escape}/images/cart-pokupok-min.png" style="margin-right: 5px;">
				<img  height="40px" src="{$config->root_url}/design/{$settings->theme|escape}/images/cart-smart-min.png" style="margin-right: 5px;">
				<img  height="40px" src="{$config->root_url}/design/{$settings->theme|escape}/images/cart-halva-min.png" style="margin-right: 5px;">
										</div>
				<p><hr/></p>		
						
						{if trim(strip_tags($product->body)) != ''}
							<div id="product-description" class="product__description" itemprop="description">
								<div>
								{$product->body}
								</div>
							</div>
							<button type="button" class="btn btn_border btn_small product__expand-link js-expand-link" data-expand-id="#product-description" data-alt-text="Скрыть описание">Показать полное описание</button>

						{else}
							<meta itemprop="description" content="{$product->name|escape}">
						{/if}

						{if $product->features}
							{*<h2>Характеристики</h2>
							<ul class="data-list" itemprop="additionalProperty" itemscope="" itemtype="http://schema.org/PropertyValue">
								{foreach $product->features as $f}
									<li class="data-list__row">
										<span itemprop="name" class="data-list__title">{$f->name}</span>
										<span itemprop="value" class="data-list__description">{$f->value}</span>
									</li>
								{/foreach}
							</ul>*}
						{/if}
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="comments-section" id="comments-section">
		<div class="wrapper">
			<div class="comments-block">
				<h2 class="comments-block__title">{$comments|count} отзывов</h2>
				<div class="comments">
					{$limit_visible_comments = 3}
					{if $comments}
						{foreach $comments as $comment}
						<div class="comment{if $comment@index >= $limit_visible_comments} comment_hidden{/if}" itemprop="review" itemscope itemtype="http://schema.org/Review">
							<a name="comment_{$comment->id}"></a>
							<div class="comment__header">
								{if $comment->rating}
								<div class="comment__rating" itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
									<meta itemprop="worstRating" content="1">
									<meta itemprop="ratingValue" content="{$comment->rating}">
									<meta itemprop="bestRating" content="5">
									<div class="b-rating">
										<div class="b-rating__inner">
											{section name=rating loop=6 max=5 step=-1}
											<span class="b-rating__field{if $smarty.section.rating.index == $comment->rating} is-active{/if}">
												<span class="b-rating__star">{$smarty.section.rating.index}</span>
											</span>
											{/section}
										</div>
									</div>
								</div>
								{/if}

								<div class="comment__author" itemprop="author">{$comment->name|escape} {if !$comment->approved}<b>ожидает модерации</b>{/if}</div>
								<div class="comment__date">
									<meta itemprop="datePublished" content="{$comment->date|date:'Y-m-d'}">
									{$comment->date|date_format:"%d %m":"":"rus"}
								</div>
							</div>
							<div class="comment__content" itemprop="description">{$comment->text|escape|nl2br}</div>
						</div>
						{/foreach}
					{else}
						<p>Пока нет комментариев</p>
					{/if}
				</div>
				{if count($comments) > $limit_visible_comments}
					<button type="button" class="btn btn_border btn_small comments-more-link js-comments-more-link" data-alt-text="Скрыть">Показать еще {count($comments)-$limit_visible_comments} {(count($comments)-$limit_visible_comments)|plural:'отзыв':'отзывов':'отзыва'}</button>
				{/if}
			</div>

			<div class="add-comments-block" id="add-comments-block">
				<h2 class="add-comments-block__title">Напишите свой отзыв</h2>
				<form class="add-comments-block__form js-validation-form" method="post">
					{if $error}
						<div class="error-message">
							{if $error=='captcha'}
								Неверно введена капча
							{elseif $error=='empty_name'}
								Введите имя
							{elseif $error=='empty_comment'}
								Введите комментарий
							{/if}
						</div>
					{/if}
					<div class="form-row">
						<div class="b-add-comment">
							<textarea name="text" class="b-add-comment__textarea" placeholder=" " required>{$comment_text}</textarea>
							<div class="b-add-comment__rating">
								<div class="b-add-comment__rating-title">Ваша оценка</div>
								<div class="b-add-comment__rating-stars">
									<div class="b-rating">
										<div class="b-rating__inner">
											<label class="b-rating__field">
												<input type="radio" name="rating" value="5" class="b-rating__radio">
												<span class="b-rating__star">5</span>
											</label>
											<label class="b-rating__field is-active">
												<input type="radio" name="rating" value="4" class="b-rating__radio" checked>
												<span class="b-rating__star">4</span>
											</label>
											<label class="b-rating__field">
												<input type="radio" name="rating" value="3" class="b-rating__radio">
												<span class="b-rating__star">3</span>
											</label>
											<label class="b-rating__field">
												<input type="radio" name="rating" value="2" class="b-rating__radio">
												<span class="b-rating__star">2</span>
											</label>
											<label class="b-rating__field">
												<input type="radio" name="rating" value="1" class="b-rating__radio">
												<span class="b-rating__star">1</span>
											</label>
										</div>
									</div>
								</div>
							</div>
						</div>

					</div>
					<div class="form-row">
						<label class="form-label">Ваше имя или псевдноним</label>
						<input type="text" name="name" value="{$comment_name}" required>
					</div>
					<div class="form-btn-row">
						<button type="submit" class="btn btn_small" name="comment" value="Отправить отзыв">Отправить отзыв</button>
					</div>
				</form>
			</div>
		</div>
	</section>

	{if $related_products}
		<section class="similiar-catalog-section">
			<div class="wrapper">
				<h2 class="similiar-catalog-section__title">С этим товаром также заказывают</h2>
			</div>
			<div class="similiar-catalog js-similiar-catalog">
				<ul class="similiar-catalog__list">
					{foreach $related_products as $related_product}
						<li class="similiar-catalog__item">
							{include file="_product.tpl" product=$related_product}
						</li>
					{/foreach}
				</ul>
			</div>
		</section>
	{/if}

</section>
