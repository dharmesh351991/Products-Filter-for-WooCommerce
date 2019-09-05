jQuery(document).ready(function(argument) {
	console.log(object);
	jQuery('form#woocodex-filter-form').submit(function() {
		dataString =  jQuery(this).serialize();
		jQuery.ajax({
			type : 'post',
			url : object.ajaxurl,
			data : dataString,
			dataType : 'json',
			success : function(response) {
				jQuery('.products').html(response.result);
				return false;
			}
		});
		return false;
	});
} );