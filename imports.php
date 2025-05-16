	 <?php

/**
 * Content Import file is parse and process the Xml data from the content.xml file.
 * Also Site title and slogan are imported from the xml file.
 *
 * @package TemplateToaster
 */
 
 /* TemplateToaster_Parse_Content class is used to parse the data from content.xml file. After parsing xml data is converted to different arrays
    for examples pages after parsing store data in $all_pages array, menu data after parsing cconverted to $menu_info,
    sidebar data after parsing stroed into $all_sidebars array, contactform data after parsing stored into $contactus_info.*/
class TemplateToaster_Parse_Content
{

    private $xml_content;

    function __construct($path)
    {
        $this->xml_content = simplexml_load_file($path);
        if (!$this->xml_content) {
            die("Unable to Import content");
        }
    }
    
    // get the site title from xml file
    public function get_site_title()
    {
        if (!isset($this->xml_content->sitetitle)) {
            return false;
        }
        $site_title = (string)$this->xml_content->sitetitle;       
        return $site_title;
    }
    
    // get the site slogan from xml file
    public function get_site_slogan()
    {
        if (!isset($this->xml_content->siteslogan)) {
            return false;
        }
        $site_slogan = (string)$this->xml_content->siteslogan;       
        return $site_slogan;
    }
    
     // get the blog page names
    public function get_blog_page_name(){
		if (!isset($this->xml_content->post_page_name)) {
			return false;
		}
		$tt_blog_page_name = (string)$this->xml_content->post_page_name;       
        return $tt_blog_page_name;
	}

    // get the pages from xml file
    public function get_pages_data()
    {
        if (!isset($this->xml_content->pages)
            || !isset($this->xml_content->pages->page)
        ) {
            return false;
        }

        $all_pages = array();
        foreach ($this->xml_content->pages->page as $page_node) {
            $this->parse_all_pages($page_node, $all_pages);
        }
        return $all_pages;
    }

    // get the menu from xml file 

    public function get_menu_data()
    {
        if (!isset($this->xml_content->menu)
            || !isset($this->xml_content->menu->menu_item)
        ) {
            return false;
        }
        $menu_info = array();
        foreach ($this->xml_content->menu->menu_item as $menu_item) {
            $this->parse_menu($menu_item, $menu_info);
        }
        return $menu_info;

    }

    // get the sidebar content from the xml file

    public function get_sidebar_data()
    {
        if (!isset($this->xml_content->sidebars)
            || !isset($this->xml_content->sidebars->sidebar)
        ) {
            return false;
        }

        $all_sidebars = array();
        foreach ($this->xml_content->sidebars->sidebar as $sidebar_node) {
            $all_blocks = array();
            $this->parse_all_blocks($sidebar_node, $all_blocks);
            $all_sidebars[] = array(
                'name' => (string)$sidebar_node->attributes()->name,
                'blocks' => $all_blocks
            );
        }
        return $all_sidebars;
    }
    
     // get the menu from xml file 


    public function get_contactusform_data($contact_form)
    {
        $contactus_info = array();
        $i =  0;
        
        	$i = $i+1;
        
        if (!isset($contact_form->ListViewItem))
        {
            return false;
        }
        
        $contactus = array();
       foreach ($contact_form->ListViewItem as $item) {
        	$item_name = preg_replace('/\s+/', '', $item);
        	/*if("Email" == (string)$item_name)
        	continue;*/
        	
            $this->parse_items($item_name, $item, $contactus);
        }
       return $contactus;

    }
    
     // get the footer from xml file
    
    public function get_footer_data()
    {
        if (!isset($this->xml_content->footers)
            || !isset($this->xml_content->footers->footer)
        ) {
            return false;
        }

        $all_cell = array();
        foreach ($this->xml_content->footers->footer as $cell) {
            $all_cells = array();
            $this->parse_all_blocks($cell, $all_cells);
            $all_cell[] = array(
                'name' => (string)$cell->attributes()->name,
                'cell' => $all_cells
            );
        }
       
        return $all_cell;
    }
    
     private function parse_items($item_name ,$item, &$contactus_info)
     {
       	  $item_id = 'ttr_item_id';
    	  $item_req = $item_id.'req';
    	  if(isset($item->{'ListViewItem.Tag'}->ContactFormFieldTags))
    	  {
    	  $req = (string)$item->{'ListViewItem.Tag'}->ContactFormFieldTags->attributes()->Tag;
    	  }
    	  else
    	  {
    	  $req = $req = (string)$item->attributes()->Tag;
    	  }
    	  $contentstring = (string)$item->attributes()->ContentStringFormat;
    	  $hidden = (string)$item->attributes()->IsTabStop;
    	  $form_item = null;
        if($req == "Mandate"){
				  $form_item = array(
				  "$item_id" => (string)$item,
				  "item_format" => $contentstring,
				  "$item_req" => 'on',
				  "is_hidden" => $hidden );
			  }
			  else{
				  $form_item = array(
				  "$item_id" => (string)$item,
				  "item_format" => $contentstring,
				  "$item_req" => 'off',
				  "is_hidden" => $hidden);
			  }
          $contactus_info[] = $form_item;
    }


    // Parse the pages from xml file

    private function parse_all_pages($page_node, &$all_pages)
    {
        $page = array(
            'meta_ID' => (string)$page_node->meta_ID,
            'name' => (string)$page_node->page_name,
            'template_name'=> (string)$page_node->page_template_name,
            'title' => (string)$page_node->page_title,
            'header_id' => (string)$page_node->page_header_id,
            'status' => (string)$page_node->page_status,
             'visibility' => (string)$page_node->page_title_visibility,
            'content' => (string)$page_node->page_content,
            'contact_forms' => $page_node->contactforms,
        );

        $all_pages[] = $page;

    }

