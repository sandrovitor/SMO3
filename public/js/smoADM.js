var mapaCentro = {lat: -12.906985,  lng: -38.443157};
var mapProp = {
		center: mapaCentro,
		zoom:12
    };
    
    
function initMap(draggable = false, gps_marker = '')
{

	if(gps_marker == '') {
		map = new google.maps.Map(document.getElementById('mapsAPI'), mapProp);
		var marker = new google.maps.Marker({
			position: mapaCentro,
			map: map,
			draggable: draggable,
			animation: google.maps.Animation.DROP
		});
	} else {
		var x = gps_marker.split(",");
		var pos_marker = {lat: parseFloat(x[0]), lng: parseFloat(x[1])};
		
		map = new google.maps.Map(document.getElementById('mapsAPI'), {
			center: pos_marker,
			zoom: 16
		});
		
		var marker = new google.maps.Marker({
			position: pos_marker,
			map: map,
			draggable: draggable,
			animation: google.maps.Animation.DROP
		});
		
	}
	
	google.maps.event.addListener(marker, 'dragend', function(){
		var textGPS = '';
		textGPS = this.getPosition().lat().toFixed(6) + ',' + this.getPosition().lng().toFixed(6);
		$('[name="gps"], [name="gpsval"]').val(textGPS);
		/*document.getElementById('gps').value = textGPS;
		document.getElementById('gps1').value = textGPS;*/
	});
}

function getGPSAtual()
{
	if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(showPosition, GPSError);
	} else {
		alert('O navegador não possui suporte à esta função.');
	}
}

function showPosition(position)
{
	console.log(position);
	$('[name="gps"], [name="gpsval"]').val(position.coords.latitude.toFixed(6)+', '+position.coords.longitude.toFixed(6));
	// Atualiza mapa
	initMap(true, $('[name="gps"]').val());
}

function GPSError(error) {
	switch(error.code) {
		case error.PERMISSION_DENIED:
			alert("Usuário negou o acesso à localização.");
			break;
		case error.POSITION_UNAVAILABLE:
			alert("Informações da localização está indisponível.");
			break;
		case error.TIMEOUT:
			alert("O pedido para localização expirou.");
			break;
		case error.UNKNOWN_ERROR:
			alert("Erro desconhecido.");
			break;
	}
}

function motivodivToggle()
{
	if($('[name="ativo"]').find(':selected').val() == 'not' || $('[name="ocultar"]').find(':selected').val() == 'yes') {
		$('#motivo-div').slideDown('fast');
	} else {
		$('#motivo-div').slideUp('fast');
	}
}

function pendAction( pendId, confPend )
{
	$.ajax({
        url: "/admin/surdo/pendencias",
		method: "POST", // Método pode ser POST, GET ou outro verbo HTTP
		data: {
			pendId: pendId,
			confPend: confPend
		},
        processData: true, // Processar os dados antes do envio
        statusCode: codigoDeStatus, // Retorno do código de cabeçalho HTTP
        cache: false, // Não usar dados em cache
        success: function(data) {
			console.log(data);
			if(data == true) {
				
				$('#pendencia-'+pendId).slideUp('fast');
				$('[onclick^="showPendencia('+pendId+')"]').slideUp('fast');
				setTimeout(function(){
					$('#pendencia-'+pendId+', [onclick^="showPendencia('+pendId+')"]').remove();
				}, 700);
			} else {
				alert('O servidor retornou uma mensagem inesperada:'+"\n\n"+data);
			}
            
        }, // Em caso de sucesso
        error: function() {
            //alert("Ocorreu um erro");
        }, // Em caso de erro
        complete: function(jqXHR){
            //console.log(jqXHR);
        } // Quando a conexão finalizar (com sucesso ou erro)
    });
}


$(document).ready(function(){
    if($('#mapsAPI').length > 0 && $('[name="gps"]').data('gpsdisabled') !== true) {
        initMap(true, '');
	}
	
	$(document).on('click', '.form-check-div .form-check', function(){
		$(event.target).find('.form-check-input').trigger('click');
	});
	$(document).on('click', '.form-check-div .form-check-input', function(){
		for(a = 0; a < $('.form-check-div .form-check-input').length; a++) {
			let i = $('.form-check-div .form-check-input').eq(a);
			if($(i).prop('checked') == true) {
				$(i).parents('.form-check').addClass('active');
			} else {
				$(i).parents('.form-check').removeClass('active');
			}
		}
		
	});
});