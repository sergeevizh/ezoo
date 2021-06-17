$(document).ready(function() {

	$('.filter-sort').each(function() {
		var filter_sort = $(this);
		var filter_sort_title = filter_sort.find('.filter-sort__title');
		var filter_sort_dropdown = filter_sort.find('.filter-sort__dropdown');
		var filter_sort_link = filter_sort_dropdown.find('.filter-sort__dropdown-link');

		filter_sort_title.click(function(e) {
			e.preventDefault();
			filter_sort.toggleClass('is-open');
		});

		filter_sort_link.click(function(e) {
			// не работал клик по сортировке
			//e.preventDefault();
			filter_sort_link.removeClass('is-active');
			$(this).addClass('is-active');
			filter_sort_title.text($(this).text());
			filter_sort.removeClass('is-open');

			$(this).closest('.filter_mob-fix').removeClass('is-active');
			$('.js-filter-sort-section-info').text($(this).text());
		});
	});

	$(document).click(function(e) {
		if ($(e.target).closest('.filter-sort').length < 1) {
			$('.filter-sort').removeClass('is-open');
		}
	});

	$('.js-filter-colors-all-link').click(function(e) {
		e.preventDefault();
		if (!$(this).hasClass('is-active')) {
			$('.js-filter-colors-all').slideDown(function() {
				$('.js-page-sidebar').trigger('sticky_kit:recalc');
			});
			$(this).text('Скрыть').addClass('is-active');
		} else {
			$('.js-filter-colors-all').slideUp(function() {
				$('.js-page-sidebar').trigger('sticky_kit:recalc');
			});
			$(this).text('Показать все цвета').removeClass('is-active');
		}
	});

	// Brands
	// $('.js-more-brands-link').click(function(e){
	// 	e.preventDefault();
	// 	popupOpen('#filter-check-list-all');
	// });

	/*$('.js-filter-check-list-all input.js-filter-brands-checkbox').change(function(){
	 $('.js-filter-check-list').empty();
	 var html = '';
	 var text= '';
	 $('.js-filter-check-list-all input.js-filter-brands-checkbox:checked').each(function(){
	 html += '<div class="filter-check-list__row">';
	 html += '<div class="check-row">';
	 html += '<label>';
	 html += '<input type="checkbox" class="js-styler js-filter-brands-checkbox" name="'+ $(this).attr('name') +'" checked>';
	 html += '<span class="check-row__text">'+ $(this).closest('.filter-check-list__row').find('.check-row__text').text() +'</span>';
	 html += '</label>';
	 html += '</div>';
	 html += '</div>';

	 var text_separate = text ? ', ' : '';
	 text += (text_separate + '' + $(this).closest('.filter-check-list__row').find('.check-row__text').text());
	 });
	 $('.js-filter-check-list').html(html);
	 $('.js-filter-brands-section-info').text(text);
	 $('.js-filter-check-list .js-styler').styler();
	 $('.js-page-sidebar').trigger('sticky_kit:recalc');
	 });
	 */

	$('.js-filter-check-list').on('change', 'input.js-filter-brands-checkbox', function() {

		// if($('.js-filter-check-list-all').length >0) {
		// 	$('.js-filter-check-list-all input.js-filter-brands-checkbox[name='+ $(this).attr('name') +']').prop('checked', $(this).is(':checked')).trigger('refresh');
		// }

		var text = '';

		$('.js-filter-check-list input.js-filter-brands-checkbox:checked').each(function() {
			//$(this).closest('.js-filter-check-list').find('input.js-filter-brands-checkbox:checked').each(function(){
			var text_separate = text ? ', ' : '';
			text += (text_separate + '' + $(this).closest('.check-row').find('.check-row__text').text());
		});

		//$(this).closest('.js-filter-check-list').find('.js-filter-brands-section-info').text(text);
		$('.js-filter-brands-section-info').text(text);

		showFilterTooltip($(this).closest('.filter-check-list__row').offset().top +
			$(this).closest('.filter-check-list__row').height() / 2 - $('.js-page-sidebar').offset().top);
	});

	// !Brands

	// Price
	// TODO жесть, выглядит как copy & paste
	$('.js-filter-price-range').each(function() {
		var range = $(this),
			range_parents = range.parents('.js-filter-price'),
			range_min = range_parents.find('.js-filter-price-min'),
			range_max = range_parents.find('.js-filter-price-max'),
			input_min = range_parents.find('.js-filter-input-price-min'),
			input_max = range_parents.find('.js-filter-input-price-max');

		range.slider({
			range: true,
			min: range.data('min'),
			max: range.data('max'),
			values: range.data('values'),
			step: range.data('step'),
			slide: function(event, ui) {
				range_min.text(ui.values[0]);
				range_max.text(ui.values[1]);

				input_min.val(ui.values[0]);
				input_max.val(ui.values[1]);

				$('.js-filter-price-section-info-min').text(ui.values[0]);
				$('.js-filter-price-section-info-max').text(ui.values[1]);

				showFilterTooltip($(this).offset().top - $('.js-page-sidebar').offset().top);
			}
		});

		range_min.text(range.slider('values', 0));
		range_max.text(range.slider('values', 1));
	});
	// !Price

	// Price Input
	$('.js-filter-price-input-min').keyup(function() {
		$('.js-filter-price-input-section-info-min').text($(this).val());
		showFilterTooltip($(this).offset().top + $(this).outerHeight() / 2 - $('.js-page-sidebar').offset().top);
	});

	$('.js-filter-price-input-max').keyup(function() {
		$('.js-filter-price-input-section-info-max').text($(this).val());
		showFilterTooltip($(this).offset().top + $(this).outerHeight() / 2 - $('.js-page-sidebar').offset().top);
	});
	// !Price Input

	// Объем
	// TODO переделать
	// 1. вставлять не val() а text()
	// 2. в фильтре может быть несколько выпадалок select
	// так же рекомендую етуже проблемму пересмотреть в других обработчиках фильтра
	$('select.js-filter-capacity').change(function() {
		$(this).closest('.js-filter-capacity-section').find('.js-filter-capacity-section-info').text($(this).val());

		//$('.js-filter-capacity-section-info').text($(this).val());
		showFilterTooltip($(this).offset().top + $(this).outerHeight() / 2 - $('.js-page-sidebar').offset().top);
	});
	// !Объем

	// Параметр с 2 вариантами
	// TODO переделать
	// вопрос такой, а как сбросить если выбрали одно из значений ? поетому предлогаю второй клик по выбраному фильтру должен сбрасывать
	// пересмотреть данную проблемму и в других обработчиках
	$('.filter-check-group__input').change(function() {
		showFilterTooltip($(this).closest('.filter-check-group').offset().top +
			$(this).closest('.filter-check-group').outerHeight() / 2 - $('.js-page-sidebar').offset().top);
	});
	// !Параметр с 2 вариантами
	/*
	 // Размер
	 $('.filter-check__input').change(function(){
	 showFilterTooltip($(this).closest('.filter-check__item').offset().top + $(this).closest('.filter-check__item').outerHeight() / 2 - $('.js-page-sidebar').offset().top);
	 });
	 // !Размер*/

	/*	// Цвет
	 $('.js-filter-colors-input').change(function(){
	 $('.js-filter-colors-section-info').empty();
	 $('.js-filter-colors-input:checked').each(function(){
	 $(this).next('.filter-colors__color').clone().appendTo('.js-filter-colors-section-info');
	 });
	 showFilterTooltip($(this).closest('.filter-colors__item').offset().top + $(this).closest('.filter-colors__item').outerHeight() / 2 - $('.js-page-sidebar').offset().top);
	 });
	 // !Цвет*/

	$(document).click(function() {
		if ($('.js-sidebar-filter-tooltip').hasClass('is-visible')) {
			$('.js-sidebar-filter-tooltip').removeClass('is-visible');
		}
	});

	$('.js-sidebar-nav-block-link').click(function(e) {
		e.preventDefault();
		$('html').addClass('is-lock');
		$('.js-sidebar-nav-block-link').addClass('is-active');
		$('.js-sidebar-nav-block').addClass('is-open');
	});

	$('.js-sidebar-nav-block-close').click(function(e) {
		e.preventDefault();
		$('html').removeClass('is-lock');
		$('.js-sidebar-nav-block-link').removeClass('is-active');
		$('.js-sidebar-nav-block').removeClass('is-open');
	});

	$('.js-sidebar-filter-block-link').click(function(e) {
		e.preventDefault();
		$('html').addClass('is-lock');
		$('.js-sidebar-filter-block-link').addClass('is-active');
		$('.js-sidebar-filter-block').addClass('is-open');
	});

	$('.js-sidebar-filter-block-close').click(function(e) {
		e.preventDefault();
		$('html').removeClass('is-lock');
		$('.js-sidebar-filter-block-link').removeClass('is-active');
		$('.js-sidebar-filter-block').removeClass('is-open');
	});

	$('.js-category-nav-block-link').click(function(e) {
		e.preventDefault();
		$('html').addClass('is-lock');
		$('.js-category-nav-block-link').addClass('is-active');
		$('.js-category-nav-block').addClass('is-open');
	});

	$('.js-category-nav-block-close').click(function(e) {
		e.preventDefault();
		$('html').removeClass('is-lock');
		$('.js-category-nav-block-link').removeClass('is-active');
		$('.js-category-nav-block').removeClass('is-open');
	});

	$('.filter__header').click(function(e) {
		e.preventDefault();
		$('.filter_mob-fix').removeClass('is-active');
		$(this).closest('.filter_mob-fix').addClass('is-active');
	});

	$('.filter__content-close').click(function(e) {
		e.preventDefault();
		$(this).closest('.filter_mob-fix').removeClass('is-active');
	});

	//Чекаем бренд в фильтре на странице подкатегории бренда
	var brand = $('.filter-check-list__row');
	brand.each(function () {

		var brand_link = $(this).find('a').attr('href');
		if (location.href == brand_link || location.href.indexOf(brand_link) != -1 && brand_link != 'catalog/'){
//			$(this).find('.js-filter-brands-checkbox').addClass('checked');
			$(this).find('.js-filter-brands-checkbox').prop('checked', true);
			$('form#filter button[type=submit]').replaceWith('<a href="' + brand_link + '" class="btn submit-link">Подобрать</a>')
		}
	});

});