    // Pares the menu from the xml file

    private function parse_menu($menu_item, &$menu_info)
    {
        $menu_item_info = array(
            'title' => (string)$menu_item->menu_item_title,
            'path' => (string)$menu_item->menu_item_path,
            'parent' => (string)$menu_item->menu_item_parent,
            'url' => (string)$menu_item->menu_item_url,
            'slug' => (string)$menu_item->menu_item_slug
        );

        $menu_info[] = $menu_item_info;

    }

    // parse the sidebar blocks from xml file

    private function parse_all_blocks($blocks_node, &$all_blocks)
    {
        if (!isset($blocks_node)) {
            return;
        }

        $widget_nodes = $blocks_node->xpath('./*[self::block]');
        $result = array();
        foreach ($widget_nodes as $node) {
            $block = array();
            $block['type'] = (string)$node->attributes()->type;
            $block['name'] = (string)$node->attributes()->name;
            $block['title'] = (string)$node->attributes()->title;
            $block['tt_blockID'] = (string)$node->attributes()->tt_blockID;
            
            if (isset($node->contactforms)) {
                $block['contactforms'] = $node->contactforms;
            }
            if (isset($node->content)) {
                $block['content'] = (string)$node->content;
            }
            if (isset($node->widget_pages->widget_page)) {
                $page_list = array();
                foreach ($node->widget_pages->widget_page as $pages) {
                    $page_list[] = (string)$pages;
                }
                $block['show_on_page'] = $page_list;
            }
            $result[] = $block;
        }
        $all_blocks = array_merge($all_blocks, $result);
    }
}


/* TemplateToaster_Import_Content class is used to process the parsed data. start_import() method is used to save the data into
 the database, and if the data is successfully saved into the database then it will reture the true, else return false. */
class TemplateToaster_Import_Content
{
//Theme_Content_Import

    public $uploads;
    private $page_list , $slug_list = array();
    private $parser;

	// Parse xml and return required details to front for options.
    public function xml_parser(){
        $this->parser = new TemplateToaster_Parse_Content(get_template_directory() . '/content/content.xml');
        // parses content.xml
        $page_info = $this->parser->get_pages_data();
        $menu_info = $this->parser->get_menu_data();
        $sidebars_info = $this->parser->get_sidebar_data();
        $images = get_template_directory() . "/content/images";
        $videos = get_template_directory() . "/content/video";
        $files = get_template_directory() . "/content/files";
        $title = $this->parser->get_site_title();
        $slogan = $this->parser->get_site_slogan();
        $tt_blog_page_title = $this->parser->get_blog_page_name();
        $footers_info = $this->parser->get_footer_data();

        $pages = array(); 
        $menus = array();
        $footers = array();
        $sidebars = array();
        $media = array();
        $result = (object) [];

        if($page_info){
            foreach ($page_info as $page_id => $page){
                array_push($pages, array('title' => $page['title'],'name' => $page['name']));
            }
            $result->Pages = $pages;
        }
        if($menu_info){
            foreach ($menu_info as $menu_id => $menu){
                array_push($menus, array('title' => $menu['title'],'slug' => $menu['slug']));
            }
            $result->Menu = $menus;
        }
        if($sidebars_info){
            foreach ($sidebars_info as $sidebar_id => $sidebar){
                foreach ($sidebar as $sidebar_blocks_id => $sidebar_blocks){
                    if($sidebar['name'] == 'sidebar-1' ){
                        $sidebars['Sidebar Left Widgets'] = array();
                        foreach (is_array($sidebar_blocks) ? $sidebar_blocks : array() as $sidebar_block_id => $sidebar_block){
                            array_push($sidebars['Sidebar Left Widgets'], array('title' => $sidebar_block['title'],'tt_blockID' => $sidebar_block['tt_blockID']));
                        }
                    }
                    else{
                        $sidebars['Sidebar Right Widgets'] = array();
                        foreach (is_array($sidebar_blocks) ? $sidebar_blocks : array() as $sidebar_block_id => $sidebar_block){
                            array_push($sidebars['Sidebar Right Widgets'], array('title' => $sidebar_block['title'],'tt_blockID' => $sidebar_block['tt_blockID']));
                        }
                    }
                }
            }
            $result->Sidebars = $sidebars;
        }
        if($footers_info){
            $i=1;
            foreach ($footers_info as $footer_id => $footer){
                foreach ($footer as $footer_blocks_id => $footer_blocks){
                    foreach (is_array($footer_blocks) ? $footer_blocks  : array() as $footer_block_id => $footer_block){
                        array_push($footers, array('title' => 'Footer Column'.$i,'tt_blockID' => $footer_block['tt_blockID']));
                        $i++;
                    }
                }
            }
            $result->Footer = $footers;
        }
        
       

         // if Images exists show image import option. 
         if (file_exists($images)) {
            array_push( $media,array('title' => 'Images', 'id' => 'image'));
         }
         
         // if video exists show video import option.
         if (file_exists($videos)) {
            array_push( $media,array('title' => 'Videos', 'id' => 'video'));
          }

		 if (file_exists($files)) { 
            array_push( $media,array('title' => 'Files', 'id' => 'files'));
          }
          
          if (file_exists($images) || file_exists($videos) || file_exists($files)) {
            $result->Media = $media;
          }
          
        return json_encode($result);
    }
    
