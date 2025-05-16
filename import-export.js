	jQuery(document).ready( function() {
		
	jQuery("#export_yes").click(function (){			
			jQuery.post(			
		    ajaxurl, 
		    {
		        'action': 'export_xml',
		        'data':   ''
		    }, 
		    function(response){
	   			function download(filename, text) {
				  var element = document.createElement('a');
				  element.setAttribute('href', 'data:text/json;charset=utf-8,' + encodeURIComponent(text));
				  element.setAttribute('download', filename);
				  element.style.display = 'none';				  
				  document.body.appendChild(element);
				  element.click();				 
				}
				
				response = response.trim();
				if(response.startsWith("<?xml")) 
				{
					download('themeoptions.xml', response);
				}
				else
				{
					alert(tt_ajax.tt_savechanges);	
				}	
		    }
    	);
		});
	jQuery(document).find("#importfile").val('');	
	jQuery('#import_options_yes').click(function() {
		var input = jQuery(document).find("#importfile");
								
	    if (input[0].files && input[0].files[0] && input[0].files[0]['type'] == 'text/xml') 
		{
  			form_data = input[0].files[0];
			var reader = new FileReader();
	        var x = reader.readAsText(form_data);
	        reader.onload = function(e) {
	        file_data = e.target.result
	        
	        if(file_data)
	        {
				sendfile(file_data);
			}
        	};
        }	
        else
        {       	
			alert(tt_ajax.tt_xmlerror);
			window.location.reload(true);
		}
		
		function sendfile(file_data)
		{
				jQuery.post(			
			    ajaxurl, 
			    {
			        'action': 'import_xml',
			        data: {'file': file_data},
			        'processData': false,
	    			'contentType': false,
	    			'type': 'POST',		        	        
			    },		     
			    function(data){	
			    	data = data.trim();
			    	if(data && data != null)
			    	{
			    		jQuery(document).find("#importfile").val(''); 
						alert(data);
						window.location.reload(true);												
					}
			    });
		}
	});
});