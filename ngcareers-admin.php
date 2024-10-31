<?php 
$array_of_options = get_option( 'ngcareers_settings' );
$public_key = $array_of_options[ 'ngcareers_public_key' ];
$private_key = $array_of_options[ 'ngcareers_private_key' ];
$specs = $array_of_options[ 'ngcareers_spec' ];
$locs = $array_of_options[ 'ngcareers_loc' ];
$url = $array_of_options[ 'ngcareers_url' ];
$use = $array_of_options[ 'ngcareers_use_slug' ];

 ?>
<div class="wrap">
<h2>Ngcareers Settings</h2>
<form action="" method="post">
<table class="form-table">
<tbody><tr>
<th scope="row"><label for="ngcareers_public_key">Public Key</label></th>
<td><input name="ngcareers_public_key" type="text" id="ngcareers_public_key" value="<?php echo $public_key; ?>" class="regular-text"></td>
</tr>
<tr>
<th scope="row"><label for="ngcareers_private_key">Private Key</label></th>
<td><input name="ngcareers_private_key" type="text"  id="ngcareers_private_key" value="<?php echo $private_key; ?>" class="regular-text">
<p class="description" id="tagline-description">Do not share your private Key</p></td>
<div><a href="https://ngcareers.com/api/v1/public-getkey" target="keyz" onClick="document.getElementById('keyz').style.display='block';">Get New Keys</a></div>
</tr>
<tr>
<th></th> 
<td><iframe name="keyz" id="keyz" style="width:500px; height:100px; border:1px solid #ccc; display:none" frameborder="1" scrolling="no"></iframe>  </td>
</tr>
<tr>
<th scope="row"><label for="ngcareers_specs">Job Specializations</label></th>
<td>

<select id="ngcareers_specs" name="ngcareers_spec[]" multiple="multiple" aria-describedby="specs-description">
<option value="" selected="selected"> All Fields </option>
	<?php 
	$catz =  getspecs();	
	asort($catz);
	foreach($catz as $specid => $specname):
	$extras = '';
	
	if(isset($specs)){$spec_array=explode('*',$specs);
	if(in_array($specid,$spec_array)) {
					$extras = 'selected="selected"';
				  }
		}
	echo '<option value="'.$specid.'" '.$extras.'>'.$specname.'</option>';
	endforeach;
?>
</select>
<p class="description" id="specs-description">Choose specializations of jobs to display.</p>
</td>

</tr>
<tr>
<th scope="row"><label for="ngcareers_loc">Job Locations</label></th>
<td>
<select name="ngcareers_loc[]" multiple="multiple" id="ngcareers_loc" class="multiple" aria-describedby="locs-description">
			<option value="" selected="selected">All Locations</option>
             <?php 
		   $states = getlocs(); foreach($states as $state1n => $state1d):
		    $extras = '';
		   if(isset($locs)){ if(is_array($locs)){$loc_array = $locs;}else{$loc_array=explode('*',$locs);}
			  if(in_array($state1n,$loc_array)) {
				$extras = 'selected="selected"';
			  }}
		echo '<option value="'.$state1n.'" '.$extras.'>'.$state1d.'</option>';
		endforeach;
		?>   
	</select>
    <p class="description" id="locs-description">Choose locations of jobs to display.</p>
    </td>
</tr>

<tr>
<th scope="row"><label for="ngcareers_url">URL Slug</label></th>
<td><input name="ngcareers_url" type="text" id="ngcareers_url" value="<?php echo $url; ?>" aria-describedby="url-description" class="regular-text">
<p class="description" id="url-description">Link to job listing page - www.yourdomain.com/URLSlug (No spaces)</p></td>
</tr>

<tr>
<th scope="row"><label for="ngcareers_use_slug">Use Auto URL</label></th>
<td>
<select name="ngcareers_use_slug" id="ngcareers_use_slug" aria-describedby="use-description">
			<option value="1" <?php if($use==1){ ?>selected="selected"<?php }?>>Yes</option>
            <option value="0" <?php if($use==0){ ?>selected="selected"<?php }?>>No</option>
            </select>
<p class="description" id="use-description">If Yes is selected - Automatically creates a sub page with above slug, such that www.yourdomain.com/URLSlug opens the job board. If you choose no, create a custom page and add [ngcareers] in the text body and save.</p></td>
</tr>
	
	</tbody></table>

<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>
</form>
</div>