    // Parse xml and match filtered import options by user then import content only for selected options .
    public function start_import($filteredContent)
    {
    	global $count;
    	global $tt_blog_page_title;
    	$success = true;
        $this->uploads = wp_upload_dir();
        $this->parser = new TemplateToaster_Parse_Content(get_template_directory() . '/content/content.xml');
        // parses content.xml
        $pages_info = $this->parser->get_pages_data();
        $menu_info = $this->parser->get_menu_data();
        $sidebars_info = $this->parser->get_sidebar_data();
        $images = get_template_directory() . "/content/images";
        // video upload work
        $videos = get_template_directory() . "/content/video";
        $files = get_template_directory() . "/content/files";
        $title = $this->parser->get_site_title();
        $slogan = $this->parser->get_site_slogan();        	
        $tt_blog_page_title = $this->parser->get_blog_page_name();
        $footers_info = $this->parser->get_footer_data();

        if($pages_info){
            foreach ($pages_info as $page_id => $page){
                if(!in_array($page['name'],$filteredContent['pages_info'])){
                    unset($pages_info[$page_id]);
                }
            }
        }
        if($menu_info){
            foreach ($menu_info as $menu_id => $menu){
                if(!in_array($menu['slug'], $filteredContent['menu_info'])){
                    unset($menu_info[$menu_id]);
                }
            }
        }
        $this->menu_info = $menu_info;
        
        if($sidebars_info){
            foreach ($sidebars_info as $sidebar_id => $sidebar){
                foreach ($sidebar as $sidebar_blocks_id => $sidebar_blocks){
                    foreach (is_array($sidebar_blocks) ? $sidebar_blocks  : array() as $sidebar_block_id => $sidebar_block){
                        if(!in_array($sidebar_block['tt_blockID'], $filteredContent['sidebars_info'])){
                            unset($sidebars_info[$sidebar_id][$sidebar_blocks_id][$sidebar_block_id]);
                        }
                        else{
                            foreach ($sidebar_block['show_on_page'] as $page_id => $page_name){
                                if(!in_array($page_name, array_column($pages_info, 'name'))){
                                    unset($sidebar_block['show_on_page'][$page_id]);
                                }
                            }
                        }
                    }
                }
            }
        }
        if($footers_info){
            foreach ($footers_info as $footer_id => $footer){
                foreach ($footer as $footer_blocks_id => $footer_blocks){
                    foreach (is_array($footer_blocks) ? $footer_blocks  : array() as $footer_block_id => $footer_block){
                        if(!in_array($footer_block['tt_blockID'], $filteredContent['footers_info'])){
                            unset($footers_info[$footer_id]);
                        }
                    }
                }
            }
        }

        // if Images exists in content and import option is selected uploads it to the upload directory
        if (file_exists($images) && in_array("image", $filteredContent['media_info'])) {
           $success = $success && $this->upload_media('images');
        }
        
        
        // if video exists in content and import option is selected uploads it to the upload directory
        if (file_exists($videos) && in_array("video", $filteredContent['media_info'])) {
            $success = $success && $this->upload_media('video');
         }

         // if files exists in content and import option is selected uploads it to the upload directory
        if (file_exists($files) && in_array("files", $filteredContent['media_info'])) {
            $success = $success && $this->upload_media('files');
         }

        // if pages_info array is not empty start processing Pages
        if ($pages_info) {
            $success = $success && $this->insert_pages($pages_info);
        }

        // if menu_info array is not empty strat processing menu
        if ($menu_info) {
            $success = $success && $this->insert_menu($menu_info);
        }
        
        // if footers_info or sidebar_info array is not empty remove widgets.
        if ($footers_info || $sidebars_info) {
            $success = $success && $this->remove_old_widgets();
        }

        // if sidebar_info array is not empty start processing Sidebar blocks
        if ($sidebars_info) {
            $success = $success && $this->insert_sidebars($sidebars_info);
        }
        
         // if contactus_info array is not empty start processing Contact us form
      /*  if ($contactus_info) {
            foreach($contactus_info as $num => $con)
        	{
        		foreach($con as $numm => $con_info)
        		{
            		$success = $success && $this->upadate_contactus_form($con_info);
				}
			}
        } */
        
        // if title set from TemplateToaster , update it
        if ($title) {
           update_option('blogname', $title);
        }
        
        // if slogan set from TemplateToaster , update it
        if ($slogan) {
            update_option('blogdescription', $slogan);
        }
        
        // if footers_info array is not empty start processing Pages
        if ($footers_info) {
            $success = $success && $this->insert_footer($footers_info);
        }
        
         return $success;
        
    }
    
