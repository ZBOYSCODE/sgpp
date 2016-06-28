$(document).ready(function(){

	var fecha 	= $("#fecha").val();
	var url 	= $("#frm").attr('action');

	carga_registros();

	$(document).on('change', '#fecha', function(){
		quitar_alerta();

		fecha = $("#fecha").val();
		carga_registros();
	});

	$(document).on('click', '#btn_filtrar', function(e){

		e.preventDefault();
		carga_registros();

	});

	function carga_registros()
	{
		$("#data").html("");

		if(fecha == ''){return false;}

		proyecto 	= $("#proyectos").val();
		estado 		= $("#estados").val();
		usuario 	= $("#usuarios").val();

		if( $('#sin_hr').prop('checked') ) {
		    hhr = true;
		}else{
			hhr = false;
		}

		var datos = {
			'fecha' 	: 	fecha,
			'proyecto'	: 	proyecto,
			'usuario' 	: 	usuario,
			'hhr' 		: 	hhr,
			'estado'	: 	estado
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
		    html: 	"<div class='row bg-info cab-list-act'>"+
		    		"<div class='col-xs-2'><strong>Proyecto</strong></div> "+
	    			"<div class='col-xs-4'><strong>Actividad</strong></div> "+
	    			"<div class='col-xs-2'><strong>HH Estimadas</strong></div> "+
	    			"<div class='col-xs-2'><strong>HH Reales</strong></div> " +
	    			"<div class='col-xs-2'><strong>Estado</strong></div> "+
	    			"</div>"
		}).appendTo('#data');


		$.each(data, function(id, b){

			

			jQuery('<div/>', {
			    id: 'usr-'+id,
			    class: 'row',
			    html: "<div class='row'><div class='form-group col-md-12'><strong>"+b.nombre+"</<strong></div></div>"
			}).appendTo('#data');


			if(b.actividades){
				$.each(b.actividades, function(c, act){
					// Añadimos una actividad al bloque correspondiente
					jQuery('<div/>', {
					    id: 'act-'+act.id,
					    class: 'row',
					    html: 	"<div class='form-group col-xs-2'>"+act.proyecto+"</div> "+
				    			"<div class='form-group col-xs-4'>"+act.descripcion+"</div> "+
				    			"<div class='form-group col-xs-2'>"+act.hh_estimadas+" Hrs.</div> "+
				    			"<div class='form-group col-xs-2'>"+act.hh_reales+" Hrs.</div> "+
				    			"<div class='form-group col-xs-2'>"+act.estado+"</div> "

					}).appendTo('#usr-'+id);
				});
			}

			jQuery('<div/>', {
			    class: 'row bg-warning',
			    html: 	"<div class='form-group col-xs-4 col-xs-offset-2'><strong>Total</strong></div>"+
		    			"<div class='form-group col-xs-2 '><strong>"+IntToTime(b.cntHrsE)+" Hrs.</strong></div> "+
		    			"<div class='form-group col-xs-2'><strong>"+IntToTime(b.cntHrsR)+" Hrs.</strong></div> "

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

	function IntToTime(int)
        {
            var min = int % 60;//min
            var hrs = Math.floor(int / 60);//hrs

            if(min<10){
                min = "0"+min;
            }

            if(hrs<10){
                hrs = "0"+hrs;
            }

            return hrs+":"+min;
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