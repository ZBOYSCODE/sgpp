$(document).ready(function(){

	var url 		= $("#frm").attr('action');
	var ejecutar	= $("#ejecutar").attr('data-caso');

	// Ejecutar funciones dependiendo donde estemos
	switch(ejecutar){
		case 'list':
			//ejecutar funciones en la lista de tareas (tarea/index)
			cargar_lista_tareas();
	}


	$(document).on('click', '.tarea', function(){

		var tarea = $(this).attr('data-id');

		carga_tarea(tarea);
	});



	function cargar_lista_tareas()
	{
		var datos = {}

		fun = $.xajax(datos, url+'/getListaTareas');
		fun.success(function (data)
		{
			if(data.estado)
			{
				$.each(data.tareas, function(i, tarea){
	
					//draggable='true'
					var div = 	"<div  class='tarea tarea_prioridad_"+tarea.prior+"' id='tarea_"+tarea.id+"' data-id='"+tarea.id+"' data-toggle='modal' data-target='#data-tarea'>"+
									"<p><strong>"+tarea.nombre+"</strong></p>"+
									"<div class='data-tarea'>"+
										"<label class=''>"+tarea.proy+"</label>"+
										"<label class='pull-right'>"+tarea.fecha+"</label></div>"+
								"</div>";

					$("#td_estado_"+tarea.estado).append(div);

				});
				


			}else{
				$.bootstrapGrowl(data.msg,{type:'danger'});
				$.log(data);
			}
		});
	}

	function carga_tarea(id)
	{
		var datos = {
			'tarea':id
		}

		fun = $.xajax(datos, url+'/getTarea');
		fun.success(function (data)
		{
			if(data.estado)
			{
				$("#nombre_tarea").val();
				$("#nombre_proyecto").val();
				$("#desc_tarea").val();
				$("#fecha_tarea").val();

			
			}else{
				$.bootstrapGrowl(data.msg,{type:'danger'});
				$.log(data);
			}
		});
	}

});