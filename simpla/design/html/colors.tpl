{* Вкладки *}
{capture name=tabs}
	{if in_array('products', $manager->permissions)}<li><a href="index.php?module=ProductsAdmin">Товары</a></li>{/if}
	{if in_array('categories', $manager->permissions)}<li><a href="index.php?module=CategoriesAdmin">Категории</a></li>{/if}
	{if in_array('brands', $manager->permissions)}<li><a href="index.php?module=BrandsAdmin">Бренды</a></li>{/if}
	{if in_array('features', $manager->permissions)}<li><a href="index.php?module=FeaturesAdmin">Свойства</a></li>{/if}
	<li class="active"><a href="index.php?module=ColorsAdmin">Цвета</a></li>
	{if in_array('sizes', $manager->permissions)}<li><a href="index.php?module=SizesAdmin">Размеры</a></li>{/if}
{/capture}

{* Title *}
{$meta_title='Цвета' scope=parent}

{* Заголовок *}
<div id="header">
	<h1>Цвета</h1>
	<a class="add" href="{url module=ColorAdmin return=$smarty.server.REQUEST_URI}">Добавить цвет</a>
</div>

{if $colors}
<div id="main_list">

	<form id="list_form" method="post">
	<input type="hidden" name="session_id" value="{$smarty.session.id}">

		<div id="list" class="sortable">
			{foreach $colors as $color}
			<div class="row">
				<input type="hidden" name="positions[{$color->id}]" value="{$color->position}">
				<div class="move cell"><div class="move_zone"></div></div>
		 		<div class="checkbox cell">
					<input type="checkbox" name="check[]" value="{$color->id}" />
				</div>
				<div class="image cell">
					<span class="color_icon" style="background-color:#{$color->color};{if $color->image}background-image:url('../{$config->colors_images_dir}{$color->image}');{/if}"></span>
				</div>

				<div class="name product_name cell">
					<a href="{url module=ColorAdmin id=$color->id return=$smarty.server.REQUEST_URI}">{$color->name|escape}</a>
				</div>
				<div class="icons cell">

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
	Нет цветов
{/if}

<style>
	.color_icon {
		width: 30px;
		height: 30px;
		margin: 1px;
		border: 1px solid #e6e6e6;
		display: inline-block;
		-webkit-border-radius: 4px;
		-moz-border-radius: 4px;
		border-radius: 4px;
	}
</style>



{literal}
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

	// Сортировка списка
	$(".sortable").sortable({
		items:".row",
		handle: ".move_zone",
		tolerance:"pointer",
		scrollSensitivity:40,
		opacity:0.7,
		axis: "y",
		update:function()
		{
			$("#list_form input[name*='check']").attr('checked', false);
			$("#list_form").ajaxSubmit();
		}
	});

	// Выделить все
	$("#check_all").click(function() {
		$('#list input[type="checkbox"][name*="check"]').attr('checked', 1-$('#list input[type="checkbox"][name*="check"]').attr('checked'));
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
