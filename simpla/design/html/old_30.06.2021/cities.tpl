{* Вкладки *}
{capture name=tabs}
	{if in_array('settings', $manager->permissions)}<li><a href="index.php?module=SettingsAdmin">Настройки</a></li>{/if}
	{if in_array('currency', $manager->permissions)}<li><a href="index.php?module=CurrencyAdmin">Валюты</a></li>{/if}
	{if in_array('delivery', $manager->permissions)}<li><a href="index.php?module=DeliveriesAdmin">Доставка</a></li>{/if}
	{if in_array('payment', $manager->permissions)}<li><a href="index.php?module=PaymentMethodsAdmin">Оплата</a></li>{/if}
	{if in_array('managers', $manager->permissions)}<li><a href="index.php?module=ManagersAdmin">Менеджеры</a></li>{/if}
	<li class="active"><a href="index.php?module=CitiesAdmin">Города доставки</a></li>
{/capture}

{* Title *}
{$meta_title='Города доставки' scope=parent}


{* Заголовок *}
<div id="header">
	<h1>Города доставки</h1>
	<a class="add" href="index.php?module=CityAdmin">Добавить город</a>
</div>

{if $cities}
<!-- Основная часть -->
<div id="main_list">
	<form id="form_list" method="post">
	<input type="hidden" name="session_id" value="{$smarty.session.id}">

		<div id="list">
			{foreach $cities as $m}
			<div class="{if !$m->visible}invisible{/if} row">
				<input type="hidden" value="{$m->id}">
				<div class="user_name cell">
					<a href="index.php?module=CityAdmin&id={$m->id}">{$m->name|escape}</a>
				</div>
				<div class="icons cell">
					<a class="enable"    title="Активен"                 href="#"></a>
                    <a href="index.php?module=CityAdmin&id={$m->id}">Редактировать</a>
				</div>
				<div class="clear"></div>
			</div>
			{/foreach}
		</div>
	</form>
</div>
{/if}
{literal}
<script>
	// Показать город
	$("a.enable").click(function() {
		var icon        = $(this);
		var line        = icon.closest("div.row");
		var id          = line.find('input[type="hidden"]').val();
		var state       = line.hasClass('invisible')?1:0;
		icon.addClass('loading_icon');
		$.ajax({
			type: 'POST',
			url: 'ajax/update_object.php',
			data: {'object': 'city', 'id': id, 'values': {'visible': state}, 'session_id': '{/literal}{$smarty.session.id}{literal}'},
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
</script>
{/literal}
