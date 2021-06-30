{* Шаблон текстовой страницы *}
{$meta_title = "Бонусная программ" scope=parent}
{* Канонический адрес страницы *}
{$canonical="/{$page->url}" scope=parent}
<section class="section">
	<div class="wrapper">
		<h1 data-page="{$page->id}">{$page->header|escape}</h1>
		{$page->body}
		
		
		<div class="align-center bonus__btns">
          <button class="btn btn__bonusAll">Все бонусы</button>
          <button class="btn btn__bonusPersonal">Мои бонусы</button>
        </div>

        <div class="bonus__all">
          <ul class="bonus__list">
			  
		  {for $i=0;$i<$count_bonus;$i++}
			<li class="bonus__item">
			{if $bonus_all[$i]->verst==1}
              <a href="#modal__bonus-{$bonus_all[$i]->id}" class="bonus__item-img show__modal">
                <img src="{$bonus_all[$i]->img_preview}" alt="img-{$bonus_all[$i]->id}"
                  draggable="false" loading="lazy" title="img-{$bonus_all[$i]->id}">
              </a>
			  {/if}
			  {if $bonus_all[$i]->verst==2}
			    <a href="#modal__bonus-{$bonus_all[$i]->id}" class="bonus__item-img show__modal">
                <img
                  src="{$bonus_all[$i]->img_preview}"
                  alt="img-{$bonus_all[$i]->name}" draggable="false" loading="lazy" title="img-{$bonus_all[$i]->name}">
              </a>
			   {if $i+1<$count_bonus}
              <a href="#modal__bonus-{$bonus_all[$i+1]->id}" class="bonus__item-img show__modal">
                <img
                  src="{$bonus_all[$i+1]->img_preview}"
                  alt="img-{$bonus_all[$i+1]->name}" draggable="false" loading="lazy" title="img-{$bonus_all[$i+1]->name}">
              </a>
			  {else}
				<a class="bonus__item-img show__modal">
				<img src="simpla/files/bonus/white.jpg"
                  alt="white.jpg" draggable="false" loading="lazy" title="white.jpg">
				  </a>
			  {/if}
			  <span style="display:none">{$i++}</span>
			  {/if}
			  </li>
		  {/for}
	    </ul>
        </div>
        <div class="bonus__personal" >
 		  {if $user}
			 <ul class="bonus__list">
			  
		  {for $i=0;$i<$count_bonus_my;$i++}
			<li class="bonus__item">
			{if $bonus_my[$i]->verst==1}
              <a href="#modal__bonus-{$bonus_my[$i]->id}-my" class="bonus__item-img show__modal">
                <img src="{$bonus_my[$i]->img_preview}" alt="img-{$bonus_my[$i]->name}"
                  draggable="false" loading="lazy" title="img-{$bonus_my[$i]->name}">
              </a>
			  {/if}
			  {if $bonus_my[$i]->verst==2}
			    <a href="#modal__bonus-{$bonus_my[$i]->id}-my" class="bonus__item-img show__modal">
                <img src="{$bonus_my[$i]->img_preview}"
                  alt="img-{$bonus_my[$i]->name}" draggable="false" loading="lazy" title="img-{$bonus_my[$i]->name}">
              </a>
			  {if $i+1<$count_bonus_my}
              <a href="#modal__bonus-{$bonus_my[$i+1]->id}-my" class="bonus__item-img show__modal">
                <img src="{$bonus_my[$i+1]->img_preview}"
                  alt="img-{$bonus_my[$i+1]->name}" draggable="false" loading="lazy" title="img-{$bonus_my[$i+1]->name}">
              </a>
			  {else}
			   <a class="bonus__item-img show__modal">
				<img src="simpla/files/bonus/white.jpg"
                  alt="white.jpg" draggable="false" loading="lazy" title="white.jpg">
				  </a>
			  {/if}
			  <span style="display:none">{$i++}</span>
			  {/if}
			  </li>
		  {/for}
	    </ul>
		  
		  {else}
		   <div class="bonus__text align-center">
            <p>Пожалуйста, авторизуйтесь, чтобы увидеть свои бонусы.</p>
          </div>
		  {/if}
        </div>
		
		
		
		
		
	</div>
