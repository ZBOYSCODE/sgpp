$(document).ready(function(){

	var users = [];

	$(document).on('change', '#personaSelected', function(){

		var id 		= $(this).val();
		var name 	= $("#personaSelected option:selected").text();

		$("#personaSelected option[value='"+$(this).val()+"']").remove();

		$(this).trigger("chosen:updated");

		users.push(id);

		//$("#listado_personas ul").append("<li>"+name+"</li>");

		// Añadimos el bloque
		jQuery('<li/>', {
		    id: id,
		    class: 'lista_users',
		    text: name
		}).appendTo('.listado_personas ul');

		jQuery('<input/>', {
			name: 'users[]',
			type: 'hidden',
		    value: id
		}).appendTo('#input_users');

		
	});

});