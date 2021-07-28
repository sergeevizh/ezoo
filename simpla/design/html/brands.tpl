{* Вкладки *}
{capture name=tabs}
	{if in_array('products', $manager->permissions)}<li><a href="index.php?module=ProductsAdmin">Товары</a></li>{/if}
	{if in_array('categories', $manager->permissions)}<li><a href="index.php?module=CategoriesAdmin">Категории</a></li>{/if}
	<li class="active"><a href="index.php?module=BrandsAdmin">Бренды</a></li>
	{if in_array('features', $manager->permissions)}<li><a href="index.php?module=FeaturesAdmin">Свойства</a></li>{/if}
	{if in_array('colors', $manager->permissions)}<li><a href="index.php?module=ColorsAdmin">Цвета</a></li>{/if}
	{if in_array('regions', $manager->permissions)}<li><a href="index.php?module=RegionsAdmin">Магазины</a></li>{/if}
{/capture}

{* Title *}
{$meta_title='Бренды' scope=parent}

{* Заголовок *}
<div id="header">
	<h1>Бренды</h1>
	<a class="add" href="{url module=BrandAdmin return=$smarty.server.REQUEST_URI}">Добавить бренд</a>
</div>

{if $brands}
<div id="main_list" class="brands">

	<form id="list_form" method="post">
	<input type="hidden" name="session_id" value="{$smarty.session.id}">

		<div id="list" class="brands">
			{foreach $brands as $brand}
			<div class="row{if $brand->visible_is_main} visible_is_main{/if}{if $brand->label_new} label_new{/if}">
		 		<div class="checkbox cell">
					<input type="checkbox" name="check[]" value="{$brand->id}" />
				</div>
				<div class="cell">
					<a href="{url module=BrandAdmin id=$brand->id return=$smarty.server.REQUEST_URI}">{$brand->name|escape}</a>
				</div>
				<div class="icons cell">
					<a class="label_new" title="new" href="#"></a>
					<a class="visible_is_main" title="Показать на главной" href="#"></a>
					<a class="preview" title="Предпросмотр в новом окне" href="../brands/{$brand->url}" target="_blank"></a>
					<a class="delete"  title="Удалить" href="#"></a>
				</div>
				<div class="clear"></div>
			</div>
			{/foreach}
		</div>

		<div id="action">
			<label id="check_all" class="dash_link">Выбрать все</label>

			<span id="select">
			<select name="action">
				<option value="delete">Удалить</option>
			</select>
			</span>
			<input id="apply_action" class="button_green" type="submit" value="Применить">
		</div>

	</form>
</div>
{else}
Нет брендов
{/if}

{literal}
	<style>

		.icons a.label_new {
			background-image: url(design/images/new.png);

		}
		.icons a.visible_is_main {
			background-image: url(design/images/star.png);

		}

		.label_new .icons a.label_new, .visible_is_main .icons a.visible_is_main {
			-webkit-filter: none;
			-moz-filter: none;
			-ms-filter: none;
			-o-filter: none;
			filter: none;
		}

		.icons a.label_new, .icons a.visible_is_main {
			-webkit-filter: grayscale(100%);
			-moz-filter: grayscale(100%);
			-ms-filter: grayscale(100%);
			-o-filter: grayscale(100%);
			filter: grayscale(100%);
			filter: gray; /* IE 6-9 */
		}

	</style>
<script>
$(function() {

	// Раскраска строк
	function colorize()
	{
		$("#list div.row:even").addClass('even');
		$("#list div.row:odd").removeClass('even');
	}
	// Раскрасить строки сразу
	colorize();

	// Показать label new
	$("a.label_new").click(function() {
		var icon        = $(this);
		var line        = icon.closest("div.row");
		var id          = line.find('input[type="checkbox"][name*="check"]').val();
		var state       = line.hasClass('label_new')?0:1;
		icon.addClass('loading_icon');
		$.ajax({
			type: 'POST',
			url: 'ajax/update_object.php',
			data: {'object': 'brands', 'id': id, 'values': {'label_new': state}, 'session_id': '{/literal}{$smarty.session.id}{literal}'},
			success: function(data){
				icon.removeClass('loading_icon');
				if(state)
					line.addClass('label_new');
				else
					line.removeClass('label_new');

			},
			dataType: 'json'
		});
		return false;
	});
	// показать на главной
	$("a.visible_is_main").click(function() {
		var icon        = $(this);
		var line        = icon.closest("div.row");
		var id          = line.find('input[type="checkbox"][name*="check"]').val();
		var state       = line.hasClass('visible_is_main')?0:1;
		icon.addClass('loading_icon');
		$.ajax({
			type: 'POST',
			url: 'ajax/update_object.php',
			data: {'object': 'brands', 'id': id, 'values': {'visible_is_main': state}, 'session_id': '{/literal}{$smarty.session.id}{literal}'},
			success: function(data){
				icon.removeClass('loading_icon');
				if(state)
					line.addClass('visible_is_main');
				else
					line.removeClass('visible_is_main');

			},
			dataType: 'json'
		});
		return false;
	});


	// Выделить все
	$("#check_all").click(function() {
		$('#list input[type="checkbox"][name*="check"]').attr('checked', $('#list input[type="checkbox"][name*="check"]:not(:checked)').length>0);
	});

	// Удалить
	$("a.delete").click(function() {
		$('#list input[type="checkbox"][name*="check"]').attr('checked', false);
		$(this).closest("div.row").find('input[type="checkbox"][name*="check"]').attr('checked', true);
		$(this).closest("form").find('select[name="action"] option[value=delete]').attr('selected', true);
		$(this).closest("form").submit();
	});

	// Подтверждение удаления
	$("form").submit(function() {
		if($('#list input[type="checkbox"][name*="check"]:checked').length>0)
			if($('select[name="action"]').val()=='delete' && !confirm('Подтвердите удаление'))
				return false;
	});

});
</script>
{/literal}
