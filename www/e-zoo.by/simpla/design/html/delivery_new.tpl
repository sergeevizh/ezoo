{* Вкладки *}
{capture name=tabs}
	{if in_array('settings', $manager->permissions)}<li><a href="index.php?module=SettingsAdmin">Настройки</a></li>{/if}
	{if in_array('currency', $manager->permissions)}<li><a href="index.php?module=CurrencyAdmin">Валюты</a></li>{/if}
	<li class="active"><a href="index.php?module=DeliveriesAdmin">Доставка</a></li>
	{if in_array('payment', $manager->permissions)}<li><a href="index.php?module=PaymentMethodsAdmin">Оплата</a></li>{/if}
	{if in_array('managers', $manager->permissions)}<li><a href="index.php?module=ManagersAdmin">Менеджеры</a></li>{/if}
{/capture}

{if $delivery->id}
{$meta_title = $delivery->name scope=parent}
{else}
{$meta_title = 'Новый способ доставки' scope=parent}
{/if}

{* Подключаем Tiny MCE *}
{include file='tinymce_init.tpl'}
{literal}
	<style>
		.autocomplete-suggestions{
			background-color: #ffffff;
			overflow: hidden;
			border: 1px solid #e0e0e0;
			overflow-y: auto;
		}
		.autocomplete-suggestions .autocomplete-suggestion{cursor: default;}
		.autocomplete-suggestions .selected { background:#F0F0F0; }
		.autocomplete-suggestions div { padding:2px 5px; white-space:nowrap; }
		.autocomplete-suggestions strong { font-weight:normal; color:#3399FF; }
	</style>
	<script src="design/js/autocomplete/jquery.autocomplete-min.js"></script>

	<script>

$(function(){

    // Удаление связанного товара
    $(".related_products a.delete").live('click', function() {
        $(this).closest("div.row").fadeOut(200, function() { $(this).remove(); });
        return false;
    });


    // Добавление связанного товара
    var new_related_product = $('#new_related_product').clone(true);
    $('#new_related_product').remove().removeAttr('id');

    $("input#related_products").autocomplete({
        serviceUrl:'ajax/search_products.php',
        minChars:0,
        noCache: false,
        onSelect:
            function(suggestion){
                $("input#related_products").val('').focus().blur();
                new_item = new_related_product.clone().appendTo('.related_products');
                new_item.removeAttr('id');
                new_item.find('a.related_product_name').html(suggestion.data.name);
                new_item.find('a.related_product_name').attr('href', 'index.php?module=ProductAdmin&id='+suggestion.data.id);
                new_item.find('input[name*="related_products"]').val(suggestion.data.id);
                if(suggestion.data.image)
                    new_item.find('img.product_icon').attr("src", suggestion.data.image);
                else
                    new_item.find('img.product_icon').remove();
                new_item.show();
            },
        formatResult:
            function(suggestions, currentValue){
                var reEscape = new RegExp('(\\' + ['/', '.', '*', '+', '?', '|', '(', ')', '[', ']', '{', '}', '\\'].join('|\\') + ')', 'g');
                var pattern = '(' + currentValue.replace(reEscape, '\\$1') + ')';
                return (suggestions.data.image?"<img align=absmiddle src='"+suggestions.data.image+"'> ":'') + suggestions.value.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>');
            }

    });


});
</script>
{/literal}

{if $message_success}
<!-- Системное сообщение -->
<div class="message message_success">
	<span class="text">{if $message_success == 'added'}Способ доставки добавлен{elseif $message_success == 'updated'}Способ доставки изменен{/if}</span>
	{if $smarty.get.return}
	<a class="button" href="{$smarty.get.return}">Вернуться</a>
	{/if}
</div>
<!-- Системное сообщение (The End)-->
{/if}

{if $message_error}
<!-- Системное сообщение -->
<div class="message message_error">
	<span class="text">{if $message_error == 'empty_name'}Не указано название доставки{/if}</span>
	<a class="button" href="">Вернуться</a>
</div>
<!-- Системное сообщение (The End)-->
{/if}


<!-- Основная форма -->
<form method=post id=product enctype="multipart/form-data">
<input type=hidden name="session_id" value="{$smarty.session.id}">
	<div id="name">
		<input class="name" name=name type="text" value="{$delivery->name|escape}"/>
		<input name=id type="hidden" value="{$delivery->id}"/>
		<div class="checkbox">
			<input name=enabled value='1' type="checkbox" id="active_checkbox" {if $delivery->enabled}checked{/if}/> <label for="active_checkbox">Активен</label>
		</div>
	</div>

	<!-- Левая колонка свойств товара -->
	<div id="column_left">
		<!-- Параметры страницы -->
		<div class="block layer">
			<h2>Стоимость доставки</h2>
			<ul>
				<li><label class=property>Стоимость</label><input name="price" class="simpla_small_inp" type="text" value="{$delivery->price}" /> {$currency->sign}</li>
				<li><label class=property>Бесплатна от</label><input name="free_from" class="simpla_small_inp" type="text" value="{$delivery->free_from}" /> {$currency->sign}</li>
				<li><label class=property for="separate_payment">Оплачивается отдельно</label><input id="separate_payment" name="separate_payment" type="checkbox" value="1" {if $delivery->separate_payment}checked{/if} /></li>
			</ul>
		</div>
		<!-- Параметры страницы (The End)-->
	</div>
	<!-- Левая колонка свойств товара (The End)-->

	<!-- Левая колонка свойств товара -->
	<div id="column_right">
		<div class="block layer">
		<h2>Возможные способы оплаты</h2>
		<ul>
		{foreach $payment_methods as $payment_method}
			<li>
			<input type=checkbox name="delivery_payments[]" id="payment_{$payment_method->id}" value='{$payment_method->id}' {if in_array($payment_method->id, $delivery_payments)}checked{/if}> <label for="payment_{$payment_method->id}">{$payment_method->name}</label><br>
			</li>
		{/foreach}
		</ul>
		</div>
	</div>
	<!-- Левая колонка свойств товара (The End)-->

	<!--Процент скидок на товары-->
	<div class="sale-block layer" style="clear: both">
	<h2>Скидки</h2>
		{$times = [
		10 => '10:00 - 12:00',
		12 => '12:00 - 15:00',
		15 => '15:00 - 17:00',
		17 => '17:00 - 19:00',
		19 => '19:00 - 21:00',
		21 => '21:00 - 23:00'
		]}
	<ul id="list_deliver_disconts">
		{foreach $deliveries_discount as $delivery_discount}
			<li>
				<label class=property>Скидка от</label>
				<input type="text" class="simpla_small_inp" style="width: 100px;" name="deliveries_discount[discount_from][]" value="{$delivery_discount->discount_from}"> {$currency->sign} -
				<input type="text" class="simpla_small_inp" style="width: 100px;" name="deliveries_discount[discount_percent][]" value="{$delivery_discount->discount_percent}"> %
				<select name="deliveries_discount[discount_period_code][]" class="order-block__field js-time" required>
					<option value="">Время действия скидки</option>
					{foreach $times as $key=> $time}
						<option {if $delivery_discount->discount_period_code == $key} selected {/if} value="{$key}">{$time}</option>
					{/foreach}
				</select>
				<span class="delete" title="Удалить скидку"></span>
			</li>
		{/foreach}
	</ul>
		<span id="add_delivery_discont">Добавить скидку</span>
	</div>
	<!--Процент скидок на товары End-->
	<!-- Описагние товара -->
	<div class="block layer">

		<div class="delivery-disabled">
			<h2>Исключения</h2>
			<div id="product_brand" style="width: 50%;float:left;">
				<label>Бренды</label>
				<select name="brands[]" multiple size="25" style="width: 350px;margin-left: 15px;">
					<option value='0'  brand_name=''>Не указан</option>
					{foreach $brands as $brand}





						<option value='{$brand->id}'

								{if $brand_selected}
									{foreach from=$brand_selected item=item}
										{if $item->value==$brand->id}selected="selected"{/if}
									{/foreach}
								{/if}





								brand_name='{$brand->name|escape}'>{$brand->name|escape}</option>



					{/foreach}
				</select>
			</div>


			<div id="product_categories" style="width:50%;float:left;">
				<label>Категория</label>
					<select name="categories[]" multiple size="25" style="width: 350px;margin-left: 15px;">
						{function name=category_select level=0}
							{foreach $categories as $category}
								<option value='{$category->id}'
										{if $category_selected}
											{foreach from=$category_selected item=item}
												{if $item->value==$category->id}selected="selected"{/if}
											{/foreach}
										{/if}


										{if $category->id == $selected_id}selected{/if} category_name='{$category->name|escape}'>{section name=sp loop=$level}&nbsp;&nbsp;&nbsp;&nbsp;{/section}{$category->name|escape}</option>
								{category_select categories=$category->subcategories selected_id=$selected_id  level=$level+1}
							{/foreach}
						{/function}
						{category_select categories=$categories selected_id=$product_category->id}
					</select>
			</div>
		</div>

		<div style="clear:both;margin-bottom: 25px;"></div>
		<div class="block layer">
			<label>Товары</label>
			<div id=list class="sortable related_products">
				{foreach $related_products as $related_product}
					<div class="row">
						<div class="move cell">
							<div class="move_zone"></div>
						</div>
						<div class="image cell">
							<input type=hidden name=related_products[] value='{$related_product->id}'>
							<a href="{url id=$related_product->id}">
								<img class=product_icon src='{$related_product->images[0]->filename|resize:35:35}'>
							</a>
						</div>
						<div class="name cell">
							<a href="{url id=$related_product->id}">{$related_product->name}</a>
						</div>
						<div class="icons cell">
							<a href='#' class="delete"></a>
						</div>
						<div class="clear"></div>
					</div>
				{/foreach}
				<div id="new_related_product" class="row" style='display:none;'>
					<div class="move cell">
						<div class="move_zone"></div>
					</div>
					<div class="image cell">
						<input type=hidden name=related_products[] value=''>
						<img class=product_icon src=''>
					</div>
					<div class="name cell">
						<a class="related_product_name" href=""></a>
					</div>
					<div class="icons cell">
						<a href='#' class="delete"></a>
					</div>
					<div class="clear"></div>
				</div>
			</div>
			<input type=text name=related id='related_products' class="input_autocomplete" placeholder='Выберите товар чтобы добавить его'>
		</div>
		<div style="clear:both;margin-bottom: 25px;"></div>





		<h2>Описание</h2>
		<textarea name="description" class="editor_small">{$delivery->description|escape}</textarea>
	</div>
	<!-- Описание товара (The End)-->
	<input class="button_green button_save" type="submit" name="" value="Сохранить" />

</form>
<div id="copy_li_discont" style="display: none">
	<li>
		<label class=property>Скидка от</label>
		<input type="text" class="simpla_small_inp" style="width: 100px;" name="deliveries_discount[discount_from][]" value="0"> {$currency->sign} -
		<input type="text" class="simpla_small_inp" style="width: 100px;" name="deliveries_discount[discount_percent][]" value="0"> %
		<select name="deliveries_discount[discount_period_code][]" class="order-block__field js-time">
			<option value="">Время действия скидки</option>
			{foreach $times as $key=> $time}
				<option value="{$key}">{$time}</option>
			{/foreach}
		</select>
		<span class="delete" title="Удалить скидку"></span>
	</li>
</div>
<script>
    $('#add_delivery_discont').on('click' ,function () {
        $('#copy_li_discont li').clone().appendTo('#list_deliver_disconts');
    });
    $('#list_deliver_disconts').on('click',function () {
        if ($(event.target).hasClass('delete')) deleteDeliverDiscont(event.target);
    });
    function deleteDeliverDiscont(element) {
        $(element).parents('li').remove();
    };
</script>
<!-- Основная форма (The End) -->

