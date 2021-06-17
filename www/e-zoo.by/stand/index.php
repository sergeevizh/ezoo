<a href="http://e-zoo.by/?utm_source=infokiosk&utm_medium=danamall">
	<img class="sale" src="img/sale.png">
	<img class="cat" src="img/cat.png">
	<div class="button">
		<img class="button-top" src="img/button-top.png">
		<img class="button-bottom" src="img/button-bottom.png">
	</div>
</a>

<style>
	body {
		margin: 0;
		overflow: hidden;
	}
	a {
		display: block;
		height: 100%;
		width: 100%;
		background: url('img/home.png') no-repeat center center / cover;
	}
	img {
		position: absolute;
	}
	img.sale {
		height: 300px;
		top: 350px;
		right: 150px;
		animation: sale 1.5s infinite ease-in-out;
		animation-delay: 0.5s;
	}
	img.cat {
		z-index: 10;
		animation: cat 1.5s infinite ease-in-out;
	}
	.button {
		position: absolute;
    display: flex;
    justify-content: center;
    align-items: flex-end;
    width: 713px;
    height: 800px;
		left: 183px;
		top: 800px;
	}
	img.button-top {
		bottom: 120px;
		animation: button 1.5s infinite ease-in-out;
		animation-delay: 0.5s;
	}

	@keyframes sale {
		20% {transform: scale(1.5)}
		to {transform: scale(1)}
	}
	@keyframes cat {
		from {transform: translateY(0)}
		50% {transform: translateY(520px)}
		to {transform: translateY(0)}
	}
	@keyframes button {
		from {transform: translateY(0)}
		20% {
			transform: translateY(120px);
			
		}
		to {transform: translateY(0)}
	}
</style>