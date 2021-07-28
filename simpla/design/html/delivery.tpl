{* Вкладки *}
{capture name=tabs}
	{if in_array('settings', $manager->permissions)}<li><a href="index.php?module=SettingsAdmin">Настройки</a></li>{/if}
	{if in_array('currency', $manager->permissions)}<li><a href="index.php?module=CurrencyAdmin">Валюты</a></li>{/if}
	<li class="active"><a href="index.php?module=DeliveriesAdmin">Доставка</a></li>
	{if in_array('payment', $manager->permissions)}<li><a href="index.php?module=PaymentMethodsAdmin">Оплата</a></li>{/if}
	{if in_array('managers', $manager->permissions)}<li><a href="index.php?module=ManagersAdmin">Менеджеры</a></li>{/if}
	{if in_array('cities', $manager->permissions)}<li><a href="index.php?module=CitiesAdmin">Города доставки</a></li>{/if}
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
			<h2>Скидки</h2>
			<ul>
				{foreach $deliveries_discount as $delivery_discount}
				<li>
					<label class=property>Скидка от</label>
					<input type="text" class="simpla_small_inp" style="width: 100px;" name="deliveries_discount[discount_from][]" value="{$delivery_discount->discount_from}"> {$currency->sign} -
					<input type="text" class="simpla_small_inp" style="width: 100px;" name="deliveries_discount[discount_percent][]" value="{$delivery_discount->discount_percent}"> %
				</li>
				{/foreach}
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
	<div class="list_city layer" style="clear: both">
		<h2>Города доставки</h2>
		<ul id="list_cities_deliver">
			{foreach $cities_deliver as $city_deliver}
				<li>
					<label class=property>Город </label>
					<select name="cities_deliver[city_id][]" class="order-block__field" required>
						<option value="">Выбрать город</option>
						{foreach $cities as $city}
							<option value='{$city->id}'
									{if $city_deliver->city_id==$city->id}selected="selected"{/if}
									>{$city->name|escape}</option>
						{/foreach}
					</select> доставка от&nbsp
					<input type="text" class="simpla_small_inp" style="width: 100px;" required name="cities_deliver[from_sum][]" value="{$city_deliver->from_sum}">руб, процент скидки -&nbsp
					<input type="text" class="simpla_small_inp" style="width: 80px;" name="cities_deliver[discount_percent][]" value="{$city_deliver->discount_percent}"> %, отключить прочие скидки -&nbsp
					<input name=cities_deliver[check_sale_other][] value='1' type="checkbox" id="city_sale_other" {if $city_deliver->check_sale_other}checked{/if}/><label for="city_sale_other">Да</label>&nbsp
					<span class="delete batton" title="Удалить скидку"></span>
				</li>
			{/foreach}
		</ul>
		<span id="add_city_deliver" class="add_batton_list">Добавить город</span>
	</div>
	<!--Процент скидок на товары End-->

	<!--Процент скидок на товары-->
	<div class="sale-block-brand layer" style="clear: both">
		<h2>Индивидуальные скидки на бренды</h2>
		<ul id="list_deliver_disconts_brand">
			{foreach $deliveries_brand as $delivery_brand}
				<li>
					<label class=property>Скидка </label>
					<input type="text" class="simpla_small_inp" style="width: 100px;" required name="deliveries_brand[discount_percent][]" value="{$delivery_brand->discount_percent}"> % на бренд -&nbsp
					<select name="deliveries_brand[brands_id][]" class="order-block__field" required>
						<option value="">Выбрать бренд</option>
						{foreach $brands as $brand}
							<option value='{$brand->id}'
									{if $delivery_brand->brands_id==$brand->id}selected="selected"{/if}
									brand_name='{$brand->name|escape}'>{$brand->name|escape}</option>
						{/foreach}
					</select> сумма -&nbsp
					<input type="text" class="simpla_small_inp" style="width: 100px;" required name="deliveries_brand[discount_from][]" value="{$delivery_brand->discount_from}">
					<span class="delete" title="Удалить скидку"></span>
				</li>
			{/foreach}
		</ul>
		<span id="add_delivery_discont_brand">Добавить скидку</span>
	</div>
	<!--Процент скидок на товары End-->

	<!--Процент скидок на товары на определённую дату-->
	{if $delivery->id!=2}
	<div class="sale-block-brand layer" style="clear: both">
		<h2>Скидка на дату</h2>
		<ul id="list_deliver_disconts_date">
			{foreach $deliveries_date as $delivery_date}
				<li>
					<label class=property>Скидка </label>
					<input type="text" class="simpla_small_inp" style="width: 100px;" required name="deliveries_date[discount_percent][]" value="{$delivery_date->discount_percent}"> % на дату -&nbsp
					<input type="date" class="simpla_small_inp" style="width: 200px;" required name="deliveries_date[date_sale][]" value="{$delivery_date->date_sale}"> сумма -&nbsp
					<input type="text" class="simpla_small_inp" style="width: 100px;" required name="deliveries_date[discount_from][]" value="{$delivery_date->discount_from}">
					<span class="delete" title="Удалить скидку"></span>
				</li>
			{/foreach}
		</ul>
		<span id="add_delivery_discont_date">Добавить скидку</span>
	</div>
	<div class="sale-block-brand layer" style="clear: both">
		<h2>Скидка на дату по бренду</h2>
		<ul id="list_deliver_disconts_date_brand">
			{foreach $deliveries_date_brand as $delivery_date_brand}
				<li>
					<label class=property>Скидка </label>
					<input type="text" class="simpla_small_inp" style="width: 100px;" required name="deliveries_date_brand[discount_percent][]" value="{$delivery_date_brand->discount_percent}"> % на дату -&nbsp
					<input type="date" class="simpla_small_inp" style="width: 200px;" required name="deliveries_date_brand[date_sale][]" value="{$delivery_date_brand->date_sale}"> сумма -&nbsp
					<input type="text" class="simpla_small_inp" style="width: 100px;" required name="deliveries_date_brand[discount_from][]" value="{$delivery_date_brand->discount_from}">на бренд -&nbsp
					<select name="deliveries_date_brand[brands_id][]" class="order-block__field" required>
						<option value="">Выбрать бренд</option>
						{foreach $brands as $brand}
							<option value='{$brand->id}'
									{if $delivery_date_brand->brands_id==$brand->id}selected="selected"{/if}
									brand_name='{$brand->name|escape}'>{$brand->name|escape}</option>
						{/foreach}
					</select>
					<span class="delete" title="Удалить скидку"></span>
				</li>
			{/foreach}
		</ul>
		<span id="add_delivery_discont_date_brand">Добавить скидку</span>
	</div>
	{/if}
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
		{if $delivery->id==2}
		<div class="list_area" style="float: none">
			<h2>Список пунктов выдачи Минск</h2>
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
		<div style="clear:both;margin-bottom: 25px;"></div>
		{/if}
		<h2>Описание</h2>
		<textarea name="description" class="editor_small">{$delivery->description|escape}</textarea>
	</div>
	<!-- Описание товара (The End)-->
	<input class="button_green button_save" type="submit" name="" value="Сохранить" />
