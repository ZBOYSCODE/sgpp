$(document).ready(function(){

	$(document).on('click', '.delete', function(){

		var id = $(this).attr('data-id');

		if(confirm("¿Seguro desea eliminar el usuario?")){
			delete_user(id, $(this));
		}

	});

	$(document).on('click', '.activa', function(){

		var id = $(this).attr('data-id');


		activa_user(id, $(this));

	});

	function activa_user(id, button)
	{
		var datos = {
			'user_id' : id
		}

		var url = $("#frm").attr('action');
		url += "/activa";


		js = $.xajax(datos, url);

		js.success(function (data)
		{
			if(data.estado)
			{
				$.bootstrapGrowl(data.msg);

				$("#tr_"+id).removeClass('deleted');

				$(button).removeClass('activa').addClass('delete').children().removeClass('fa-thumbs-o-up').addClass('fa-times');

				$("#estado_"+id).text("Sí");

			}else{
				$.bootstrapGrowl(data.msg,{type:'danger'});
			}
		});
	}

	function delete_user(id, button)
	{
		var datos = {
			'user_id' : id
		}

		var url = $("#frm").attr('action');
		url += "/delete";


		js = $.xajax(datos, url);

		js.success(function (data)
		{
			if(data.estado)
			{
				$.bootstrapGrowl(data.msg);

				$("#tr_"+id).addClass('deleted');

				$(button).removeClass('delete').addClass('activa').children().removeClass('fa-times').addClass('fa-thumbs-o-up');
				$("#estado_"+id).text("No");

			}else{
				$.bootstrapGrowl(data.msg,{type:'danger'});
			}
		});
	}

});