{$meta_title = "Ваш заказ №`$order->id`" scope=parent}

<section class="section">
	<div class="wrapper">
		<section class="thankyou-container">
			<h1 style="width: 100%">Спасибо за заказ!</h1>
			<div class="col">
				<div class="subtitle">Ваш заказ №{$order->id}
					{if $order->status == 0}принят{/if}!
					{if $order->status == 1}в обработке{elseif $order->status == 2}выполнен{/if}
					{if $order->paid == 1}, оплачен{else}{/if}
					<br>
					<span style="font-size: 1.5rem;">
						В ближайшее время вам придет СМС с подтверждением!
					<br>
					Остались вопросы? Позвоните нам: <a href="tel:7255">7255</a>!
					</span>
				</div>
				{if !$order->paid && $delivery->id!=2}
					{* Выбор способа оплаты *}
					{if $payment_methods && !$payment_method && $order->total_price>0}
						<form method="post">
							<h2>Выберите способ оплаты</h2>
							<div class="checklist">
								{foreach $payment_methods as $payment_method}
									<label class="checklist__row">
										<input type="radio" class="checklist__input js-styler" name="payment_method_id" value="{$payment_method->id}" {if $payment_method@first}checked{/if}>
										<span class="checklist__text">
										{$payment_method->name}, к оплате {$order->total_price|convert:$payment_method->currency_id}&nbsp;{$all_currencies[$payment_method->currency_id]->sign}
										<div class="description">
											{$payment_method->description}
										</div>
									</span>
									</label>
								{/foreach}
							</div>
							<input type='submit' class="btn basket-btn" value='Закончить заказ'>
						</form>
						{* Выбраный способ оплаты *}
					{elseif $payment_method}
						<br>
						<!--<form method="post">
							<input class="btn btn_border" type="submit" name="reset_payment_method" value='Выбрать другой способ оплаты'>
						</form>-->
						<div class="js-add-class-btn">
							{* Форма оплаты, генерируется модулем оплаты *}
							{if $payment_method->id==15}
								{checkout_form order_id=$order->id module=$payment_method->module}
							{else}
								{checkout_form order_id=$order->id module=$payment_method_assist->module}

							{/if}
							<script>
								document.addEventListener("DOMContentLoaded", function() {
									document.querySelector('.js-add-class-btn [type=submit]').classList.add('btn', 'btn_large')
								});
							</script>
						</div>
					{/if}
				{/if}
				<a href="{$config->root_url}" class="thankyou-back">Вернуться на главную</a>
			</div>
			<div class="col">
				<img src="{$config->root_url}/design/{$settings->theme|escape}/images/thankyou-logo.png" alt="e-zoo">
				{if $bonus}
				<div style="margin-top:15px;text-align: center;">Поздравляю! Вам подарен бонус,<br> получите его <a href="./bonus/">здесь</a>!</div>
				{/if}
			</div>
		</section>
	</div>
</section>

