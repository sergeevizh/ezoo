{* Вкладки *}
{capture name=tabs}
	<li><a href="index.php?module=UsersAdmin">Покупатели</a></li>
	{if in_array('groups', $manager->permissions)}<li><a href="index.php?module=GroupsAdmin">Группы</a></li>{/if}
	{if in_array('coupons', $manager->permissions)}<li><a href="index.php?module=CouponsAdmin">Купоны</a></li>{/if}
	<li class="active"><a href="index.php?module=PhonesAdmin">Номера телефонов</a></li>
{/capture}

{* Title *}
{$meta_title='Номера телефонов' scope=parent}
<script src="{$config->root_url}/simpla/design/js/piecon/piecon.js"></script>
<script>
	var group_id='{$group_id|escape}';
	var keyword='{$keyword|escape}';
	var sort='{$sort|escape}';

	{literal}

	$(function() {

		// On document load
		$('input#start').click(function() {

			Piecon.setOptions({fallback: 'force'});
			Piecon.setProgress(0);
			$("#progressbar").progressbar({ value: 0 });
			do_export();

		});

		function do_export(page)
		{

			page = typeof(page) != 'undefined' ? page : 1;
			var form = $('.d-filter'),
					replace = 'module=PhonesAdmin&',
					request = form.serialize().replace(replace, '');


			console.log(form.serialize());

			$.ajax({
				url: "ajax/export_phones.php?"+request,
				data: {page:page},
				dataType: 'json',
				beforeSend: function(data){
				},
				success: function(data){

					console.log(data);

					if(data.error)
					{
						$("#progressbar").hide('fast');
						alert(data.error);
					}
					else if(data && !data.end)
					{
						Piecon.setProgress(Math.round(100*data.page/data.totalpages));
						$("#progressbar").progressbar({ value: 100*data.page/data.totalpages });
						do_export(data.page*1+1);
					}
					else
					{
						Piecon.setProgress(100);
						$("#progressbar").hide('fast');
						window.location.href = 'files/export_phones/phones.csv';

					}
				},
				error:function(xhr, status, errorThrown) {
					alert(errorThrown+'\n'+xhr.responseText);
				}
			});

		}
	});
	{/literal}