</form>
<!-- Основная форма (The End) -->
<div id="copy_li_discont" style="display: none">
	<li>
		<label class=property>Скидка </label>
		<input type="text" class="simpla_small_inp" required style="width: 100px;" name="deliveries_brand[discount_percent][]" value=""> % на бренд -&nbsp
		<select name="deliveries_brand[brands_id][]" class="order-block__field" required>
			<option value="">Выбрать бренд</option>
			{foreach $brands as $brand}
				<option value='{$brand->id}'
						brand_name='{$brand->name|escape}'>{$brand->name|escape}</option>
			{/foreach}
		</select> сумма -&nbsp
		<input type="text" class="simpla_small_inp" style="width: 100px;" required name="deliveries_brand[discount_from][]" value="0">
		<span class="delete" title="Удалить скидку"></span>
	</li>
</div>
<div id="copy_li_discont_date" style="display: none">
	<li>
		<label class=property>Скидка </label>
		<input type="text" class="simpla_small_inp" style="width: 100px;" required name="deliveries_date[discount_percent][]" value=""> % на дату -&nbsp
		<input type="date" class="simpla_small_inp" style="width: 200px;" required name="deliveries_date[date_sale][]" value=""> сумма -&nbsp
		<input type="text" class="simpla_small_inp" style="width: 100px;" required name="deliveries_date[discount_from][]" value="">
		<span class="delete" title="Удалить скидку"></span>
	</li>
