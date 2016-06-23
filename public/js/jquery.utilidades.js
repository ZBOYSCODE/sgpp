(function($){

	$.fn.extend({
		renderSelect: function(objet, slc = null){

			var th = this;

			$.each(objet, function(indice, valor){
				// creamos el objeto <option>
				var option = jQuery('<option />', {
				    value	: indice,
				    text	: valor
				});

				// indicamos si uno las opciones es seleccionada por defecto
				if(slc != null && slc == indice){
					option.attr('selected', true);
				}

				// añadimos la opcion al select
				option.appendTo(th)
			});
		}

	});

	jQuery.log = function(msg){
		console.log(msg);
	}



})(jQuery)