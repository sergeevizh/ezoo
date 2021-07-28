<div class="wrapper slider-wrapper">
	<section class="main-slider-block">
     <div class="main-slider js-main-slider">
      
      
      
         
      
      
      <div class="main-slider__item">
<a href="https://e-zoo.by/dostavka" class="main-slider__item-inner" data-echo-background="design/{$settings->theme|escape}/images/12_12_12.png">
<div class="main-slider__item-content">
</div>
</a>
</div> 
      
      
      
      <div class="main-slider__item">
<a href="https://e-zoo.by/dostavka" class="main-slider__item-inner" data-echo-background="design/{$settings->theme|escape}/images/89_89.png">
<div class="main-slider__item-content">
</div>
</a>
</div> 
      
      
      
      
      
     
       <div class="main-slider__item">
<a href="https://e-zoo.by/bonusoidy" class="main-slider__item-inner" data-echo-background="design/{$settings->theme|escape}/images/1_1_1.png">
<div class="main-slider__item-content">
</div>
</a>
</div> 
      
      
       <div class="main-slider__item">
<a href="#" class="main-slider__item-inner" data-echo-background="design/{$settings->theme|escape}/images/18_18.png">
<div class="main-slider__item-content">
</div>
</a>
</div> 
      
      
      <div class="main-slider__item">
<a href="https://docs.google.com/forms/d/e/1FAIpQLSenlfgW4_uWWAfq_vhEfHI5gXxUe8LcC86mjfhKBmF3NCbFig/viewform" class="main-slider__item-inner" data-echo-background="design/{$settings->theme|escape}/images/podpiska.png">
<div class="main-slider__item-content">
</div>
</a>
</div> 
      
       
       <div class="main-slider__item">
<a href="https://royalpromo.by/" class="main-slider__item-inner" data-echo-background="design/{$settings->theme|escape}/images/11111_111111.png">
<div class="main-slider__item-content">
</div>
</a>
</div>      
      
      
       <div class="main-slider__item">
<a href="https://e-zoo.by/dostavka" class="main-slider__item-inner" data-echo-background="design/{$settings->theme|escape}/images/leto.png">
<div class="main-slider__item-content">
</div>
</a>
</div>
   
      
       <div class="main-slider__item">
<a href="#" class="main-slider__item-inner" data-echo-background="design/{$settings->theme|escape}/images/dominos.png">
<div class="main-slider__item-content">
</div>
</a>
</div> 
      
      
      
       <div class="main-slider__item">
<a href="#" class="main-slider__item-inner" data-echo-background="design/{$settings->theme|escape}/images/J_2123.png">
<div class="main-slider__item-content">
</div>
</a>
</div> 
      
          
      
     
      <div class="main-slider__item">
<a href="https://e-zoo.by/bonusnaya-programma" class="main-slider__item-inner" data-echo-background="design/{$settings->theme|escape}/images/19_19.png">
<div class="main-slider__item-content">
</div>
</a>
</div>  
      
      
       
      <div class="main-slider__item">
<a href="https://e-zoo.by/bonusnaya-programma" class="main-slider__item-inner" data-echo-background="design/{$settings->theme|escape}/images/1_2_3.png">
<div class="main-slider__item-content">
</div>
</a>
</div>  
      
           
      
       <div class="main-slider__item">
<a href="https://e-zoo.by/brands/pogryzuhin" class="main-slider__item-inner" data-echo-background="design/{$settings->theme|escape}/images/111pogriz.png">
<div class="main-slider__item-content">
</div>
</a>
</div> 
            

               
      
                  
                 
       
     
      
     
      

		</div>
	</section>
</div>



<div class="for-mobile index-category-block">

	{if $categories}
		<ul>

			{foreach from=$categories item=item}

				<li><a href="/catalog/{$item->url}">
						<span class="bg-image">
							{if $item->image}
								<img src="../{$config->categories_images_dir}{$item->image}" alt="{$item->name|escape}">
							{else}
								<img src="../files/uploads/logo-mobile.png" alt="logo">
							{/if}
						</span>{$item->name}</a></li>


			{/foreach}

{*			<li class="li-actions"><a href="/actions">Акции</a></li>*}
		</ul>

	{/if}

</div>
