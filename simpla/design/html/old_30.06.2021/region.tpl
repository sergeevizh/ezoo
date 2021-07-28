{* Вкладки *}
{capture name=tabs}
	{if in_array('products', $manager->permissions)}<li><a href="{url module=ProductsAdmin keyword=null category_id=null brand_id=null filter=null page=null}">Товары</a></li>{/if}
	{if in_array('categories', $manager->permissions)}<li><a href="index.php?module=CategoriesAdmin">Категории</a></li>{/if}
	{if in_array('brands', $manager->permissions)}<li><a href="index.php?module=BrandsAdmin">Бренды</a></li>{/if}
	{if in_array('features', $manager->permissions)}<li><a href="index.php?module=FeaturesAdmin">Свойства</a></li>{/if}
	{if in_array('colors', $manager->permissions)}<li><a href="index.php?module=ColorsAdmin">Цвета</a></li>{/if}
	<li class="active"><a href="index.php?module=RegionsAdmin">Магазины</a></li>
{/capture}

{if $region->id}
{$meta_title = $region->name scope=parent}
{else}
{$meta_title = 'Новый магазин' scope=parent}
{/if}

{* Подключаем Tiny MCE *}
{include file='tinymce_init.tpl'}
 

{if $message_success}
<!-- Системное сообщение -->
<div class="message message_success">
	<span>{if $message_success == 'added'}Магазин добавлен{elseif $message_success == 'updated'}Магазин обновлен{/if}</span>
	{if $smarty.get.return}
	<a class="button" href="{$smarty.get.return}">Назад</a>
	{/if}
</div>
<!-- Системное сообщение (The End)-->
{/if}

{if $message_error}
<!-- Системное сообщение -->
<div class="message message_error">
	<span>{if $message_error == 'empty_name'}Введите полное название{/if}</span>
	<span>{if $message_error == 'empty_short_name'}Введите точное название города{/if}</span>
	<span>{if $message_error == 'empty_code_is'}Введите код склада из 1С{/if}</span>
	<a class="button" href="">Назад</a>
</div>
<!-- Системное сообщение (The End)-->
{/if}



<!-- Основная форма -->
<form method=post id=product enctype="multipart/form-data">
	<input type=hidden name="session_id" value="{$smarty.session.id}">
	<div id="name">
		Полное название<input class="name" required name=name type="text" value="{$region->name|escape}"/> 
		<input name='id' type="hidden" value="{$region->id}"/> 
		<div class="checkbox">
			<input name=enabled value='1' type="checkbox" id="active_checkbox" {if $region->enabled}checked{/if}/> <label for="active_checkbox">Активна</label>
		</div>
	</div> 
    <div id="name">
        Точное название города<input class="name" required name=short_name type="text" value="{$region->short_name|escape}"/>
    </div> 
    <div id="name">
        Код склада из 1C<input class="name" name=code_is required type="text" value="{$region->code_is|escape}"/>
    </div> 
 
	<!-- Описание товара (The End)-->
	<input class="button_green button_save" type="submit" name="" value="Сохранить" />
	
</form>
<!-- Основная форма (The End) -->