    // Managed tag array work
    private function save_contact_form($contact_form, $attr)
	{	
		global $count;
		$i =  100;
		$j = 0;
		$mail_arr = array();
		foreach($contact_form as $con_info)
		{	
			$mailtag = "";
			$i = $i+1;	
			$j += 1;
			$properties[] = '<div class="form-group row g-0 mb-3"> ';
			foreach($con_info as $key => $value)
			{
			if ($key != 'item_format')
			{
				if (strtolower($value) != 'on' && strtolower($value) != 'off' && strtolower($value) != 'true' && strtolower($value) != 'false')
				{
					if(strtolower($con_info['is_hidden']) == 'true'){
							$properties[] = '<label class="col-md-4 control-label" hidden> ';
						}else{
							$properties[] = '<label class="col-md-4 control-label"> ';
						}
					
					$properties[] = $value;
				}
				
				if(strpos($key,'req')==true && $value == 'on') 
				{
					$properties[] = ' (required) ';
					$properties[] = '</label> ';
					$properties[] = '<div class="col-md-8"> ';
					if(strtolower($con_info['ttr_item_id']) == "email" && strtolower($con_info['is_hidden']) == 'false')
					{
					$properties[] = '[email* text-'.$i.' class:form-control ] ';
					}
					else
					{
						if(strtolower($con_info['is_hidden']) == 'true'){
							$properties[] = '[hidden text text-'.$i.' class:form-control ] ';
						}else{
							$properties[] = '[text* text-'.$i.' class:form-control ] ';
						}
					
					}
					$mailtag = 'text-'.$i;
				}	
				elseif(strpos($key,'req')==true && $value == 'off')			
				{
					$properties[] = '</label> ';
					$properties[] = '<div class="col-md-8"> ';
					if(strtolower($con_info['ttr_item_id']) == "email" && strtolower($con_info['is_hidden']) == 'false')
					{
					$properties[] = '[email* text-'.$i.' class:form-control ] ';
					}
					else
					{
					if(strtolower($con_info['is_hidden']) == 'true'){
							$properties[] = '[hidden text text-'.$i.' class:form-control ] ';
						}else{
							$properties[] = '[text text-'.$i.' class:form-control ] ';
						}
					}
					if(strtolower($con_info['is_hidden']) == 'true'){
							$mailtag = 'text';
						}else{
							$mailtag = 'text-'.$i;
						}
				}
				}
				if($mailtag){
					$mail_arr[$j] =  $mailtag;
					$mailtag = '';
					//$j++;
				}
				
			}
			$properties[] = '</div></div>';
			$properties[] = "\n";
		}
		
		$f_code = '[file your-file';
		if(isset($attr['filetype']) && !empty($attr['filetype'])){
		$f_ext = strtolower($attr['filetype']);
		$f_ext = str_replace(',', '|', $attr['filetype']);
		$f_code .= ' filetypes:'.$f_ext; // '|' separated file types.
		}
		if(isset($attr['filesize'])){
		$val = $attr['filesize'] * 1024 * 1024; // set file limit in bytes.
		$val =  round($val);
		$f_code .= ' limit:'.$val;
		}
		$f_code .=  ']' ;
		
		// Update the messege fields name 
 		if(!isset($attr['msg_name']))
        {
            $attr['msg_name']="Message";
        }		
		
		$properties[] = '<div class="form-group row g-0 mb-3"><label class="col-md-4 control-label"> ' .$attr['msg_name']. '</label>';
		$properties[] = '<div class="col-md-8"> [textarea* your-message class:form-control 40x4] </div></div>';
		$properties[] = "\n";
		if($attr['BrowseButtonVisibility'] == "Visible"){
		$properties[] = '<div class="form-group row g-0 mb-3"><label class="col-md-4 control-label"> File </label>';
		$properties[] = '<div class="col-md-8"><label class="contact_file btn-file"> '.$f_code.' Browse</label><span id="upload-file" class="filename"> No File Selected </span></div></div>';
		$properties[] = "\n";
		}
		$properties[] = '<div class="form-group row g-0 mb-3"><div class="col-md-8 col-md-offset-4 offset-md-4 ">'; // Merged Bootstrap 3 & 4 classes.
		$properties[] = '[submit id:submitform "Send Message"]';
		$properties[] = '</div></div>';
		
		$post_content = implode( $properties );
	    $contactform_arr = get_option('contact_form');
			$adminemail = $contactform_arr['0']['ttr_email'];
			if($adminemail)
			{
				$admin_email = $adminemail;
			}
			else
			{
				$admin_email = get_option('admin_email');
			}	
			
			
		// To set form tags		
		$post_mail = array(
			'subject' => sprintf(
				_x( '%1$s "%2$s"', 'mail subject', '001' ),
				get_bloginfo( 'name' ), '['.$mail_arr[3].']' ),
			'sender' => sprintf( '['.$mail_arr[1].'] <%s>', '['.$mail_arr[2].']' ),
			'body' =>
				sprintf( __( 'From: %s', '001' ),
					'['.$mail_arr[1].'] <['.$mail_arr[2].']>' ) . "\n"
				. sprintf( __( 'Subject: %s', '001' ),
					'['.$mail_arr[3].']' ) . "\n\n"
				. __( 'Message Body:', '001' )
					. "\n" . '[your-message]' . "\n\n"
				. '-- ' . "\n"
				. sprintf( __( 'This e-mail was sent from a contact form on %1$s (%2$s)',
					'001' ), get_bloginfo( 'name' ), home_url() ),
			'recipient' => $admin_email,
			'additional_headers' => 'Reply-To: ['.$mail_arr[2].']',
			'attachments' => '[your-file]',
			'use_html' => 0,
			'exclude_blank' => 0,
		);

		// check if contact form already exist then update field  on the bases of previous ID. IF id not exist then add new fields 
      	$item_id = $this->get_post_id( 'tt_contactformID', 'tt_cform' . $count );
        if (!empty($item_id)) 
        {
			$page_attributes =  array(
                    'ID' => $item_id,
	                'post_type' => 'wpcf7_contact_form',
	                'post_name' => 'test_form',
	                'post_title' => 'Test Form',
	                'post_content' => $post_content,
	                'post_status' => 'publish'
	             );
			$pid = wp_update_post( $page_attributes );
            $this->pid = $pid;
        }
        else
        {
            $page_attributes =  array(
             
                'post_type' => 'wpcf7_contact_form',
                'post_name' => 'test_form',
                'post_title' => 'Test Form',
                'post_content' => $post_content,
                'post_status' => 'publish'
             );
            $pid = wp_insert_post( $page_attributes );
            $this->pid = $pid;
           
        }
	
			if ( $pid ) 
			{		
			// Fetching data from database to replace the custum mail sent message.      	
        	$sent_msg = get_post_meta( $pid, '_messages' );
            if( $sent_msg === null || empty( $sent_msg ) || empty( $sent_msg[0] )) {
                $message = array_fill('0',1, ['mail_sent_ok' => "Mail Successfully Sent"]);               
            } else {
                $message = array_replace( $sent_msg['0'], ['mail_sent_ok' => "Mail Successfully Sent"] );
            }
            
			update_post_meta( $pid, '_form' , $post_content );	
			update_post_meta( $pid, '_mail' , $post_mail );
			update_post_meta( $pid, 'tt_contactformID', 'tt_cform' . $count );
            update_post_meta( $pid, '_messages', $message );
			$count = $count+1;
			}	
			
            
			return $pid;
}

