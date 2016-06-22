$(document).ready(function(){

	var users = [];

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

});