function showFilterTooltip(top) {

	var form = $('.js-filter-form');



	$.get(form.attr('action') + '?' + form.serialize(), function(data) {



		$('.js-sidebar-filter-tooltip').attr('href', form.attr('action') + '?' + form.serialize());

		var checked = $('.filter-check-list__row').find('.checked'),
			checked_link = checked.parent().find('a').attr('href'),
			min_price_default = $('.js-filter-price-range').attr('data-min'),
			max_price_default = $('.js-filter-price-range').attr('data-max'),
			min_price_current = $('.js-filter-input-price-min').val(),
			max_price_current = $('.js-filter-input-price-max').val(),
			brand = $('.filter-check-list__row'),
			checked_number = checked.length;

		if (checked_number == 1 && checked_link != 'catalog/' && min_price_default == min_price_current && max_price_default == max_price_current){


			if ($('.filter.filter_mob-fix.js-filter-capacity-section').length>0){
				$('.filter.filter_mob-fix.js-filter-capacity-section').each(function () {

					if ($(this).find('.selected').text() != 'Все'){
						$('.js-sidebar-filter-tooltip').attr('href', form.attr('action') + '?' + form.serialize());
						brand.each(function () {

							var brand_link = $(this).find('a').attr('href');
							if (location.href.indexOf(brand_link) != -1){
								var parent_url = $('input[name=parent_cat_url]').attr('val');
								$('.js-sidebar-filter-tooltip').attr('href', parent_url + '?' + form.serialize());
								$('.submit-link').attr('href', parent_url + '?' + form.serialize())
							}
						});


						return false;
					} else {
						$('.js-sidebar-filter-tooltip').attr('href', checked_link);
						$('form#filter button[type=submit]').replaceWith('<a href="' + checked_link + '" class="btn submit-link">Подобрать</a>');
					}

				});
			} else {
				$('.js-sidebar-filter-tooltip').attr('href', checked_link);
				$('form#filter button[type=submit]').replaceWith('<a href="' + checked_link + '" class="btn submit-link">Подобрать</a>');
			}


//			console.log(max_price_current);
		}  else {

			$('.submit-link').replaceWith('<button type="submit" class="btn">Подобрать</button>');
			brand.each(function () {

				var brand_link = $(this).find('a').attr('href');
				if (location.href.indexOf(brand_link) != -1){
					var parent_url = $('input[name=parent_cat_url]').attr('val');
					$('.js-sidebar-filter-tooltip').attr('href', parent_url + '?' + form.serialize());
					$('.submit-link').attr('href', parent_url + '?' + form.serialize())
				}
			});
		}

		$('.js-sidebar-filter-tooltip').html(data).addClass('is-visible').css('top', top);






	}, 'html');

}
