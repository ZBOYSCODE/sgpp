$(document).ready(function(){

	var equipo = $("#equipo").val();

	if(equipo != '0' && equipo != 0 && equipo != 'undefined') { 
		carga_coordinadores(equipo);
	}

	$(document).on('change', '#equipo', function(){

		var equipo = $(this).val();

		if(equipo != '0' && equipo != 0 && equipo != 'undefined') { 
			carga_coordinadores(equipo);
		}

	});

	$(document).on('click', '.delete', function(e){

		e.preventDefault();

		var id = $(this).attr('data-id');

		if(confirm("¿Seguro desea eliminar este proyecto?")){
			deleteProyecto(id, $(this));
		}

	});

	$(document).on('click', '.activa', function(e){

		e.preventDefault();

		var id = $(this).attr('data-id');

		activa_proyecto(id, $(this));

	});


	function carga_coordinadores(equipo) {

		var url = $("#frm").attr('action');

		url += "/getCoordinadores";

		var datos = {
			'equipo' : equipo
		}

		js = $.xajax(datos, url);

		js.success(function (data)
		{
			if(data.estado) {
				$("#coordinador").html('');

				var seleccionado = $("#slc-coordinador").val();

				$("#coordinador").renderSelect(data.usuarios, seleccionado);
				$("#coordinador").trigger("chosen:updated");
			}

		});

	}


	function deleteProyecto(id, button)
	{

		var url = $("#frm").attr('action');
			url += "/delete";

		datos = {
			'proyecto' : id
		}

		js = $.xajax(datos, url);

		js.success(function (data)
		{
			if(data.estado) {

				$("#tr_"+id).addClass('deleted');

				$(button).removeClass('delete').addClass('activa').children().removeClass('fa-times').addClass('fa-thumbs-o-up');
				//$("#estado_"+id).text("No");

				$.bootstrapGrowl(data.msg);
				
			} else {
				$.bootstrapGrowl(data.msg,{type:'danger'});
			}

		});
	}

	function activa_proyecto(id, button)
	{
		var datos = {
			'proyecto' : id
		}

		var url = $("#frm").attr('action');
		url += "/activar";


		js = $.xajax(datos, url);

		js.success(function (data)
		{
			if(data.estado)
			{
				$.bootstrapGrowl(data.msg);

				$("#tr_"+id).removeClass('deleted');

				$(button).removeClass('activa').addClass('delete').children().removeClass('fa-thumbs-o-up').addClass('fa-times');


			}else{
				$.bootstrapGrowl(data.msg,{type:'danger'});
			}
		});
	}

});