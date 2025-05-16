jQuery(document).ready(function () {
  jQuery('#importContentButton').click(function () {
    jQuery.ajax({
      url: adminajax.ajaxurl,
      type: "POST",
      data: {
        action: 'parseXMLContent'
      },
      success: function (content_options) {
        content_options = JSON.parse(content_options);
        jQuery('#importContentBox').css({display: 'block'});
        jQuery('#importContent').empty();
        let str = "";
        jQuery.each(content_options, function (content_option_id, content_option) {
          if (content_option_id != 'Menu') {
            if(content_option_id == 'Footer'){
              if('Sidebars' in content_options){
                if(Object.keys(content_options['Sidebars']).length > 1){
                  str = str + "<div class='row'>";
                }
              }
              else{
                str = str + "<div class='row'>";
              }
            }
            else{
              str = str + "<div class='row'>";
            }
          }
          if (content_option_id != "Sidebars" && content_option.length > 0) {
          if (content_option_id == "Footer")
            {
              content_option_id += " Widgets";
            }

            str = str + "<div class='col-xl-6 col-lg-6'><div style='padding: 0px 16px;'><div class='contenttitle'><span><input type='checkbox' id='" + content_option_id + "Options' class='optionscheckbox'>Select All</span><h4>" + content_option_id + "</h4></div><ul>";
          }
          jQuery.each(content_option, function (content_id, content) {
            if (content_option_id == 'Pages') {
              str = str + "<li><input type='checkbox' class='optionscheckbox' name='pages' value='" + content['name'] + "'>" +
                content['title'] + "</li>";
            } else if (content_option_id == 'Menu') {
              str = str + "<li><input type='checkbox' class='optionscheckbox' name='menus' value='" + content['slug'] + "'>" + content['title'] + "</li>";
            } else if (content_option_id == 'Sidebars') {
              str = str + "<div class='col-xl-6 col-lg-6'><div style='padding: 0px 16px;'><div class='contenttitle'><span><input type='checkbox' id='" + content_id.split(" ").join("") + "Options' class='optionscheckbox'>Select All</span><h4>" + content_id + "</h4></div><ul>";
              jQuery.each(content, function (sidebar_content_id, sidebar_content) {
                if(content_id == 'Sidebar Left Widgets'){
                  str = str + "<li><input type='checkbox' class='optionscheckbox' name='sidebar1' value='" + sidebar_content['tt_blockID'] + "'>" + sidebar_content['title'] + "</li>";
                }
                else if(content_id == 'Sidebar Right Widgets'){
                  str = str + "<li><input type='checkbox' class='optionscheckbox' name='sidebar2' value='" + sidebar_content['tt_blockID'] + "'>" + sidebar_content['title'] + "</li>";
                }
              });
              str = str + "</ul></div></div>";
            } else if (content_option_id == 'Footer Widgets') {
              str = str + "<li><input type='checkbox' class='optionscheckbox' name='footers' value='" + content['tt_blockID'] + "'>" +
                content['title'] + "</li>";
            }else if (content_option_id == 'Media') {
              str = str + "<li><input type='checkbox' class='optionscheckbox' name='media' value='" + content['id'] + "'>" +
                content['title'] + "</li>"; // import media option
            }
          });
          if (content_option_id != "Sidebars") {
            str = str + "</ul></div></div>";
          }
          if (content_option_id != 'Pages') {
            if(content_option_id == 'Sidebars'){
              if(Object.keys(content_options['Sidebars']).length > 1){
                str = str + "</div>";
              }
            }
            else{
              str = str + "</div>";
            }
          }
        });
        jQuery("#importContent").append(str);
        jQuery(".optionscheckbox").attr('checked','checked');
      }
    });
  });

  jQuery('#importContentClose').click(function () {
    jQuery('#importContentBox').css({
      display: 'none'
    });
  });

  jQuery(window).click(function (event) {
    var target = jQuery(event.target);
    if (jQuery(event.target).attr('id')) {
      let optionsid = jQuery(event.target).attr('id');
      /*if (optionsid == 'importContentBox') {
        jQuery('#importContentBox').css({
          display: 'none'
        });
      }
      else*/
      if (optionsid == 'PagesOptions') {
        if(jQuery(target).attr('checked')){
          jQuery("[name='pages']").attr('checked', 'checked');
          jQuery("[name='menus']").attr('checked', 'checked');
          if(jQuery("[name='menus']").attr('disabled')){
            jQuery("[name='menus']").removeAttr('disabled');
          }
          jQuery("#MenuOptions").attr('checked','checked');
        }
        else{
          jQuery("[name='pages']").removeAttr('checked');
          jQuery("[name='menus']:not([value='blog-wp'])").removeAttr('checked');
          jQuery("[name='menus']:not([value='blog-wp'])").attr('disabled','disabled');
        }
      }
      else if (optionsid == 'MenuOptions') {
        if(jQuery(target).attr('checked')){
          jQuery("[name='menus']:not([disabled])").attr('checked', 'checked');
        }
        else{
          jQuery("[name='menus']").removeAttr('checked');
        }
      }
      else if (optionsid == 'Sidebar1Options') {
        if(jQuery(target).attr('checked')){
          jQuery("[name='sidebar1']").attr('checked', 'checked');
        }
        else{
          jQuery("[name='sidebar1']").removeAttr('checked');
        }
      }
      else if (optionsid == 'Sidebar2Options') {
        if(jQuery(target).attr('checked')){
          jQuery("[name='sidebar2']").attr('checked', 'checked');
        }
        else{
          jQuery("[name='sidebar2']").removeAttr('checked');
        }
      }
      else if (optionsid == 'FooterOptions') {
        if(jQuery(target).attr('checked')){
          jQuery("[name='footers']").attr('checked', 'checked');
        }
        else{
          jQuery("[name='footers']").removeAttr('checked');
        }
      }else if (optionsid == 'MediaOptions') { // select all option work.
        if (jQuery(target).attr('checked')) {
          jQuery("[name='media']").attr('checked', 'checked');
        }
        else {
          jQuery("[name='media']").removeAttr('checked');
        }
      }
    }
    
    if (jQuery(event.target).attr('name')) {
      let checkboxname = jQuery(event.target).attr('name');
      if (checkboxname == 'pages') {
        if(jQuery("[name='pages']:checked").length == jQuery("[name='pages']").length){
          jQuery("#PagesOptions").attr('checked', 'checked');
        }
        else{
          jQuery("#PagesOptions").removeAttr('checked');
        }
        if(jQuery(event.target).attr('checked')){
          jQuery("[name='menus'][value='" + jQuery(event.target).val() + "']").attr('checked', 'checked');
          if(jQuery("[name='menus'][value='" + jQuery(event.target).val() + "']").attr('disabled')){
            jQuery("[name='menus'][value='" + jQuery(event.target).val() + "']").removeAttr('disabled');
          }
        }
        else{
          jQuery("[name='menus'][value='" + jQuery(event.target).val() + "']").removeAttr('checked');
          jQuery("[name='menus'][value='" + jQuery(event.target).val() + "']").attr('disabled','disabled');
        }
      }
      else if (checkboxname == 'menus') {
        if(jQuery("[name='menus']:checked").length == jQuery("[name='menus']:not([disabled='disabled'])").length){
          jQuery("#MenuOptions").attr('checked', 'checked');
        }
        else{
          jQuery("#MenuOptions").removeAttr('checked');
        }
      }
      else if (checkboxname == 'sidebar1[]') {
        if(jQuery("[name='sidebar1']:checked").length == jQuery("[name='sidebar1']").length){
          jQuery("#Sidebar1Options").attr('checked', 'checked');
        }
        else{
          jQuery("#Sidebar1Options").removeAttr('checked');
        }
      }
      else if (checkboxname == 'sidebar2[]') {
        if(jQuery("[name='sidebar2']:checked").length == jQuery("[name='sidebar2']").length){
          jQuery("#Sidebar2Options").attr('checked', 'checked');
        }
        else{
          jQuery("#Sidebar2Options").removeAttr('checked');
        }
      }
      else if (checkboxname == 'footers[]') {
        if(jQuery("[name='footers']:checked").length == jQuery("[name='footers']").length){
          jQuery("#FooterOptions").attr('checked', 'checked');
        }
        else{
          jQuery("#FooterOptions").removeAttr('checked');
        }
      }
      else if (checkboxname == 'media[]') {
        if (jQuery("[name='media']:checked").length == jQuery("[name='media']").length) {
          jQuery("#MediaOptions").attr('checked', 'checked');
        }
        else {
          jQuery("#MediaOptions").removeAttr('checked');
        }
      }
    }
  });
});