{capture name=tabs}
	<li class="active"><a href="{url module=ProductsAdmin category_id=$product->category_id return=null brand_id=null id=null}">Товары</a></li>
	{if in_array('categories', $manager->permissions)}<li><a href="index.php?module=CategoriesAdmin">Категории</a></li>{/if}
	{if in_array('brands', $manager->permissions)}<li><a href="index.php?module=BrandsAdmin">Бренды</a></li>{/if}
	{if in_array('features', $manager->permissions)}<li><a href="index.php?module=FeaturesAdmin">Свойства</a></li>{/if}
	{if in_array('colors', $manager->permissions)}<li><a href="index.php?module=ColorsAdmin">Цвета</a></li>{/if}
	{if in_array('regions', $manager->permissions)}<li><a href="index.php?module=RegionsAdmin">Магазины</a></li>{/if}
{/capture}

{if $product->id}
{$meta_title = $product->name scope=parent}
{else}
{$meta_title = 'Новый товар' scope=parent}
{/if}

{* Подключаем Tiny MCE *}
{include file='tinymce_init.tpl'}

{* On document load *}
{literal}
<script src="design/js/autocomplete/jquery.autocomplete-min.js"></script>
	<script src="design/js/jquery/datepicker/jquery.ui.datepicker-ru.js"></script>
