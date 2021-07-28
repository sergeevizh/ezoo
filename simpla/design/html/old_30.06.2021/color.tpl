{capture name=tabs}
	{if in_array('products', $manager->permissions)}<li><a href="index.php?module=ProductsAdmin">Товары</a></li>{/if}
	{if in_array('categories', $manager->permissions)}<li><a href="index.php?module=CategoriesAdmin">Категории</a></li>{/if}
	{if in_array('brands', $manager->permissions)}<li><a href="index.php?module=BrandsAdmin">Бренды</a></li>{/if}
	{if in_array('features', $manager->permissions)}<li><a href="index.php?module=FeaturesAdmin">Свойства</a></li>{/if}
	<li class="active"><a href="index.php?module=ColorsAdmin">Цвета</a></li>
	{if in_array('sizes', $manager->permissions)}<li><a href="index.php?module=SizesAdmin">Размеры</a></li>{/if}
{/capture}
{if $color->id}
{$meta_title = $color->name scope=parent}
{else}
{$meta_title = 'Новый цвет' scope=parent}
{/if}


{* On document load *}
{literal}
<link rel="stylesheet" media="screen" type="text/css" href="design/js/colorpicker/css/colorpicker.css" />
<script type="text/javascript" src="design/js/colorpicker/js/colorpicker.js"></script>

<script>
$(function() {

	// Удаление изображений
	$(".images a.delete").click( function() {
		$("input[name='delete_image']").val('1');
		$(this).closest("ul").fadeOut(200, function() { $(this).remove(); });
		return false;
	});

	$('#color_icon, #color_link').ColorPicker({
		color: $('#color_input').val(),
		onShow: function (colpkr) {
			$(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {
			$(colpkr).fadeOut(500);
			return false;
		},
		onChange: function (hsb, hex, rgb) {
			$('#color_icon').css('backgroundColor', '#' + hex);
			$('#color_input').val(hex);
			$('#color_input').ColorPickerHide();
		}
	});
});
</script>


{/literal}

{if $message_success}
<!-- Системное сообщение -->
<div class="message message_success">
	<span>{if $message_success == 'added'}Метка добавлена{elseif $message_success == 'updated'}Метка обновлена{/if}</span>
	{if $smarty.get.return}
	<a class="button" href="{$smarty.get.return}">Вернуться</a>
	{/if}
</div>
<!-- Системное сообщение (The End)-->
{/if}
<!-- Основная форма -->
<form method=post id=product enctype="multipart/form-data">
	<input type=hidden name="session_id" value="{$smarty.session.id}">
	<div id="name">
		<input class="name" name="name" type="text" value="{$color->name|escape}"/> 
		<input name=id type="hidden" value="{$color->id|escape}"/> 
		<div class="checkbox">
			<span id="color_icon" style="background-color:#{$color->color};" class="order_label_big"></span>
			<input id="color_input" name="color" class="simpla_inp" type="hidden" value="{$color->color|escape}" />
		</div>



	<input class="button_green button_save" type="submit" name="" value="Сохранить" />
		
	</div> 
	<div id="column_right">
	
		<!-- Изображение категории -->	
		<div class="block layer images">
			<h2>Изображение 35х35</h2>
			<input class='upload_image' name=image type=file>			
			<input type=hidden name="delete_image" value="">
			{if $color->image}
			<ul>
				<li>
					<a href='#' class="delete"><img src='design/images/cross-circle-frame.png'></a>
					<img src="../{$config->colors_images_dir}{$color->image}" alt="" />
				</li>
			</ul>
			{/if}
		</div>
		
	</div>
	
</form>
<!-- Основная форма (The End) -->