    private function insert_pages($pages_info)
    {
    	global $tt_blog_page_title;
    	$result = true;
        $menu_order = 0;
        foreach ($pages_info as $num => $page) {
            $content = '';
            $inserted = false;
            if(array_key_exists('content', $page)){
					    $content = $this->set_image_src($page['content']);
					    }
					    
			$contact_forms = $page['contact_forms'];		
	        			
	        	foreach($contact_forms as $contact_info)		
	        	{		
	        	foreach($contact_info as $contact_form)
	        	{
	        	$contact_form_object = $this->parser->get_contactusform_data($contact_form);		
	        	if(!$contact_form_object)return;		
	        	$con_id = $contact_form['id'];   
	        	
	        	$attr = $contact_form->attributes();
	        				
	        	$contact_form_id = $this->save_contact_form($contact_form_object, $attr); 		
	        	        			
	        	$content = str_replace($con_id, $contact_form_id, $content);		
	        	}
				}	        	
	        		
			$template_file_name = "page-templates/".$page['template_name'] . "_page.php";
            $meta_ID = $page['meta_ID'];
            $meta_class = $page['name'];
            //$meta_class = $page['title'];
            $id = $this->get_post_id('tt_pageID', $meta_ID);
			$vis = $page['visibility'];
            if (!empty($id)) {
                $page_attributes = array(
                    'ID' => $id,
                    'post_type' => 'page',
                    'post_name' => $page['name'],
                    'post_title' => $page['title'],
                    'page_title_visibility' => $page['visibility'],
                    'post_content' => $content,
                    'post_status' => $page['status'],
                    'menu_order' => ++$menu_order,
                );
                $post_id = wp_update_post($page_attributes);
                if ($post_id != 0){
                	 if (strtolower($page['header_id']) == "home"  ) {
					    update_option('page_on_front', $post_id);
			        	update_option('show_on_front', 'page');
		        	}
					update_post_meta($post_id, 'tt_pageID', $meta_ID);
                	update_post_meta($post_id, 'ttr_page_title_checkbox', $vis);
                	update_post_meta($post_id, '_wp_page_template', $template_file_name);
                	$inserted = true;
                }
                $id = null;
            } else {
                $page_attributes = array(
                    'post_type' => 'page',
                    'post_name' => $page['name'],
                    'post_title' => $page['title'],
                    'post_content' => $content,
                    'post_status' => $page['status'],
                    'menu_order' => ++$menu_order,
                );
                $post_id = wp_insert_post($page_attributes);
                if ($post_id != 0){
					 if (strtolower($page['header_id']) == "home" ) {
					    update_option('page_on_front', $post_id);
			        	update_option('show_on_front', 'page');
		        	}
                    add_post_meta($post_id, 'tt_pageID', $meta_ID, false);
	                add_post_meta($post_id, 'ttr_page_title_checkbox', $vis, false); 
	                add_post_meta($post_id, '_wp_page_template', $template_file_name);
	                $inserted = true;
	               }
                $id = null;
            }
            $this->page_list[$meta_class] = 'page-' . $post_id;
        	$this->slug_list[$meta_class] = $this->get_the_page_slug($post_id) ;
        	$result = $result && $inserted;
        }
        
        // create new block page only if block page in menu list.
        if(in_array('blog-wp', array_column($this->menu_info, 'slug'))){
	        // Set the blog page
	        //$blog_title = get_the_title( get_option('page_for_posts', true) );
	      	$blog = get_page_by_path($tt_blog_page_title);
	        
	        if (!empty($blog)) {
	        	wp_delete_post( $blog->ID, true );
	        	delete_post_meta($blog->ID, 'tt_pageID', 'tt_page0');
	        	delete_post_meta($blog->ID, 'tt_pageClass', $blog->post_title);
	        	
	        }
            $page_attributes = array(
                'post_type' => 'page',
                'post_name' => $tt_blog_page_title,
                'post_title' => 'Blog', 
                'post_content' => '',
                'post_status' => 'publish'
            );
            $blog_id = wp_insert_post($page_attributes);
            add_post_meta($blog_id, 'tt_pageID', 'tt_page0', false);
            add_post_meta($blog_id, 'tt_pageClass', $tt_blog_page_title, false);
            update_option('page_for_posts', $blog_id);
            $this->slug_list[$tt_blog_page_title] = $this->get_the_page_slug($blog_id) ;
		}
            
       return $result;
    }
    
    
function tt_img_src($match)  {
	$uploads =  wp_upload_dir();
            list($str, $src_attr, $quote, $filename, $png) = $match;
            return $src_attr . $quote . $uploads['url'] . '/' . str_replace(" ","-",$png) . $quote; //Replaced space in image name by hyphen according to wordpress media library
 }

function tt_link_src($match) {
    		list($str, $href, $quote, $fun, $pagename) = $match;
            return $href . $quote . home_url( '/'. strtolower($pagename)) . $quote;
 }
 
