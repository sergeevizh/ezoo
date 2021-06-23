<?php /* Smarty version Smarty-3.1.18, created on 2021-06-21 15:26:09
         compiled from "simpla/design/html/bonusOne.tpl" */ ?>
<?php /*%%SmartyHeaderCode:42007949560d026ec220382-67796524%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '764d47eb11b4eb4b32950eead0803a95121b1cdf' => 
    array (
      0 => 'simpla/design/html/bonusOne.tpl',
      1 => 1624278360,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '42007949560d026ec220382-67796524',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.18',
  'unifunc' => 'content_60d026ec6670e6_79118126',
  'variables' => 
  array (
    'manager' => 0,
    'limits' => 0,
    'message_success' => 0,
    'message_error' => 0,
    'config' => 0,
    'bonuss' => 0,
    'bonus' => 0,
    'image_prev' => 0,
    'image' => 0,
    'cities' => 0,
    'city' => 0,
    'delivery_city' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_60d026ec6670e6_79118126')) {function content_60d026ec6670e6_79118126($_smarty_tpl) {?><?php $_smarty_tpl->_capture_stack[0][] = array('tabs', null, null); ob_start(); ?>
	<?php if (in_array('settings',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?><li><a href="index.php?module=SettingsAdmin">Настройки</a></li><?php }?>
	<?php if (in_array('currency',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?><li><a href="index.php?module=CurrencyAdmin">Валюты</a></li><?php }?>
	<?php if (in_array('delivery',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?><li><a href="index.php?module=DeliveriesAdmin">Доставка</a></li><?php }?>
	<?php if (in_array('payment',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?><li><a href="index.php?module=PaymentMethodsAdmin">Оплата</a></li><?php }?>
	<?php if (in_array('managers',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?><li><a href="index.php?module=ManagersAdmin">Менеджеры</a></li><?php }?>
	<?php if (in_array('cities',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?><li><a href="index.php?module=CitiesAdmin">Города доставки</a></li><?php }?>
	<li class="active"><a href="index.php?module=BonusAdmin">Бонус</a></li>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>

<?php $_smarty_tpl->tpl_vars['meta_title'] = new Smarty_variable("Бонус", null, 1);
if ($_smarty_tpl->parent != null) $_smarty_tpl->parent->tpl_vars['meta_title'] = clone $_smarty_tpl->tpl_vars['meta_title'];?>
<script>
	var limit_rows = <?php echo count($_smarty_tpl->tpl_vars['limits']->value);?>
;
</script>
<script>
$(function(){
//продумать удаление изображений с разных id
	// Удаление изображений
	$(".images a.delete").live('click', function() {
		 $(this).closest("li").fadeOut(200, function() { $(this).remove(); });
		 return false;
	});
});
</script>


	<script src="design/js/autocomplete/jquery.autocomplete-min.js"></script>
	<script src="design/js/jquery/datepicker/jquery.ui.datepicker-ru.js"></script>
	

<?php if ($_smarty_tpl->tpl_vars['message_success']->value) {?>
<!-- Системное сообщение -->
<div class="message message_success">
	<span class="text"><?php if ($_smarty_tpl->tpl_vars['message_success']->value=='saved') {?>Настройки сохранены<?php }?></span>
	<?php if ($_GET['return']) {?>
	<a class="button" href="<?php echo $_GET['return'];?>
">Вернуться</a>
	<?php }?>
</div>
<!-- Системное сообщение (The End)-->
<?php }?>

<?php if ($_smarty_tpl->tpl_vars['message_error']->value) {?>
<!-- Системное сообщение -->
<div class="message message_error">
	<span class="text"><?php if ($_smarty_tpl->tpl_vars['message_error']->value=='watermark_is_not_writable') {?>Установите права на запись для файла <?php echo $_smarty_tpl->tpl_vars['config']->value->watermark_file;?>
<?php }?></span>
	<a class="button" href="">Вернуться</a>
</div>
<!-- Системное сообщение (The End)-->
<?php }?>


<!-- Основная форма -->
<form method="post" action="" enctype='multipart/form-data' id="product" >
<input type=hidden name="session_id" value="<?php echo $_SESSION['id'];?>
">
<div id="name">
<h1><?php if ($_smarty_tpl->tpl_vars['bonuss']->value->id) {?>Заказ №<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['bonuss']->value->id, ENT_QUOTES, 'UTF-8', true);?>
<?php } else { ?>Новый Бонус<?php }?></h1>
<div id=next_order>
	<a title="Назад" class=prev_order href="index.php?module=BonusAdmin">←</a>
	</div>
</div>
<!-- Общие параметры -->
	<div class="block">
		<h2>Общие данные бонуса</h2>
		<ul>
			<li><label class=property>Имя Бонуса</label><input name="bonus_name" class="simpla_inp" type="text" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['bonus']->value->name, ENT_QUOTES, 'UTF-8', true);?>
" /></li>
			<li><label class=property>Анонс</label><input name="bonus_preview" class="simpla_inp" type="text" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['bonus']->value->preview, ENT_QUOTES, 'UTF-8', true);?>
" /></li>
			<li><label class=property>Полное описание</label><input name="bonus_description" class="simpla_inp" type="text" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['bonus']->value->description, ENT_QUOTES, 'UTF-8', true);?>
" /></li>
		</ul>
	</div>
<!-- Изображения Бонуса -->
	<div class="block layer images">
		<h2>Изображения бонуса</h2>
		 <h3>Анонс</h3>
		<?php if ($_smarty_tpl->tpl_vars['bonus']->value->{$_smarty_tpl->tpl_vars['image_prev']->value}) {?>
		<ul><li>
			<a href='#' class="delete"><img src='design/images/cross-circle-frame.png'></a>
			<img src="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['resize'][0][0]->resize_modifier($_smarty_tpl->tpl_vars['bonus']->value->{$_smarty_tpl->tpl_vars['image_prev']->value},100,100);?>
" alt="" />
			<input type=hidden name='images[]' value='<?php echo $_smarty_tpl->tpl_vars['bonus']->value->{$_smarty_tpl->tpl_vars['image_prev']->value}->id;?>
'>//надо ли??
		</li></ul>
		<?php }?>
		<div id="add_image"></div>
		<span class=upload_image><input id="fileToUpload" type="file" name="image_prev" multiple accept=".jpg,.jpeg,.png,.gif"></span>
		<div id="add_image"></div>
		<h3>Основное</h3>
		<?php if ($_smarty_tpl->tpl_vars['bonus']->value->{$_smarty_tpl->tpl_vars['image']->value}) {?>
		<ul><li>
			<a href='#' class="delete"><img src='design/images/cross-circle-frame.png'></a>
			<img src="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['resize'][0][0]->resize_modifier($_smarty_tpl->tpl_vars['bonus']->value->{$_smarty_tpl->tpl_vars['image']->value},100,100);?>
" alt="" />
			<input type=hidden name='images[]' value='<?php echo $_smarty_tpl->tpl_vars['bonus']->value->{$_smarty_tpl->tpl_vars['image']->value}->id;?>
'>//надо ли??
		</li></ul>
		<?php }?>
		<div id="add_image"></div>
		<span class=upload_image><input id="fileToUpload" type="file" name="image_big" multiple accept=".jpg,.jpeg,.png,.gif"></span>
		<div id="add_image"></div>
	</div>
	<div class="block layer">
		<h2>Условия бонуса</h2>
		<ul>
			<li><label class=property>Дата заказа:</label><input name="bonus_date_order" class="simpla_inp" type="date" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['bonus']->value->dateorder, ENT_QUOTES, 'UTF-8', true);?>
" /></li>
			<li><label class=property>Акция c:</label><input name="bonus_date_ac_from" class="simpla_inp" type="date" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['bonus']->value->dateacfrom, ENT_QUOTES, 'UTF-8', true);?>
" /></li>
			<li><label class=property>по:</label><input name="bonus_date_ac_to" class="simpla_inp" type="date" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['bonus']->value->dateacto, ENT_QUOTES, 'UTF-8', true);?>
" /></li>
			<li><label class=property>Срок действия c:</label><input name="bonus_date_from" class="simpla_inp" type="date" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['bonus']->value->datefrom, ENT_QUOTES, 'UTF-8', true);?>
" /></li>
			<li><label class=property>по:</label><input name="bonus_date_to" class="simpla_inp" type="date" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['bonus']->value->dateto, ENT_QUOTES, 'UTF-8', true);?>
" /></li>
		</ul>
		<div id="column_left">
		<?php  $_smarty_tpl->tpl_vars['city'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['city']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['cities']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['city']->key => $_smarty_tpl->tpl_vars['city']->value) {
$_smarty_tpl->tpl_vars['city']->_loop = true;
?>
			<span><?php echo $_smarty_tpl->tpl_vars['city']->value;?>
</span>
		<?php } ?>
		
			<ul><li><label class=property>Города:</label><select name="city[]" multiple="multiple" size="20" style="height:300px;">
			<?php  $_smarty_tpl->tpl_vars['city'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['city']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['delivery_city']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['city']->key => $_smarty_tpl->tpl_vars['city']->value) {
$_smarty_tpl->tpl_vars['city']->_loop = true;
?>
				<option value="<?php echo $_smarty_tpl->tpl_vars['city']->value->id;?>
"><?php echo $_smarty_tpl->tpl_vars['city']->value->name;?>
</option>
			<?php } ?>
			</select></li></ul>
		</div>
		<div id="column_right">
			<ul><li><label class=property>Бренды:</label><select name="brend[]" multiple="multiple" size="20" style="height:300px;">
			
			</select></li></ul>
		</div>
		
		<h3>Товары</h3>
		<label class=property>Добавить товар:</label>
		
		
		<div id="add_purchase">
			<input type=text name=related id='add_purchase' class="input_autocomplete" placeholder='Выберите товар чтобы добавить его'>
		</div>
	
	
	
	
	</div>
	<div class="block layer">
		<h2>Файл CSV</h2>
		<ul><li><label class=property>Выберите файл CSV</label><input type="file" class="import_file" name="csv_file" id="csv_file" accept=".csv" /></li></ul>
	</div>
	
	<input class="button_green button_save" type="submit" name="save" value="Сохранить" />
</form><?php }} ?>
