$(document).ready(function(){

	var proyecto 	= 0;
	var fecha 		= null;
	var numb 		= 0;
	var url 		= $("#frm").attr('action');

	$(document).on('change', '#proyectos', function(){

		$("#bloques").html("");

		proyecto 	= $("#proyectos").val();
		fecha		= $("#fecha").val();

		if(proyecto == 0){return false;}
		if(fecha == ''){return false;}

		carga_registros();
	});

	$(document).on('click', '.add-block', function(){

		// verificamos si el proyecto está seleccionado
		if(proyecto == 0){
			alerta("¡Debe seleccionar un proyecto!", 'alert-warning');
			return  false;
		}else{

			numb++;

			// creamos el bloque en la bd
			idbloque = crear_bloque(proyecto, numb);

			if(!idbloque)
			{
				alerta("¡Lo sentimos, ha ocurrido un error al crear el bloque!", 'alert-danger');
				log("¡Error al crear bloque!");
				return false;
			}
		}
		

		// Añadimos el bloque
		jQuery('<div/>', {
		    id: 'bloque-'+idbloque,
		    class: 'col-md-12 bloque',
		    'data-num': numb
		}).appendTo('#bloques');

		// Añadimos el div superior del bloque
		jQuery('<div/>', {
		    id: 'tit-'+idbloque,
		    class: 'row col-md-12',
		    'data-num': numb,
		    html: 	"<div class='col-xs-9'><p class='num_bloque'>bloque #"+numb+"</p></div>"+
		    		"<div class='col-xs-3'>"+
		    		"<button class='btn btn-danger pull-right delete-bloque' data-bloque='"+idbloque+"'>"+
		    		"<i class='fa fa-trash-o' title='Eliminar bloque'></i></button>"+
		    		"<button class='btn btn-success pull-right add-actividad' data-bloque='"+idbloque+"'>"+
		    		"<i class='hi hi-plus' title='Guardar actividad'></i></button>"+
		    		"</div>"
		}).appendTo('#bloque-'+idbloque);

	});

	$(document).on('click', '.delete-bloque', function(){
		var bloque = $(this).attr('data-bloque');
		eliminar_bloque(bloque);
	});

	$(document).on('click', '.delete-act', function(){
		var actividad = $(this).attr('data-id');


		if(typeof actividad !== "undefined")
		{
			// si es una actividad creada en la bd la eliminamos
			eliminar_actividad(actividad);
		} else {
			// si no, solo removemos el elemento
			$(this).parent().parent().parent().remove();
		}

		
	});

	$(document).on('click', '.add-actividad', function(){

		idbloque = $(this).attr('data-bloque');
	
		// Añadimos una actividad al bloque correspondiente
		jQuery('<div/>', {
		    //id: 'act-'+idbloque,
		    class: 'col-md-12 actividad',
		    //'data-num': idbloque,
		    html: "<div class='form-horizontal'>"+
		    			"<div class='form-group col-md-6 inputAct'><input class='form-control input-actividad' id='input_act_"+idbloque+"' placeholder='Actividad' /></div> "+
		    			"<div class='form-group col-md-2 inputHE'><input class='form-control input-actividad' id='input_hh_"+idbloque+"' type='time' value='00:00' /></div> "+
		    			"<div class='form-group col-md-2 inputHR'><input class='form-control input-actividad' id='input_hhreal_"+idbloque+"' type='time' value='00:00' /></div> "+
		    			"<div class='form-group col-md-2'>"+
		    				"<a class='btn btn-default guardar-act' href='#' role='button' data-bloque='"+idbloque+"'>"+
		    				"<i class='hi hi-floppy_disk' title='Añadir actividad'></i></a>"+
		    				"<a class='btn btn-default delete-act' href='#' role='button'>"+
		    				"<i class='fa fa-trash-o' title='Eliminar actividad'></i></a>"+

		    			"</div> "+
		    	"	</div>"

		}).appendTo('#tit-'+idbloque);
	});


	$(document).on('click', '.guardar-act', function(){

		var $btn = $(this);

		idbloque 		= $(this).attr('data-bloque');

		if(typeof idbloque !== "undefined")
		{
			idActividad = 0;
		
		}else{
			idActividad = $(this).attr('data-id');

			if(typeof idActividad !== "undefined") {
				idbloque = false;
			}else{
				alerta("¡Lo sentimos, ha ocurrido un error al obtener actividad!", 'alert-danger');
				return false;
			}
		}

		var actividad 		= $(this).parent().parent().children(".inputAct").children().val();
		var horas 			= $(this).parent().parent().children(".inputHE").children().val();
		var horas_reales 	= $(this).parent().parent().children(".inputHR").children().val();


		if(actividad 	== ''){alerta("¡Favor ingresar la descripción de la actividad!", 'alert-warning'); return false;}
		if(horas 		== ''){alerta("¡Favor ingresar las horas planificadas!", 'alert-warning'); return false;}			
		if(proyecto 	== ''){alerta("¡Favor seleccionar un proyecto!", 'alert-warning'); return false;}

		var fecha		= $("#fecha").val();
		if(fecha 		== ''){alerta("¡Favor seleccionar una fecha!", 'alert-warning'); return false;}

		var datos = {
			'idbloque'		: idbloque,
			'idActividad'	: idActividad,
			'descripcion' 	: actividad,
			'horas'			: horas,
			'horas_reales'	: horas_reales,
			'proyecto' 		: proyecto,
			'fecha'			: fecha
		}
		 

		var aj = ajax(datos, 'guardarActividad');

		aj.success(function (data) {
			if(data.estado){
				$btn.removeAttr("data-bloque");
				$btn.attr('data-id', data.id);
				$btn.parent().parent().parent().attr("id", "act-"+data.id);
				$btn.parent().children(".delete-act").attr('data-id', data.id);

				$btn.children().removeClass("hi hi-floppy_disk");
				$btn.children().addClass("fa fa-refresh");
			}else{
				alerta(data.msg);
			}
		});


	});

	function crear_bloque(proyecto, numb)
	{
		var datos = {
			'proyecto' 	: proyecto,
			'fecha'		: fecha,
			'orden' 	: numb
		}
	
		var id = false;
		bloque = ajax(datos, 'crearBloque', false);

		bloque.success(function (data) {
			if(data.estado){
				id = data.id;
			}else{
				alerta(data.msg);
			}
		});

		return id;
	}

	function eliminar_bloque(bloque){
		var datos = {
			'bloque' 	: bloque
		}
	
		bloque = ajax(datos, 'deleteBloque');
		bloque.success(function (data)
		{
			if(data.estado)
			{
				// una vez eliminado, quitamos el div
				$("#bloque-"+data.id).addClass('danger').fadeOut('slow',function(){
	            	$(this).remove();
	            	updateNumBloques();
	            });
			}else{
				alerta(data.msg, 'alert-danger');
			}
		});
	}

	function eliminar_actividad(actividad)
	{
		var datos = {
			'actividad' 	: actividad
		}
	
		bloque = ajax(datos, 'deleteActividad');
		bloque.success(function (data)
		{
			if(data.estado)
			{
				// una vez eliminado, quitamos el div
				$("#act-"+data.id).addClass('danger').fadeOut('slow',function(){
	            	$(this).remove();
	            	updateNumBloques();
	            });
			}else{
				alerta(data.msg, 'alert-danger');
			}
		});
	}


	function carga_registros()
	{
		//se cargaran los bloques y actividades creadas, y se seteara el numero de bloques

		var datos = {
			'proyecto'	: 	proyecto,
			'fecha' 	: 	fecha
		}

		js = ajax(datos, 'cargarRegistros');
		js.success(function (data)
		{
			if(data.estado)
			{
				$("#num_bloques").val(data.nbloques);
				numb = data.nbloques;

				var html = renderHTMLBloques(data.bloques);

				$("#bloques").html(html);

			}else{
				alerta(data.msg, 'alert-danger');
			}
		});
	

		log("Se cargan los registros para el proyecto "+proyecto+" con fecha "+fecha);
		return true;
	}

	function renderHTMLBloques(bloques)//objeto
	{

		var numbloques = 0;

		$.each(bloques, function(a, b){

			// Añadimos el bloque
			var idbloque = b.id;
			numbloques++;


			jQuery('<div/>', {
			    id: 'bloque-'+idbloque,
			    class: 'col-md-12 bloque',
			    //'data-num': numb
			}).appendTo('#bloques');

			// Añadimos el div superior del bloque
			jQuery('<div/>', {
			    id: 'tit-'+idbloque,
			    class: 'row col-md-12',
			    'data-num': numbloques,
			    html: 	"<div class='col-xs-9'><p class='num_bloque'>bloque #"+numbloques+"</p></div>"+
			    		"<div class='col-xs-3'>"+
			    		"<button class='btn btn-danger pull-right delete-bloque' data-bloque='"+idbloque+"'>"+
			    		"<i class='fa fa-trash-o' title='Eliminar bloque'></i></button>"+
			    		"<button class='btn btn-success pull-right add-actividad' data-bloque='"+idbloque+"'>"+
			    		"<i class='hi hi-plus' title='Guardar actividad'></i></button>"+
			    		"</div>"
			}).appendTo('#bloque-'+idbloque);


			if(b.actividades){
				$.each(b.actividades, function(c, act){
					// Añadimos una actividad al bloque correspondiente
					jQuery('<div/>', {
					    id: 'act-'+act.id,
					    class: 'col-md-12 actividad',
					    //'data-num': idbloque,
					    html: "<div class='form-horizontal'>"+
					    			"<div class='form-group col-md-6 inputAct'><input class='form-control input-actividad' id='input_act_"+idbloque+"' 		type='text'	value='"+act.descripcion+"' placeholder='Actividad' /></div> "+
					    			"<div class='form-group col-md-2 inputHE'> <input class='form-control input-actividad' id='input_hh_"+idbloque+"' 		type='time' value='"+act.hh_estimadas+"' /></div> "+
					    			"<div class='form-group col-md-2 inputHR'> <input class='form-control input-actividad' id='input_hhreal_"+idbloque+"' 	type='time' value='"+act.hh_reales+"' /></div> "+
					    			"<div class='form-group col-md-2 '>"+
					    				"<a class='btn btn-default guardar-act' href='#' role='button' data-id='"+act.id+"'>"+
					    				"<i class='fa fa-refresh' title='Actualizar actividad'></i></a>"+
					    				"<a class='btn btn-default delete-act' href='#' role='button' data-id='"+act.id+"'>"+
		    							"<i class='fa fa-trash-o' title='Eliminar actividad'></i></a>"+
					    			"</div> "+
					    	"	</div>"

					}).appendTo('#tit-'+idbloque);
				});
			}
				


		});
	}

	function updateNumBloques()
	{

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

		jQuery('<div/>', {
		    class 	: 'alert '+tipo_alerta,
		    role 	: 'alert',
		    html 	: '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
		    			"<strong>Atención :</strong> "+msg
		}).appendTo('#message_error');

	}

	function log(msg){
		console.log(msg);
	}

});