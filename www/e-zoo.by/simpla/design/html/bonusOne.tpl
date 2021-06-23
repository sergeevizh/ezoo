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
{literal}
	<script src="design/js/autocomplete/jquery.autocomplete-min.js"></script>
	<script src="design/js/jquery/datepicker/jquery.ui.datepicker-ru.js"></script>
<script>
$(function() {
	// Удаление изображений pre
	$(".images a#del_pre").live('click', function() {
		 $(this).closest("li").fadeOut(200, function() { $(this).remove(); });
		 $("#add_img_pre").css('display','block');
		 return false;
	});
	// Удаление изображений det
	$(".images a#del_det").live('click', function() {
		 $(this).closest("li").fadeOut(200, function() { $(this).remove(); });
		 $("#add_img_det").css('display','block');
		 return false;
	});
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

	
{/literal}
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


<!-- Основная форма -->
<form method="post" action="" enctype='multipart/form-data' id="product" >
<input type=hidden name="session_id" value="{$smarty.session.id}">
<div id="name">
<h1>{if $bonus->id}Бонус №{$bonus->id|escape}{else}Новый Бонус{/if}</h1>
<div id=next_order>
	<a title="Назад" class=prev_order href="index.php?module=BonusAdmin">←</a>
	</div>
</div>
<!-- Общие параметры -->
	<div class="block">
		<h2>Общие данные бонуса</h2>
		<ul>
			<li><label class=property>Включен:</label><input name="bonus_status" class="simpla_inp" type="checkbox" value="1" {if $bonus->status} checked {/if} /></li>
			<li><label class=property>Имя Бонуса</label><input name="bonus_name" class="simpla_inp" type="text" value="{$bonus->name|escape}" required /></li>
			<li><label class=property>Анонс</label><textarea name="bonus_desc_mini" class="simpla_inp" required>{$bonus->desc_mini|escape}</textarea></li>
			<li><label class=property>Полное описание</label><textarea name="bonus_description" class="simpla_inp" required>{$bonus->description|escape}</textarea></li>
			<li><label class=property>Скидка, %</label><input name="bonus_percent" class="simpla_inp" type="text" value="{$bonus->percent|escape}" /></li>
		</ul>
	</div>

		
<!-- Изображения Бонуса -->
	<div class="block layer images">
		<h2>Изображения бонуса</h2>
		 <h3>Анонс</h3> 
		 {if $bonus->img_preview}
		<ul><li>
			<a href='#' class="delete" id="del_pre" style="margin-top: 0px;"><img src='design/images/cross-circle-frame.png'></a>
			<img src="{$bonus->img_preview}" alt="" />
		</li></ul>
		{/if}
		<div id="add_image"></div>
		<span class="upload_image" id="add_img_pre" style="{if $bonus->img_preview}display:none{/if}"><input id="fileToUpload" type="file" name="img_preview" multiple accept=".jpg,.jpeg,.png,.gif"></span>
		<div id="add_image"></div>
		<h3>Основное</h3>
		{if $bonus->img_detail}
		<ul><li>
			<a href='#' class="delete" id="del_det"><img src='design/images/cross-circle-frame.png'></a>
			<img src="{$bonus->img_detail}" alt=""/>
		</li></ul>
		{/if}
		<div id="add_image"></div>
		<span class="upload_image" id="add_img_det" style="{if $bonus->img_detail}display:none{/if}"><input id="fileToUpload" type="file" name="img_detail" multiple accept=".jpg,.jpeg,.png,.gif"></span>
		<div id="add_image"></div>
		
	</div>
	<style>
	.conditions li{
		display: flex !important;
	}
	.condif{
		margin-right: 10px;
	}
	#product .block label.property{
		width: 150px !important;
	}
	</style>
	<script>
		function setDisable(name) {
			var inpt = "input[name='bonus_" + name + "']";
			if(name == 'dilevery') { inpt = '#select_dilevery'; }
			if(name == 'cities') { inpt = '#select_cities'; }
			if(name == 'brands') { inpt = '#select_brands'; }
			if(name == 'products') { inpt = '#related_products'; }
			if($('#st_' + name).prop('checked')){
				$(inpt).attr('disabled', false);
			}
			else {
				$(inpt).attr('disabled', true);
			}
		}
		function setDisableTwo(name,inp) {
			var inpt_to = inp + 'to';
			var inpt_from = inp + 'from';
			if($('#st_' + name).prop('checked')){
				$("input[name='" + inpt_to + "']").attr('disabled', false);
				$("input[name='" + inpt_from + "']").attr('disabled', false);
			}
			else {				
				$("input[name='" + inpt_to + "']").attr('disabled', true);
				$("input[name='" + inpt_from + "']").attr('disabled', true);
			}
		}
	</script>
	<div class="block layer">
		<h2>Условия бонуса</h2>
		<ul class="conditions"><li>
				<input onchange="setDisable('summ');" name="st_summ" id="st_summ" class="simpla_inp condif" type="checkbox" value="1" {if $bonus->ifstatus['st_summ']} checked {/if} />
				<label class=property>Сумма заказа:</label>
				<input name="bonus_summ" class="simpla_inp" type="text" value="{$bonus->summ|escape}" {if !$bonus->ifstatus['st_summ']} disabled {/if} />
			</li><li>
				<input onchange="setDisable('date_order');" name="st_date_order" id="st_date_order" class="simpla_inp condif" type="checkbox" value="1" {if $bonus->ifstatus['st_date_order']} checked {/if} />
				<label class=property>Дата заказа:</label>
				<input name="bonus_date_order" class="simpla_inp" type="date" value="{$bonus->date_order|escape}" {if !$bonus->ifstatus['st_date_order']} disabled {/if}/>
			</li><li>
				<input onchange="setDisableTwo('sale','bonus_date_ac_');" name="st_sale" id="st_sale" class="simpla_inp condif" type="checkbox" value="1" {if $bonus->ifstatus['st_sale']} checked {/if} />
				<span style="display: inline-flex;" id="">
					<label class=property>Акция c:</label>
					<input name="bonus_date_ac_from" class="simpla_inp" type="date" value="{$bonus->time_from_sale|escape}" {if !$bonus->ifstatus['st_sale']} disabled{/if}/>
					<label class="property" style="width: 20px !important;margin-left:10px;">по:</label>
					<input name="bonus_date_ac_to" class="simpla_inp" type="date" value="{$bonus->time_to_sale|escape}" {if !$bonus->ifstatus['st_sale']} disabled{/if}/>
				</span>
			</li><li>
				<input onchange="setDisableTwo('time', 'bonus_date_');" name="st_time" id="st_time" class="simpla_inp condif" type="checkbox" value="1" {if $bonus->ifstatus['st_time']} checked {/if} />
				<span style="display: inline-flex;" id="">
					<label class=property>Срок действия c:</label>
					<input name="bonus_date_from" class="simpla_inp" type="date" value="{$bonus->time_from|escape}" {if !$bonus->ifstatus['st_time']} disabled{/if}/>
					<label class=property style="width: 20px !important;margin-left:10px;">по:</label>
					<input name="bonus_date_to" class="simpla_inp" type="date" value="{$bonus->time_to|escape}"  {if !$bonus->ifstatus['st_time']} disabled{/if}/>
				</span>
			</li><li>
				<input onchange="setDisable('dilevery');" id="st_dilevery" name="st_dilevery" class="simpla_inp condif" type="checkbox" value="1" {if $bonus->ifstatus['st_dilevery']} checked {/if} />
				<label class=property>Время доставки</label>
				<select id="select_dilevery" name="bonus_time_dilevery" class="order-block__field js-time" style="width:145px;" {if !$bonus->ifstatus['st_dilevery']} disabled {/if}>
					<option value="">Выберите время</option>
					{foreach $times as $time=>$k}
						<option value="{$time}" {if $time==$bonus->time_dilevery} selected {/if}>{$time}</option>
					{/foreach}
				</select>
				</li>
		</ul>
		<div id="column_left">
			<ul><li style="display: flex;">
				<input onchange="setDisable('cities');" id="st_cities" name="st_cities" class="simpla_inp condif" type="checkbox" value="1" {if $bonus->ifstatus['st_cities']} checked {/if} />
				<label class=property>Города:</label>
				<select name="city[]" multiple="multiple" size="20" style="height:300px;" id="select_cities" {if !$bonus->ifstatus['st_cities']} disabled {/if} >
				{foreach $delivery_city as $city}
					<option value="{$city->id}" {if in_array($city->id,$boncity)}selected {/if}>{$city->name}</option>
				{/foreach}
				</select>
			</li></ul>
		</div>
		<div id="column_right">
			<ul><li style="display: flex;">
			<input onchange="setDisable('brands');" id="st_brands" name="st_brands" class="simpla_inp condif" type="checkbox" value="1" {if $bonus->ifstatus['st_brands']} checked {/if} />
			<label class=property>Бренды:</label>
			<select name="brend[]" multiple="multiple" size="20" style="height:300px;" id="select_brands" {if !$bonus->ifstatus['st_brands']} disabled {/if}>
			{foreach $brands as $brand}
				<option value="{$brand->id}" {if in_array($brand->id,$bonbrands)}selected {/if} >{$brand->name}</option>
			{/foreach}
			</select>
			</li></ul>
		</div>	
		<div style="display: inline-flex;">
			<input onchange="setDisable('products');" id="st_products" name="st_products" class="simpla_inp condif" type="checkbox" value="1" {if $bonus->ifstatus['st_products']} checked {/if} />
			<h3>Товары</h3>
		</div>	
		<div id=list class="sortable related_products" >
			{foreach $related_products as $related_product}
				<div class="row">
					<div class="move cell">
						<div class="move_zone"></div>
					</div>
					<div class="image cell">
					<input type=hidden name=related_products[] value='{$related_product['id']}'>
					<a href="{url id=$related_product['id']}">
					<img class=product_icon src='{$related_product['imgname']|resize:35:35}'>
					</a>
					</div>
					<div class="name cell">
					<a href="{url id=$related_product['id']}">{$related_product['name']}</a>
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
			<label class=property>Добавить товар:</label>	
			<input type=text name=related id='related_products' class="input_autocomplete" placeholder='Выберите товар чтобы добавить его' {if !$bonus->ifstatus['st_products']} disabled {/if} >
	</div>
	<div class="block layer">
		<h2>Файл CSV</h2>
		<div id="column_left">
			<label class=property>Выберите файл CSV</label>
			<input type="file" class="import_file" name="csv_file" id="csv_file" accept=".csv"/>
			<input type="hidden" name="csv" value="{$bonus->csv}"/>
		</div>
		{if $bonus->csv}
			<div id="column_right"><a href = "{$bonus->csv}" ><img src="/simpla/design/images/csv.png" alt=""/>Скачать</a></div>
		{/if}
	</div>
	<div>
	
	{if $bonus->id}<input class="button_red button_delete" type="submit" name="delete" value="Удалить" />{/if}
	<input class="button_green button_save" type="submit" name="save" value="Сохранить" />
	</div>
</form>