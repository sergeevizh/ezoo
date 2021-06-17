{* Вкладки *}
{capture name=tabs}
	{if in_array('products', $manager->permissions)}<li><a href="index.php?module=ProductsAdmin">Товары</a></li>{/if}
	<li class="active"><a href="index.php?module=CategoriesAdmin">Категории</a></li>
	{if in_array('brands', $manager->permissions)}<li><a href="index.php?module=BrandsAdmin">Бренды</a></li>{/if}
	{if in_array('features', $manager->permissions)}<li><a href="index.php?module=FeaturesAdmin">Свойства</a></li>{/if}
	{if in_array('colors', $manager->permissions)}<li><a href="index.php?module=ColorsAdmin">Цвета</a></li>{/if}
	{if in_array('regions', $manager->permissions)}<li><a href="index.php?module=RegionsAdmin">Магазины</a></li>{/if}
{/capture}

{if $category->id}
{$meta_title = $category->name scope=parent}
{else}
{$meta_title = 'Новая категория' scope=parent}
{/if}

{* Подключаем Tiny MCE *}
{include file='tinymce_init.tpl'}

{* On document load *}
{literal}
<script src="design/js/jquery/jquery.js"></script>
<script src="design/js/jquery/jquery-ui.min.js"></script>
<script src="design/js/autocomplete/jquery.autocomplete-min.js"></script>
<style>
.autocomplete-w1 { background:url(img/shadow.png) no-repeat bottom right; position:absolute; top:0px; left:0px; margin:6px 0 0 6px; /* IE6 fix: */ _background:none; _margin:1px 0 0 0; }
.autocomplete { border:1px solid #999; background:#FFF; cursor:default; text-align:left; overflow-x:auto; min-width: 300px; overflow-y: auto; margin:-6px 6px 6px -6px; /* IE6 specific: */ _height:350px;  _margin:0; _overflow-x:hidden; }
.autocomplete .selected { background:#F0F0F0; }
.autocomplete div { padding:2px 5px; white-space:nowrap; }
.autocomplete strong { font-weight:normal; color:#3399FF; }
</style>
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
<script>
$(function() {


	// Сортировка связанных товаров
	$(".sortable").sortable({
		items: "div.row",
		tolerance:"pointer",
		scrollSensitivity:40,
		opacity:0.7,
		handle: '.move_zone'
	});

	// Удаление связанного товара
	$(".related_categories a.delete").live('click', function() {
		$(this).closest("div.row").fadeOut(200, function() { $(this).remove(); });
		return false;
	});

	// Добавление связанного товара
	var new_related_category = $('#new_related_category').clone(true);
	$('#new_related_category').remove().removeAttr('id');

	$("input#related_categories").autocomplete({
		serviceUrl:'ajax/search_categories.php',
		minChars:0,
		noCache: false,
		onSelect:
			function(suggestion){
				$("input#related_categories").val('').focus().blur();
				new_item = new_related_category.clone().appendTo('.related_categories');
				new_item.removeAttr('id');
				new_item.find('a.related_category_name').html(suggestion.data.name);
				new_item.find('a.related_category_name').attr('href', 'index.php?module=CategoryAdmin&id='+suggestion.data.id);
				new_item.find('input[name*="related_categories"]').val(suggestion.data.id);
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

	// Удаление изображений
	$(".images a.delete").click( function() {
		$("input[name='delete_image']").val('1');
		$(this).closest("ul").fadeOut(200, function() { $(this).remove(); });
		return false;
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
	return name;
}

function generate_meta_description()
{
	if(typeof(tinyMCE.get("description")) =='object')
	{
		description = tinyMCE.get("description").getContent().replace(/(<([^>]+)>)/ig," ").replace(/(\&nbsp;)/ig," ").replace(/^\s+|\s+$/g, '').substr(0, 512);
		return description;
	}
	else
		return $('textarea[name=description]').val().replace(/(<([^>]+)>)/ig," ").replace(/(\&nbsp;)/ig," ").replace(/^\s+|\s+$/g, '').substr(0, 512);
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

<link rel="stylesheet" media="screen" type="text/css" href="design/js/colorpicker/css/colorpicker.css"/>
<script type="text/javascript" src="design/js/colorpicker/js/colorpicker.js"></script>
<script>
    $(function () {


        // Удаление баннера
        $(".banners a.delete").click(function () {
            $("input[name='delete_banner']").val('1');
            $(this).closest("ul").fadeOut(200, function () {
                $(this).remove();
            });
            return false;
        });

        // Удаление фонового изображения
        $(".background-image a.delete").click(function () {
            $("input[name='delete_background']").val('1');
            $(this).closest("ul").fadeOut(200, function () {
                $(this).remove();
            });
            return false;
        });

        $('#color_icon, #color_link').ColorPicker({
            color: $('#color_input').val(),
            onShow: function (colpkr) {
                $(colpkr).fadeIn(500);
                return false;
            },
            onHide: function (colpkr) {
                $(colpkr).fadeOut(500);
                return false;
            },
            onChange: function (hsb, hex, rgb) {
                $('#color_icon').css('backgroundColor', '#' + hex);
                $('#color_input').val(hex);
                $('#color_input').ColorPickerHide();
            }
        });
    });
</script>

{/literal}


{if $message_success}
<!-- Системное сообщение -->
<div class="message message_success">
	<span class="text">{if $message_success=='added'}Категория добавлена{elseif $message_success=='updated'}Категория обновлена{else}{$message_success}{/if}</span>
	<a class="link" target="_blank" href="../catalog/{$category->url}">Открыть категорию на сайте</a>
	{if $smarty.get.return}
	<a class="button" href="{$smarty.get.return}">Вернуться</a>
	{/if}

	<span class="share">
		<a href="#" onClick='window.open("http://vkontakte.ru/share.php?url={$config->root_url|urlencode}/catalog/{$category->url|urlencode}&title={$category->name|urlencode}&description={$category->description|urlencode}&image={$config->root_url|urlencode}/files/categories/{$category->image|urlencode}&noparse=true","displayWindow","width=700,height=400,left=250,top=170,status=no,toolbar=no,menubar=no");return false;'>
  		<img src="{$config->root_url}/simpla/design/images/vk_icon.png" /></a>
		<a href="#" onClick='window.open("http://www.facebook.com/sharer.php?u={$config->root_url|urlencode}/catalog/{$category->url|urlencode}","displayWindow","width=700,height=400,left=250,top=170,status=no,toolbar=no,menubar=no");return false;'>
  		<img src="{$config->root_url}/simpla/design/images/facebook_icon.png" /></a>
		<a href="#" onClick='window.open("http://twitter.com/share?text={$category->name|urlencode}&url={$config->root_url|urlencode}/catalog/{$category->url|urlencode}&hashtags={$category->meta_keywords|replace:' ':''|urlencode}","displayWindow","width=700,height=400,left=250,top=170,status=no,toolbar=no,menubar=no");return false;'>
  		<img src="{$config->root_url}/simpla/design/images/twitter_icon.png" /></a>
	</span>


</div>
<!-- Системное сообщение (The End)-->
{/if}

{if $message_error}
<!-- Системное сообщение -->
<div class="message message_error">
	<span class="text">{if $message_error=='url_exists'}Категория с таким адресом уже существует{elseif $message_error=='name_empty'}У категории должно быть название{elseif $message_error=='url_empty'}URl адрес не может быть пустым{/if}</span>
	<a class="button" href="">Вернуться</a>
</div>
<!-- Системное сообщение (The End)-->
{/if}


<!-- Основная форма -->
<form method=post id=product enctype="multipart/form-data">
<input type=hidden name="session_id" value="{$smarty.session.id}">
	<div id="name">
		<input class="name" name=name type="text" value="{$category->name|escape}"/>
		<input name=id type="hidden" value="{$category->id|escape}"/>
		<div class="checkbox">
			<input name=visible value='1' type="checkbox" id="active_checkbox" {if $category->visible}checked{/if}/> <label for="active_checkbox">Активна</label>
		</div>
		<div class="checkbox">
			<input name=visible_is_main value='1' type="checkbox" id="visible_is_main_checkbox" {if $category->visible_is_main}checked{/if}/> <label for="visible_is_main_checkbox">На главной</label>
		</div>
		<div class="checkbox">
			<input name=visible_childs value='1' type="checkbox" id="visible_childs_checkbox" {if $category->visible_childs}checked{/if}/> <label for="visible_childs_checkbox">Выводить как раздел</label>
		</div>
	</div>
	<div class="setting_cat_page">
		<div class="checkbox">
			<input name=visible_cat_page value='1' type="checkbox" id="visible_cat_page_checkbox" {if $category->visible_cat_page}checked{/if}/> <label for="visible_cat_page_checkbox">Выводить категорию на странице разделов</label>
		</div>
		<div class="checkbox">
			<label for="name_cat_page">Название на странице раздела</label>
			<input class="name_cat_page" name=name_cat_page type="text" value="{$category->name_cat_page|escape}"/>
		</div>
		<div class="checkbox">
			<input id="hide" name=hide type="checkbox" value='1' {if $category->hide}checked{/if}/><label for="visible_cat_page_checkbox">Скрыть категорию</label>
		</div>
		<hr>
	</div>
	<br>
	<div>Параметры для выгрузки в маркет</div><br>
	<div>
		<div>
			<label class="property">Тип товара яндекс:  </label><input name="name_cat_market" size="80" type="text" value="{$category->name_cat_market|escape}"/>
		</div>
	</div><br>
	<div>
		<!--<div>
			<label class="property">Название кат. на маркете:  </label><input name="name_cat_yan_market" size="80" type="text" value="{$category->name_cat_yan_market|escape}"/>
		</div>-->
		<br>
		<div>
			<label class="property">Название кат. на онлайнер:  </label><input name="name_cat_market_onliner" size="80" type="text" value="{$category->name_cat_market_onliner|escape}"/>
		</div>
	</div><br>
	<hr>
	<div id="product_categories">
			<select name="parent_id">
				<option value='0'>Корневая категория</option>
				{function name=category_select level=0}
				{foreach $cats as $cat}
					{if $category->id != $cat->id}
						<option value='{$cat->id}' {if $category->parent_id == $cat->id}selected{/if}>{section name=sp loop=$level}&nbsp;&nbsp;&nbsp;&nbsp;{/section}{$cat->name}</option>
						{category_select cats=$cat->subcategories level=$level+1}
					{/if}
				{/foreach}
				{/function}
				{category_select cats=$categories}
			</select>
	</div>

	<!-- Левая колонка свойств товара -->
	<div id="column_left">

		<!-- Параметры страницы -->
		<div class="block layer">
			<h2>Параметры страницы</h2>
			<ul>
				<li><label class=property>Адрес</label><div class="page_url">/catalog/</div><input name="url" class="page_url" type="text" value="{$category->url|escape}" /></li>
				<li><label class=property>Заголовок</label><input name="meta_title" class="simpla_inp" type="text" value="{$category->meta_title|escape}" /></li>
				<li><label class=property>Ключевые слова</label><input name="meta_keywords" class="simpla_inp" type="text" value="{$category->meta_keywords|escape}" /></li>
				<li><label class=property>Описание</label><textarea name="meta_description" class="simpla_inp">{$category->meta_description|escape}</textarea></li>
				<li><label class=property>H1</label><input name="h1_head" class="simpla_inp" type="text" value="{$category->h1_head|escape}" /></li>
			</ul>
		</div>
		<!-- Параметры страницы (The End)-->

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

		<!-- Изображение категории -->
		<div class="block layer images">
			<h2>Изображение категории</h2>
			<input class='upload_image' name=image type=file>
			<input type=hidden name="delete_image" value="">
			{if $category->image}
			<ul>
				<li>
					<a href='#' class="delete"><img src='design/images/cross-circle-frame.png'></a>
					<img src="../{$config->categories_images_dir}{$category->image}" alt="" />
				</li>
			</ul>
			{/if}
		</div>

		<div class="block layer">
			<h2>Связанные категории:</h2>
			<div id=list class="sortable related_products related_categories">
				{foreach $related_categories as $related_category}
					<div class="row">
						<div class="move cell">
							<div class="move_zone"></div>
						</div>
						<div class="image cell">
							<input type="hidden" name="related_categories[]" value='{$related_category->id}'>
							<a href="{url id=$related_category->id}">
								{if $related_category->image}
									<img class=product_icon src='../{$config->categories_images_dir}{$related_category->image}?{rand(1, 99999)}'>
								{/if}
							</a>
						</div>
						<div class="name cell">
							<a href="{url id=$related_category->id}">{$related_category->name}</a>
						</div>
						<div class="icons cell">
							<a href='#' class="delete"></a>
						</div>
						<div class="clear"></div>
					</div>
				{/foreach}
				<div id="new_related_category" class="row" style='display:none;'>
					<div class="move cell">
						<div class="move_zone"></div>
					</div>
					<div class="image cell">
						<input type=hidden name=related_categories[] value=''>
						<img class=product_icon src=''>
					</div>
					<div class="name cell">
						<a class="related_product_name related_category_name" href=""></a>
					</div>
					<div class="icons cell">
						<a href='#' class="delete"></a>
					</div>
					<div class="clear"></div>
				</div>
			</div>
			<input type=text name=related id='related_categories' class="input_autocomplete" placeholder='Выберите название'>
		</div>
	</div>
	<!-- Правая колонка свойств товара (The End)-->

	<!-- Параметры страницы -->
	<div class="block layer">
		<h2>Кастомизация страницы категории</h2>
		<div style="display: flex">
			<label style="width: 300px;">Цвет фона</label>
			<div class="checkbox" style="padding-left: 0;">
				<span id="color_icon" class="brand-color"  style="width: 300px;border-radius:50px;background-color:#{$category->color};"></span>
				<input id="color_input" name="color" class="simpla_inp" type="hidden" value="{if $category->color}{$category->color}{/if}">
			</div>
		</div>
		<div class="background-image" style="display: flex;margin-top: 40px;">
			<label style="width: 300px;">Картинка для фона</label>
			<input class="upload_image" name="background" type="file">
			<input type="hidden" name="delete_background" value="">
			{if $category->background}
			<ul>
				<li>
					<a href='#' class="delete"><img src='design/images/cross-circle-frame.png'></a>
					<img src="../{$config->categories_background_images_dir}{$category->background}" alt=""/>
				</li>
			</ul>
			{/if}
		</div>
		<div class="banners" style="display: flex;margin-top: 40px;">
			<label style="width: 300px;">Баннер</label>
			<input class="upload_image" name="banner" type="file">
			<input type="hidden" name="delete_banner" value="">
			{if $category->banner}
			<ul>
				<li>
					<a href='#' class="delete"><img src='design/images/cross-circle-frame.png'></a>
					<img src="../{$config->categories_banners_images_dir}{$category->banner}" alt=""/>
				</li>
			</ul>
			{/if}
		</div>

	</div>
	<!-- Параметры страницы (The End)-->

	<!-- Описагние категории -->
	<div class="block layer">
		<h2>Описание</h2>
		<textarea name="description" class="editor_large">{$category->description|escape}</textarea>
	</div>
	<!-- Описание категории (The End)-->
	<input class="button_green button_save" type="submit" name="" value="Сохранить" />

</form>
<!-- Основная форма (The End) -->

