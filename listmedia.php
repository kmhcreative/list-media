<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
* Plugin Name: List Media
* Plugin URI: https://en-gb.wordpress.org/plugins/media-list/
* Description: Adds the ability to quickly list posts or media attached to a page with pagination via [listmedia] shortcode.
* Version: 1.4.2
* Author: D. Relton, K.M. Hansen
* Author URI: https://profiles.wordpress.org/mauvedev/
* License: GPLv2 or later
* License URI: http://www.gnu.org/licenses/gpl-2.0.html
* Text Domain: media-list
**/

/* 	FORKED VERSION of "MEDIALIST" PLUGIN
	=====================================
	The modifications in this plugin have been submitted to the original
	plugin developer, D. Relton, but have not yet been accepted or implemented
	in the original plugin.  So, to prevent overwriting these modifications
	in the event there is an update that does not include them this plugin
	has been forked from the original.
*/
function media_list_load_plugin_textdomain() {
load_plugin_textdomain( 'media-list', false, dirname( plugin_basename(__FILE__)) . '/languages/');
}
add_action( 'plugins_loaded', 'media_list_load_plugin_textdomain' );
if ( !class_exists( 'listmediapluginclass' ) ) { //check if class is already taken.
class listmediapluginclass {
//constructor
function __construct() {
	add_action('init', array( $this, 'listmediamainplugininit' )); //initialise shortcodes
	add_action('the_posts', array( $this, 'checkforlistmediashortcode' )); //perform the check when the_posts() function is called
	add_action( 'init' , array( $this, 'listmedia_add_taxonomies' ));
}
function listmedia_add_taxonomies() {
	register_taxonomy_for_object_type( 'category', 'page' );
	register_taxonomy_for_object_type( 'post_tag', 'page' );
	/*	A number of other media category plugins add post categories/tags to attachments, if they did then
		we shouldn't do it again.  WP Media Library Catagories plugin can either use post categories or a 
		user-defined custom taxonomy.  Either way if plugin's option exists we don't need to add it too.
	*/
	if ( !in_array('attachment', get_taxonomy( 'category' )->object_type) && !get_option('wpmlc_settings') ){
		register_taxonomy_for_object_type( 'category', 'attachment' );
	}
	if ( !in_array('attachment', get_taxonomy( 'post_tag' )->object_type) ){
		register_taxonomy_for_object_type( 'post_tag', 'attachment' );
	}
}
function checkforlistmediashortcode($posts) {
    if ( empty($posts) )
        return $posts;
    //false because we have to search through the posts first
    $found = false;
    //search through each post
    foreach ($posts as $post) {
        //check the post content for the short code
        if ( stripos($post->post_content, '[listmedia') )
            //we have found a post with the short code
            $found = true;
            //stop the search
            break;
        }
    if ($found){
        //$listmediadirurl contains the path to the plugin folder
        $listmediadirurl = plugin_dir_url( __FILE__ );
		wp_enqueue_style( 'media-list',$listmediadirurl.'styles/styles.css' );
		wp_register_script( 'media-list',$listmediadirurl.'js/listmediapaging.js', array('jquery') );
		wp_enqueue_script( 'media-list' );
		//Localize the script with new data
		wp_localize_script(
		'media-list',
		'passtojq',
		array(
			'vpages' => __('Pages','media-list' ),
			'voffsep' => __('of','media-list' ),
			'vprev' => __('Prev','media-list'),
			'vnext' => __('Next','media-list'),
			)
		);
    }
    return $posts;
}
function listmediageturlfilesize($listmediaquery, $listmediaformatsize = true){
	$file_url = wp_get_attachment_url( $listmediaquery->ID );
    $head = array_change_key_case(get_headers($file_url, 1));
    //content-length of download (in bytes), read from Content-Length: field
    $clen = isset($head['content-length']) ? $head['content-length'] : 0;
    //cannot retrieve file size, return "-1"
    if (!$clen) {
        return;
    }
    if (!$listmediaformatsize) {
        return $clen; 
		//return size in bytes
    }
    $size = $clen;
    switch ($clen) {
        case $clen < 1024:
            $size = $clen .' B'; break;
        case $clen < 1048576:
            $size = round($clen / 1024,1) .' KB'; break;
        case $clen < 1073741824:
            $size = round($clen / 1048576,1) . ' MB'; break;
        case $clen < 1099511627776:
            $size = round($clen / 1073741824,1) . ' GB'; break;
    }
    return $size; 
	//return formatted size
}
function listmediastitchmimes($listmediaaddstitch){
		$mimetype = explode(",", $listmediaaddstitch);
		$mimeappend = "";
		$i = 0;
		foreach ($mimetype as $mediatype) {
		//add comma so we can concatenate mime types when multiple types are defined in the shortcode
		if ($i > 0){
			$mimeappend	.= ",";
		}
		  switch ($mediatype) {
		  case "pdf":
			$mimeappend .= "application/pdf,application/x-pdf,application/acrobat,applications/vnd.pdf,text/pdf,text/x-pdf";
			break;
		  case "xls":
		  case "excel":
			$mimeappend .= "application/vnd.ms-excel,application/vnd.oasis.opendocument.spreadsheet,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";  
			break;
		  case "doc":
			$mimeappend .= "application/doc,application/vnd.msword,application/vnd.ms-word,application/winword,application/word,application/x-msw6,application/x-msword,application/msword,application/vnd.oasis.opendocument.text,application/vnd.openxmlformats-officedocument.wordprocessingml.document";
			break;
		  case "ppt":
			$mimeappend .= "application/mspowerpnt,application/vnd-mspowerpoint,application/powerpoint,application/x-powerpoint,application/vnd.ms-powerpoint,application/mspowerpoint,application/ms-powerpoint,application/vnd.oasis.opendocument.presentation,application/vnd.openxmlformats-officedocument.presentationml.presentation";
			break;      
		  case "zip":
			$mimeappend .= "application/zip,application/x-zip,application/x-zip-compressed,application/x-compress,application/x-compressed,multipart/x-zip,application/rar,application/x-tar,application/x-7z-compressed";
			break;
		  case "text":
			$mimeappend .= "text/plain,text/csv,text/tab-separated-values,text/calendar,text/richtext,text/css,text/html";
			break;
		  case "audio":
			$mimeappend .= "audio/mpeg,audio/wav,audio/x-ms-wma,audio/midi";
			break;
		  case "images":
			$mimeappend .= "image/jpeg,image/gif,image/png,image/bmp,image/tiff,image/x-icon";
			break;
		  case "other":
			$mimeappend .= "application/sql,application/x-sql,text/sql,text/x-sql,application/octet-stream,application/sql,application/x-sql,text/sql,text/x-sql,application/xml,application/x-xml,text/xml,text/x-xml,application/x-msdownload";
			break;
		  default:
		    $mimeappend .= "image/x-icon";
			break;
			}
			$i++;
		}
		return $mimeappend;
}
function listmediagetthemimetype($listmediamediatype) {
	//check type from shortcode
	switch ($listmediamediatype) {
		case 'pdf':
			$mediatype = 'application/pdf,application/x-pdf,application/acrobat,applications/vnd.pdf,text/pdf,text/x-pdf';
			break;
		case 'doc':
			$mediatype = 'application/doc,application/vnd.msword,application/vnd.ms-word,application/winword,application/word,application/x-msw6,application/x-msword,application/msword,application/vnd.oasis.opendocument.text,application/vnd.openxmlformats-officedocument.wordprocessingml.document';
			break;
		case 'excel':
			$mediatype = 'application/vnd.ms-excel,application/vnd.oasis.opendocument.spreadsheet,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
			break;
		case 'ppt':
			$mediatype = 'application/mspowerpnt,application/vnd-mspowerpoint,application/powerpoint,application/x-powerpoint,application/vnd.ms-powerpoint,application/mspowerpoint,application/ms-powerpoint,application/vnd.oasis.opendocument.presentation,application/vnd.openxmlformats-officedocument.presentationml.presentation';
			break;
		case 'zip':
			$mediatype = 'application/zip,application/x-zip,application/x-zip-compressed,application/x-compress,application/x-compressed,multipart/x-zip,application/rar,application/x-tar,application/x-7z-compressed';
			break;
		case 'text':
			$mediatype = 'text/plain,text/csv,text/tab-separated-values,text/calendar,text/richtext,text/css,text/html';
			break;
		case 'audio':
			$mediatype = 'audio/mpeg,audio/wav,audio/x-ms-wma,audio/midi';
			break;
	    case 'images':
			$mediatype = 'image/jpeg,image/gif,image/png,image/bmp,image/tiff,image/x-icon';
			break;
		case 'other':
			$mediatype = 'application/sql,application/x-sql,text/sql,text/x-sql,application/octet-stream,application/sql,application/x-sql,text/sql,text/x-sql,application/xml,application/x-xml,text/xml,text/x-xml,application/x-msdownload';
			break;
		default:
		    $mediatype = 'image/x-icon';
			break;
	}
}
function listmediaumbrellamimetype($listmediaquery) {
		//pulls the mime and match it to display the umbrella type.
		$iconbymime = get_post_mime_type();
		$type="";
		switch ($iconbymime) {
		  case "application/pdf":
		  case "application/x-pdf": 
		  case "application/acrobat": 
		  case "applications/vnd.pdf":
		  case "text/pdf":
		  case "text/x-pdf":
			$type = "pdf";
			break;
		  case "application/vnd.ms-excel":
		  case "application/vnd.oasis.opendocument.spreadsheet":
		  case "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet": //xlsx
			$type = "xls";
			break;
		  case "application/doc":
		  case "application/vnd.msword": 
		  case "application/vnd.ms-word":
		  case "application/winword":
		  case "application/word":
		  case "application/x-msw6":
		  case "application/x-msword":
		  case "application/msword":
		  case "application/vnd.oasis.opendocument.text":
		  case "application/vnd.openxmlformats-officedocument.wordprocessingml.document": // docx
			$type = "doc";
			break;
		  case "application/mspowerpnt":
		  case "application/vnd-mspowerpoint":
		  case "application/powerpoint":
		  case "application/x-powerpoint":
		  case "application/vnd.ms-powerpoint":
		  case "application/mspowerpoint":
		  case "application/ms-powerpoint":
		  case "application/vnd.oasis.opendocument.presentation":
		  case "application/vnd.openxmlformats-officedocument.presentationml.presentation": //pptx
			$type = "ppt";
			break;      
		  case "application/zip":
		  case "application/x-zip":
		  case "application/x-zip-compressed":
		  case "application/x-compress":
		  case "application/x-compressed":
		  case "multipart/x-zip":
		  case "application/rar":
		  case "application/x-tar":
		  case "application/x-7z-compressed":
		  case "application/octet-stream": // Mac DMG files
			$type = "zip";
			break;	
		  case "text/plain":
		  case "text/csv":
		  case "text/tab-separated-values":
		  case "text/calendar":
		  case "text/richtext":
		  case "text/css":
		  case "text/html":
			$type = "text";
			break;
		  case "audio/mpeg":
			$type = "mp3";
			break;
		  case "audio/wav":
			 $type = "wav";
			 break;
		  case "audio/x-ms-wma":
			$type = "wma";
			break;
			case "audio/midi":
			$type = "mid";
			break;
		  case "image/jpeg":
		    $type = "jpg";
			break;
		  case "image/gif":
		    $type = "gif";
			break;
		  case "image/png":
		    $type = "png";
			break;
		  case "image/bmp":
		    $type = "bmp";
			break;
		  case "image/tiff":
		    $type = "tiff";
			break;
		  case "image/x-icon":
			$type = "icon";
			break;
		  case "application/sql":
		  case "application/x-sql":
		  case "text/sql":
		  case "text/x-sql":
		  case "application/octet-stream":
			$type = "sql";
			break;
		  case "application/xml":
		  case "application/x-xml,":
		  case "text/xml":
		  case "text/x-xml":
			$type = "xml";
			break;
		  case "application/x-msdownload":
		  //case "application/octet-stream":
			$type = "exe";
			break;
		}
		// override mimetype for certain post type
		$ptype = get_post_type();
		switch ($ptype){
			case "post":
				$type = "post";
				break;
			case "page":
				$type = "page";
				break;
			case "tribe_events":
				$type = "event";
				break;
			case "mpcs-course":
				$type = "course";
				break;
		} 
		if($type != "") {
		  return $type;
		} else {	
			// no mimetype so see if we can get it from the permalink
			preg_match('/.*\.(pdf|xls|doc|docx|ppt|zip|txt|mp3|wav|wma|mid|jpg|jpeg|gif|png|bmp|tif|tiff|ico|sql|xml|exe)/i', get_permalink(), $matches);
			if (count($matches) > 0){
				switch ($matches[1]){
					case "docx":
						$type = "doc";
						break;
					case "jpeg":
						$type = "jpg";
						break;
					case "tif":
						$type = "tiff";
						break;
					case "txt":
						$type = "text";
						break;
					case "ico":
						$type = "icon";
						break;
					case "":
						// permalink had no file extension in it so...
						$type = "Posted";
						break;
					default:
						// otherwise return what match found...
						$type = $matches[1];
						break;
				};
			} else {
				$type = "Posted";
			}
		  return $type;
		}	 
}
function listmediamainplugin($listmediaatts = [], $content = null ) {
$listmediaatts = array_change_key_case((array)$listmediaatts, CASE_LOWER);
//unique ID ready per instance
$squid['instance'] = uniqid();
global $post; //post data
$mlout = ''; //output markup
$maxloop = 1; //set ready to count loop iterations.
$listofmimes='';
	//extract shortcode attributes.
    $attributes = shortcode_atts([
               'type' => 'attachment',
			   'mediatype' => 'excel,pdf,doc,zip,ppt,text,audio,images',
			   'order' => 'ASC',
			   'orderby' => 'title',
			   'taxonomy' => '',
			   'terms' => '',
			   'category' => '',
			   'showcats' => 0,
			   'mediaitems' => 10,
			   'paginate' => 1,
			   'sticky' => 1,
			   'style' => '',
			   'max' => 200,
			   'globalitems' => 0,
			   'author' => 'notset',
			   'search' => 0,
			   'filters' => '',
			   'tags' => '',
			   'showtags' => 0,
			   'rml_folder' => null // Real Media Library compatibility
    ], $listmediaatts);
        wp_enqueue_script('media-list');	
		//if post type changed alter steps
		if ( $attributes['type'] == 'attachment' ) { 
		$listofmimes = $this->listmediastitchmimes($attributes['mediatype']);
		$this->listmediagetthemimetype($attributes['mediatype']);
		}

	if ($attributes['taxonomy'] && $attributes['terms'] ){
		
	    if(!is_numeric($attributes['terms'])) { // Category is a slug or name
        $taxonomy = array(
              'relation' => 'OR',
              array(
                'taxonomy' => $attributes['taxonomy'],
                'field'    => 'name',
                'terms'    => array_map('trim', explode(',', $attributes['terms'])),
                'operator' => 'IN'
              ),
              array(
                'taxonomy' => $attributes['taxonomy'],
                'field'    => 'slug',
                'terms'    => array_map('trim', explode(',', $attributes['terms'])),
                'operator' => 'IN'
              )
            );
		  } else { // Category is an ID
				$taxonomy = array(
				  array(
					'taxonomy' => $attributes['taxonomy'],
					'field'    => 'term_id',
					'terms'    => array_map('trim', explode(',', $attributes['terms'])),
					'operator' => 'IN'
				  )
				);
		  }		
	} else {
		$taxonomy = '';
	}
	//build arguments for wp_query
	$args = array(
		'post_parent' => $post->ID,
		'post_type' => $attributes['type'],
		'author_name' => $attributes['author'],
       'post_status' => 'inherit',
 		'post_status' => 'publish',
       'post_mime_type' => $listofmimes,
		'posts_per_page' => -1,
		'order' => $attributes['order'],
		'orderby' => $attributes['orderby'],
        'tax_query' => $taxonomy,
		'category_name' => $attributes['category'],
		'ignore_sticky_posts' => $attributes['sticky'],
		'post_parent__not_in' => array(0),
		'tag' => $attributes['tags'],
		'rml_folder' => $attributes['rml_folder'] // Real Media Library compatibility
    );
    


    
	//check shortcode format & apply defaults if necessary
	foreach($attributes as $arraykey => $number) 
    {
        switch($arraykey ) 
        {
            case 'paginate':
				if(preg_match("/^[^0-9]*$/", $number)){
				$attributes['paginate'] = 1;
				}
                break;
            case 'mediaitems':
				if(preg_match("/^[^0-9]*$/", $number)){
				$attributes['mediaitems'] = 10;
				}
                break;
            case 'sticky':
				if(preg_match("/^[^0-9]*$/", $number)){
				$attributes['sticky'] = 1;
				}
                break;
			case 'max':
				if(preg_match("/^[^0-9]*$/", $number)){
				$attributes['max'] = 200;
				}
				break;
        }
    }
	//update args as needed
	if ($attributes['paginate'] == 0 ){
		$attributes['max'] = $attributes['mediaitems'];//when pagination is disabled we set the max value to the mediaitems value, so max looped items is max items displayed.
	} 
	if ($attributes['author'] == 'notset'){
		unset ($args['author_name']);
	}
	if (($attributes['globalitems'] == 1 && $attributes['type'] == 'attachment')){
		$args['post_status'] = 'any';
		unset ($args['post_parent__not_in']);
		unset ($args['post_parent']);
		unset ($args['ignore_sticky_posts']);
	} elseif ($attributes['type'] == 'attachment' ) {
		unset ($args['ignore_sticky_posts']);
	} elseif ($attributes['type'] == 'post') {
		unset ($args['post_parent__not_in']);
		unset ($args['post_parent']);
		unset ($args['post_mime_type']);
		$args['post_status'] = 'publish';
	} elseif ($attributes['taxonomy'] && $attributes['terms'] ){
		unset ($args['post_parent__not_in']);
		unset ($args['post_parent']);
		unset ($args['ignore_sticky_posts']);
		unset ($args['category']);
//		unset ($args['tag']);
		unset ($args['rml_folder']);
	}
	//instantiate new query instance.
    $listmediaquery = new WP_Query( $args );
     
    //check that we have query results.
    if ( $listmediaquery->have_posts() ) {
        //begin generating markup.
        $mlout .= '<section class="listmedia-embedded-section">';
	    $mlout .= '<div mediajqref="listmedia-construct" id="lmid-' . $squid['instance'] . '"' . 'class="listmedia ' . $attributes['style'] . '" data-instance="' . $squid['instance'] . '" data-token="' . $attributes['mediaitems'] . '" data-paging="' . $attributes['paginate'] . '">';
		if ($attributes['search'] == 1){
		$mlout .= '<div class="listmedia-search ' .$attributes['style'] . '"><input type="text" class="listmedia-search lm-search-' . $squid['instance'] . ' lm-search ' . $attributes['style'] .'"><a class="listmedia-gosearch">'. __('Search','media-list') .'</a>';

		if ($attributes['filters']){
			$mlout .= '<div class="listmedia-search-filters"> - or - <strong>Filter by:</strong> ';
			foreach( array_map('trim', explode(',', $attributes['terms'])) as $filter){
				$mlout .= '<a onclick="this.parentElement.parentElement.firstChild.value=\'\';this.parentElement.parentElement.firstChild.value=\''.$filter.'\';" class="listmedia-gosearch filterlink">'.$filter.'</a>';
			};
			$mlout .= '</div>';
		}
		$mlout .= '</div>';
		
		
		
		
		}
		$mlout .= '<ul class="lm-ul ' . $attributes['style'] . '" style="list-style-type:none;">';
		$tarpin = 0;
        //start looping over the query results and stop when maxloop iterations reaches max set in array.
        while ( $listmediaquery->have_posts() && $maxloop <= $attributes['max'] ) {
			  $listmediaquery->the_post();
			  $filetype = $this->listmediaumbrellamimetype($listmediaquery);
				$mlout .= '<li class="lm-li ' . $attributes['style'] . '">';	
				$mlout .= '<a class="lm-item ' . $attributes['style'] . ' listmedia '. $attributes['style'] .' ' . $filetype . '"';
				$mlout .= 'href="';
				if ( $attributes['type'] == 'attachment' ) {
					
					$mlout .= wp_get_attachment_url ( $listmediaquery->ID );
				} else
				{
					$mlout .= get_permalink();
				}
					if (in_array($filetype, array("sql","xml","exe"))){
						//change switch to allow certain files to be downloaded properly with the correct extension.
						$mlout .= '"download="' . get_the_title() . '.' . $filetype . '">';
					}
					else {
						$mlout .= '"target="_blank">';
					}
					$mlout .= get_the_title();
				
				if ( $attributes['type'] == 'attachment') {
					$filesize = '(' . $this->listmediageturlfilesize($listmediaquery) . ')';
				} else {
					$filesize = '';	// maybe not downloadable
				}
				
				$mlout .= ' <span class="lm-details ' . $attributes['style'] . '"><span class="lm-date">' . get_the_date($listmediaquery->ID) . '</span><br/><span class="lm-type-size"><span class="lm-type">' . $filetype . '</span> <span class="lm-size">' . $filesize . '</span></span></span>';
				$mlout .= '</a>';
				
				if ($attributes['showcats'] == '1'){
					$showcats = '';
				} else {
					// hide them but they're still searchable text
					$showcats = ' style="display:none;"';
				}
				if ($attributes['showtags'] == '1'){
					$showtags = '';
				} else {
					$showtags = ' style="display:none;"';
				}
					if ($attributes['taxonomy']){
						$taxcat = $attributes['taxonomy'];
						if ($attributes['tags']){
							$tags = get_the_terms($listmediaquery->ID,$attributes['tags']);
						} else {
							$tags = [];
						}
					} else {
						$taxcat = 'category';
						if ($attributes['tags']){
							$tags = get_the_tags($listmediaquery->ID);
						} else {
							$tags = [];
						}
					}
					if (!empty($taxcat)){
						$terms = get_the_terms($listmediaquery->ID,$taxcat);
						$terms_list = join(', ', wp_list_pluck(array_reverse($terms), 'name'));
						$mlout .= '<br/><span class="lm-terms-list"'.$showcats.'>'.$terms_list.'</span>';
					}
					if (!empty($tags)){
						$tags_list = join(', ', wp_list_pluck($tags, 'name'));
						$mlout .= '<br/><span class="lm-tags-list"'.$showtags.'>'.$tags_list.'</span>';
					}

					$mlout .= '</li>';
				$maxloop++; //iterate counter
        }
			//close elements
			$mlout .= '</ul></div>';
			$mlout .= '</section>'; 
    } else {
        //output message to let user know that no posts were found.
        $mlout = '<section class="listmedia-embedded-section">';
		$mlout .= '<div class="listmedia-alert" style="background-color:#2196F3;">';
		$mlout .= '<span class="listmedia-closebtn" ';
		$mlout .= 'onclick="';
		$mlout .= "this.parentElement.style.display='none';";
		$mlout .= '"';
		$mlout .= '">&times;</span><strong>Info! </strong>'. __('No posts or attachments to display.','media-list') .'</div>';
        $mlout .= '</section>';
		//end generating markup.
    }
	wp_reset_postdata();
	return $mlout;
}
function listmediamainplugininit() {
	add_shortcode( 'listmedia', array( $this,'listmediamainplugin' ));
}
}//end class
//create object
new listmediapluginclass();
}
/* We need dashicons font on frontend, this assumes WP >3.8 and will not enqueue twice if something else already did it */
function listmedia_icons() {
	wp_enqueue_style( 'dashicons');
}
add_action( 'wp_enqueue_scripts', 'listmedia_icons' );

/*	Add GitHub Update Checker */
	if ( is_admin() ) {
		@require('plugin-update-checker/plugin-update-checker.php');
			$listmediaUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
			'https://github.com/kmhcreative/list-media',
				__FILE__,'list-media'
			);
			$listmediaUpdateChecker->getVcsApi()->enableReleaseAssets();	
	};
?>