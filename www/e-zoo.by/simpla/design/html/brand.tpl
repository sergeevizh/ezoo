{* Вкладки *}
{capture name=tabs}
	{if in_array('products', $manager->permissions)}<li><a href="index.php?module=ProductsAdmin">Товары</a></li>{/if}
	{if in_array('categories', $manager->permissions)}<li><a href="index.php?module=CategoriesAdmin">Категории</a></li>{/if}
	<li class="active"><a href="index.php?module=BrandsAdmin">Бренды</a></li>
	{if in_array('features', $manager->permissions)}<li><a href="index.php?module=FeaturesAdmin">Свойства</a></li>{/if}
	{if in_array('colors', $manager->permissions)}<li><a href="index.php?module=ColorsAdmin">Цвета</a></li>{/if}
	{if in_array('regions', $manager->permissions)}<li><a href="index.php?module=RegionsAdmin">Магазины</a></li>{/if}
{/capture}

{if $brand->id}
{$meta_title = $brand->name scope=parent}
{else}
{$meta_title = 'Новый бренд' scope=parent}
{/if}

{* Подключаем Tiny MCE *}
{include file='tinymce_init.tpl'}


{* On document load *}
{literal}
<script src="design/js/autocomplete/jquery.autocomplete-min.js"></script>
<script src="design/js/jquery/datepicker/jquery.ui.datepicker-ru.js"></script>
<script>
$(function() {

    $('.supply_date_input').datepicker({
        regional:'ru'
    })

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
	$('input[textarea="meta_description"]').change(function() { meta_description_touched = true; });
	$('input[name="url"]').change(function() { url_touched = true; });

	$('input[name="name"]').keyup(function() { set_meta(); });

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
		name = $('input[name="name"]').val();
		return name;
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

});