<script>
$(function() {

    $('.expand_regions').click(function (e) {
        e.preventDefault();

        $(this).closest('.variant_amount').find('.hidden-region').show();

        $(this).next('.hide_regions').css('display', 'inline');
        $(this).hide();
    })

    $('.hide_regions').click(function (e) {
        e.preventDefault();

        $(this).closest('.variant_amount').find('.hidden-region').hide();

        $(this).prev('.expand_regions').css('display', 'inline');
        $(this).hide();
    })

	$('input[name="variants[sale_end][]"]').datepicker({
		regional:'ru'
	});

    $('.date-picker').datepicker({
        regional:'ru'
    });

	// Добавление категории
	$('#product_categories .add').click(function() {
		$("#product_categories ul li:last").clone(false).appendTo('#product_categories ul').fadeIn('slow').find("select[name*=categories]:last").focus();
		$("#product_categories ul li:last span.add").hide();
		$("#product_categories ul li:last span.delete").show();
		return false;
	});

	// Удаление категории
	$("#product_categories .delete").live('click', function() {
		$(this).closest("li").fadeOut(200, function() { $(this).remove(); });
		return false;
	});

	// Сортировка вариантов
	$("#variants_block").sortable({ items: '#variants ul' , axis: 'y',  cancel: '#header', handle: '.move_zone' });
	// Сортировка вариантов
	$("table.related_products").sortable({ items: 'tr' , axis: 'y',  cancel: '#header', handle: '.move_zone' });


	// Сортировка связанных товаров
	$(".sortable").sortable({
		items: "div.row",
		tolerance:"pointer",
		scrollSensitivity:40,
		opacity:0.7,
		handle: '.move_zone'
	});


	// Сортировка изображений
	$(".images ul").sortable({ tolerance: 'pointer'});

	// Удаление изображений
	$(".images a.delete").live('click', function() {
		 $(this).closest("li").fadeOut(200, function() { $(this).remove(); });
		 return false;
	});
	// Загрузить изображение с компьютера
	$('#upload_image').click(function() {
		$("<input class='upload_image' name=images[] type=file multiple  accept='image/jpeg,image/png,image/gif'>").appendTo('div#add_image').focus().click();
	});
	// Или с URL
	$('#add_image_url').click(function() {
		$("<input class='remote_image' name=images_urls[] type=text value='http://'>").appendTo('div#add_image').focus().select();
	});
	// Или перетаскиванием
	if(window.File && window.FileReader && window.FileList)
	{
		$("#dropZone").show();
		$("#dropZone").on('dragover', function (e){
			$(this).css('border', '1px solid #8cbf32');
		});
		$(document).on('dragenter', function (e){
			$("#dropZone").css('border', '1px dotted #8cbf32').css('background-color', '#c5ff8d');
		});

		dropInput = $('.dropInput').last().clone();

		function handleFileSelect(evt){
			var files = evt.target.files; // FileList object
			// Loop through the FileList and render image files as thumbnails.
		    for (var i = 0, f; f = files[i]; i++) {
				// Only process image files.
				if (!f.type.match('image.*')) {
					continue;
				}
			var reader = new FileReader();
			// Closure to capture the file information.
			reader.onload = (function(theFile) {
				return function(e) {
					// Render thumbnail.
					$("<li class=wizard><a href='' class='delete'><img src='design/images/cross-circle-frame.png'></a><img onerror='$(this).closest(\"li\").remove();' src='"+e.target.result+"' /><input name=images_urls[] type=hidden value='"+theFile.name+"'></li>").appendTo('div .images ul');
					temp_input =  dropInput.clone();
					$('.dropInput').hide();
					$('#dropZone').append(temp_input);
					$("#dropZone").css('border', '1px solid #d0d0d0').css('background-color', '#ffffff');
					clone_input.show();
		        };
		      })(f);

		      // Read in the image file as a data URL.
		      reader.readAsDataURL(f);
		    }
		}
		$('.dropInput').live("change", handleFileSelect);
	};

	// Удаление варианта
	$('a.del_variant').click(function() {
		if($("#variants ul").size()>1)
		{
			$(this).closest("ul").fadeOut(200, function() { $(this).remove(); });
		}
		else
		{
			$('#variants_block .variant_name input[name*=variant][name*=name]').val('');
			$('#variants_block .variant_name').hide('slow');
			$('#variants_block').addClass('single_variant');
		}
		return false;
	});

	// Загрузить файл к варианту
	$('#variants_block a.add_attachment').click(function() {
		$(this).hide();
		$(this).closest('li').find('div.browse_attachment').show('fast');
		$(this).closest('li').find('input[name*=attachment]').attr('disabled', false);
		return false;
	});

	// Удалить файл к варианту
	$('#variants_block a.remove_attachment').click(function() {
		closest_li = $(this).closest('li');
		closest_li.find('.attachment_name').hide('fast');
		$(this).hide('fast');
		closest_li.find('input[name*=delete_attachment]').val('1');
		closest_li.find('a.add_attachment').show('fast');
		return false;
	});


	// Добавление варианта
	var variant = $('#new_variant').clone(true);
	$('#new_variant').remove().removeAttr('id');
	$('#variants_block span.add').click(function() {
		if(!$('#variants_block').is('.single_variant'))
		{
			$(variant).clone(true).appendTo('#variants').fadeIn('slow').find("input[name*=variant][name*=name]").focus();
		}
		else
		{
			$('#variants_block .variant_name').show('slow');
			$('#variants_block').removeClass('single_variant');
		}
		return false;
	});


	function show_category_features(category_id)
	{
		$('ul.prop_ul').empty();
		$.ajax({
			url: "ajax/get_features.php",
			data: {category_id: category_id, product_id: $("input[name=id]").val()},
			dataType: 'json',
			success: function(data){
				for(i=0; i<data.length; i++)
				{
					feature = data[i];

					line = $("<li><label class=property></label><input class='simpla_inp' type='text'/></li>");
					var new_line = line.clone(true);
					new_line.find("label.property").text(feature.name);
					new_line.find("input").attr('name', "options["+feature.id+"]").val(feature.value);
					new_line.appendTo('ul.prop_ul').find("input")
					.autocomplete({
						serviceUrl:'ajax/options_autocomplete.php',
						minChars:0,
						params: {feature_id:feature.id},
						noCache: false
					});
				}
			}
		});
		return false;
	}

	// Изменение набора свойств при изменении категории
	$('select[name="categories[]"]:first').change(function() {
		show_category_features($("option:selected",this).val());
	});

	// Автодополнение свойств
	$('ul.prop_ul input[name*=options]').each(function(index) {
		feature_id = $(this).closest('li').attr('feature_id');
		$(this).autocomplete({
			serviceUrl:'ajax/options_autocomplete.php',
			minChars:0,
			params: {feature_id:feature_id},
			noCache: false
		});
	});

	// Добавление нового свойства товара
	var new_feature = $('#new_feature').clone(true);
	$('#new_feature').remove().removeAttr('id');
	$('#add_new_feature').click(function() {
		$(new_feature).clone(true).appendTo('ul.new_features').fadeIn('slow').find("input[name*=new_feature_name]").focus();
		return false;
	});


	// Удаление связанного товара
	$(".related_products a.delete").live('click', function() {
		 $(this).closest("div.row").fadeOut(200, function() { $(this).remove(); });
		 return false;
	});


	// Добавление связанного товара
	var new_related_product = $('#new_related_product').clone(true);
	$('#new_related_product').remove().removeAttr('id');

	$("input#related_products").autocomplete({
		serviceUrl:'ajax/search_products.php',
		minChars:0,
		noCache: false,
		onSelect:
			function(suggestion){
				$("input#related_products").val('').focus().blur();
				new_item = new_related_product.clone().appendTo('.related_products');
				new_item.removeAttr('id');
				new_item.find('a.related_product_name').html(suggestion.data.name);
				new_item.find('a.related_product_name').attr('href', 'index.php?module=ProductAdmin&id='+suggestion.data.id);
				new_item.find('input[name*="related_products"]').val(suggestion.data.id);
				if(suggestion.data.image)
					new_item.find('img.product_icon').attr("src", suggestion.data.image);
				else
					new_item.find('img.product_icon').remove();
				new_item.show();
			},
		formatResult:
			function(suggestions, currentValue){
				var reEscape = new RegExp('(\\' + ['/', '.', '*', '+', '?', '|', '(', ')', '[', ']', '{', '}', '\\'].join('|\\') + ')', 'g');
				var pattern = '(' + currentValue.replace(reEscape, '\\$1') + ')';
  				return (suggestions.data.image?"<img align=absmiddle src='"+suggestions.data.image+"'> ":'') + suggestions.value.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>');
			}

	});


	// infinity
	$("input[name*=variant][name*=stock]").focus(function() {
		if($(this).val() == '∞')
			$(this).val('');
		return false;
	});

	$("input[name*=variant][name*=stock]").blur(function() {
		if($(this).val() == '')
			$(this).val('∞');
	});


	// Автозаполнение мета-тегов
	meta_title_touched = true;
	meta_keywords_touched = true;
	meta_description_touched = true;
	url_touched = true;

	if($('input[name="meta_title"]').val() == generate_meta_title() || $('input[name="meta_title"]').val() == '')
		meta_title_touched = false;
	if($('input[name="meta_keywords"]').val() == generate_meta_keywords() || $('input[name="meta_keywords"]').val() == '')
		meta_keywords_touched = false;
	if($('textarea[name="meta_description"]').val() == generate_meta_description() || $('textarea[name="meta_description"]').val() == '')
		meta_description_touched = false;
	if($('input[name="url"]').val() == generate_url() || $('input[name="url"]').val() == '')
		url_touched = false;

	$('input[name="meta_title"]').change(function() { meta_title_touched = true; });
	$('input[name="meta_keywords"]').change(function() { meta_keywords_touched = true; });
	$('textarea[name="meta_description"]').change(function() { meta_description_touched = true; });
	$('input[name="url"]').change(function() { url_touched = true; });

	$('input[name="name"]').keyup(function() { set_meta(); });
	$('select[name="brand_id"]').change(function() { set_meta(); });
	$('select[name="categories[]"]').change(function() { set_meta(); });

});

