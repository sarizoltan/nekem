jQuery(document).ready( function() {
jQuery("#import_yes").click(function ( event ){	
	var $button = jQuery(this);
	 jQuery('#importContentClose').css({
    display: 'none'
  });
   $button.prop('disabled', true).after('<div class="load-progress" style="float:right;margin:5px;"><img src="' +  MyAjax.loading_src + '" /></div>');
   event.preventDefault();
  var arrayname = [];
  var arrayvalue = [];
  // get all selected checkboxes names and values only.
  jQuery("#importContent ul li input:checkbox.optionscheckbox:checked").each(function() { 
    arrayvalue.push(jQuery(this).val()); 
}); 
  jQuery("#importContent ul li input:checkbox.optionscheckbox:checked").each(function() { 
    arrayname.push(jQuery(this).attr("name")); 
});

    jQuery.post(			
    ajaxurl, 
    {
        'action': 'templatetoaster_content_import',
        processData: true,
        dataType: 'json', 
        contentType : 'application/json',
        'importt': 'yes',
        'formdata': {"name":arrayname, "values":arrayvalue}
    }, 
    function(response){
     jQuery('#importContentClose').css({
    display: 'block'
  });
      jQuery('#importContentBox').css({
        display: 'none'
      });
       $button.prop('disabled', false);
      jQuery('.load-progress').remove();
     alert( jQuery.trim( response ) );
    }
);
});
});