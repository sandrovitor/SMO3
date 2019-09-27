<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- jQuery (necessario para os plugins Javascript do Bootstrap) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> <!-- jQuery -->
<script src="js/bootstrap.min.js"></script>
<!-- Bootstrap CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
<link href="https://fonts.googleapis.com/css?family=Montserrat:500" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Saira+Semi+Condensed" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Oxygen" rel="stylesheet">
<link rel="icon" href="resources/images/favicon_64.png" />
<title>SMO ::: LOGIN</title>
<style>
body {
	padding-top: 15px;
    background: #f1fef4;
}
.form-box {
	width: 300px;
	border: 1px solid #065a1a;
    background: #ffffff;
	margin: 0 auto;
	margin-top: 10px;
	padding: 30px 20px 10px;
	box-sizing: border-box;
	border-radius: 4px;
	box-shadow: 0px 0px 10px #999;
	position: relative;
}
.msg-copyright {
	width: 300px;
	box-sizing: border-box;
	padding: 5px;
	margin:0 auto;
	margin-top: 20px;
	margin-bottom: 10px;
	position:relative;
	font-family: 'Saira Semi Condensed', sans-serif;
	
}
hr {
	width:150px;
	border: 1px solid #eee;
}
.outbox {
	margin:0 auto;
	max-width:150px;
	
}

.textbox {
    padding:10px 15px;
    width: 100%;
	border-radius: 10px;
	border: 1px solid #bbb;
	outline: 0;
    transition: all .4s ease-in-out;
    font-weight: bold;
    color: #065a1a;
}
.textbox input[type='text'],
.textbox input[type='password'],
.textbox input[type='text']:FOCUS,
.textbox input[type='password']:FOCUS,
.textbox input[type='text']:ACTIVE,
.textbox input[type='password']:ACTIVE {
    height: 32px;
	font-family: 'Saira Semi Condensed', sans-serif;
	font-size: 18px;
    outline:0;
    border:0;
    letter-spacing: 1px;
}
.textbox input[type='password'] {
    width: 200px;
    max-width: 205px;
}
.textbox button {
    height: 32px;
    padding:0;
    padding-top: 5px;
    border:0;
    outline:0;
    background:none;
    font-size: 20px;
    float:right;
    color:#999;
    transition-duration: .3s
}
.textbox button:HOVER {
    color: #333;
}
.textbox:focus-within {
	border-color: #065a1a;
}
.fonte2 {
	font-family: 'Oxygen', sans-serif;
	font-weight: bold;
	font-size: 32px;
}
#esqueceu_senha {
	z-index: 100;
	position: absolute;
	top:0;
	left: 0;
	background: #1abaa4;
	width: 100%;
	height:100%;
	border-radius: 4px;
	box-sizing: border-box;
	padding: 20px;
	color: #fff;
	display:none;
}
#esqueceu_senha > h4 {
	color: #212326;
	margin-bottom: 20px;
}

/* ICON CLOSE */
.bar1, .bar2 {
	width: 25px;
	height: 3px;
	background-color: #444;
	margin-bottom: 5px;
}
.bar1 {
	transform: rotate(-45deg) translate(-5px, 0px);
}
.bar2 {
	transform: rotate(45deg) translate(-5px, -1px);
}
.icon-close {
	position:absolute;
	top: 30px;
	right: 15px;
	cursor: pointer;
}
/* ICON CLOSE */
</style>
<script>
function divClose() {
	$('#esqueceu_senha').fadeOut('fast');
}
function divOpen() {
	$('#esqueceu_senha').fadeIn('fast');
}
$(document).ready(function(){
	$(document).on('click', '.mostra_senha',function(){
		var icon = $(this).find('span.glyphicon');
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
	<h3 class="text-center fonte2">LS Castelo Branco
		<br><small>Sistema de Mapas Online</small>
	</h3>
	<div class="outbox" title="versão 3.0"><img class="outbox" src="resources/images/v3.png"></div>
	<hr>
	
	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-8 col-md-offset-2">
				@yield('msgRetorno')
			</div>
		</div>
	</div>

	<div class="form-box">
		<div id="esqueceu_senha">
			<h4>Esqueceu sua senha?</h4>
			Entre em contato com o administrador do sistema para que ele possa resetar sua senha e devolver seu acesso.
			
			<div class="icon-close" onclick="divClose()">
				<div class="bar1"></div>
				<div class="bar2"></div>
			</div>
		</div>
		<form action="login" method="post">
			
			<div class="form-group textbox">
				<input type="text" name="usuario" placeholder="login" required value="{{$_SESSION['user'] or ''}}">
			</div>
			<div class="form-group textbox">
				<input type="password" name="senha" placeholder="senha" required>
				<button type="button" data-target="" title="Mostrar senha" class="mostra_senha"><span class="glyphicon glyphicon-eye-open"></span></button>
			</div>
			<div class="form-group">
				<label style="margin-left: 14px; cursor:pointer;">
					<input type="checkbox" name="save_user" value="yes"> Lembrar meu usuário
				</label>
			</div>
			<div class="form-group text-center">
				<input type="hidden" name="urlBack" value="">
				<button type="submit" class="btn btn-sm btn-success"><span class="glyphicon glyphicon-log-in"></span> <strong>Acessar</strong></button>
				<button type="button" class="btn btn-sm btn-default" onclick="divOpen()">Esqueci minha senha</button>
			</div>
		</form>
	</div>
	<div class="text-center msg-copyright">
		<strong>LSCB &copy;2017</strong> É proibido a reprodução ou cópia do conteúdo deste site para qualquer fim.
	</div>
</body>
<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
</html>