$(document).ready(function(){

	var proyecto 	= 0;
	var fecha 		= null;
	var numb 		= 0;
	var num_act 	= 0;
	var url 		= $("#frm").attr('action');

	// array que tendrán los datos para crear los 'select'
	var slc_proy;
	var slc_estado;
	

	get_proyectos();
	get_estados();
	
	carga_registros();
	

	$(document).on('change', '#proyectos', function(){
		quitar_alerta();
		proyecto 	= 	$("#proyectos").val();
	});

	$(document).on('change', '#fecha', function(){
		quitar_alerta();
		carga_registros();
	});

	$(document).on('change', '.inputEstado select', function(){
		cambio_color(this);
	});

	$(document).on('click', '.add-block', function(){

		quitar_alerta();

		numb++;

		// creamos el bloque en la bd
		idbloque = crear_bloque(proyecto, numb);

		if(!idbloque)
		{
			alerta("¡Lo sentimos, ha ocurrido un error al crear el bloque!", 'alert-danger');
			$.log("¡Error al crear bloque!");
			return false;
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
		    class: 'row',
		    'data-num': numb,
		    html: 	"<div class='col-xs-9'>"+
		    			"<p class='num_bloque' id='hrs-"+idbloque+"'>Horas estimadas : <span class='hrsE'>00:00</span> hrs."+
		    			"<br>Horas reales : <span class='hrsR'>00:00</span> hrs.</p>"+
		    		"</div>"+
		    		"<div class='col-xs-3'>"+
		    		"<button class='btn btn-danger pull-right delete-bloque' data-bloque='"+idbloque+"'>"+
		    		"<i class='fa fa-trash-o' title='Eliminar bloque'></i></button>"+
		    		"<button class='btn btn-success pull-right add-actividad' data-bloque='"+idbloque+"'>"+
		    		"<i class='hi hi-plus' title='Guardar actividad'></i></button>"+
		    		"</div>"
		}).appendTo('#bloque-'+idbloque);

	});

	$(document).on('click', '.delete-bloque', function(){
		quitar_alerta();
		var bloque = $(this).attr('data-bloque');
		eliminar_bloque(bloque);
	});

	$(document).on('click', '.delete-act', function(){
		quitar_alerta();
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

	$(document).on('change', '.inhhe', function(){// input ingreso horas estimadas

		var hrs = $(this).val();
		var bloque = $(this).attr('id');

		bloque = bloque.split("_");
		bloque = bloque[2];

		var $span = $("#hrs-"+bloque+" .hrsE");
		var horas_bloque = $span.text();

		var totalHoras = sumar_horas_estimadas(bloque);

		if(horamayor(totalHoras)){
			alerta("¡El total de horas es mayor al configurado por bloque!", 'alert-warning');
		}
		
		$span.text(totalHoras);
	});

	$(document).on('change', '.inhhr', function(){// input ingreso horas estimadas

		var hrs = $(this).val();
		var bloque = $(this).attr('id');

		bloque = bloque.split("_");
		bloque = bloque[2];

		var $span = $("#hrs-"+bloque+" .hrsR");
		var horas_bloque = $span.text();

		var totalHoras = sumar_horas_reales(bloque);
		
		$span.text(totalHoras);
	});

	$(document).on('click', '.add-actividad', function(){
		
		quitar_alerta();
		
	
		idbloque = $(this).attr('data-bloque');
	
		// Añadimos una actividad al bloque correspondiente
		jQuery('<div/>', {
		    //id: 'act-'+idbloque,
		    class: 'col-md-12 actividad',
		    //'data-num': idbloque,
		    html: 	"<label>Nueva actividad</label>"+

		    		"<div class='row'>"+
		    			"<div class='form-group col-md-4 col-md-offset-1 inputProy'><select class='form-control input-actividad' id='input_proy_"+idbloque+""+num_act+"'><option value='0'>Seleccione</option></select></div>"+
		    			"<div class='form-group col-md-2 inputHE'><input class='form-control input-actividad inhhe' id='input_hh_"+idbloque+"' type='time' value='00:00' /></div> "+
		    			"<div class='form-group col-md-2 inputHR'><input class='form-control input-actividad inhhr' id='input_hhreal_"+idbloque+"' type='time' value='00:00' /></div> "+
		    			"<div class='form-group col-md-1'>"+
		    				"<a class='btn btn-primary form-control guardar-act' href='#' role='button' data-bloque='"+idbloque+"' data-proyecto='"+proyecto+"'>"+
		    				"<i class='hi hi-floppy_disk' title='Añadir actividad'></i></a>"+
		    			"</div> "+
		    			"<div class='form-group col-md-1'>"+
		    				"<a class='btn btn-danger delete-act form-control' href='#' role='button'>"+
		    				"<i class='fa fa-trash-o' title='Eliminar actividad'></i></a>"+
		    			"</div> "+
		    			"<div class='form-group col-md-8 col-md-offset-1 inputAct'><input class='form-control input-actividad' id='input_act_"+idbloque+"' placeholder='Actividad' /></div> "+
		    			"<div class='form-group col-md-2 inputEstado'><select class='form-control input-actividad' id='input_estado_"+idbloque+""+num_act+"'></select></div>"+
		    			
		    	"	</div>"

		}).appendTo('#tit-'+idbloque);

		$("#input_proy_"+idbloque+""+num_act).renderSelect(slc_proy);
		$("#input_estado_"+idbloque+""+num_act).renderSelect(slc_estado, 1);
		$("#input_estado_"+idbloque+""+num_act).addClass('estado_1');

		num_act++;
	});


	$(document).on('click', '.guardar-act', function(){

		quitar_alerta();

		var $btn = $(this);

		idbloque 		= $(this).attr('data-bloque');

		var idproyecto 	= $(this).parent().parent().children(".inputProy").children().val();

		if(typeof idbloque !== "undefined")
		{
			// se quiere crear una nueva actividad
			idActividad = 0;
			
			if(idproyecto 	== 0){
				alerta("¡Favor seleccionar un proyecto!", 'alert-warning');
				return false;
			}
		
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
		var estado 			= $(this).parent().parent().children(".inputEstado").children().val();

		if(estado 		== 0) {alerta("¡Favor seleccione estado de la actividad!", 'alert-warning'); return false;}
		if(actividad 	== ''){alerta("¡Favor ingresar la descripción de la actividad!", 'alert-warning'); return false;}
		if(horas 		== ''){alerta("¡Favor ingresar las horas planificadas!", 'alert-warning'); return false;}			
		

		var fecha		= $("#fecha").val();
		if(fecha 		== ''){alerta("¡Favor seleccionar una fecha!", 'alert-warning'); return false;}

		var datos = {
			'idbloque'		: idbloque,
			'idActividad'	: idActividad,
			'descripcion' 	: actividad,
			'horas'			: horas,
			'horas_reales'	: horas_reales,
			'proyecto' 		: idproyecto,
			'estado'		: estado,
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

				$btn.parent().parent().parent().children('label').text(data.nombre_proyecto);

				alerta('¡Actividad guardada con exito!', 'alert-success');
			}else{
				alerta(data.msg, 'alert-danger');
			}
		});


	});

	function crear_bloque(proyecto, numb)
	{

		fecha		= $("#fecha").val();
		if(fecha == ''){return false;}

		var datos = {
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
	            });
			}else{
				alerta(data.msg, 'alert-danger');
			}
		});
	}

	function get_estados()
	{
		var datos = {};

		js = ajax(datos, 'getEstados');
		js.success(function (data)
		{
			if(data.estado)
			{
				slc_estado = data.estados;

			}else{
				alerta(data.msg, 'alert-danger');
			}
		});
	}

	function get_proyectos()
	{
		var datos = {};

		js = ajax(datos, 'getProyectos');
		js.success(function (data)
		{
			if(data.estado)
			{
				slc_proy = data.proyectos;

			}else{
				alerta(data.msg, 'alert-danger');
			}
		});
	}

	function carga_registros()
	{

		$("#bloques").html("");

		fecha		= $("#fecha").val();
		if(fecha == ''){return false;}


		//se cargaran los bloques y actividades creadas, y se seteara el numero de bloques

		var datos = {
			//'proyecto'	: 	proyecto,
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
	

		$.log("Se cargan los registros para el proyecto "+proyecto+" con fecha "+fecha);
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
			    class: 'row',
			    'data-num': numbloques,
			    html: 	"<div class='col-xs-9'>"+
			    			"<p class='num_bloque' id='hrs-"+idbloque+"'>Horas estimadas : <span class='hrsE'>"+b.cntHrsE+"</span> hrs."+
			    			"<br>Horas reales : <span class='hrsR'>"+b.cntHrsR+"</span> hrs.</p>"+
			    		"</div>"+
			    		"<div class='col-xs-3'>"+
			    		"<button class='btn btn-danger pull-right delete-bloque' data-bloque='"+idbloque+"'>"+
			    		"<i class='fa fa-trash-o' title='Eliminar bloque'></i></button>"+
			    		"<button class='btn btn-success pull-right add-actividad' data-bloque='"+idbloque+"'>"+
			    		"<i class='hi hi-plus' title='Guardar actividad'></i></button>"+
			    		"</div>"
			}).appendTo('#bloque-'+idbloque);


			if(b.actividades){
				$.each(b.actividades, function(c, act){

					var nombre_proyecto = $("#proyectos [value='"+act.proyecto_id+"']").text();
					// Añadimos una actividad al bloque correspondiente
					jQuery('<div/>', {
					    id: 'act-'+act.id,
					    class: 'col-md-12 actividad color-estado'+act.estado_id,
					    //'data-num': idbloque,
					    html: 	"<label>"+nombre_proyecto+"</label>"+
					    		"<div class='row'>"+
					    			"<div class='form-group col-md-4 col-md-offset-1 inputProy'><select class='form-control input-actividad' id='input_proy_"+idbloque+"_act"+act.id+"'><option>Seleccione</option></select></div>"+
					    			"<div class='form-group col-md-2 inputHE'> <input class='form-control input-actividad inhhe' id='input_hh_"+idbloque+"' 		type='time' value='"+act.hh_estimadas+"' /></div> "+
					    			"<div class='form-group col-md-2 inputHR'> <input class='form-control input-actividad inhhr' id='input_hhreal_"+idbloque+"' 	type='time' value='"+act.hh_reales+"' /></div> "+
					    			
					    			"<div class='form-group col-md-1 '>"+
					    				"<a class='btn btn-primary guardar-act form-control' href='#' role='button' data-id='"+act.id+"' data-proyecto='"+act.proyecto_id+"'>"+
					    				"<i class='fa fa-refresh' title='Actualizar actividad'></i></a>"+
					    			"</div>"+
					    			"<div class='form-group col-md-1 '>"+
					    				"<a class='btn btn-danger delete-act form-control' href='#' role='button' data-id='"+act.id+"'>"+
		    							"<i class='fa fa-trash-o' title='Eliminar actividad'></i></a>"+
					    			"</div> "+

					    			"<div class='form-group col-md-8 col-md-offset-1 inputAct'><input class='form-control input-actividad' id='input_act_"+idbloque+"' 		type='text'	value='"+act.descripcion+"' placeholder='Actividad' /></div> "+
					    			"<div class='form-group col-md-2 inputEstado'><select class='form-control input-actividad estado_"+act.estado_id+"' id='input_estado_"+idbloque+"_act"+act.id+"'></select></div>"+

					    	"	</div>"

					}).appendTo('#tit-'+idbloque);

					$("#input_proy_"+idbloque+"_act"+act.id).renderSelect(slc_proy, act.proyecto_id);
					$("#input_estado_"+idbloque+"_act"+act.id).renderSelect(slc_estado, act.estado_id);
				});
			}
				


		});
	}

	function cambio_color(obj)
	{
		var id = $(obj).val();

		$(obj).removeClass();

		$(obj).addClass("form-control input-actividad estado_"+id);
	}

	function horamayor(hrs)
	{
		var datos = {
			'horas' : hrs
		}

		dif = ajax(datos, 'diferenciaHoraBloque');
		dif.success(function (data)
		{
			if(!data.estado)
			{
				alerta(data.msg, 'alert-warning');
			}
		});
	}

	function sumar_horas_estimadas(bloque)
	{
		var $ele = $("#tit-"+bloque).children(".actividad");
		var total = '00:00';

		$.each($ele, function(a,actividad){
			var hra = $(actividad).children().children('.inputHE').children().val();
			total = difhrs(total, hra);
		});

		return total;
	}

	function sumar_horas_reales(bloque)
	{
		var $ele = $("#tit-"+bloque).children(".actividad");
		var total = '00:00';

		$.each($ele, function(a,actividad){
			var hra = $(actividad).children().children('.inputHR').children().val();
			total = difhrs(total, hra);
		});

		return total;
	}

	function difhrs(hr1, hr2){

		horas1 = hr1.split(":");
		horas2 = hr2.split(":");

		horatotale=new Array();

		
		for(a=0;a<2;a++)
		{
		  horas1[a]=(isNaN(parseInt(horas1[a])))?0:parseInt(horas1[a])
		  horas2[a]=(isNaN(parseInt(horas2[a])))?0:parseInt(horas2[a])

		  horatotale[a]=(horas1[a]+horas2[a]);
		}

		horatotal=new Date()
		horatotal.setHours(horatotale[0]);
		horatotal.setMinutes(horatotale[1]);

		return  horatotal.getHours()+":"+horatotal.getMinutes();
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
                $.log(data.msg);
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

});