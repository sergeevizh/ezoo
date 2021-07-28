{* Вкладки *}
{capture name=tabs}
	{if in_array('products', $manager->permissions)}<li><a href="{url module=ProductsAdmin keyword=null category_id=null brand_id=null filter=null page=null}">Товары</a></li>{/if}
	{if in_array('categories', $manager->permissions)}<li><a href="index.php?module=CategoriesAdmin">Категории</a></li>{/if}
	{if in_array('brands', $manager->permissions)}<li><a href="index.php?module=BrandsAdmin">Бренды</a></li>{/if}
	{if in_array('features', $manager->permissions)}<li><a href="index.php?module=FeaturesAdmin">Свойства</a></li>{/if}
	{if in_array('colors', $manager->permissions)}<li><a href="index.php?module=ColorsAdmin">Цвета</a></li>{/if}
	<li class="active"><a href="index.php?module=RegionsAdmin">Магазины</a></li>
{/capture}

{* Title *}
{$meta_title ="Магазины" scope=parent}

{* Заголовок *}
<div id="header">
	<h1>Страны</h1>
	<a class="add" href="{url module=RegionAdmin}">Добавить магазин</a>
</div>

{if $regions}
<div id="main_list">
 
	<form id="list_form" method="post">
		<input type="hidden" name="session_id" value="{$smarty.session.id}">
		<div id="list">		
			{foreach $regions as $region}
			<div class="{if !$region->enabled}invisible{/if} row">
				<input type="hidden" name="positions[{$region->id}]" value="{$region->position}">
		 
		 		<div class="checkbox cell">
					<input type="checkbox" name="check[]" value="{$region->id}" />				
				</div>
				<div class="name cell">
					<a href="{url module=RegionAdmin id=$region->id return=$smarty.server.REQUEST_URI}">{$region->name|escape}</a>
				</div>
				<div class="icons cell">
					<a class="enable" title="Активна/Не активна" href="#"></a>
					<a class="delete" title="Удалить" href="#"></a>
				</div>
				<div class="clear"></div>
			</div>
			{/foreach}
		</div>
	
		<div id="action">
		<label id="check_all" class="dash_link">Выбрать все</label>
	
		<span id="select">
		<select name="action">
			<option value="enable">Сделать видимыми</option>
			<option value="disable">Сделать невидимыми</option>
			<option value="delete">Удалить</option>
		</select>
		</span>
	
		<input id="apply_action" class="button_green" type="submit" value="Сохранить"/>
	
		</div>
	</form>	
</div>
{else}
	Нет магазиновв
{/if}

{* On document load *}
{literal}
<script>
$(function() {
 

 
	// Раскраска строк
	function colorize()
	{
		$(".row:even").addClass('even');
		$(".row:odd").removeClass('even');
	}
	// Раскрасить строки сразу
	colorize();
 

	// Выделить все
	$("#check_all").click(function() {
		$('#list input[type="checkbox"][name*="check"]').attr('checked', $('#list input[type="checkbox"][name*="check"]:not(:checked)').length>0);
	});	

	// Удалить 
	$("a.delete").click(function() {
		$('#list_form input[type="checkbox"][name*="check"]').attr('checked', false);
		$(this).closest(".row").find('input[type="checkbox"][name*="check"]').attr('checked', true);
		$(this).closest("form").find('select[name="action"] option[value=delete]').attr('selected', true);
		$(this).closest("form").submit();
	});
	

	// Показать
	$("a.enable").click(function() {
		var icon        = $(this);
		var line        = icon.closest(".row");
		var id          = line.find('input[type="checkbox"][name*="check"]').val();
		var state       = line.hasClass('invisible')?1:0;
		icon.addClass('loading_icon');
		$.ajax({
			type: 'POST',
			url: 'ajax/update_object.php',
			data: {'object': 'region', 'id': id, 'values': {'enabled': state}, 'session_id': '{/literal}{$smarty.session.id}{literal}'},
			success: function(data){
				icon.removeClass('loading_icon');
				if(state)
					line.removeClass('invisible');
				else
					line.addClass('invisible');				
			},
			dataType: 'json'
		});	
		return false;	
	});


	$("form").submit(function() {
		if($('select[name="action"]').val()=='delete' && !confirm('Подтвердите удаление'))
			return false;	
	});
});
</script>
{/literal}