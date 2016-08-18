$(document).ready(function(){

	var url = $("#frm").attr('action');

	$(document).on('click', '.delete', function(e){

		e.preventDefault();

		var equipo = $(this).attr('data-id');

		var datos = {
			'equipo' : equipo
		}
	
		fun = $.xajax(datos, url+'/delete');
		fun.success(function (data)
		{
			if(data.estado)
			{
				$("#tr_"+equipo).addClass('danger').fadeOut('slow',function(){
	            	$(this).remove();
	            });

			}else{
				$.bootstrapGrowl(data.msg,{type:'danger'});
				$.log(data);
			}
		});

	});

	$(document).on('click', '.lista_usuarios', function(e){

		e.preventDefault();

		var equipo = $(this).attr('data-id');

		var datos = {
			'id' : equipo
		}
	
		fun = $.xajax(datos, url+'/getUsuarios');
		fun.success(function (data)
		{
			if(data.estado)
			{
				$.log(data);

				$("#nombre_equipo").text(data.nombre);

				if(data.usuarios){

					var lista = "<ul>";
					$.each(data.usuarios, function(a, usuario){
						lista += "<li>"+usuario.name+"</li>";
					});

					$("#listado-usuarios").html(lista);
				}else{
					$("#listado-usuarios").text("Sin resultados.");
				}

			}else{
				$.bootstrapGrowl(data.msg,{type:'danger'});
				$.log(data);
			}
		});
	});
});