</script>
<style>
	.ui-progressbar-value { background-image: url(design/images/progress.gif); background-position:left; border-color: #009ae2;}
	#progressbar{ clear: both; height:29px; }
	#result{ clear: both; width:100%;}
	#download{ display:none;  clear: both; }
</style>
<script src="design/js/jquery/datepicker/jquery.ui.datepicker-ru.js"></script>
<script>



	$(document).ready(function () {

		$('input[name="date_start"]').datepicker({
			regional:'ru'
		});

		$('input[name="date_end"]').datepicker({
			regional:'ru'
		});

	});
</script>
{* Поиск *}
{if $users || $keyword}
	<form method="get">
		<div id="search">
			<input type="hidden" name="module" value='UsersAdmin'>
			<input class="search" type="text" name="keyword" value="{$keyword|escape}" />
			<input class="search_button" type="submit" value=""/>
		</div>
	</form>
{/if}

{* Заголовок *}
<div id="header">
	{*	{if $keyword && $users_count>0}
		<h1>{$users_count|plural:'Нашелся':'Нашлось':'Нашлись'} {$users_count} {$users_count|plural:'покупатель':'покупателей':'покупателя'}</h1>
		{elseif $users_count>0}
		<h1>{$users_count} {$users_count|plural:'покупатель':'покупателей':'покупателя'}</h1>
		{else}
		<h1>Нет покупателей</h1>
		{/if}*}
	<h1>Номера телефонов</h1>


	<div id='progressbar'></div>
	<input class="button_green" id="start" type="button" name="" value="Экспортировать" />


</div>

{*{if $users}*}
<!-- Основная часть -->
<div id="main_list">

	<!-- Листалка страниц -->
	{include file='pagination.tpl'}
	<!-- Листалка страниц (The End) -->

	<div id="sort_links" style='display:block;'>
		<!-- Ссылки для сортировки -->
		Упорядочить по
		{if $sort!='name'}<a href="{url sort=name}">имени</a>{else}имени{/if} или
		{if $sort!='date'}<a href="{url sort=date}">дате</a>{else}дате{/if}
		<!-- Ссылки для сортировки (The End) -->
	</div>

	<form id="form_list" method="post" action="{url module=PhonesAdmin}">
		<input type="hidden" name="session_id" value="{$smarty.session.id}">

		<div id="list" class="phones">
			{foreach $phones as $phone}
				<div class="row even">
					<div class="checkbox cell">
						<input type="checkbox" name="check[]" value="1587">
					</div>
					<div class="user_name cell">
						{if $phone->user}
							<a href="index.php?module=UserAdmin&id={$phone->user}">{$phone->name}</a>
						{else}
							{$phone->name}
						{/if}
					</div>
					<div class="user_phone cell">
						<a href="index.php?module=OrderAdmin&id={$phone->id}">{$phone->phone}</a>
					</div>
					<div class="user_register cell">
						{if $phone->user}
							Зарегистрирован
						{else}
							Незарегистрирован
						{/if}
					</div>
					<div class="user_date cell">
						{$phone->date|date}
					</div>
					<div class="icons cell">
						<a class="enable" title="Активен" href="#"></a>
						<a class="delete" title="Удалить" href="#"></a>
					</div>
					<div class="clear"></div>
				</div>
			{/foreach}



			{*			{foreach $phones as $phone}
						<div class="{if !$phone->enabled}invisible{/if} row">
							 <div class="checkbox cell">
								<input type="checkbox" name="check[]" value="{$phone->id}"/>
							</div>
							<div class="user_name cell">
								<a href="index.php?module=UserAdmin&id={$user->id}">{$user->name|escape}</a>
							</div>
							<div class="user_email cell">
								<a href="mailto:{$user->name|escape}<{$user->email|escape}>">{$user->email|escape}</a>
							</div>
							<div class="user_group cell">
								{$groups[$user->group_id]->name}
							</div>
							<div class="icons cell">
								<a class="enable" title="Активен" href="#"></a>
								<a class="delete" title="Удалить" href="#"></a>
							</div>
							<div class="clear"></div>
						</div>
						{/foreach}*}
		</div>

		<div id="action">
			<label id="check_all" class="dash_link">Выбрать все</label>

			<span id=select>
		<select name="action">
			<option value="disable">Заблокировать</option>
			<option value="enable">Разблокировать</option>
			<option value="delete">Удалить</option>
		</select>
		</span>

			<input id="apply_action" class="button_green" type="submit" value="Применить">
		</div>

	</form>

	<!-- Листалка страниц -->
	{include file='pagination.tpl'}
	<!-- Листалка страниц (The End) -->

</div>
{*{/if}*}

<!-- Меню -->
<div id="right_menu">
	<form class="d-filter" method="GET">
		<div class="filter-heading">Фильтр</div>
		<input type="hidden" name="module" value="PhonesAdmin">
		<ul>
			<li>
				<input name="contr_name" type="text"   value="{if $contr_name}{$contr_name}{/if}" placeholder="Введите имя контрагента"/>
			</li>
			<li>
				<input name="contr_phone" class="contr_phone" type="text"   value="{if $contr_phone}{$contr_phone}{/if}" placeholder="Введите номер телефона"/>
			</li>
			<li>

				<input type="radio" name="registration" value="registered" {if $registration_filter == 'registered'}checked{/if}><label for="registration">Зарегистрированные</label></li>
			<li>

				<input type="radio" name="registration" value="unregistered" {if $registration_filter == 'unregistered'}checked{/if}><label for="registration">Не зарегистрированные</label></li>
			<li>

				<input type="radio" name="registration" value="all" {if $registration_filter == ''}checked{/if}><label for="registration">Все</label></li>
			{*			<li>
							<input class="d-checkbox" id="register" type="checkbox" name="register" value="1"><label for="register">Зарегистрирован</label>
						</li>
						<li>
							<input class="d-checkbox" id="unregister" type="checkbox" name="unregister" value="0"><label for="unregister">Не зарегестрирован</label>
						</li>*}
			<li>
				<label>Дата от:</label>
				<input name="date_start" class="d-data" type="text"   value="{if $date_start}{$date_start}{/if}" autocomplete="off"/>
			</li>
			<li>
				<label>Дата до:</label>
				<input name="date_end" class="d-data" type="text"   value="{if $date_end}{$date_end}{/if}"  autocomplete="off"/>
			</li>
			<li>
				<input type="submit" value="Подобрать">
			</li>
		</ul>
	</form>

</div>
<!-- Меню  (The End) -->

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

		// Выделить все
		$("#check_all").click(function() {
			$('#list input[type="checkbox"][name*="check"]').attr('checked', 1-$('#list input[type="checkbox"][name*="check"]').attr('checked'));
		});

		// Удалить
		$("a.delete").click(function() {
			$('#list input[type="checkbox"][name*="check"]').attr('checked', false);
			$(this).closest(".row").find('input[type="checkbox"][name*="check"]').attr('checked', true);
			$(this).closest("form").find('select[name="action"] option[value=delete]').attr('selected', true);
			$(this).closest("form").submit();
		});

		/*	// Скрыт/Видим
			$("a.enable").click(function() {
				var icon        = $(this);
				var line        = icon.closest(".row");
				var id          = line.find('input[type="checkbox"][name*="check"]').val();
				var state       = line.hasClass('invisible')?1:0;
				icon.addClass('loading_icon');
				$.ajax({
					type: 'POST',
					url: 'ajax/update_object.php',
					data: {'object': 'user', 'id': id, 'values': {'enabled': state}, 'session_id': '{/literal}{$smarty.session.id}{literal}'},
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
	});*/

		// Подтверждение удаления
		$("form").submit(function() {
			if($('#list input[type="checkbox"][name*="check"]:checked').length>0)
				if($('select[name="action"]').val()=='delete' && !confirm('Подтвердите удаление'))
					return false;
		});
	});

</script>
{/literal}
