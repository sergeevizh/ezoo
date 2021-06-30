{capture name=tabs}
	{if in_array('settings', $manager->permissions)}<li><a href="index.php?module=SettingsAdmin">Настройки</a></li>{/if}
	{if in_array('currency', $manager->permissions)}<li><a href="index.php?module=CurrencyAdmin">Валюты</a></li>{/if}
	{if in_array('delivery', $manager->permissions)}<li><a href="index.php?module=DeliveriesAdmin">Доставка</a></li>{/if}
	{if in_array('payment', $manager->permissions)}<li><a href="index.php?module=PaymentMethodsAdmin">Оплата</a></li>{/if}
	{if in_array('managers', $manager->permissions)}<li><a href="index.php?module=ManagersAdmin">Менеджеры</a></li>{/if}
	{if in_array('cities', $manager->permissions)}<li><a href="index.php?module=CitiesAdmin">Города доставки</a></li>{/if}
	<li class="active"><a href="index.php?module=BonusAdmin">Бонус</a></li>
{/capture}

{$meta_title = "Бонус" scope=parent}
<script>
	var limit_rows = {$limits|@count};
</script>
<style>
    .form-row {
        margin-bottom: 15px;
        padding-right: 15px;
    }
    .form-row label {
        display: block;
        color: #777;
        margin-bottom: 5px;
    }
    .form-row input[type="text"] {
        width: 100%;
        padding: 5px;
        box-sizing: border-box;
    }

    /* Стили для вывода превью */
    .img-item {
        display: inline-block;
        margin: 0 20px 20px 0;
        position: relative;
        user-select: none;
    }
    .img-item img {
        border: 1px solid #767676;
    }
    .img-item a {
        display: inline-block;
        background: url(/remove.png) 0 0 no-repeat;
        position: absolute;
        top: -5px;
        right: -9px;
        width: 20px;
        height: 20px;
        cursor: pointer;
    }
</style>
{literal}
	<script src="design/js/autocomplete/jquery.autocomplete-min.js"></script>
	<script src="design/js/jquery/datepicker/jquery.ui.datepicker-ru.js"></script>
	
{/literal}

{* Заголовок *}
<div id="header">
	<h1>{if $bonus_count}{$bonus_count}{else}Нет{/if} Бонус{$bonus_count|plural:'':'ов':'а'}</h1>
	<a class="add" href="{url module=BonusOneAdmin}">Добавить бонус</a>
</div>


{if $message_success}
<!-- Системное сообщение -->
<div class="message message_success">
	<span class="text">{if $message_success == 'saved'}Настройки сохранены{/if}</span>
	{if $smarty.get.return}
	<a class="button" href="{$smarty.get.return}">Вернуться</a>
	{/if}
</div>
<!-- Системное сообщение (The End)-->
{/if}

{if $message_error}
<!-- Системное сообщение -->
<div class="message message_error">
	<span class="text">{if $message_error == 'watermark_is_not_writable'}Установите права на запись для файла {$config->watermark_file}{/if}</span>
	<a class="button" href="">Вернуться</a>
</div>
<!-- Системное сообщение (The End)-->
{/if}
<!-- Спиоск всех бонусов -->
<div class="block" id="main_list">
	{foreach $bonusall as $bonus}
		<a href="{url module=BonusOneAdmin id={$bonus->id} return=$smarty.server.REQUEST_URI}" title="{$bonus->name}" class="
		{if $bonus->status == 1 }green_button {/if} {if $bonus->status == 0 }red_button {/if} mybonus">{$bonus->name|escape}</a>
	{/foreach}
</div>

<!-- Меню -->
<div id="right_menu">
	<div class="all">Всего бонусов:{$bonus_count|escape}</div>
</div>
<!-- Меню  (The End) -->