function set_meta()
{
	if(!meta_title_touched)
		$('input[name="meta_title"]').val(generate_meta_title());
	if(!meta_keywords_touched)
		$('input[name="meta_keywords"]').val(generate_meta_keywords());
	if(!meta_description_touched)
		$('textarea[name="meta_description"]').val(generate_meta_description());
	if(!url_touched)
		$('input[name="url"]').val(generate_url());
}

function generate_meta_title()
{
	name = $('input[name="name"]').val();
	return name;
}

function generate_meta_keywords()
{
	name = $('input[name="name"]').val();
	result = name;
	brand = $('select[name="brand_id"] option:selected').attr('brand_name');
	if(typeof(brand) == 'string' && brand!='')
			result += ', '+brand;
	$('select[name="categories[]"]').each(function(index) {
		c = $(this).find('option:selected').attr('category_name');
		if(typeof(c) == 'string' && c != '')
    		result += ', '+c;
	});
	return result;
}

function generate_meta_description()
{
	if(typeof(tinyMCE.get("annotation")) =='object')
	{
		description = tinyMCE.get("annotation").getContent().replace(/(<([^>]+)>)/ig," ").replace(/(\&nbsp;)/ig," ").replace(/^\s+|\s+$/g, '').substr(0, 512);
		return description;
	}
	else
		return $('textarea[name=annotation]').val().replace(/(<([^>]+)>)/ig," ").replace(/(\&nbsp;)/ig," ").replace(/^\s+|\s+$/g, '').substr(0, 512);
}

function generate_url()
{
	url = $('input[name="name"]').val();
	url = url.replace(/[\s]+/gi, '-');
	url = translit(url);
	url = url.replace(/[^0-9a-z_\-]+/gi, '').toLowerCase();
	return url;
}

