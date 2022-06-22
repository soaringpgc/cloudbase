<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Cloud_Base
 * @subpackage Cloud_Base/public/partials
 */
?>
<!-- 
<script language="JavaScript">
	var cb_admin_tab = "flights";
</script>
 -->


<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div >
<?php 	        
	global $wpdb;
	$table_name = $wpdb->prefix . "cloud_base_aircraft";	
	$table_type = $wpdb->prefix . "cloud_base_aircraft_type";	
	$table_status = $wpdb->prefix . "cloud_base_aircraft_status";	
 	$detail_edit=false;
	if (isset( $status_atts['details'] )) {
		$details = $status_atts['details']==='true' ? true : false ;
		if ($details && current_user_can('edit_users')){
			$detail_edit='true';
		}
	} else {
		$details = false;
	}
//	$detail_edit='true';
	//$details = true ;
	if ($details )	{
	    	$sql = "SELECT * FROM {$table_name} s inner join {$table_type} t on s.aircraft_type=t.id inner join {$table_status} u on s.status=u.id  WHERE s.valid_until is NULL  ORDER BY  s.aircraft_id" ;				
			$items = $wpdb->get_results( $sql, OBJECT);	
 		if ($detail_edit) {

			echo('<div class="table-container"><div class="table-heading ">Fleet Status</div>');
			Echo ('<div class="table-row"><div class="table-col ">Registation</div><div class="table-col ">Competition</div><div class="table-col ">Model</div><div class="table-col ">Status</div>
			<div class="table-col ">Annual Due</div><div class="table-col ">Registration Due</div><div class="table-col ">Transponder</div><div class="table-col ">Comments</div><div class="table-col ">Update</div></div>');
			foreach($items as $item){	
 	
			 	echo ' <form class="table-row"  action="'.admin_url("admin-post.php").'" method="post" id="update_aircraft"> <input type="hidden" name=action value="update_aircraft">
			 		<div class="table-col">'.$item->registration.'</div>';
			 	echo ' <div class="table-col">'.$item->compitition_id.'</div>';
				echo ' <div class="table-col">'.$item->model.'</div>';			
                echo '<div class="table-col"> <select name="status" id="status" >' ;
        			$sql = "SELECT * FROM ". $table_status . " WHERE active = 1 ORDER BY title ASC ";
        			$astats = $wpdb->get_results( $sql, OBJECT);       	
        			foreach($astats as $key){ 	
        				if ($key->id == $item->status) {
        					echo '<option value=' . $key->id . ' selected>'. $key->title . ' </option>';
        				} else {
        					echo '<option value=' . $key->id . '>'. $key->title . '</option>';
        				}
            		};
         		echo ( '</select> </div>');
				echo ' <div class="table-col"><input type="date" id="annual_due_date" name="annual_due_date" value="'.$item->annual_due_date.'"></div>';
				echo ' <div class="table-col"><input type="date" id="registration_due_date" name="registration_due_date" value="'.$item->registration_due_date.'"></div>';
				echo ' <div class="table-col"><input type="date" id="transponder_due" name="transponder_due" value="'.$item->transponder_due.'"></div>';
				echo ' <div class="table-col"><input type="text" id="comment" name="comment" value="'.$item->comment.'"> </div>';
				echo ' <input type="hidden" id="key" name="key" value="'.$item->aircraft_id.'">';
				echo ' <div class="table-col"><button type="submit" value="submit">Update</button></div>';
				echo '<input type="hidden" name="source_page" value="';
				echo  the_permalink() . '">';
			    wp_nonce_field('update_aircraft'); 
		 		echo(' </form>');
		 	}

 		} else {
			echo('<div class="table-container"><div class="table-heading ">Fleet Status</div>');
			Echo ('<div class="table-row"><div class="table-col ">Registation</div><div class="table-col ">Compition</div><div class="table-col ">Model</div><div class="table-col ">Status</div>
			<div class="table-col ">Annual Due</div><div class="table-col ">Registration Due</div><div class="table-col ">Comments</div></div>');
			foreach($items as $item){
//			var_dump($item);			 			 	
			 	echo '<div class="table-row"> <div class="table-col">'.$item->registration.'</div>';
			 	echo ' <div class="table-col">'.$item->compitition_id.'</div>';  
				echo ' <div class="table-col">'.$item->model.'</div>';
				echo ' <div class="table-col">'.$item->title.'</div>';				
				echo ' <div class="table-col">'.$item->annual_due_date.'</div>';
				echo ' <div class="table-col">'.$item->registration_due_date.'</div>';
				echo ' <div class="table-col">'.$item->transponder_due.'</div>';
				echo ' <div class="table-col">'.$item->comment.'</div>';
		 		echo('</div>');
		 	}
		 }
	} else {
		
	 if( current_user_can( 'read' ) ) {	      
	     $sql = "SELECT s.compitition_id as cid, u.title as status, u.color as color, s.date_updated as udate FROM {$table_name} s inner join {$table_type} t on s.aircraft_type=t.id inner join {$table_status} u on s.status=u.id  WHERE s.valid_until is NULL " ;				
	//     $sql = "SELECT s.compitition_id as cid, u.title as status, u.color as color  FROM wp_cloud_base_aircraft s inner join wp_cloud_base_aircraft_type t on s.aircraft_type=t.id inner join wp_cloud_base_aircraft_status u on s.status=u.id  WHERE s.valid_until is NULL " ;				
		  $items = $wpdb->get_results( $sql, OBJECT);
		  echo '<div><div class="hform"> Fleet Status:</div>';
		   	$ldate = '0000-00-00';
		 	foreach($items as $item){
		 		if ($item->udate > $ldate){
		 			$ldate = $item->udate ;
		 		}
		 		echo ' <div class="hform" style="color:'.$item->color.'">'.$item->cid.'</div>';
		 	}	
		  $date_time = strtotime($ldate) ;
		  echo '</div><div> Updated: ' . date('d M Y', $date_time). '</div>';	 
//		  echo '</div><div> Last Updated: ' .$ldate. '  Details </div>';	 
	}     
}
?>

</div>


