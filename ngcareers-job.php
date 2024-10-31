<?php 
 
 fetchjobs($ngcareersslug);

	function fetchjobs($ngcareersslug){
			$jobid = '';
			$pageno = 1;
			$states = getlocs();
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
				
				echo '<div id="primary" class="content-area">
				<div id="content">
		<main id="main" class="site-main" role="main">
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
            </main></div></div>
        <?php 
			}
		else if($count==1){
				$customngcareersjobtitle = $results[0]['job_title']." at ".$results[0]['company'];
				
			echo '<div id="primary" class="content-area">
					<div id="content">
		<main id="main" class="site-main" role="main"><article class="post type-post status-publish format-standard hentry"><div class="brand"><a href="https://ngcareers.com"><img src="'.plugins_url( 'assets/plugin-ngcareers.png', __FILE__ ).'" alt="Ngcareers" /></a></div><div class="clear"></div>';
				for($i=0;$i < $count;$i++) {?>
					
					<header class="entry-header">	
			   <h1 class="entry-title"><a href="/<?php echo $ngcareersslug; ?>/?job=<?php echo $results[$i]['id'];?>&jobtitle=<?php echo urlencode($results[$i]['job_title'].' at '.$results[$i]['company']);?>">
			   <?php echo $results[$i]['job_title']; ?></a></h1>
               </header>
			   <div class="entry-footer">
			<?php echo ucwords($results[$i]['company']); ?></strong> - <em><?php if($results[$i]['sno']!='' && $results[$i]['sno']!=0 && $results[$i]['sno']==1) {?> <?php echo $states[$results[$i]['state_id']];} else if ($results[$i]['sno'] > 1) { echo $results[$i]['sno']." Locations";} else { echo "Not Specified"; }?></em> - <span><?php echo ucwords($results[$i]['jobtype']);?></span></div>
			   <div class="entry-content">
			   <div><?php echo html_entity_decode($results[$i]['description']);?></div>
               
               <div>
			   <h4>REQUIREMENTS</h4>
			   <?php echo html_entity_decode($results[$i]['requirements']);?></div>
               
               <div class="apply"><a href="https://ngcareers.com/pp/<?php echo $results[$i]['id'];?>?site=<?php echo $_SERVER['SERVER_NAME'];?>" target="_blank" class="submit">APPLY NOW</a></div> </div> 
              
	<?php				
				}
			echo '</article></main></div></div>';	
		}
		
		else{
			 
		}
	}
	
	else{
		echo '<div id="primary" class="content-area">
			<div id="content">
		<main id="main" class="site-main" role="main"><article class="post type-post status-publish format-standard hentry"><div class="entry-content">' . $results . '</div></article></main></div></div>';
		}

	}

	
?>
