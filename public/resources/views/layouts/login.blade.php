<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- Bootstrap CSS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous"> <!-- Font Awesome 5 -->
<!-- jQuery library -->
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<link href="https://fonts.googleapis.com/css?family=Montserrat:500" rel="stylesheet">
<!--<link href="https://fonts.googleapis.com/css?family=Saira+Semi+Condensed" rel="stylesheet">-->
<!--<link href="https://fonts.googleapis.com/css?family=Oxygen" rel="stylesheet">-->
<link rel="icon" href="resources/images/favicon_64.png" />
<title>@yield('titulo')</title>
<style>
body, html {
	padding: 0px;
	margin: 0px;
	background: #f8f8f7;
	font-family: Montserrat, sans-serif;
	overflow: hidden;
	font-size: 14px;
}
#body {
	position: absolute;
	height: 100vh;
	width: 100vw;
	display: flex;
	justify-content: center;
	align-items: center;
	flex-direction: column;
	overflow: hidden;
}
.content {
	padding: 20px;
	max-width: 400px;
	width: 100%;	
}
.box-content, .msg-retorno {
	max-width: 800px;
	width:100%;
	margin-bottom: 1rem;
}
.box-content{
	background: #ffffff;
	border: 1px solid #eaeae8;
	display: flex;
	flex-direction:row;
	border-radius: 3px;
}
.content-image {
	max-width: 400px;
	width: 100%;
	background: url("@yield('loginFoto')");
	background-repeat: no-repeat;
}
.content > h3 {
	margin-top: 0;
	margin-bottom: 30px;
}
h3 > small {
	font-size:1.1rem;
}
.textbox {
	border:1px solid #D5D4D2;
	transition: all .3s ease-in-out;
}
.textbox:focus-within {
	box-shadow: 0 0 5px #cccccc;
}

input[type="text"],
input[type="password"] {
	width: 100%;
	height: 40px;
	line-height:24px;
	/*font-size: 1.5rem;*/
	padding: 5px 10px;
	border:none;
	outline: 0;
}
input[type="password"],
input[name="senha"] {
	width: calc(100% - 40px);
}
button {
	padding: 10px 20px;
	color: white;
	background-color: #006bb3;
	border: 0;
}
.senha-util {
	background: transparent;
	padding:0;
	color: #007acc;
	font-size: 1.5rem;
	width: 35px;
}

.msg-copyright {
	max-width: 800px;
	padding: 0 2rem;
	margin: 0 auto;
}
.body-head {
	position:fixed;
	top:0;
	left:0;
	width:100%;
	max-width: 100vw;
	background-color: #c4ffdb;
	height: 50px;
	
}
.body-head img {
	max-height: 50px;
	height: auto;
}

.body-foot {
	position:fixed;
	bottom:0;
	left:0;
	width:100%;
	max-width: 100vw;
	background-color: #dedede;
	padding: .8rem .5rem .8rem;
}
.bg-smo {
    background-color: rgba(0,121,107,1);
    color: rgb(236, 236, 236);
}
@media ( max-width: 991px ) {
	.content-image {
		display:none;
	}
	.box-content, .msg-copyright, .msg-retorno {
		max-width: 400px;
	}
}



</style>
<script>
function divClose() {
	$('#esqueceu_senha').fadeOut('fast');
}
function divOpen() {
	$('#esqueceu_senha').fadeIn('fast');
}
function esqueciSenha() {
	alert('Avise para os administradores.');
}
function verSenhas() {
	let alvo = $('.senha');
	if(alvo.attr('type') == 'password') {
		alvo.attr('type', 'text');
	} else {
		alvo.attr('type', 'password');
	}
}
$(document).ready(function(){
	$(document).on('click', '.senha-util',function(){
		var icon = $(event.target);
		// Verifica se há um TARGET definido
		if($(this).data('target') != ''){
			var alvo = $($(this).data('target'));
		} else {
			var alvo = $(this).siblings('input');
		}
		// Faz alterações
		if(alvo.attr('type') == 'password') {
			alvo.attr('type', 'text');
			icon.removeClass('glyphicon-eye-open').addClass('glyphicon-eye-close');
			$(this).attr('title','Esconder senha');
		} else {
			alvo.attr('type', 'password');
			icon.removeClass('glyphicon-eye-close').addClass('glyphicon-eye-open');
			$(this).attr('title','Mostrar senha');
		}
	});
});
</script>
</head>



<body>
	<div id="body">
		<div class="body-head">
			<img src="/resources/images/smo.png" class="bg-smo" style="padding: 0px .5rem; margin-right:.5rem;" alt="Logo do SMO"> Sistema de Mapas Online
		</div>
		<div class="msg-retorno">
			@yield('msgRetorno')
		</div>
		<div class="box-content">
			@yield('conteudo')
		</div>
		<div class="body-foot text-center">
		
		<div class="text-center msg-copyright">
			<strong>LSCB &copy;2017 - {{date('Y')}}</strong>. É proibido a reprodução ou cópia do conteúdo deste site para qualquer fim.
		</div>
		</div>
	</div>
</body>
<!-- Latest compiled JavaScript -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</html>