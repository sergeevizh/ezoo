{* Главная страница магазина *}

{* Для того чтобы обернуть центральный блок в шаблон, отличный от index.tpl *}
{* Укажите нужный шаблон строкой ниже. Это работает и для других модулей *}
{$wrapper = 'index.tpl' scope=parent}

{* Канонический адрес страницы *}
{$canonical="" scope=parent}
{include file="_main-slider-block.tpl"}

{include file="main-actions-section.tpl"}
<div class="index-form-late-courier">
	<div class="wrapper wrapper-late-content">
		<div class="late_courier_block">
			<p>Если к Вам опоздал курьер, нажмите <span><button class="late_courier_btn show-late_courier_btn">сюда</button></span></p>
		</div>
	</div>
</div>
<div class="index-form-subscribe">
    <div class="wrapper">
        <form method="post" id="form-subscribe">
            <div class="form-text">Хотите узнавать о новых скидках
                и специальных предложениях?</div>

            <input type="text" name="email" placeholder="Ваш e-mail"/>
            <input type="submit" name="send" value="Подписаться"/>
        </form>
    </div>


</div>
{include file="_main-hits-section.tpl"}

{include file="_main-catalog-section.tpl"}
{include file="_main-brands-section.tpl"}



{* TODO  на главной найти место для h1 и текста*}
<div class="wrapper home-description">
	<br>
    <h1>{$page->header}</h1>

    {$page->body}
	<button class="btn btn_border btn_small product__expand-link js-more-description"><span>Читать далее</span><span>Скрыть</span></button>
</div>
<div class="chPopUp-custom" id="other">
	<div class="close"></div>
	<div class="inner">
		<div class="need-coast">{$settings->other_city}</div>
	</div>
	<div class="continue">OK</div>
</div>
