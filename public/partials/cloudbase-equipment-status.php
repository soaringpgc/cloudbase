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


<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div >
<h2> Equipment Summary </h2>

<div hx-get="<?php echo admin_url('admin-ajax.php'); ?>?action=htmx_status_get" 
	hx-trigger="load delay:500ms"
	hx-target="this"
	hx-swap="outerHTML"
	hx-vals='{"function":"cb_status_summary", "details": " <?php  echo( $status_atts["details"]==='true' ? true  : false ); ?> " }'>                      
</div>

<!-- 
<div hx-get="<?php echo admin_url('admin-ajax.php'); ?>?action=htmx_status_get" 
	hx-vals='{ "function":"cb_status_hr_report", "hr_report":"100h"}' 
	hx-trigger="load delay:1000ms" hx-target="#equipment-detail"></div>
 -->

<div id="equipment-detail"></div> 
