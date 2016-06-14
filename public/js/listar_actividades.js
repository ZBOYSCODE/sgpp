$(document).ready(function(){

	var fecha 	= $("#fecha").val();
	var url 	= $("#frm").attr('action');

	carga_registros();

	$(document).on('change', '#fecha', function(){
		quitar_alerta();

		fecha = $("#fecha").val();
		carga_registros();
	});

	function carga_registros()
	{
		$("#data").html("");

		if(fecha == ''){return false;}

		var datos = {
			'fecha' 	: 	fecha
		}

		js = ajax(datos, 'cargarRegistros');

		js.success(function (data)
		{
			if(data.estado)
			{
				renderHTMLBloques(data.user);
			}else{
				alerta(data.msg, 'alert-danger');
			}
		});
	

		log("Se cargan los registros para la fecha "+fecha);
		return true;
	}

	function renderHTMLBloques(data){

		$("#data").append("<hr class='row'>");

		jQuery('<div/>', {
		    class: 'row',
		    html: 	"<div class='col-xs-4'>Proyecto</div> "+
	    			"<div class='col-xs-4'>Actividad</div> "+
	    			"<div class='col-xs-2'>HH Estimadas</div> "+
	    			"<div class='col-xs-2'>HH Reales</div> "
		}).appendTo('#data');


		$("#data").append("<hr class='row'>");


		$.each(data, function(id, b){

			

			jQuery('<div/>', {
			    id: 'usr-'+id,
			    class: 'row',
			    html: "<label>"+b.nombre+"</label>"
			}).appendTo('#data');


			if(b.actividades){
				$.each(b.actividades, function(c, act){
					// Añadimos una actividad al bloque correspondiente
					jQuery('<div/>', {
					    id: 'act-'+act.id,
					    class: 'row',
					    html: 	"<div class='form-group col-xs-4'>"+act.proyecto+"</div> "+
				    			"<div class='form-group col-xs-4'>"+act.descripcion+"</div> "+
				    			"<div class='form-group col-xs-2'>"+act.hh_estimadas+" Hrs.</div> "+
				    			"<div class='form-group col-xs-2'>"+act.hh_reales+" Hrs.</div> "

					}).appendTo('#usr-'+id);
				});
			}

			jQuery('<div/>', {
			    class: 'row',
			    html: 	"<div class='form-group col-xs-4 col-xs-offset-4'><strong>Total</strong></div>"+
		    			"<div class='form-group col-xs-2 '><strong>"+b.cntHrsE+" Hrs.</strong></div> "+
		    			"<div class='form-group col-xs-2'><strong>"+b.cntHrsR+" Hrs.</strong></div> "

			}).appendTo('#usr-'+id);

			$("#data").append("<hr class='row'>");

		});
	}

	function ajax(datos, metodo, async = true)
	{
		return $.ajax({
            async	: async,
            type 	: 'POST',
            data 	: datos,
            url 	: url+'/'+metodo,
            dataType: 'json',
            success : function(data)
            {
                log(data.msg);
                return data; 
            }
        });
	}

	function alerta(msg, tipo_alerta){

		quitar_alerta();

		jQuery('<div/>', {
		    class 	: 'alert '+tipo_alerta,
		    role 	: 'alert',
		    html 	: '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
		    			"<strong>Atención :</strong> "+msg
		}).appendTo('#message_error');

	}

	function quitar_alerta()
	{
		$("#message_error").children().addClass('danger').hide('fast', function(){
			$(this).remove();
		});
	}

	function log(msg){
		console.log(msg);
	}


});