</section>
<!--Модальные окна формируем-->
{foreach $bonus_all as $bonus}
<div class="modal" id="modal__bonus-{$bonus->id}">
      <div class="modal__body __bonus">
        <div class="modal__content __bonus">
          <div class="modal__close close__modal"><span></span><span></span></div>
          <h3 class="modal__bonus-title">{$bonus->name}</h3>
          <div class="modal__bonus-img">
            <img src="{$bonus->img_detail}" alt="img-{$bonus->id}"
              draggable="false" loading="lazy" title="img-{$bonus->id}">
          </div>
          <div class="modal__bonus-text">
			{$bonus->desc_mini}
          </div> 
        </div>
      </div>
    </div>

{/foreach}
<!--user-->
{*убрать не авторизацию*}
{if $user}
<script>
function getbonus(id){
	button = "#bonus_btn_" + id;
	divtext= "#modal__bonus-text_all_" + id;
	$(button).css('display','none');
	$(divtext).css('display','block');
}
function getsale (id,percent){
document.cookie = 'bonus='+id+';path=/';
document.cookie = 'percent='+percent+';path=/';
$("#sale-percent-" + id).css('display','block');
}
</script>
{literal}
<script>
function getpromo(id,promo,user_id){
$("#sale-promo-" + id).css('display','block');
$("#sale-promo-" + id).css('display','block');
	/*если нужно ставить статус 0 запрос
	$.ajax({
		url: "./ajax/bonuspromo.php",
		type:"POST",
		data:{id_bonus: id, promo: promo, user_id:user_id},
		success:function(result){	
			if(result)
				$("#sale-promo-" + id).css('display','block');
			
		}});*/
}
</script>
{/literal}
{foreach $bonus_my as $bonus}
<div class="modal" id="modal__bonus-{$bonus->id}-my">
      <div class="modal__body __bonus">
        <div class="modal__content __bonus">
          <div class="modal__close close__modal"><span></span><span></span></div>
          <h3 class="modal__bonus-title">{$bonus->name}</h3>
          <div class="modal__bonus-img">
            <img src="{$bonus->img_detail}" alt="img-{$bonus->id}"
              draggable="false" loading="lazy" title="img-{$bonus->id}">
          </div>
          <div class="modal__bonus-text">
			{$bonus->desc_mini}
          </div>
			<a id="bonus_btn_{$bonus->id}" onclick="getbonus({$bonus->id})" class=" modal__bonus-btn btn">Получить бонус</a>
		   <div id="modal__bonus-text_all_{$bonus->id}" style="display:none;" class="modal__bonus-text">
			<div class="bonus__text align-center">Как получить бонус</div>
			{$bonus->description}
			<a class="modal__bonus-btn btn bonus__text align-center"
				{if $bonus->percent && $bonus->percent!=0}
					onclick="getsale({$bonus->id},{$bonus->percent});" 
				{/if}
				{if $bonus->promo && !empty($bonus->promo)}
					onclick="getpromo({$bonus->id},'{$bonus->promo}',{$user->id});" 
				{/if}
					>Активировать бонус</a>
			<div style="display:block" id="my_sale_bonus">
			{if $bonus->percent && $bonus->percent!=0}
				<div id="sale-percent-{$bonus->id}" style="display:none">Ваша скидка {$bonus->percent} % будет применена в <a href="/cart/">корзине</a> при оформлении заказа.</div>
			{/if}
			{if $bonus->promo && !empty($bonus->promo)}
				<div id="sale-promo-{$bonus->id}" style="display:none">Ваш промокод <noindex><a href="{$bonus->promo}" rel="nofollow" target="_blank">{$bonus->promo}</a></noindex> можете применить на сайте {$bonus->service}.</div>
			{/if}
			
			</div>
			
			
          </div>
		  
        </div>

      </div>
    </div>

{/foreach}

{/if}
    
