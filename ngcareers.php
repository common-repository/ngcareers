<?php
    /*
    Plugin Name: Ngcareers Job Board
    Plugin URI: https://blog.ngcareers.com/ngcareers-plugin/
    Description: Plugin for displaying jobs in Nigeria from Ngcareers.com, this plugin allows you to show jobs by category/specialization in a page, your visitors can view details and apply.
    Author: Ngcareers
    Version: 1.13
    Author URI: https://ngcareers.com
    */

	 
	//add_action('activate_ngcareers/ngcareers.php', 'ngcareers_install');
	register_activation_hook(__FILE__, 'ngcareers_install');
	register_deactivation_hook(__FILE__, 'ngcareers_uninstall');
	
	$ngcareersslug = '';
	$ngpageslug = '';
	$ngcareersuse = 0;
	
	function ngcareers_regkeys(){
		
		$response = wp_remote_get( 'https://ngcareers.com/api/v1/public-getkey', array('headers' => array('Content-Type'  => ' application/json', 'Referer' => $_SERVER['SERVER_NAME'])) );
		$body = wp_remote_retrieve_body( $response );
		$keys = json_decode($body, true);
		return $keys;
		
	}
	
	
	function ngcareers_install(){
		$keys = array();
		$keys = ngcareers_regkeys();
		$myoptions = array('ngcareers_public_key'=>'', 'ngcareers_private_key' => '', 'ngcareers_spec'=>'', 'ngcareers_loc' => '', 'ngcareers_url' => 'jobs', 'ngcareers_use_slug' => 1);
		if(!empty($keys)){
			$myoptions['ngcareers_public_key'] = $keys['public_key'];	
			$myoptions['ngcareers_private_key'] = $keys['private_key'];
		}
		
		if(false == get_option('ngcareers_settings')){
		add_option('ngcareers_settings', $myoptions);

		}
	}
	

	function ngcareers_wp_title($title) {
	global $ngcareersslug;
	global $ngpageslug;
	$check = 0;

	if(is_page()){
	global $post;
   	$post_slug=$post->post_name;
	if($post_slug == $ngcareersslug){
	$check = 1;
	}
	}
	else{
	
	if ($ngpageslug == $ngcareersslug) {
	$check = 1;
	}
	}
	if($check==1){
	if(isset($_GET['jobtitle'])){
		$title = urldecode($_GET['jobtitle']);
	}
	else{
	$title = "Latest Jobs in Nigeria";
	}
	// Add the site name.
	$title = $title . ' | ' . get_bloginfo('name');
	}
	return $title;
	}
	
	function ngcareers_meta_tags() {
	global $ngcareersslug;
	global $ngpageslug;
	$check = 0;

	if(is_page()){
	global $post;
   	$post_slug=$post->post_name;
	if($post_slug == $ngcareersslug){
	$check = 1;
	}
	}
	else{
	
	if ($ngpageslug == $ngcareersslug) {
	$check = 1;
	}
	}
	if($check==1){
	if(isset($_GET['jobtitle'])){
	echo '<meta name="description" content="'.$_GET['jobtitle'].' in Nigeria '. get_bloginfo('name').' powered by Ngcareers.com" />';
	echo '<meta name="keywords" content="'.$_GET['jobtitle'].', jobs in Nigeria, '. get_bloginfo('name').', jobs by Ngcareers.com, vacancies in Nigeria, jobs, vacancy, ngcareers" />';
	}
	else{
	echo '<meta name="description" content="View all latest jobs in Nigeria by '. get_bloginfo('name').' powered by Ngcareers.com" />';
	echo '<meta name="keywords" content="careers nigeria, jobs in Nigeria, '. get_bloginfo('name').', jobs by Ngcareers.com, vacancies in Nigeria, jobs, vacancy, ngcareers" />';
	}
	}
}
	function ngcareers_uninstall(){
		
		delete_option('ngcareers_settings');
		flush_rewrite_rules();
		
	}
	
	function ngcareers_styles() {
	wp_enqueue_style( 'NgcareersStylesheet', plugins_url('style.css', __FILE__) );
}
	add_action( 'wp_enqueue_scripts', 'ngcareers_styles' );
		//add_action('init', 'action_init_redirect');
	
	
	function action_init_redirect() {
	  add_rewrite_rule('/jobs/?', 'index.php?joblist=ngcareers-job', 'top');
	 }
	
	 add_filter('query_vars', 'filter_query_vars');
	
	 function filter_query_vars($query_vars) {
		global $ngcareersslug;
		global $ngcareersuse;
		 $myoptions = get_option('ngcareers_settings');
		 $ngcareersslug = $myoptions['ngcareers_url'];
		$ngcareersuse =  $myoptions['ngcareers_use_slug'];
	  return $query_vars;
	  }
	
	 add_action('parse_request', 'action_parse_request');
	add_filter( 'wp_title', 'ngcareers_wp_title', 20);
	add_action( 'wp_head', 'ngcareers_meta_tags' );
	 function action_parse_request(&$wp) {
		global $ngpageslug;
		global $ngcareersslug;
		global $ngcareersuse;
		$ngpageslug = $wp->query_vars['pagename'];
		if($wp->query_vars['pagename'] == $ngcareersslug && $ngcareersuse==1){
	  //if (array_key_exists('joblist', $wp->query_vars)) {
		 get_header();
		 require "ngcareers-job.php";
		 get_sidebar(); 
		 get_footer(); 
		 exit();
	   }
	  } 
	  
	  
  
	function ngcareers_joblist($id, $pageno=1){
		
		$myoptions = get_option('ngcareers_settings');

		if(!empty($myoptions)){
		if($id==''){
		$url = 'https://ngcareers.com/api/v1/jobslist';
		if($myoptions['ngcareers_spec'] != '' || $myoptions['ngcareers_loc'] != '' || $pageno!=null){
		$url = 'https://ngcareers.com/api/v1/jobslist/?';
		if($pageno!=null){$url .= 'page='.$pageno;}
		if($myoptions['ngcareers_spec'] != ''){$url .= '&spec='.$myoptions['ngcareers_spec'];}
		if($myoptions['ngcareers_loc'] != ''){ $url .= '&loc='.$myoptions['ngcareers_loc'];}
		}
		}else {
			
			$url = 'https://ngcareers.com/api/v1/jobslist/'.$id;
		}
		
		
			$reqtime = time();
			

			$public_key = $myoptions['ngcareers_public_key']; 
			$private_key = $myoptions['ngcareers_private_key']; 
			$accesstoken = hash_hmac('sha1', $public_key.$reqtime,$private_key);
			$site_url = $_SERVER['SERVER_NAME'];

			//set headers
			$args = array('headers' => array('Content-Type'  => ' application/json', 'Referer' => $site_url, 'token' =>$accesstoken, 'client_id' => $public_key, 'requestTime' => $reqtime));
			$response = wp_remote_get( $url, $args);
			$results = wp_remote_retrieve_body( $response );
			$http_code = wp_remote_retrieve_response_code( $response );
			
			if($http_code == 200){
				
				return json_decode($results, true);
			}
			else {
				
				return $results;
			}
}
	}
	
	function ngcareers_settings()
	{
		if($_POST['ngcareers_public_key']){
			$options_update = array('ngcareers_public_key'=>'', 'ngcareers_private_key' => '', 'ngcareers_spec'=>'', 'ngcareers_loc' => '');
			if(!empty($_POST['ngcareers_spec'])){
				
				$options_update['ngcareers_spec'] = trim(implode('*', $_POST['ngcareers_spec']));
				
			}
			
			if(!empty($_POST['ngcareers_loc'])){
				
				$options_update['ngcareers_loc'] = trim(implode('*', $_POST['ngcareers_loc']));
			}
			$url = 'jobs';
			if($options_update['ngcareers_url']!=''){$url = trim(htmlspecialchars($options_update['ngcareers_url']));}
			
			if($_POST['ngcareers_public_key']!='' && $_POST['ngcareers_private_key']!=''){
				
				$options_update['ngcareers_public_key'] = trim($_POST['ngcareers_public_key']);
				$options_update['ngcareers_private_key'] = trim($_POST['ngcareers_private_key']);
				$options_update['ngcareers_url'] = $url;
				$options_update['ngcareers_use_slug'] = trim($_POST['ngcareers_use_slug']);
				update_option('ngcareers_settings', $options_update);
			}
			}
			
    include 'ngcareers-admin.php';
	}
 
	function ngcareers_admin_actions()
	{
    	add_options_page(
			"Ngcareers", 
			"Ngcareers", 
			1, 
			"ngcareers-admin", 
			"ngcareers_settings");
	}
 
	
	add_action('admin_menu', 'ngcareers_admin_actions');
	
	function getspecs(){
		
	$catz = array( '3' => 'Banking', '4' => 'ICT/ Software', '7' => 'Oil and Gas', '9'=>'Finance / Accounting', '10' => 'Telecom', '11' => 'Consulting','12' => 'Graduate / Entry-Level','13' => 'Sales/ Business Development','15' => 'Other', '28' => 'Driving / Haulage', '40' => 'Executive / Management','50' => 'Engineering','53' => 'Customer Service / Accounts Mgt','82' => 'Technical/ Artisan', '89' => 'Aviation/ Airline', '92' => 'Law/ Legal','118' => 'Human Resources / Recruitment','124' => 'Security / Intelligence', '125' => 'Administration/ Office/ Operations','146' => 'Teaching / Education / Research', '192' => 'Secretarial', '228' => 'Automotive/ Car Services', '240' => 'Internship/ Industrial Training', '269' => 'Medical/ Health', '308' => 'Maritime Services / Shipping', '333' => 'Surveying / Real Estate / Property', '336' => 'Construction / Mining', '345' => 'Media / Art','351' => 'NGO / Community Services', '353' => 'Government Agencies', '907' => 'Advertising/ PR / Marketing', '946' => 'Research/ Survey', '1033' => 'Transport / Logistics / Supply', '1184' => 'Hospitality / Tourism / Travels','1490' => 'Military / Para-Military', '1532' => 'Production / Manufacturing', '2022' => 'Project/ Safety/ Risk Management','2658' => 'Analyst/ Quality Control','2759' => 'Procurement/ Purchasing','6563' => 'Agriculture / Agro Allied');
  return $catz;	
		
	}
	
	function getlocs(){
		
		$states= array (1 => "Abia", 2 => "Abuja", 3 => "Adamawa", 4 => "Akwa ibom", 5 => "Anambra", 6 => "Bauchi", 7 => "Bayelsa", 8 => "Benue", 9 => "Borno", 10 => "Cross River", 11 => "Delta", 12 => "Edo", 13 => "Ebonyi", 14 => "Ekiti", 15 => "Enugu", 16 => "Gombe", 17 => "Imo", 18 => "Jigawa", 19 => "Kaduna", 20 => "Kano", 21 => "Katsina", 22 => "Kebbi", 23 => "Kogi ", 24 => "Kwara ", 25 => "Lagos", 26 => "Niger", 27 => "Ogun", 28 => "Ondo", 29 => "Osun", 30 => "Oyo", 31 => "Nassarawa", 32 => "Plateau", 33 => "Rivers (PH)", 34 => "Sokoto", 35 => "Taraba", 36 => "Yobe", 37 => "Zamfara", 38 => "Other");
		return $states;
	}
		
	function ngListjobs(){
			global $post;

			global $ngcareersslug;
			$states = getlocs();
		if($ngcareersslug == ''){
    		$ngcareersslug=$post->post_name;
		}
			$jobid = '';
			$pageno = 1;
		if(isset($_GET['job'])){
			if(is_numeric($_GET['job'])){
			$jobid= trim($_GET['job']);
			}
		}
		
		if(isset($_GET['pageno'])){
			if(is_numeric($_GET['pageno'])){
			$pageno= trim($_GET['pageno']);
			}
		}
		$nextpageno = $pageno + 1;
			$results = ngcareers_joblist($jobid, $pageno);
			if(is_array($results)){
			$count = count($results);
			if($count>1){
				
				echo '<div id="content" class="content-area content">
		<div class="brand"><a href="https://ngcareers.com"><img src="'.plugins_url( 'assets/plugin-ngcareers.png', __FILE__ ).'" alt="Ngcareers" /></a></div><div class="clear"></div>';
				for($i=0;$i < $count;$i++) {?>
					
					<article class="post type-post status-publish format-standard hentry"><header class="entry-header">	
			   <h2 class="entry-title"><a href="/<?php echo $ngcareersslug; ?>/?job=<?php echo $results[$i]['id'];?>&jobtitle=<?php echo urlencode($results[$i]['job_title'].' at '.$results[$i]['company']);?>">
			   <?php echo $results[$i]['job_title']; ?></a></h2>
               </header>
               <div class="entry-content"><?php echo substr(strip_tags(html_entity_decode($results[$i]['description'])),0,200);?>...<a href="/<?php echo $ngcareersslug; ?>/?job=<?php echo $results[$i]['id'];?>&jobtitle=<?php echo $results[$i]['job_title'].' '.$results[$i]['company'];?>" class="more">
			   Read More</a> </div> 
			    <div class="entry-footer">
			<strong><?php echo ucwords($results[$i]['company']); ?></strong> - <em><?php if($results[$i]['sno']!='' && $results[$i]['sno']!=0 && $results[$i]['sno']==1) {?> <?php echo $states[$results[$i]['state_id']];} else if ($results[$i]['sno'] > 1) { echo $results[$i]['sno']." Locations";} else { echo "Not Specified"; }?></em> - <em><?php echo ucwords($results[$i]['jobtype']);?></em> <strong>Posted on</strong>: <em> <?php echo $results[$i]['advert_date']; ?></em></div>
			   
			</article>
	<?php				
				}
			
		?>
         <div class="clear"></div>
        	<div class="nav-links meta-nav pagination" style="width:100px; float:none; margin:10px auto !important;"><a class="page-numbers" href="/<?php echo $ngcareersslug;?>/?pageno=<?php echo $nextpageno; ?>">Next</a></div>
            <div class="clear"></div>
            </div>
        <?php 
			}
		else if($count==1){
				$customngcareersjobtitle = $results[0]['job_title']." at ".$results[0]['company'];
				
			echo '<div id="content" class="content-area content">
		<article class="post type-post status-publish format-standard hentry"><div class="brand"><a href="https://ngcareers.com"><img src="'.plugins_url( 'assets/plugin-ngcareers.png', __FILE__ ).'" alt="Ngcareers" /></a></div><div class="clear"></div>';
				for($i=0;$i < $count;$i++) {?>
					
					<header class="entry-header">	
			   <h1 class="entry-title"><a href="/<?php echo $ngcareersslug; ?>/?job=<?php echo $results[$i]['id'];?>&jobtitle=<?php echo urlencode($results[$i]['job_title'].' at '.$results[$i]['company']);?>">
			   <?php echo $results[$i]['job_title']; ?></a></h1>
               </header>
			   <div class="entry-footer">
			<strong><?php echo ucwords($results[$i]['company']); ?></strong> - <em><?php if($results[$i]['sno']!='' && $results[$i]['sno']!=0 && $results[$i]['sno']==1) {?> <?php echo $states[$results[$i]['state_id']];} else if ($results[$i]['sno'] > 1) { echo $results[$i]['sno']." Locations";} else { echo "Not Specified"; }?></em> - <span><?php echo ucwords($results[$i]['jobtype']);?></span></div>
			   <div class="entry-content">
			   <div><?php echo html_entity_decode($results[$i]['description']);?></div>
               
               <div>
			   <h4>REQUIREMENTS</h4>
			   <?php echo html_entity_decode($results[$i]['requirements']);?></div>
               
               <div class="apply"><a href="https://ngcareers.com/pp/<?php echo $results[$i]['id'];?>?site=<?php echo $_SERVER['SERVER_NAME'];?>" target="_blank" class="submit">APPLY NOW</a></div> </div> 
              
	<?php				
				}
			echo '</article></div>';	
		}
		
		else{
			 
		}
	}
	
	else{
		echo '<div id="content" class="content-area content">
		<article class="post type-post status-publish format-standard hentry"><div class="entry-content">' . $results . '</div></article></div>';
		}

	}
	add_shortcode('ngcareers', 'ngListjobs');
	
	
	?>