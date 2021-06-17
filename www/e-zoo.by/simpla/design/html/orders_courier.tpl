{* Вкладки *}
{capture name=tabs}
	{if in_array('orders', $manager->permissions)}
	<li {if $status===0}class="active"{/if}><a href="{url module=OrdersAdmin status=0 keyword=null id=null page=null label=null}">Новые</a></li>
	<li {if $status==1}class="active"{/if}><a href="{url module=OrdersAdmin status=1 keyword=null id=null page=null label=null}">Приняты</a></li>
	<li {if $status==2}class="active"{/if}><a href="{url module=OrdersAdmin status=2 keyword=null id=null page=null label=null}">Выполнены</a></li>
	<li {if $status==3}class="active"{/if}><a href="{url module=OrdersAdmin status=3 keyword=null id=null page=null label=null}">Удалены</a></li>
	{if $keyword}
	<li class="active"><a href="{url module=OrdersAdmin keyword=$keyword id=null label=null}">Поиск</a></li>
	{/if}
	{/if}
	<li><a href="{url module=OrdersLabelsAdmin keyword=null id=null page=null label=null}">Метки</a></li>
	{if in_array('delivery_area', $manager->permissions)}
	<li class="active"><a href="{url module=OrdersCouriersAdmin keyword=null id=null page=null label=null}">Курьеры</a></li>
	{/if}
{/capture}

{if $label->id}
{$meta_title = $label->name scope=parent}
{else}
{$meta_title = 'Новый Курьер' scope=parent}
{/if}

{* On document load *}

{if $message_success}
<!-- Системное сообщение -->
<div class="message message_success">
	<span class="text">{if $message_success == 'added'}Курьер добавлен{elseif $message_success == 'updated'}Курьер обновлен{/if}</span>
	{if $smarty.get.return}
	<a class="button" href="{$smarty.get.return}">Вернуться</a>
	{/if}
</div>
<!-- Системное сообщение (The End)-->
{/if}

{if $message_error}
    <div class="message_box message_error">
        <span>{if $message_error == 'empty_name'}Имя не может быть пустым{/if}</span>
    </div>
{/if}

<!-- Основная форма -->
<form method=post id=product enctype="multipart/form-data">
	<input type=hidden name="session_id" value="{$smarty.session.id}">
	<div id="name">
		<label>Имя курьера:</label><input class="name" name="name" type="text" value="{$courier->name|escape}"/>
		<input name=id type="hidden" value="{$courier->id|escape}"/>
	</div>
	<div>
		<label>Номер:</label>
		<input class="name" name="phone_courier" type="text" value="{$courier->phone_courier|escape}"/>
	</div>
	<br>
	<div>
		<input type="checkbox" name="start" value='1' {if $courier->start}checked{/if}> <label>Использовать по умолчанию</label>
	</div>
	<input class="button_green button_save" type="submit" name="" value="Сохранить" />




</form>
<!-- Основная форма (The End) -->

