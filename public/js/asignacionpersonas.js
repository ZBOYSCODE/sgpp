$(document).ready(function(){

	var users = [];
	
	var url = $("#frm").attr('action');

	$(document).on('change', '#personaSelected', function(){

		var id 		= $(this).val();
		var name 	= $("#personaSelected option:selected").text();

		$("#personaSelected option[value='"+$(this).val()+"']").remove();

		$(this).trigger("chosen:updated");

		users.push(id);

		// Añadimos el bloque
		jQuery('<li/>', {
		    id: id,
		    'data-name': name,
		    class: 'lista_users',
		    html: name+" <a class='quitar_select'><i class='fa fa-times'></i></a>"
		}).appendTo('.listado_personas ul');

		jQuery('<input/>', {
			name: 'users[]',
			type: 'hidden',
		    value: id,
		    id: 'inp-'+id
		}).appendTo('#input_users');

	});

	$(document).on('click', '.quitar_select', function(){

		var id 		= $(this).parent().attr('id');
		var name 	= $(this).parent().attr('data-name');

		$(this).parent().hide('fast', function(){
			$("#inp-"+id).remove();
			$(this).remove();
		})



		$("#personaSelected").append('<option value='+id+'>'+name+'</option>');

		$('#personaSelected').trigger("chosen:updated");

	});

	$(document).on('click', '.del_pp', function(){

		if(confirm("¿seguro quiere eliminar este usuario del proyecto?")){
			del_persona_proyecto($(this).attr('data-id'));// ID persona_proyecto !
		}
		
	});


	function del_persona_proyecto(id)
	{
		var datos = {
			'prsn_proy_id' 	: id
		}
	
		bloque = ajax(datos, 'deletePersonaProyecto');

		bloque.success(function (data)
		{
			if(data.estado)
			{
				// una vez eliminado, quitamos el div
				$("#tr_"+data.id).addClass('danger').fadeOut('slow',function(){
	            	$(this).remove();
	            });

	            $("#msg").alerta(data.msg, 'alert-success');

			}else{
				$("#msg").alerta(data.msg, 'alert-danger');
			}
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
                $.log(data.msg);
                return data; 
            }
        });
	}

});