function translit(str)
{
	var ru=("А-а-Б-б-В-в-Ґ-ґ-Г-г-Д-д-Е-е-Ё-ё-Є-є-Ж-ж-З-з-И-и-І-і-Ї-ї-Й-й-К-к-Л-л-М-м-Н-н-О-о-П-п-Р-р-С-с-Т-т-У-у-Ф-ф-Х-х-Ц-ц-Ч-ч-Ш-ш-Щ-щ-Ъ-ъ-Ы-ы-Ь-ь-Э-э-Ю-ю-Я-я").split("-")
	var en=("A-a-B-b-V-v-G-g-G-g-D-d-E-e-E-e-E-e-ZH-zh-Z-z-I-i-I-i-I-i-J-j-K-k-L-l-M-m-N-n-O-o-P-p-R-r-S-s-T-t-U-u-F-f-H-h-TS-ts-CH-ch-SH-sh-SCH-sch-'-'-Y-y-'-'-E-e-YU-yu-YA-ya").split("-")
 	var res = '';
	for(var i=0, l=str.length; i<l; i++)
	{
		var s = str.charAt(i), n = ru.indexOf(s);
		if(n >= 0) { res += en[n]; }
		else { res += s; }
    }
    return res;
}

</script>

<style>
.autocomplete-suggestions{
background-color: #ffffff;
overflow: hidden;
border: 1px solid #e0e0e0;
overflow-y: auto;
}
.autocomplete-suggestions .autocomplete-suggestion{cursor: default;}
.autocomplete-suggestions .selected { background:#F0F0F0; }
.autocomplete-suggestions div { padding:2px 5px; white-space:nowrap; }
.autocomplete-suggestions strong { font-weight:normal; color:#3399FF; }
</style>
{/literal}



{if $message_success}
<!-- Системное сообщение -->
<div class="message message_success">
	<span class="text">{if $message_success=='added'}Товар добавлен{elseif $message_success=='updated'}Товар изменен{else}{$message_success|escape}{/if}</span>

	<a class="link" target="_blank" href="../products/{$product->url}">Открыть товар на сайте</a>
	{if $smarty.get.return}
	<a class="button" href="{$smarty.get.return}">Вернуться</a>
	{/if}

	<span class="share">
		<a href="#" onClick='window.open("http://vkontakte.ru/share.php?url={$config->root_url|urlencode}/products/{$product->url|urlencode}&title={$product->name|urlencode}&description={$product->annotation|urlencode}&image={$product_images.0->filename|resize:1000:1000|urlencode}&noparse=true","displayWindow","width=700,height=400,left=250,top=170,status=no,toolbar=no,menubar=no");return false;'>
  		<img src="{$config->root_url}/simpla/design/images/vk_icon.png" /></a>
		<a href="#" onClick='window.open("http://www.facebook.com/sharer.php?u={$config->root_url|urlencode}/products/{$product->url|urlencode}","displayWindow","width=700,height=400,left=250,top=170,status=no,toolbar=no,menubar=no");return false;'>
  		<img src="{$config->root_url}/simpla/design/images/facebook_icon.png" /></a>
		<a href="#" onClick='window.open("http://twitter.com/share?text={$product->name|urlencode}&url={$config->root_url|urlencode}/products/{$product->url|urlencode}&hashtags={$product->meta_keywords|replace:' ':''|urlencode}","displayWindow","width=700,height=400,left=250,top=170,status=no,toolbar=no,menubar=no");return false;'>
  		<img src="{$config->root_url}/simpla/design/images/twitter_icon.png" /></a>
	</span>

</div>
<!-- Системное сообщение (The End)-->
{/if}

{if $message_error}
<!-- Системное сообщение -->
<div class="message message_error">
	<span class="text">{if $message_error=='url_exists'}Товар с таким адресом уже существует{elseif $message_error=='empty_name'}Введите название{else}{$message_error|escape}{/if}</span>
	{if $smarty.get.return}
	<a class="button" href="{$smarty.get.return}">Вернуться</a>
	{/if}
</div>
<!-- Системное сообщение (The End)-->
{/if}


<!-- Основная форма -->
<form method=post id=product enctype="multipart/form-data">
<input type=hidden name="session_id" value="{$smarty.session.id}">

 	<div id="name">
		<input class="name" name=name type="text" value="{$product->name|escape}"/>
		<input name=id type="hidden" value="{$product->id|escape}"/>
		<div class="checkbox">
			<input name=visible value='1' type="checkbox" id="active_checkbox" {if $product->visible}checked{/if}/> <label for="active_checkbox">Активен</label>
		</div>
		<div class="checkbox">
			<input name=featured value="1" type="checkbox" id="featured_checkbox" {if $product->featured}checked{/if}/> <label for="featured_checkbox">Рекомендуемый</label>
		</div>
		<div style="display: table-row">

			<div class="checkbox">
				<input name=pickup value="1" type="checkbox" id="pickup_checkbox" {if $product->pickup}checked{/if}/> <label for="pickup_checkbox">Только самовывоз</label>
			</div>
			<div class="checkbox">
				<input name=lecense value="1" type="checkbox" id="lecense_checkbox" {if $product->lecense}checked{/if}/> <label for="lecense_checkbox">Ветпрепарат с лицензией</label>
			</div>
			<div class="checkbox">
				<input name=marketing_offer value="1" type="checkbox" id="marketing_offer_checkbox" {if $product->marketing_offer}checked{/if}/> <label for="marketing_offer_checkbox">Акция маркетинг</label>
			</div>
			<!--featured_cart-->
			<div class="checkbox">
				<input name=featured_cart value="1" type="checkbox" id="featured_cart_checkbox" {if $product->featured_cart}checked{/if}/> <label for="featured_cart_checkbox">Рекомендованный <span>(для корзины)</span> </label>
			</div>
			<!--END featured_cart-->
		</div>


	</div><hr>
	<div>Параметры для выгрузки в маркет</div><br>
	<div>
		<div>
			<label class="property">Наименование для маркета:  </label><input name="name_yan_market" size="80" type="text" value="{$product->name_yan_market|escape}"/>
		</div>
	</div><br><hr>
	
	<div>Параметры для выгрузки в Onliner.by</div><br>
	<div>
		<div>
			<label class="property">Наименование для Onliner.by:  </label><input name="name_onliner_market" size="80" type="text" value="{$product->name_onliner_market|escape}"/>
		</div>
	</div><br><hr>

	<div id="product_brand" {if !$brands}style='display:none;'{/if}>
		<label>Бренд</label>
		<select name="brand_id">
            <option value='0' {if !$product->brand_id}selected{/if} brand_name=''>Не указан</option>
       		{foreach $brands as $brand}
            	<option value='{$brand->id}' {if $product->brand_id == $brand->id}selected{/if} brand_name='{$brand->name|escape}'>{$brand->name|escape}</option>
        	{/foreach}
		</select>
	</div>


	<div id="product_categories" {if !$categories}style='display:none;'{/if}>
		<label>Категория</label>
		<div>
			<ul>
				{foreach $product_categories as $product_category name=categories}
				<li>
					<select name="categories[]">
						{function name=category_select level=0}
						{foreach $categories as $category}
								<option value='{$category->id}' {if $category->id == $selected_id}selected{/if} category_name='{$category->name|escape}'data-parent-cat="{$category->parent_id}">{section name=sp loop=$level}&nbsp;&nbsp;&nbsp;&nbsp;{/section}{$category->name|escape}</option>
								{category_select categories=$category->subcategories selected_id=$selected_id  level=$level+1}
						{/foreach}
						{/function}
						{category_select categories=$categories selected_id=$product_category->id}
					</select>
					<span {if not $smarty.foreach.categories.first}style='display:none;'{/if} class="add"><i class="dash_link">Дополнительная категория</i></span>
					<span {if $smarty.foreach.categories.first}style='display:none;'{/if} class="delete"><i class="dash_link">Удалить</i></span>
				</li>
				{/foreach}
			</ul>
		</div>
	</div>


 	<!-- Варианты товара -->
	<div id="variants_block" {assign var=first_variant value=$product_variants|@first}{if $product_variants|@count <= 1 && !$first_variant->name}class=single_variant{/if}>
		<ul id="header">
			<li class="variant_move"></li>
			<li class="variant_name">Название варианта</li>
			<li class="variant_sku">Артикул</li>
			<li class="variant_price">Цена, {$currency->sign}</li>
			<li class="variant_discount">Старая, {$currency->sign}</li>
			<li class="variant_amount">Кол-во</li>
			<li class="variant_sale-end">Конец акции</li>
		</ul>
		<div id="variants">
			{assign var="x" value="0"}
			{foreach $product_variants as $variant}

			<ul>
				<li class="variant_move"><div class="move_zone"></div></li>
				<li class="variant_name">      <input name="variants[id][{$x}]"            type="hidden" value="{$variant->id|escape}" /><input name="variants[name][{$x}]" type="" value="{$variant->name|escape}" /> <a class="del_variant" href=""><img src="design/images/cross-circle-frame.png" alt="" /></a></li>
				<li class="variant_sku">       <input name="variants[sku][{$x}]"           type="text"   value="{$variant->sku|escape}" /></li>
				<li class="variant_price">     <input name="variants[price][{$x}]"         type="text"   value="{$variant->price|escape}" /></li>
				<li class="variant_discount">  <input name="variants[compare_price][{$x}]" type="text"   value="{$variant->compare_price|escape}" /></li>
				<li class="variant_amount">
					<span>Основной склад</span><input name="variants[stock][{$x}]" type="text" value="{if $variant->infinity || $variant->stock == ''}∞{else}{$variant->stock|escape}{/if}" />{$settings->units}
					<input class="date-picker" type="text" name="variants[variant_supply][{$x}][0]" value="{$variant->supply_dates[0]|escape}"placeholder="Дата поставки" style="width: 100px;" autocomplete="off">
					{assign var="i" value="0"}
					{foreach $regions as $region}
					{assign var="i" value=$i+1}

					<div
						{if $i > 2} class="hidden-region" {/if}><span>{$region->name}</span>
						<input name="variants[region_stock_{$region->id}][{$x}]" type="text" value="{$variant->region_stock_{$region->id}|escape}" />{$settings->units}
						<input class="date-picker" type="text" name="variants[variant_supply][{$x}][{$region->id}]" value="{$variant->supply_dates[{$region->id}]|escape}"placeholder="Дата поставки" style="width: 100px;" autocomplete="off">
					</div>
					{/foreach}
					<a class="dash_link expand_regions" href="#" style="display: inline;color:#008fe9;border-bottom-color: #008fe9;">Показать больше ↓</a>
					<a class="dash_link hide_regions" href="#" style="color:#008fe9;border-bottom-color: #008fe9;">Скрыть ↑</a>

				</li>




				<li class="variant_sale-end">    <input name="variants[sale_end][{$x}]"         type="text"   value="{$variant->sale_end|escape}" /></li>
				<li class="variant_download">

					{if $variant->attachment}
					<span class=attachment_name>{$variant->attachment|truncate:25:'...':false:true}</span>
					<a href='#' class=remove_attachment><img src='design/images/bullet_delete.png'  title="Удалить цифровой товар"></a>
					<a href='#' class=add_attachment style='display:none;'><img src="design/images/cd_add.png" title="Добавить цифровой товар" /></a>
					{else}
					<a href='#' class=add_attachment><img src="design/images/cd_add.png"  title="Добавить цифровой товар" /></a>
					{/if}
					<div class=browse_attachment style='display:none;'>
						<input type=file name=attachment[]>
						<input type=hidden name=delete_attachment[]>
					</div>
				</li>
			</ul>
			{assign var="x" value=$x+1}
			{/foreach}
		</div>
		<ul id=new_variant style='display:none;'>
			<li class="variant_move"><div class="move_zone"></div></li>
			<li class="variant_name"><input name="variants[id][]" type="hidden" value="" /><input name="variants[name][]" type="" value="" /><a class="del_variant" href=""><img src="design/images/cross-circle-frame.png" alt="" /></a></li>
			<li class="variant_sku"><input name="variants[sku][]" type="" value="" /></li>
			<li class="variant_price"><input  name="variants[price][]" type="" value="" /></li>
			<li class="variant_discount"><input name="variants[compare_price][]" type="" value="" /></li>
			<li class="variant_amount"><input name="variants[stock][]" type="" value="∞" />{$settings->units}</li>
			<li class="variant_download">
				<a href='#' class=add_attachment><img src="design/images/cd_add.png" alt="" /></a>
				<div class=browse_attachment style='display:none;'>
					<input type=file name=attachment[]>
					<input type=hidden name=delete_attachment[]>
				</div>
			</li>
		</ul>

		<input class="button_green button_save" type="submit" name="" value="Сохранить" />
		<span class="add" id="add_variant"><i class="dash_link">Добавить вариант</i></span>
 	</div>
	<!-- Варианты товара (The End)-->

	<!--Процент скидок на товары-->
	<hr>
	<div class="sale-block-products layer">
		<h2>Индивидуальные скидки на товары</h2>
		<ul id="list_deliver_disconts_product">
			{foreach $deliveries_products as $delivery_product}
				<li>
					<label class=property>Скидка </label>
					<input type="text" class="simpla_small_inp" style="width: 100px;" required name="deliveries_products[discount_percent][]" value="{$delivery_product->discount_percent}"> % на доставку -&nbsp
					<select name="deliveries_products[delivery_id][]" class="order-block__field" required>
						<option value="">Выбрать метод доставки</option>
						{foreach $deliveries as $delivery}
							<option value='{$delivery->id}'
									{if $delivery_product->delivery_id==$delivery->id}selected="selected"{/if}
									delivery_name='{$delivery->name|escape}'>{$delivery->name|escape}</option>
						{/foreach}
					</select> сумма от -&nbsp
					<input type="text" class="simpla_small_inp" style="width: 100px;" required name="deliveries_products[discount_from][]" value="{$delivery_product->discount_from}">
					<span class="delete" title="Удалить скидку"></span>
				</li>
			{/foreach}
		</ul>
		<span id="add_delivery_discont_product">Добавить скидку</span>
		<input class="button_green button_save" type="submit" name="" value="Сохранить" />
		<div style="clear: both"></div>
	</div>
	<hr>
	<!--Процент скидок на товары End-->

	<!--Скидка на второй товар-->
	<div class="sale-block-products layer">
		<h2>Скидка на второй товар</h2>
		<span>
			<input name=sale_double_item value='1' type="checkbox" id="sale_double_item" {if $product->sale_double_item}checked{/if}/> <label for="sale_double_item">Включить скидку при доставке курьером</label>
		</span>
		- <span><input name=sale_double_item_value type="number" value="{$product->sale_double_item_value|escape}"/> %</span>
		<br>
		<span>
			<input name=sale_double_item_sam value='1' type="checkbox" id="sale_double_item_sam" {if $product->sale_double_item_sam}checked{/if}/> <label for="sale_double_item_sam">Включить скидку при самовывозе</label>
		</span>
		- <span><input name=sale_double_item_sam_value type="number" value="{$product->sale_double_item_sam_value|escape}"/> %</span>
		<input class="button_green button_save" type="submit" name="" value="Сохранить" />
		<div style="clear: both"></div>
	</div>
	<hr>
	<!--Скидка на второй товар-->

 	<!-- Левая колонка свойств товара -->
	<div id="column_left">

		<!-- Параметры страницы -->
		<div class="block layer">
			<h2>Параметры страницы</h2>
			<ul>
				<li><label class=property>Адрес</label><div class="page_url"> /products/</div><input name="url" class="page_url" type="text" value="{$product->url|escape}" /></li>
				<li><label class=property>Заголовок</label><input name="meta_title" class="simpla_inp" type="text" value="{$product->meta_title|escape}" /></li>
				<li><label class=property>Ключевые слова</label><input name="meta_keywords" class="simpla_inp" type="text" value="{$product->meta_keywords|escape}" /></li>
				<li><label class=property>Описание</label><textarea name="meta_description" class="simpla_inp" />{$product->meta_description|escape}</textarea></li>
			</ul>
		</div>
		<!-- Параметры страницы (The End)-->

		<div class="block layer" {if !$categories}style='display:none;'{/if}>
			<h2>Свойства товара</h2>

			<ul class="prop_ul">
				{foreach $features as $feature}
					{assign var=feature_id value=$feature->id}
					<li feature_id={$feature_id}><label class=property>{$feature->name}</label><input class="simpla_inp" type="text" name=options[{$feature_id}] value="{$options.$feature_id->value|escape}" /></li>
				{/foreach}
			</ul>
			<!-- Новые свойства -->
			<ul class=new_features>
				<li id=new_feature><label class=property><input type=text name=new_features_names[]></label><input class="simpla_inp" type="text" name=new_features_values[] /></li>
			</ul>
			<span class="add"><i class="dash_link" id="add_new_feature">Добавить новое свойство</i></span>
			<input class="button_green button_save" type="submit" name="" value="Сохранить" />
		</div>

		<!-- Свойства товара (The End)-->

		{*
		<!-- Экспорт-->
		<div class="block">
			<h2>Экспорт товара</h2>
			<ul>
				<li><input id="exp_yad" type="checkbox" /> <label for="exp_yad">Яндекс Маркет</label> Бид <input class="simpla_inp" type="" name="" value="12" /> руб.</li>
				<li><input id="exp_goog" type="checkbox" /> <label for="exp_goog">Google Base</label> </li>
			</ul>
		</div>
		<!-- Свойства товара (The End)-->
		*}

	</div>
	<!-- Левая колонка свойств товара (The End)-->

	<!-- Правая колонка свойств товара -->
	<div id="column_right">

		<!-- Изображения товара -->
		<div class="block layer images">
			<h2>Изображения товара</h2>
			<ul>{foreach $product_images as $image}<li>
					<a href='#' class="delete"><img src='design/images/cross-circle-frame.png'></a>
					<img src="{$image->filename|resize:100:100}" alt="" />
					<input type=hidden name='images[]' value='{$image->id}'>
				</li>{/foreach}</ul>
			<div id=dropZone>
				<div id=dropMessage>Перетащите файлы сюда</div>
				<input type="file" name="dropped_images[]" multiple class="dropInput">
			</div>
			<div id="add_image"></div>
			<span class=upload_image><i class="dash_link" id="upload_image">Добавить изображение</i></span> или <span class=add_image_url><i class="dash_link" id="add_image_url">загрузить из интернета</i></span>
		</div>

		<div class="block layer">
			<h2>Связанные товары</h2>
			<div id=list class="sortable related_products">
				{foreach $related_products as $related_product}
				<div class="row">
					<div class="move cell">
						<div class="move_zone"></div>
					</div>
					<div class="image cell">
					<input type=hidden name=related_products[] value='{$related_product->id}'>
					<a href="{url id=$related_product->id}">
					<img class=product_icon src='{$related_product->images[0]->filename|resize:35:35}'>
					</a>
					</div>
					<div class="name cell">
					<a href="{url id=$related_product->id}">{$related_product->name}</a>
					</div>
					<div class="icons cell">
					<a href='#' class="delete"></a>
					</div>
					<div class="clear"></div>
				</div>
				{/foreach}
				<div id="new_related_product" class="row" style='display:none;'>
					<div class="move cell">
						<div class="move_zone"></div>
					</div>
					<div class="image cell">
					<input type=hidden name=related_products[] value=''>
					<img class=product_icon src=''>
					</div>
					<div class="name cell">
					<a class="related_product_name" href=""></a>
					</div>
					<div class="icons cell">
					<a href='#' class="delete"></a>
					</div>
					<div class="clear"></div>
				</div>
			</div>
			<input type=text name=related id='related_products' class="input_autocomplete" placeholder='Выберите товар чтобы добавить его'>
		</div>

		<input class="button_green button_save" type="submit" name="" value="Сохранить" />

	</div>
	<!-- Правая колонка свойств товара (The End)-->

	<!-- Описагние товара -->
	<div class="block layer">
		<h2>Краткое описание</h2>
		<textarea name="annotation" class="editor_small">{$product->annotation|escape}</textarea>
	</div>

	<div class="block">
		<h2>Полное  описание</h2>
		<textarea name="body" class="editor_large">{$product->body|escape}</textarea>
	</div>
	<!-- Описание товара (The End)-->
	<input class="button_green button_save" type="submit" name="" value="Сохранить" />

</form>
<div id="copy_li_discont_product" style="display: none">
	<li>
		<label class=property>Скидка </label>
		<input type="text" class="simpla_small_inp" style="width: 100px;" required name="deliveries_products[discount_percent][]" value="0"> % на доставку -&nbsp
		<select name="deliveries_products[delivery_id][]" class="order-block__field" required>
			<option value="">Выбрать метод доставки</option>
			{foreach $deliveries as $delivery}
				<option value='{$delivery->id}'	delivery_name='{$delivery->name|escape}'>{$delivery->name|escape}</option>
			{/foreach}
		</select> сумма от -&nbsp
		<input type="text" class="simpla_small_inp" style="width: 100px;" required name="deliveries_products[discount_from][]" value="0">
		<span class="delete" title="Удалить скидку"></span>
	</li>
</div>
<script>
	$('#add_delivery_discont_product').on('click' ,function () {
		$('#copy_li_discont_product li').clone().appendTo('#list_deliver_disconts_product');
	});
	$('#list_deliver_disconts_product').on('click',function () {
		if ($(event.target).hasClass('delete')) deleteDeliverDiscont(event.target);
	});
	function deleteDeliverDiscont(element) {
		$(element).parents('li').remove();
	};

	var selection = $('#product_categories').find('select');
	selection.on('change',function () {
		var selected = selection.children('option::selected').val(),
			selected_parent = selection.children('option::selected').attr('data-parent-cat');
		selection.each(function () {
			console.log('хех');
		})
		if (selected == '766835' || selected == '766888' || selected_parent == '766835' || selected_parent == '766888') {
			$('#pickup_checkbox').prop('checked', true);
            $('#lecense_checkbox').prop('checked', true);
		} else {
			$('#pickup_checkbox').prop('checked', false);
            $('#lecense_checkbox').prop('checked', false);
		}

	});
</script>
<!-- Основная форма (The End) -->