 function tt_video_src($match)  {
	$uploads =  wp_upload_dir();
            list($str, $src_attr, $quote, $filename, $png) = $match;
            return $src_attr . $quote . $uploads['url'] . '/' . str_replace(" ","-",$png) . $quote; //Replaced space in image name by hyphen according to wordpress media library
 }


    // Replaces the Img sources according to your 
    private function set_image_src($post)
    {
    	  $that = $this;
        $str = '<?php echo $theme_path_content; ?>';
        $post = str_replace($str, '', $post);
        $post = preg_replace_callback('/(src=)([\'"])([\/\\\]?images[\/\\\]?)(.*?)\2()/', array($this,'tt_img_src'), $post);
        $post = preg_replace_callback('/(src=)([\'"])([\/\\\]?video[\/\\\]?)(.*?)\2()/', array($this,'tt_img_src'), $post);
        $post = preg_replace_callback('/(href=)([\'"])([\/\\\]?files[\/\\\]?)(.*?)\2()/', array($this,'tt_img_src'), $post);
        $post = preg_replace_callback('/(href=)([\'"])([\/\\\]?[\'<][\'?]php echo get_permalink[\'(] get_page_by_path[\'(][\'"](.*?)[\'"][\')]\s[\')][\';][\'?][\'>][\'"][\/\\\]?)/', array($this,'tt_link_src'), $post);
        return $post;
      }
    
    // get the post ID of the page to check post already exist into the databse.
    private function get_post_id($key, $value)
    {
        global $wpdb;

        $sql = $wpdb->prepare("SELECT * FROM " . $wpdb->postmeta . " WHERE meta_key = %s AND meta_value = %s", $key, $value);

        $meta = $wpdb->get_results($sql);

        if (is_array($meta) && !empty($meta) && isset($meta[0])) {
            $meta = $meta[0];
        }
        if (is_object($meta)) {
            return $meta->post_id;
        } else {
            return false;
        }
    }
    
    // to get the page_slug of the page from the datbase.
    private function get_the_page_slug($id) 
    {
		$post_data = get_post($id, ARRAY_A);
		$slug = $post_data['post_name'];
		return $slug; 
	}

    // upload the images to wordpress directory
    private function upload_media($mtype)
    {
    global $wp_filesystem;
        WP_Filesystem();
    	$ttr_media = array();
    	$result = true;
        $tt_media_dir = get_template_directory() . '/content/'.$mtype.'/';
        $tt_content_media = opendir($tt_media_dir);
        while ($tt_read_media = readdir($tt_content_media)) {
            if ($tt_read_media != '.' && $tt_read_media != '..') {
                  $result = wp_upload_bits($tt_read_media, null, $wp_filesystem->get_contents($tt_media_dir . $tt_read_media));
                  if (isset($result['error']) && $result['error']) {
        			continue;
    				}
                  array_push($ttr_media,$result['file']);                
            }
        }
        $this->templatetoaster_import_content_image($ttr_media);
         return $result;
    }

    // generate the menu named TT_menu and set it as default when theme is activated 
    private function insert_menu($menu_info)
    {
    	$result = true;
        $menuname = 'TT_menu';
        $ttmenulocation = 'primary';
        $menu_exists = wp_get_nav_menu_object($menuname);
       	wp_delete_nav_menu($menuname);
        $menu_id = wp_create_nav_menu($menuname);
        $this->custom_menuID = $menu_id;
        $new_menu_obj = array();
        $nav_items_to_add = $menu_info;

         foreach ($nav_items_to_add as $slug => $nav_item) {
            $item = strtolower($nav_item['title']);
            $new_menu_obj[$item] = array();
            $nav_parent = $nav_item['parent'];
            $nav_parent_id = 0;
            if (array_key_exists('parent', $nav_item) && $nav_parent != "")
            {	
              $new_menu_obj[$item]['parent'] = $nav_item['parent'];
				      $nav_parent_id = $new_menu_obj[$nav_item['parent']]['id'];
			      }   
            $url = $nav_item['url'];
            $itemtype = array();
			$WooCommerce = false;
            		            
            if ($url == null && empty($url)) {
				// removed to manage shop page check below.
            	if (array_key_exists($nav_item['slug'], $this->slug_list))
            	{
            		$slug = $this->slug_list[$nav_item['slug']];
            		$object_id =  get_page_by_path($slug)->ID;
            		} 
            		
            		if ($WooCommerce)
            		{
                        if($nav_item['title'] == 'WpShop')
                        {
                        $nav_item['title'] = 'Shop';
                        $object_id = get_page_by_path($nav_item['title'])->ID;
                        } else {
                            $object_id = get_page_by_path($nav_item['slug'])->ID;
                        }
            		}
            		
            		if($nav_item['slug'] == 'blog-wp')
            		{
            		$object_id = get_page_by_path($nav_item['title'])->ID;
            		$nav_item['title'] = 'Blog';
            		} 

                $itemtype = array(
                    'menu-item-title' => $nav_item['title'],
                    'menu-item-object' => 'page',
                    'menu-item-parent-id' => $nav_parent_id,
                    'menu-item-object-id' => $object_id,
                    'menu-item-type' => 'post_type',
                    'menu-item-status' => 'publish');
            } else {
                if(preg_match('/^#/',$url)){
                    $url = get_site_url().'/'.$url;
                }
                $itemtype = array(
                    'menu-item-title' => $nav_item['title'],
                    'menu-item-object' => 'custom',
                   	'menu-item-parent-id' => $nav_parent_id,
                    'menu-item-object-id' => get_page_by_path($nav_item['title'])->ID,
                    'menu-item-type' => 'custom',
                    'menu-item-status' => 'publish',
                    'menu-item-url' => $url);
            }
            $wperror = $menu_item = wp_update_nav_menu_item($menu_id, 0, $itemtype);
            if ( is_wp_error( $menu_item ) ) {
			   $wperror = false;
			}
			else{
				$wperror = true;
				$new_menu_obj[$item]['id'] = $menu_item;
			}
            
            $result = $result && $wperror;
        }
       
        //if (!has_nav_menu($ttmenulocation)) { // wp latest version does not have menu posiion set due to which TT imported menu does not apply to primary menu position.
            $locations = get_theme_mod('nav_menu_locations');
            $locations[$ttmenulocation] = $menu_id;
            set_theme_mod('nav_menu_locations', $locations);
        //}
        return $result;
    }

