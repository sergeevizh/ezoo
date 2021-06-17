{* Страница отдельной записи блога *}

{* Канонический адрес страницы *}
{$canonical="/blog/{$post->url}" scope=parent}


<section class="section section_entry">
	<div class="wrapper">
		<div class="page-entry">
			<!-- <div class="page-entry__header">
				<div class="page-entry__title"></div>
				<div class="page-entry__aside"></div>
			</div> -->

			{*<figure class="page-entry__figure">*}
				{*<img src="images/temp/img-1.jpg" alt="" class="page-entry__figure-img">*}
			{*</figure>*}

			<div class="page-entry__content">
				<h1 data-post="{$post->id}">{$post->name|escape}</h1>

				<div class="page-entry__date">{$post->date|date}</div>

				{$post->text}
			</div>
		</div>
	</div>
</section>


