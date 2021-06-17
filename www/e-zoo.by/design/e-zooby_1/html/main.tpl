{* Главная страница магазина *}

{* Для того чтобы обернуть центральный блок в шаблон, отличный от index.tpl *}
{* Укажите нужный шаблон строкой ниже. Это работает и для других модулей *}
{$wrapper = 'index.tpl' scope=parent}

{* Канонический адрес страницы *}
{$canonical="" scope=parent}


{include file="_main-slider-block.tpl"}

{include file="_main-catalog-section.tpl"}

{include file="_main-hits-section.tpl"}


{include file="_main-brands-section.tpl"}

{include file="_main-actions-section.tpl"}



{* TODO  на главной найти место для h1 и текста*}
<div class="wrapper">
	<br>
    <h1>{$page->header}</h1>

    {$page->body}
</div>