	// set the widgets to the respective sidebar
    private function insert_sidebars($sidebars_info)
    {
        $result = true;
        foreach ($sidebars_info as $sidebar) {
            foreach ($sidebar['blocks'] as $block) {
                $listofpages = $block['show_on_page'] ? $block['show_on_page'] : array();
                $content = '';
                if(array_key_exists('content', $block)){
			        $content = $this->set_image_src($block['content']);
		        }
				        
				if(isset($block['contactforms'])){
	                //contact form import
	                $contact_forms = $block['contactforms'];	
		        			
		        	foreach($contact_forms as $contact_info)		
		        	{		
			        	foreach($contact_info as $contact_form)
			        	{
				        	$contact_form_object = $this->parser->get_contactusform_data($contact_form);		
				        	if(!$contact_form_object)return;		
				        	$con_id = $contact_form['id'];   
				        	
				        	$attr = $contact_form->attributes();
				        				
				        	$contact_form_id = $this->save_contact_form($contact_form_object, $attr); 		
				        	        			
				        	$content = str_replace($con_id, $contact_form_id, $content);		
		        		}
                	}
           	 	}        
                $widget_added = $this->add_widget($sidebar['name'], $block['type'], $block['title'], $content, $block['tt_blockID'], $listofpages);
                $result = $result && $widget_added;
            }
        }
        return $result;
    }
    
    private function remove_old_widgets() {
        $sidebars_widgets = get_option( 'sidebars_widgets' );
        $type = array(0 => 'nav_menu', 1 => 'text');
        for($i=0; $i<count($type); $i++){
            $wp_widget = get_option('widget_' . $type[$i]);
            $wp_widget = $wp_widget ? $wp_widget : array();
			
			// go through all sidebar positions and get widgets.
            foreach ($sidebars_widgets as $sidebar_position => $sidebar_widgets) {
                foreach (is_array($sidebar_widgets) ? $sidebar_widgets : array() as $sidebar_widget_id => $sidebar_widget) {
                    $widget = explode("-",$sidebar_widget); // split widget name in which 0 for type and 1 for id.
                    if($widget[0] == $type[$i] && array_key_exists($widget[1], $wp_widget)) { // check widget type and widget id in wp_widget
                        if(array_key_exists('tt_blockID',$wp_widget[$widget[1]])) {
                        	unset($sidebars_widgets[$sidebar_position][$sidebar_widget_id]); // unset widget from sidebar if it contains tt_blockID.
                        }
                    }
                }
            }
            
            // go through all widgets.
            foreach ($wp_widget as $widget_id => $widget) {
                if(array_key_exists('tt_blockID',is_array($widget) ? $widget : array())) {
                    unset($wp_widget[$widget_id]); // unset widget if it contains tt_blockID.
                }
            }
            
            // update sidebar_widgets and widgets to db.
            update_option('widget_' . $type[$i], $wp_widget);
            update_option('sidebars_widgets', $sidebars_widgets );
        }
        return true;
    }
    
    private function insert_footer($footers_info)
    {
        $result = true;
        foreach ($footers_info as $cells) {
            foreach ($cells['cell'] as $block) {
                $listofpages = $block['show_on_page'] ? $block['show_on_page'] : array();
                $content = '';
                if(array_key_exists('content', $block)){
			        $content = $this->set_image_src($block['content']);
		        }
                
                if(isset($block['contactforms'])){
	                //contact form import
	                $contact_forms = $block['contactforms'];	
		        			
		        	foreach($contact_forms as $contact_info)		
		        	{		
			        	foreach($contact_info as $contact_form)
			        	{
				        	$contact_form_object = $this->parser->get_contactusform_data($contact_form);		
				        	if(!$contact_form_object)return;		
				        	$con_id = $contact_form['id'];   
				        	
				        	$attr = $contact_form->attributes();
				        				
				        	$contact_form_id = $this->save_contact_form($contact_form_object, $attr); 		
				        	        			
				        	$content = str_replace($con_id, $contact_form_id, $content);		
		        		}
                	}
            	}
            	$widget_added = $this->add_widget($cells['name'], $block['type'], $block['title'], $content, $block['tt_blockID'], $listofpages);
               	$result = $result && $widget_added;
            }
        }
        return $result;
    }

