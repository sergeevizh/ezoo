<div class="wrapper slider-wrapper">
	<section class="main-slider-block">
     <div class="main-slider js-main-slider">
      
      
      
      
       <div class="main-slider__item">
<a href="https://e-zoo.by/schastlivyj-sluchaj" class="main-slider__item-inner" data-echo-background="design/{$settings->theme|escape}/images/СОЛНЕЧНЫЕ%20ДНИ,%20копия.png">
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
<a href="#" class="main-slider__item-inner" data-echo-background="design/{$settings->theme|escape}/images/M1.jpg">
<div class="main-slider__item-content">
</div>
</a>
</div>  
      
      
      
      
      <div class="main-slider__item">
<a href="#" class="main-slider__item-inner" data-echo-background="design/{$settings->theme|escape}/images/М4.jpg">
<div class="main-slider__item-content">
</div>
</a>
</div>  
      
      
      <div class="main-slider__item">
<a href="#" class="main-slider__item-inner" data-echo-background="design/{$settings->theme|escape}/images/М6.jpg">
<div class="main-slider__item-content">
</div>
</a>
</div>  
      
      
      
      <div class="main-slider__item">
<a href="#" class="main-slider__item-inner" data-echo-background="design/{$settings->theme|escape}/images/М7.jpg">
<div class="main-slider__item-content">
</div>
</a>
</div> 
      
      
      
      <div class="main-slider__item">
<a href="#" class="main-slider__item-inner" data-echo-background="design/{$settings->theme|escape}/images/М8.jpg">
<div class="main-slider__item-content">
</div>
</a>
</div> 
      
      
      
       <div class="main-slider__item">
<a href="#" class="main-slider__item-inner" data-echo-background="design/{$settings->theme|escape}/images/М9.jpg">
<div class="main-slider__item-content">
</div>
</a>
</div> 
      
    
      
      
       <div class="main-slider__item">
<a href="#" class="main-slider__item-inner" data-echo-background="design/{$settings->theme|escape}/images/М10.jpg">
<div class="main-slider__item-content">
</div>
</a>
</div> 
      
      
      
       <div class="main-slider__item">
<a href="#" class="main-slider__item-inner" data-echo-background="design/{$settings->theme|escape}/images/М11.jpg">
<div class="main-slider__item-content">
</div>
</a>
</div> 
      
      
      
       <div class="main-slider__item">
<a href="#" class="main-slider__item-inner" data-echo-background="design/{$settings->theme|escape}/images/М15.jpg">
<div class="main-slider__item-content">
</div>
</a>
</div> 
      
       
       <div class="main-slider__item">
<a href="#" class="main-slider__item-inner" data-echo-background="design/{$settings->theme|escape}/images/М16.jpg">
<div class="main-slider__item-content">
</div>
</a>
</div> 
      
       
       <div class="main-slider__item">
<a href="#" class="main-slider__item-inner" data-echo-background="design/{$settings->theme|escape}/images/М17.jpg">
<div class="main-slider__item-content">
</div>
</a>
</div> 
     
       
       <div class="main-slider__item">
<a href="#" class="main-slider__item-inner" data-echo-background="design/{$settings->theme|escape}/images/М18.jpg">
<div class="main-slider__item-content">
</div>
</a>
</div> 
     
      
      
       <div class="main-slider__item">
<a href="#" class="main-slider__item-inner" data-echo-background="design/{$settings->theme|escape}/images/М19.jpg">
<div class="main-slider__item-content">
</div>
</a>
</div> 
      
      
      
       <div class="main-slider__item">
<a href="#" class="main-slider__item-inner" data-echo-background="design/{$settings->theme|escape}/images/М20.jpg">
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
            

      <div class="main-slider__item">
<a href="https://e-zoo.by/bonusnaya-programma" class="main-slider__item-inner" data-echo-background="design/{$settings->theme|escape}/images/plato.png">
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