</div>
<div id="copy_li_discont_date_brand" style="display: none">
	<li>
		<label class=property>Скидка </label>
		<input type="text" class="simpla_small_inp" style="width: 100px;" required name="deliveries_date_brand[discount_percent][]" value=""> % на дату -&nbsp
		<input type="date" class="simpla_small_inp" style="width: 200px;" required name="deliveries_date_brand[date_sale][]" value=""> сумма -&nbsp
		<input type="text" class="simpla_small_inp" style="width: 100px;" required name="deliveries_date_brand[discount_from][]" value="">на бренд -&nbsp
		<select name="deliveries_date_brand[brands_id][]" class="order-block__field" required>
			<option value="">Выбрать бренд</option>
			{foreach $brands as $brand}
				<option value='{$brand->id}'
						brand_name='{$brand->name|escape}'>{$brand->name|escape}</option>
			{/foreach}
		</select>
		<span class="delete" title="Удалить скидку"></span>
	</li>
</div>
<div id="copy_list_city_deliver" style="display: none">
	<li>
		<label class=property>Город </label>
		<select name="cities_deliver[city_id][]" class="order-block__field" required>
			<option value="">Выбрать город</option>
			{foreach $cities as $city}
				<option value='{$city->id}'>{$city->name|escape}</option>
			{/foreach}
		</select> доставка от&nbsp
		<input type="text" class="simpla_small_inp" style="width: 100px;" required name="cities_deliver[from_sum][]" value="">руб, процент скидки -&nbsp
		<input type="text" class="simpla_small_inp" style="width: 80px;" name="cities_deliver[discount_percent][]" value=""> %, отключить прочие скидки -&nbsp
		<input name=cities_deliver[check_sale_other][] value='1' type="checkbox" id="city_sale_other"/><label for="city_sale_other">Да</label>&nbsp
		<span class="delete batton" title="Удалить скидку"></span>
	</li>
</div>
<script>
	$('#add_delivery_discont_brand').on('click' ,function () {
		$('#copy_li_discont li').clone().appendTo('#list_deliver_disconts_brand');
	});
	$('#list_deliver_disconts_brand').on('click',function () {
		if ($(event.target).hasClass('delete')) deleteDeliverDiscont(event.target);
	});
	$('#add_delivery_discont_date').on('click' ,function () {
		$('#copy_li_discont_date li').clone().appendTo('#list_deliver_disconts_date');
	});
	$('#list_deliver_disconts_date').on('click',function () {
		if ($(event.target).hasClass('delete')) deleteDeliverDiscont(event.target);
	});
	$('#add_delivery_discont_date_brand').on('click' ,function () {
		$('#copy_li_discont_date_brand li').clone().appendTo('#list_deliver_disconts_date_brand');
	});
	$('#list_deliver_disconts_date_brand').on('click',function () {
		if ($(event.target).hasClass('delete')) deleteDeliverDiscont(event.target);
	});
	$('#add_city_deliver').on('click' ,function () {
		$('#copy_list_city_deliver li').clone().appendTo('#list_cities_deliver');
	});
	$('#list_cities_deliver').on('click',function () {
		if ($(event.target).hasClass('delete')) deleteDeliverDiscont(event.target);
	});
	function deleteDeliverDiscont(element) {
		$(element).parents('li').remove();
	};
</script>
{*Для списка пунктов самовывоза минска*}
{if $delivery->id==2}
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
{/if}