	// Set the value to the widgets 
    private function add_widget($sidebar, $blocktype, $title, $content = null, $tt_blockID, $listofpages)
    {
        $wp_sidebars = get_option('sidebars_widgets');

        if (!isset($wp_sidebars[$sidebar]) && !empty($wp_sidebars[$sidebar])) {
            return false;
        }
        
        $type = ($blocktype == 'custom_menu') ? 'nav_menu' : 'text';

        $wp_widget = get_option('widget_' . $type);
        $wp_widget = $wp_widget ? $wp_widget : array();

        // new widget id is always unique
        $new_widget_id = 1;
        if (array_filter($wp_widget, 'is_int', ARRAY_FILTER_USE_KEY)){
            $new_widget_id = count(array_keys($wp_widget));
        }
        $new_widget_name = $type . '-' . $new_widget_id;
        
        // gets widgets from the selected sidebar and add widget
        $wp_sidebar_widgets = $wp_sidebars[$sidebar];
		$wp_sidebar_widgets[] = $new_widget_name;

        // puts new sidebar widgets in the list of sidebars
        $wp_sidebars[$sidebar] = $wp_sidebar_widgets;
        update_option('sidebars_widgets', $wp_sidebars);

        // creates new widget
        $wp_widget[$new_widget_id] = array();

        if (isset($title) && (strlen($title) > 0)) {
            $wp_widget[$new_widget_id]['title'] = $title;
        }

        if (isset($tt_blockID) && (strlen($tt_blockID) > 0)) {
            $wp_widget[$new_widget_id]['tt_blockID'] = $tt_blockID;
        }

        if (isset($content) && (strlen($content) > 0) && ($type == 'text')) {
            $wp_widget[$new_widget_id]['text'] = $content;
            $wp_widget[$new_widget_id]['filter'] = false;
        }

        if ($type == 'nav_menu') {
            $wp_widget[$new_widget_id]['source'] = 'Custom Menu';
            
            // Check new menu id if exist assign new menu id otherwise old menu id.
            if($this->custom_menuID){
                $wp_widget[$new_widget_id]['nav_menu'] = $this->custom_menuID;
            }
            else{
                $tt_menu_term = get_term_by('slug','tt_menu','nav_menu' );
                $wp_widget[$new_widget_id]['nav_menu'] = $tt_menu_term->term_id;
            }
            
            $wp_widget[$new_widget_id]['style'] = 'default';
            $wp_widget[$new_widget_id]['menustyle'] = 'vmenu';
            $wp_widget[$new_widget_id]['alignment'] = 'default';
        }

        if (!isset($wp_widget['_multiwidget'])) {
            $wp_widget['_multiwidget'] = 1;
        }
        
        // added to hide the widget on the particular page
       foreach ($this->slug_list as $num => $slug_name) {
            if (in_array($num, $listofpages)) {
                // page exist in pages_info array
                continue;
            }
            else {
				if(strtolower($num) == "home"){
					$wp_widget[$new_widget_id]['page-front'] = 1;
				}
				elseif(strtolower($num) != "blog-wp"){
					$pageid = $this->page_list[$num];
					$wp_widget[$new_widget_id][$pageid] = 1;
				}
				$wp_widget[$new_widget_id]['page-home'] = 1;
				$wp_widget[$new_widget_id]['page-single'] = 1; 
            }
        }

         $result = update_option('widget_' . $type, $wp_widget);
         return $result;
    }
    
    // update the fields of the contact form according to current theme
     private function upadate_contactus_form($contactus_info){
     	$get_contact_array = get_option( 'contact_form');
    	$result = array_merge($get_contact_array, $contactus_info);
		$done = update_option( 'contact_form', $result );
		return $done;
		
	}
	
// function created to start importing content images.
  	private function templatetoaster_import_content_image($ttr_media)
    {
		$parent_post_id = 0;

		// Get the path to the upload directory.
		$wp_upload_dir = wp_upload_dir();

	foreach($ttr_media as $path) {
		$wp_filetype = wp_check_filetype(basename( $path ), null );
    	$attachment = array(
    	'guid'=>  $path ,
        'post_mime_type' => $wp_filetype['type'],
        'post_parent' => $parent_post_id,
        'post_title' => preg_replace('/\.[^.]+$/', '', basename( $path )),
        'post_content' => '',
        'post_status' => 'inherit'
         );

    	$upload_id = wp_insert_attachment($attachment, $path, $parent_post_id);

    // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
    require_once( ABSPATH . 'wp-admin/includes/image.php' );
    require_once( ABSPATH . 'wp-admin/includes/media.php' );
 
    // Generate the metadata for the attachment, and update the database record.
    $attach_data = wp_generate_attachment_metadata( $upload_id, $path );

    wp_update_attachment_metadata( $upload_id, $attach_data );
    if($wp_filetype['type'] == "")
    {
        set_post_thumbnail( $parent_post_id, $upload_id );
    }
    
  }
  return;
 }
	
}

// instance created and start the importing process.
function TemplateToaster_parse_xml()
{
    $tt_content_importer = new TemplateToaster_Import_Content();
    $result = $tt_content_importer->xml_parser();
    return $result;
}

// instance created and start the importing process.
function TemplateToaster_import_start($filteredContent)
{
    $tt_content_importer = new TemplateToaster_Import_Content();
    $result = $tt_content_importer->start_import($filteredContent);
     return $result;
}

?>