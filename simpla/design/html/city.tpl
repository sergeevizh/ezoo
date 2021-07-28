{* Вкладки *}
{capture name=tabs}
	{if in_array('settings', $manager->permissions)}<li><a href="index.php?module=SettingsAdmin">Настройки</a></li>{/if}
	{if in_array('currency', $manager->permissions)}<li><a href="index.php?module=CurrencyAdmin">Валюты</a></li>{/if}
	{if in_array('delivery', $manager->permissions)}<li><a href="index.php?module=DeliveriesAdmin">Доставка</a></li>{/if}
	{if in_array('payment', $manager->permissions)}<li><a href="index.php?module=PaymentMethodsAdmin">Оплата</a></li>{/if}
	{if in_array('managers', $manager->permissions)}<li><a href="index.php?module=ManagersAdmin">Менеджеры</a></li>{/if}
	<li class="active"><a href="index.php?module=CitiesAdmin">Города доставки</a></li>
{/capture}

{if $city}
{$meta_title = $city->name}
{else}
{$meta_title = 'Новый город'}
{/if}
{if $city}
	<h1 style="float: none">Редактировать город</h1>
{else}
	<h1 style="float: none">Добавить город</h1>
{/if}
<!-- Основная форма -->
<br>
<form method=post id=city enctype="multipart/form-data" style="float: none">
<input type=hidden name="session_id" value="{$smarty.session.id}">
	<div style="display: block">
		Название города:
		<input class="name" name="name" type="text" value="{$city->name|escape}" maxlength="160"/>
		<input name="city_id" type="hidden" value="{$city->id}"/>
		<label for="visible" style="margin-left: 50px;">Активен</label>
		<input type="checkbox" class="simpla_small_inp" style="width: 20px;" id="visible" name="visible" value="1" {if $city->visible}checked{/if}>
		<label for="hide_time" style="margin-left: 50px;">Скрыть временные периоды</label>
		<input type="checkbox" class="simpla_small_inp" style="width: 20px;" id="hide_time" name="hide_time" value="1" {if $city->hide_time}checked{/if}>
		<input class="button_red button_save" type="submit" name="delete" value="Удалить город" />
	</div>
	<br>
	<div style="margin-bottom: 30px;">
		<h2>Связать со складом</h2>
		<select name="region_id">
			<option value="0" {if $city->region_id == '0'}selected{/if}>Выберите склад</option>
			<option value="1067" {if $city->region_id == '1067'}selected{/if}>Минск</option>
			{foreach $regions as $region}
			<option value="{$region->id}" {if $city->region_id == $region->id} selected {/if} >{$region->name}</option>
			{/foreach}
		</select>
	</div>
	<br>
	<div style="margin-bottom: 30px;">
		<h2>Название латиницей</h2>
		<input type="text" class="simpla_small_inp" style="width: 200px;" name="latin_name" value="{$city->latin_name}">
	</div>
	<div class="list_area" style="float: none">
		<h2>Список пунктов выдачи</h2>
		<br>
			<ul id="list_area_city">
				{foreach $city_areas as $city_area}
					<li>
						<label class=property>Название: </label>
						<input type="text" class="simpla_small_inp" style="width: 200px;" required name="city_areas[name_area][]" value="{$city_area->name_area}">
						<span class="delete batton" title="Удалить"></span>
					</li>
				{/foreach}
			</ul>
		<br>
			<span id="add_city_area" class="add_batton_list">Добавить пункт</span>
	</div>
	<div style="clear: both"></div>
	<input class="button_green button_save" type="submit" name="save" value="Сохранить" />
</form>
<div id="copy_list_area" style="display: none">
	<li>
		<label class=property>Название: </label>
		<input type="text" class="simpla_small_inp" style="width: 200px;" required name="city_areas[name_area][]" value="">
		<span class="delete batton" title="Удалить"></span>
	</li>
</div>
<script>
	$('#add_city_area').on('click' ,function () {
		$('#copy_list_area li').clone().appendTo('#list_area_city');
	});
	$('#list_area_city').on('click',function () {
		if ($(event.target).hasClass('delete')) deleteDeliverDiscont(event.target);
	});
	function deleteDeliverDiscont(element) {
		$(element).parents('li').remove();
	};
</script>
<!-- Основная форма (The End) -->