</script>
<link rel="stylesheet" media="screen" type="text/css" href="design/js/colorpicker/css/colorpicker.css"/>
<script type="text/javascript" src="design/js/colorpicker/js/colorpicker.js"></script>
<script>
    $(function () {
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
	<span class="text">{if $message_error=='url_exists'}Бренд с таким адресом уже существует{elseif $message_error=='name_empty'}У бренда должно быть название{elseif $message_error=='url_empty'}URl адрес не может быть пустым{/if}</span>
	<a class="link" target="_blank" href="../brands/{$brand->url}">Открыть бренд на сайте</a>
	{if $smarty.get.return}
	<a class="button" href="{$smarty.get.return}">Вернуться</a>
	{/if}

	<span class="share">
		<a href="#" onClick='window.open("http://vkontakte.ru/share.php?url={$config->root_url|urlencode}/brands/{$brand->url|urlencode}&title={$brand->name|urlencode}&description={$brand->description|urlencode}&image={$config->root_url|urlencode}/files/brands/{$brand->image|urlencode}&noparse=true","displayWindow","width=700,height=400,left=250,top=170,status=no,toolbar=no,menubar=no");return false;'>
  		<img src="{$config->root_url}/simpla/design/images/vk_icon.png" /></a>
		<a href="#" onClick='window.open("http://www.facebook.com/sharer.php?u={$config->root_url|urlencode}/brands/{$brand->url|urlencode}","displayWindow","width=700,height=400,left=250,top=170,status=no,toolbar=no,menubar=no");return false;'>
  		<img src="{$config->root_url}/simpla/design/images/facebook_icon.png" /></a>
		<a href="#" onClick='window.open("http://twitter.com/share?text={$brand->name|urlencode}&url={$config->root_url|urlencode}/brands/{$brand->url|urlencode}&hashtags={$brand->meta_keywords|replace:' ':''|urlencode}","displayWindow","width=700,height=400,left=250,top=170,status=no,toolbar=no,menubar=no");return false;'>
  		<img src="{$config->root_url}/simpla/design/images/twitter_icon.png" /></a>
	</span>

</div>
<!-- Системное сообщение (The End)-->
{/if}

{if $message_error}
<!-- Системное сообщение -->
<div class="message message_error">
	<span class="text">{if $message_error=='url_exists'}Бренд с таким адресом уже существует{else}{$message_error}{/if}</span>
	<a class="button" href="{$smarty.get.return}">Вернуться</a>
</div>
<!-- Системное сообщение (The End)-->
{/if}


<!-- Основная форма -->
<form method=post id=product enctype="multipart/form-data">
<input type=hidden name="session_id" value="{$smarty.session.id}">
	<div id="name">
		<input class="name" name="name" type="text" value="{$brand->name|escape}"/>
		<input name=id type="hidden" value="{$brand->id|escape}"/>
		<div class="checkbox">
			<input name="label_new" value='1' type="checkbox" id="label_new_checkbox" {if $brand->label_new}checked{/if}/> <label for="label_new_checkbox">new</label>
		</div>
	</div>
	<div>
		<div>
			<input name="visible_is_main" value="1" type="checkbox" id="visible_is_main_checkbox" {if $brand->visible_is_main}checked{/if}/> <label for="visible_is_main_checkbox">Показать в слайдере на главной</label>
		</div>
		<br>
		<div><label class="property">Сортировка в слайдере на главной:  </label> <input name="sort" min="0" class="simpla_inp" type="number" value="{$brand->sort|escape}" /></div>
	</div>
	<br>
	<div class="layer">Параметры для выгрузки в маркет</div><br>
	<div>
		<div>
			<label class="property">Наименование чистое:  </label><input name="clear-name" size="80" type="text" value="{$brand->clear_name|escape}"/>
		</div>
	</div><br>
	<div>
		<div>
			<label class="property">Изготовитель:  </label><input name="manufacturer" size="80" type="text" value="{$brand->manufacturer|escape}"/>
		</div>
	</div><br>
	<div>
		<div>
			<label class="property">Импортёр:  </label><input name="importer" size="80" type="text" value="{$brand->importer|escape}"/>
		</div>
	</div><br>


	<!-- Левая колонка свойств товара -->
	<div id="column_left">

		<!-- Параметры страницы -->
		<div class="block layer">
			<h2>Параметры страницы</h2>
			<ul>
				<li><label class=property>Адрес</label><div class="page_url"> /brands/</div><input name="url" class="page_url" type="text" value="{$brand->url|escape}" /></li>
				<li><label class=property>Заголовок</label><input name="meta_title" class="simpla_inp" type="text" value="{$brand->meta_title|escape}" /></li>
				<li><label class=property>Ключевые слова</label><input name="meta_keywords" class="simpla_inp" type="text" value="{$brand->meta_keywords|escape}" /></li>
				<li><label class=property>Описание</label><textarea name="meta_description" class="simpla_inp" />{$brand->meta_description|escape}</textarea></li>
				<li><label class=property>H1</label><input name="h1_head" type="text" value="{$brand->h1_head|escape}" /></li>
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

	<input class="button_green button_save" type="submit" name="" value="Сохранить" />
	</div>
	<!-- Левая колонка свойств товара (The End)-->

	<!-- Правая колонка свойств товара -->
	<div id="column_right">

		<!-- Изображение категории -->
		<div class="block layer images">
			<h2>Изображение бренда</h2>
			<input class='upload_image' name=image type=file>
			<input type=hidden name="delete_image" value="">
			{if $brand->image}
			<ul>
				<li>
					<a href='#' class="delete"><img src='design/images/cross-circle-frame.png'></a>
					<img src="../{$config->brands_images_dir}{$brand->image}" alt="" />
				</li>
			</ul>
			{/if}
		</div>

	</div>
	<!-- Правая колонка свойств товара (The End)-->
	<div class="block layer">
		<h2>Лицензия</h2>
		<ul>
			<li><label class=property>Ссылка на лицензию</label><input type="text" name="license_link" value="{$brand->license_link}"></li>
		</ul>
	</div>
	<!-- Параметры страницы -->
	<div class="block layer">
		<h2>Кастомизация страницы бренда</h2>
		<div style="display: flex">
			<label style="width: 300px;">Цвет фона</label>
			<div class="checkbox" style="padding-left: 0;">
				<span id="color_icon" class="brand-color"  style="width: 300px;border-radius:50px;background-color:#{$brand->color};"></span>
				<input id="color_input" name="color" class="simpla_inp" type="hidden" value="{if $brand->color}{$brand->color}{/if}">
			</div>
		</div>
		<div class="background-image" style="display: flex;margin-top: 40px;">
			<label style="width: 300px;">Картинка для фона</label>
			<input class="upload_image" name="background" type="file">
			<input type="hidden" name="delete_background" value="">
			{if $brand->background}
			<ul>
				<li>
					<a href='#' class="delete"><img src='design/images/cross-circle-frame.png'></a>
					<img src="../{$config->brands_background_images_dir}{$brand->background}" alt=""/>
				</li>
			</ul>
			{/if}
		</div>
		<div class="banners" style="display: flex;margin-top: 40px;">
			<label style="width: 300px;">Баннер</label>
			<input class="upload_image" name="banner" type="file">
			<input type="hidden" name="delete_banner" value="">
			{if $brand->banner}
			<ul>
				<li>
					<a href='#' class="delete"><img src='design/images/cross-circle-frame.png'></a>
					<img src="../{$config->brands_images_dir}{$brand->banner}" alt=""/>
				</li>
			</ul>
			{/if}
		</div>

	</div>
	<!-- Параметры страницы (The End)-->

	<div class="block layer">
		<h2>Дата поставки на склад</h2>
		<div class="supply_dates_row">
			<label for="supply_dates[0]">Основной склад</label>
			<input type="text" value="{$supply_dates[0]}" placeholder="Введите дату поставки" name="supply_dates[0]" class="supply_date_input" autocomplete="off">
		</div>
		{foreach $regions as $region}
		<div class="supply_dates_row">
			<label for="supply_dates[{$region->id}]">{$region->name}</label>
			<input type="text" value="{$supply_dates[{$region->id}]}" placeholder="Введите дату поставки" name="supply_dates[{$region->id}]" class="supply_date_input" autocomplete="off">
		</div>
		{/foreach}
	</div>


	<!-- Описагние бренда -->
	<div class="block layer">
		<h2>Описание</h2>
		<textarea name="description" class="editor_large">{$brand->description|escape}</textarea>
	</div>
	<!-- Описание бренда (The End)-->
	<input class="button_green button_save" type="submit" name="" value="Сохранить" />


</form>
<!-- Основная форма (The End) -->

