{* Шаблон текстовой страницы *}

{* Канонический адрес страницы *}
{$canonical="/{$page->url}" scope=parent}
<section class="section">
	<div class="wrapper">
		<h1 data-page="{$page->id}">{$page->header|escape}</h1>
		{$page->body}
	</div>
</section>
