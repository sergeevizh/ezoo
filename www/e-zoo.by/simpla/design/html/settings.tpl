{capture name=tabs}
	<li class="active"><a href="index.php?module=SettingsAdmin">Настройки</a></li>
	{if in_array('currency', $manager->permissions)}<li><a href="index.php?module=CurrencyAdmin">Валюты</a></li>{/if}
	{if in_array('delivery', $manager->permissions)}<li><a href="index.php?module=DeliveriesAdmin">Доставка</a></li>{/if}
	{if in_array('payment', $manager->permissions)}<li><a href="index.php?module=PaymentMethodsAdmin">Оплата</a></li>{/if}
	{if in_array('managers', $manager->permissions)}<li><a href="index.php?module=ManagersAdmin">Менеджеры</a></li>{/if}
	{if in_array('cities', $manager->permissions)}<li><a href="index.php?module=CitiesAdmin">Города доставки</a></li>{/if}
{/capture}

{$meta_title = "Настройки" scope=parent}
<script>
	var limit_rows = {$limits|@count};
</script>
{literal}
	<script src="design/js/autocomplete/jquery.autocomplete-min.js"></script>
	<script src="design/js/jquery/datepicker/jquery.ui.datepicker-ru.js"></script>
	<script>
		$(function () {
			$('.date-picker').datepicker({
				regional:'ru'
			});

			$('#promo_blocks span.add_first').click(function () {
				var html = '<li>';
				html += '<select name="promo_time[]">';
                html += '<option value="" disabled selected hidden>-- Не выбрано --</option>';
                html += '<option value="10:00-12:00">10:00-12:00</option>';
                html += '<option value="12:00-14:00">12:00-14:00</option>';
                html += '<option value="14:00-16:00">14:00-16:00</option>';
                html += '<option value="16:00-18:00">16:00-18:00</option>';
                html += '<option value="18:00-20:00">18:00-20:00</option>';
                html += '<option value="20:00-22:00">20:00-22:00</option>';
                html += '<option value="22:00-23:00">22:00-23:00</option>';
                html += '</select>';
				html += '<span class="delete"><i class="dash_link">Удалить</i></span>';
				html += '</li>';
				$('#promo_times').append(html);
			});

            $('#promo_blocks span.add_second').click(function () {
                var html = '<li>';
                html += '<select name="promo_time_second[]">';
                html += '<option value="" disabled selected hidden>-- Не выбрано --</option>';
                html += '<option value="10:00-12:00">10:00-12:00</option>';
                html += '<option value="12:00-14:00">12:00-14:00</option>';
                html += '<option value="14:00-16:00">14:00-16:00</option>';
                html += '<option value="16:00-18:00">16:00-18:00</option>';
                html += '<option value="18:00-20:00">18:00-20:00</option>';
                html += '<option value="20:00-22:00">20:00-22:00</option>';
                html += '<option value="22:00-23:00">22:00-23:00</option>';
                html += '</select>';
                html += '<span class="delete"><i class="dash_link">Удалить</i></span>';
                html += '</li>';
                $('#promo_times_second').append(html);
            });

			$('#promo_blocks').on('click', '.delete', function () {
				$(this).closest('li').remove();
			});

            // Добавление лимита
            $('#limit_blocks span.add').click(function() {
                var html = '<ul>';
                html += '<li></li>';
                html += '<li><input class="date-picker" type="text" value="" name="limits[' + limit_rows + '][date]" autocomplete="off"></li>';
                html += '<li><input type="text" name="limits[' + limit_rows + '][time]"></li>';
                html += '<li><input type="text" name="limits[' + limit_rows + '][products_limit]"></li>';
                html += '<li><select class="simpla_inp_city" name="limits[' + limit_rows + '][city]">';
                html += '<option value="" disabled selected hidden>-- Не выбрано --</option>';
                {/literal}
                    {foreach $delivery_city as $key => $city}
                    html += '<option value="{$city->name}">{$city->name}</option>';
                    {/foreach}
                        {literal}
                        html += '</select></li>';
                        html += '<li style="text-align: center; margin-top: 3px;font-size: 16px;"><b></b></li>';
                        html += '<li><span class="delete"><i class="dash_link">Удалить</i></span></li>';
                        html += '</ul>';
                        limit_rows++;
                        $('#limits').append(html);
                        $('.date-picker').datepicker({
                            regional:'ru'
                        });
                    });

            $('#limits').on('click', ' .delete', function () {
                limit_rows--;
                var limit_id = $(this).closest('ul').find('li:first-child input').val();
                var row = $(this).closest('ul');

                if (limit_id) {
                    $.ajax({
                        url: 'ajax/delete_limit.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {limit_id},
                        success: function () {
                            row.remove();
                        }
                    })
                } else {
                    row.remove();
                }

            });
		});
	</script>
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
<form method=post id=product enctype="multipart/form-data">
<input type=hidden name="session_id" value="{$smarty.session.id}">

		<!-- Параметры -->
		<div class="block">
			<h2>Настройки сайта</h2>
			<ul>
				<li><label class=property>Имя сайта</label><input name="site_name" class="simpla_inp" type="text" value="{$settings->site_name|escape}" /></li>
				<li><label class=property>Имя компании</label><input name="company_name" class="simpla_inp" type="text" value="{$settings->company_name|escape}" /></li>
				<li><label class=property>Формат даты</label><input name="date_format" class="simpla_inp" type="text" value="{$settings->date_format|escape}" /></li>
				<li><label class=property>Email для восстановления пароля</label><input name="admin_email" class="simpla_inp" type="text" value="{$settings->admin_email|escape}" /></li>
			</ul>
		</div>
		<div class="block layer">
			<h2>Оповещения</h2>
			<ul>
				<li><label class=property>Оповещение о заказах</label><input name="order_email" class="simpla_inp" type="text" value="{$settings->order_email|escape}" /></li>
				<li><label class=property>Оповещение о комментариях</label><input name="comment_email" class="simpla_inp" type="text" value="{$settings->comment_email|escape}" /></li>
				<li><label class=property>Обратный адрес оповещений</label><input name="notify_from_email" class="simpla_inp" type="text" value="{$settings->notify_from_email|escape}" /></li>
			</ul>
		</div>
		<!-- Параметры (The End)-->

		<!-- Поле "Описание" для Onliner.by -->
		<div class="block layer">
			<h2>Описание магазина (для ONLINER.BY) </h2>
			<ul>
				<li><label class=property>Описание магазина (для ONLINER.BY)</label><textarea name="onliner_descr" class="simpla_inp" cols="50" rows="40">{$settings->onliner_descr}</textarea></li>
			</ul>
		</div>
		<!-- Поле "Описание" для Onliner.by END -->

		<!-- Сообщение при попытке заказать ветпрепараты -->
		<div class="block layer">
			<h2>Сообщение при попытке заказать ветпрепараты</h2>
			<ul>
				<li><label class=property>Сообщение при попытке заказать ветпрепараты</label><textarea name="vetpreparaty" class="simpla_inp" cols="50" rows="40">{$settings->vetpreparaty}</textarea></li>
			</ul>

			<label style="display: -webkit-box;display: -ms-flexbox;display: flex;-webkit-box-align: center;-ms-flex-align: center;align-items: center;height: 30px" id="setting_pickup_global_label">

				<input type="checkbox" style="height: 20px; width: 30px;" name="setting_pickup_global" id="setting_pickup_global" {if $settings->setting_pickup_global['checkbox']}checked{/if}>
				<h4>ОТКЛЮЧИТЬ / ВКЛЮЧИТЬ РАЗРЕШЕНИЕ НА ДОСТАВКУ ДЛЯ ВСЕХ ПРОДУКТОВ ГЛОБАЛЬНО</h4>
				<img src="design/images/gif/load.gif" alt="" style="display: none">

			</label>
			<script type="text/javascript">/*<![CDATA[*/
				(function ($) {
					"use strict";

					$('#setting_pickup_global').on('click', function () {

						$.ajax({
							url: 'ajax/setting.php',
							type: 'POST',
							dataType: 'json',
							data: $(this).serialize(),
							beforeSend: function() {
								$('#setting_pickup_global_label img').show();
								$('#setting_pickup_global_label h4').hide();
								$('#setting_pickup_global_label input').hide();
							},
							complete: function() {
								$('#setting_pickup_global_label img').hide();
								$('#setting_pickup_global_label h4').show();
								$('#setting_pickup_global_label input').show();
							},
							error: function (xhr, ajaxOptions, thrownError) {
								console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
							},
							success: function (json) {
								console.log(json);
								if (json.hasOwnProperty('answer')){
									alert(json.answer)
								}
							}
						});
					});
				})(jQuery)
				/*]]>*/</script>

		</div>
		<!-- Сообщение при попытке заказать ветпрепараты END -->

		<!-- Сообщение при выборе Другого города -->
		<div class="block layer">
			<h2>Сообщение при выборе Другого города</h2>
			<ul>
				<li><label class=property>Сообщение при выборе Другого города</label><textarea name="other_city" class="simpla_inp" cols="50" rows="40">{$settings->other_city}</textarea></li>
			</ul>
		</div>
		<!-- Сообщение при выборе Другого города END -->

		<!-- Отключение дат и времени -->
		<div class="block layer">
			<h2>Отключение дат и времени</h2>
			<ul>
				<li><label class=property>Пример отклюлчения даты полностью: 15.04();<br> Пример для отключения временного промежутка для определенной даты: 15.04(10:00 - 12:00);<br> Пример отключения нескольких промежутков для определенной даты: 15.04(10:00 - 12:00,19:00 - 21:00).<br> Разделитель дат между собой символом ";".</label><textarea name="dateandtime" class="simpla_inp" cols="50" rows="40">{$settings->dateandtime}</textarea></li>
			</ul>
		</div>
		<!-- Отключение дат и времени END -->
	<div id="promo_blocks" class="block layer">
		<h2>Импорт промокодов</h2>
		{if $message_success_promo}
			<div class="message message_success">
				<span class="text">Промокоды успешно импортированы </span>
			</div>
		{/if}
		<div style="margin-bottom: 20px;"><b>Текущее количество промокодов: {$promo_count}</b></div>
		<input type="file" name="codes_file" value="Загрузить файл">
		<h3 style="margin-top: 30px;">Настройка условий получения промокода:</h3>
		<ul style="margin-top: 20px;">
			<li><label class=property>Сумма заказа ОТ</label><input type="text" name="promo_price" value="{$settings->promo_price|escape}"></li>
			<li><label class=property>Время доставки</label>
				<ul id="promo_times">
					{foreach $settings->promo_time as $promo_time}
						<li>
							<select name="promo_time[]">
								<option value="" disabled selected hidden>-- Не выбрано --</option>
								<option value="10:00-12:00" {if $promo_time === '10:00-12:00'}selected{/if}>10:00-12:00</option>
								<option value="12:00-14:00" {if $promo_time === '12:00-14:00'}selected{/if}>12:00-14:00</option>
								<option value="14:00-16:00" {if $promo_time === '14:00-16:00'}selected{/if}>14:00-16:00</option>
								<option value="16:00-18:00" {if $promo_time === '16:00-18:00'}selected{/if}>16:00-18:00</option>
								<option value="18:00-20:00" {if $promo_time === '18:00-20:00'}selected{/if}>18:00-20:00</option>
								<option value="20:00-22:00" {if $promo_time === '20:00-22:00'}selected{/if}>20:00-22:00</option>
								<option value="22:00-23:00" {if $promo_time === '22:00-23:00'}selected{/if}>22:00-23:00</option>
							</select>
							<span class="delete"><i class="dash_link">Удалить</i></span>
						</li>
					{/foreach}
				</ul>
			</li>
		</ul>
		<span class="add add_first" id="add_time"><i class="dash_link">Добавить временной промежуток</i></span>

		<!--		Добавление второго условия получения купонов-->
		{if $message_success_promo_second}
		<div class="message message_success">
			<span class="text">Промокоды успешно импортированы </span>
		</div>
		{/if}
		<div style="margin-bottom: 20px;  margin-top: 20px;"><b>Текущее количество промокодов: {$promo_count_second}</b></div>
		<input type="file" name="codes_file_second" value="Загрузить файл">
		<h3 style="margin-top: 30px;">Настройка условий получения промокода:</h3>
		<ul style="margin-top: 20px;">

			<li><label class=property>Сумма заказа ОТ</label><input type="text" name="promo_price_second" value="{$settings->promo_price_second|escape}"></li>
			<li><label class=property>Время доставки</label>
				<ul id="promo_times_second">
					{foreach $settings->promo_time_second as $promo_time_second}
					<li>
						<select name="promo_time_second[]">
							<option value="" disabled selected hidden>-- Не выбрано --</option>
							<option value="10:00-12:00" {if $promo_time_second === '10:00-12:00'}selected{/if}>10:00-12:00</option>
							<option value="12:00-14:00" {if $promo_time_second === '12:00-14:00'}selected{/if}>12:00-14:00</option>
							<option value="14:00-16:00" {if $promo_time_second === '14:00-16:00'}selected{/if}>14:00-16:00</option>
							<option value="16:00-18:00" {if $promo_time_second === '16:00-18:00'}selected{/if}>16:00-18:00</option>
							<option value="18:00-20:00" {if $promo_time_second === '18:00-20:00'}selected{/if}>18:00-20:00</option>
							<option value="20:00-22:00" {if $promo_time_second === '20:00-22:00'}selected{/if}>20:00-22:00</option>
							<option value="22:00-23:00" {if $promo_time_second === '22:00-23:00'}selected{/if}>22:00-23:00</option>
						</select>
						<span class="delete"><i class="dash_link">Удалить</i></span>
					</li>
					{/foreach}
				</ul>
			</li>
		</ul>
		<span class="add add_second" id="add_time_second"><i class="dash_link">Добавить временной промежуток</i></span>
	</div>
		<!-- Приветственное сообщение -->
		<div class="block layer">
			<h2>Приветственное сообщение</h2>
			<ul>
				<li><input name="hide_welcome" value="1" type="checkbox" id="hide_welcome" {if $settings->hide_welcome}checked{/if}/> <label for="hide_welcome">Скрыть сообщение</label></li>
				<li><label class=property>Приветственное сообщение</label><textarea name="welcome" class="simpla_inp" cols="50" rows="40">{$settings->welcome}</textarea></li>
			</ul>
		</div>
		<!-- Приветственное сообщение END -->

		<!-- Параметры -->
		<div class="block layer">
			<h2>Формат цены</h2>
			<ul>
				<li><label class=property>Разделитель копеек</label>
					<select name="decimals_point" class="simpla_inp">
						<option value='.' {if $settings->decimals_point == '.'}selected{/if}>точка: 12.45 {$currency->sign|escape}</option>
						<option value=',' {if $settings->decimals_point == ','}selected{/if}>запятая: 12,45 {$currency->sign|escape}</option>
					</select>
				</li>
				<li><label class=property>Разделитель тысяч</label>
					<select name="thousands_separator" class="simpla_inp">
						<option value='' {if $settings->thousands_separator == ''}selected{/if}>без разделителя: 1245678 {$currency->sign|escape}</option>
						<option value=' ' {if $settings->thousands_separator == ' '}selected{/if}>пробел: 1 245 678 {$currency->sign|escape}</option>
						<option value=',' {if $settings->thousands_separator == ','}selected{/if}>запятая: 1,245,678 {$currency->sign|escape}</option>
					</select>


				</li>
			</ul>
		</div>
		<!-- Параметры (The End)-->

		<!-- Параметры -->
		<div class="block layer">
			<h2>Настройки каталога</h2>
			<ul>
				<li><label class=property>Товаров на странице сайта</label><input name="products_num" class="simpla_inp" type="text" value="{$settings->products_num|escape}" /></li>
				<li><label class=property>Товаров на странице сайта(мобила)</label><input name="products_num_mobi" class="simpla_inp" type="text" value="{$settings->products_num_mobi|escape}" /></li>
				<li><label class=property>Товаров на странице админки</label><input name="products_num_admin" class="simpla_inp" type="text" value="{$settings->products_num_admin|escape}" /></li>
				<li><label class=property>Максимум товаров в заказе</label><input name="max_order_amount" class="simpla_inp" type="text" value="{$settings->max_order_amount|escape}" /></li>
				<li><label class=property>Единицы измерения товаров</label><input name="units" class="simpla_inp" type="text" value="{$settings->units|escape}" /></li>
				<li><label class=property>Маска title для страницы товара</label><input name="mask_title" class="simpla_inp" type="text" value="{$settings->mask_title|escape}" /></li>
				<li><label class=property>Маска Description для страницы товара</label><input name="mask_desc" class="simpla_inp" type="text" value="{$settings->mask_desc|escape}" /></li>
			</ul>
			<div>Параметры маски: - %H1% - заголовок товара</br> - %PRICE% - минимальная стоимость</div>
		</div>
		<!-- Параметры (The End)-->
	<div class="block layer">
		<h2>Текст ссылки на вет. лицензию</h2>
		<input type="text" name="license_button" value="{$settings->license_button|escape}">
	</div>
	<div class="block layer">
		<h2>Лимит заказов</h2>
		<div id="limit_blocks">
			<ul id="header">
				<li></li>
				<li class="limit_date">Дата</li>
				<li class="limit_time">Время</li>
				<li class="limit_limit">Лимит</li>
				<li class="limit_city">Город</li>
				<li class="limit_current">Текущее количество</li>
			</ul>
			<div id="limits">
				{foreach $limits as $key => $limit}
				<ul>
					<li><input type="hidden" value="{$limit->id}" name="limits[{$key}][id]"></li>
					<li><input class="date-picker" type="text" value="{$limit->date|escape}" name="limits[{$key}][date]" autocomplete="off"></li>
					<li><input type="text" value="{$limit->time|escape}" name="limits[{$key}][time]"></li>
					<li><input type="text" value="{$limit->products_limit|escape}" name="limits[{$key}][products_limit]"></li>
					<li><select class="simpla_inp_city" name="limits[{$key}][city]">
							{foreach $delivery_city as $city}
							<option value="{$city->name|escape}" {if $city->name === $limit->city}selected{/if}>{$city->name|escape}</option>
							{/foreach}
						</select></li>
					<li style="text-align: center; margin-top: 3px;font-size: 16px;"><b>{$limit->current|escape}</b></li>
					<li><span class="delete"><i class="dash_link">Удалить</i></span></li>
				</ul>
				{/foreach}
			</div>
			<span class="add" id="add_variant"><i class="dash_link">Добавить лимит</i></span>
		</div>
	</div>
	<div class="block layer">
		<h2>Время открытия доставки</h2>
		<ul>
			<li><label for="time_opening[10:00-12:00]" class="property">10:00-12:00</label><input type="text" name="time_opening[10:00-12:00]" value="{$settings->times['10:00-12:00']}"></li>
			<li><label for="time_opening[12:00-14:00]" class="property">12:00-14:00</label><input type="text" name="time_opening[12:00-14:00]" value="{$settings->times['12:00-14:00']}"></li>
			<li><label for="time_opening[14:00-16:00]" class="property">14:00-16:00</label><input type="text" name="time_opening[14:00-16:00]" value="{$settings->times['14:00-16:00']}"></li>
			<li><label for="time_opening[16:00-18:00]" class="property">16:00-18:00</label><input type="text" name="time_opening[16:00-18:00]" value="{$settings->times['16:00-18:00']}"></li>
			<li><label for="time_opening[18:00-20:00]" class="property">18:00-20:00</label><input type="text" name="time_opening[18:00-20:00]" value="{$settings->times['18:00-20:00']}"></li>
			<li><label for="time_opening[20:00-22:00]" class="property">20:00-22:00</label><input type="text" name="time_opening[20:00-22:00]" value="{$settings->times['20:00-22:00']}"></li>
			<li><label for="time_opening[22:00-23:00]" class="property">22:00-23:00</label><input type="text" name="time_opening[22:00-23:00]" value="{$settings->times['22:00-23:00']}"></li>
		</ul>
		<div>
			Формат указываемого времени: hh:mm.<br>
			Пример: 05:00
		</div>
	</div>
		<!-- Параметры -->
		<div class="block layer">
			<h2>Настройки смс</h2>
			<ul>
				<li><label class="property">Логин МТС</label><input name="mts_login" class="simpla_inp" type="text" value="{$settings->mts_login|escape}" /></li>
				<li><label class="property">Пароль МТС</label><input name="mts_password" class="simpla_inp" type="text" value="{$settings->mts_password|escape}" /></li>
				<li><label class="property">ID клиента</label><input name="mts_client_id" class="simpla_inp" type="text" value="{$settings->mts_client_id|escape}" /></li>
                <li><label class=property>Ключ работы с UniSender</label><input name="api_unisend_key" class="simpla_inp" type="text" value="{$settings->api_unisend_key|escape}" /></li>
                <li><label class=property>Имя работы с UniSender</label><input name="api_unisend_name" class="simpla_inp" type="text" value="{$settings->api_unisend_name|escape}" /></li>
				<li><label class=property>Маска смс "Принят"</label><input name="mask_sms_1" class="simpla_inp" type="text" value="{$settings->mask_sms_1|escape}" /></li>
				<li><label class=property>Маска смс "Собран" (курьер)</label><input name="mask_sms_2_courier" class="simpla_inp" type="text" value="{$settings->mask_sms_2_courier|escape}" /></li>
				<li><label class=property>Маска смс "Собран" (курьер, без времени)</label><input name="mask_sms_2_courier_without_time" class="simpla_inp" type="text" value="{$settings->mask_sms_2_courier_without_time|escape}" /></li>
				<li><label class=property>Маска смс "Собран" (самовывоз)</label><input name="mask_sms_2_sklad" class="simpla_inp" type="text" value="{$settings->mask_sms_2_sklad|escape}" /></li>
				<li><label class=property>Маска смс "Промокод"</label><input name="mask_sms_promo" class="simpla_inp" type="text" value="{$settings->mask_sms_promo|escape}" /></li>
				<li><label class=property>Маска второго смс "Промокод"</label><input name="mask_sms_promo_second" class="simpla_inp" type="text" value="{$settings->mask_sms_promo_second|escape}" /></li>
			</ul>
			<div>Параметры маски:</br> - %NUMBER% - номер заказа</br> - %DATE% - Дата доставки / дата забрать заказ</br> - %TIME% - Время доставки</br> - %PHONE% - Телефон доставки</br> - %PROMO% - Промокод</div>
		</div>
		<!-- Параметры (The End)-->
		<!-- Параметры -->
		<div class="block layer">
			<h2>Изображения товаров</h2>

			<ul>
				<li><label class=property>Водяной знак</label>
				<input name="watermark_file" class="simpla_inp" type="file" />

				<img style='display:block; border:1px solid #d0d0d0; margin:10px 0 10px 0;' src="{$config->root_url}/{$config->watermark_file}?{math equation='rand(10,10000)'}">
				</li>
				<li><label class=property>Горизонтальное положение водяного знака</label><input name="watermark_offset_x" class="simpla_inp" type="text" value="{$settings->watermark_offset_x|escape}" /> %</li>
				<li><label class=property>Вертикальное положение водяного знака</label><input name="watermark_offset_y" class="simpla_inp" type="text" value="{$settings->watermark_offset_y|escape}" /> %</li>
				<li><label class=property>Прозрачность знака (больше &mdash; прозрачней)</label><input name="watermark_transparency" class="simpla_inp" type="text" value="{$settings->watermark_transparency|escape}" /> %</li>
				<li><label class=property>Резкость изображений (рекомендуется 20%)</label><input name="images_sharpen" class="simpla_inp" type="text" value="{$settings->images_sharpen|escape}" /> %</li>
			</ul>
		</div>
		<!-- Параметры (The End)-->


		<!-- Параметры -->
		<div class="block layer">
			<h2>Интеграция с <a href="http://prostiezvonki.ru">простыми звонками</a></h2>
			<ul>
				<li><label class=property>Сервер</label><input name="pz_server" class="simpla_inp" type="text" value="{$settings->pz_server|escape}" /></li>
				<li><label class=property>Пароль</label><input name="pz_password" class="simpla_inp" type="text" value="{$settings->pz_password|escape}" /></li>
				<li><label class=property>Телефоны менеджеров:</label></li>
				{foreach $managers as $manager}
				<li><label class=property>{$manager->login}</label><input name="pz_phones[{$manager->login}]" class="simpla_inp" type="text" value="{$settings->pz_phones[$manager->login]|escape}" /></li>
				{/foreach}
			</ul>
		</div>
		<!-- Параметры (The End)-->

	<!-- Параметры сообщений-->
	<div class="block layer">
		<h2>Сообщения на странице заказа</h2>
		<ul>
			<li><label class=property>Верхнее сообщение</label><input name="text_top_order" class="simpla_inp" type="text" value="{$settings->text_top_order|escape}" /></li>
			<li><label class=property>Нижнее сообщение</label><input name="text_bottom_order" class="simpla_inp" type="text" value="{$settings->text_bottom_order|escape}" /></li>
		</ul>
	</div>
	<!-- Параметры (The End)-->

	<!-- Параметры сообщений-->
	<div class="block layer">
		<h2>Блок "Связь с менеджером" (mobile)</h2>
		<ul>
			<li><label class=property>Текст</label><input name="manager_contacts_text" class="simpla_inp" type="text" value="{$settings->manager_contacts_text|escape}" /></li>
			<li><label class=property>Кнопка 1</label><input name="manager_contacts_operator_1" class="simpla_inp" type="text" value="{$settings->manager_contacts_operator_1|escape}" /></li>
			<li><label class=property></label><input name="manager_contacts_phone_1" class="simpla_inp" type="text" value="{$settings->manager_contacts_phone_1|escape}" /></li>
			<li><label class=property>Кнопка 2</label><input name="manager_contacts_operator_2" class="simpla_inp" type="text" value="{$settings->manager_contacts_operator_2|escape}" /></li>
			<li><label class=property></label><input name="manager_contacts_phone_2" class="simpla_inp" type="text" value="{$settings->manager_contacts_phone_2|escape}" /></li>
			<li><label class=property>Кнопка 3</label><input name="manager_contacts_chat_text" class="simpla_inp" type="text" value="{$settings->manager_contacts_chat_text|escape}" /></li>
			<li><label class=property></label><input name="manager_contacts_chat" class="simpla_inp" type="text" value="{$settings->manager_contacts_chat|escape}" /></li>
		</ul>
	</div>
	<!-- Параметры (The End)-->


		<input class="button_green button_save" type="submit" name="save" value="Сохранить" />

	<!-- Левая колонка свойств товара (The End)-->

</form>
<!-- Основная форма (The End) -->
