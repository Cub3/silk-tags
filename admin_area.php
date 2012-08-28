<?php

/* 
Silk Tags Admin Page
-------------------------------------------------------
*/

$current_date = date("d/m/Y");

?>

<script language="javascript">
jQuery(document).ready(function(){

jQuery("#add_table_row").click(function() {
jQuery('#silk_tag_table > tbody:last').append('<tr><td>&nbsp;</td><td><input type="text" name="tag_name[]" class="input" size="40" maxlength="30" value="Insert Tag Name" onkeypress="return textonly(event);"></td><td><input type="text" name="tag_weight[]" class="input" size="10" maxlength="2" value="#" onkeypress="return numbersonly(event);"></td><td><?php echo $current_date; ?></td></tr>');
});

});

function edit_btn(tag_id) {
	var tag_select = ".tag_number_" + tag_id;
	var tag_name = jQuery(tag_select+" .tag_name").html();
	var tag_weight = jQuery(tag_select+" .tag_weight").html();
	jQuery(tag_select+" .tag_name").replaceWith('<td width="55%" class="tag_name"><input type="hidden" name="tag_edit_id[]" value="' + tag_id + '"><input type="text" name="tag_edit_name[] class="input" size="40" maxlength="30" value="' + tag_name + '" onkeypress="return textonly(event);"></td>');
	jQuery(tag_select+" .tag_weight").replaceWith('<td class="tag_weight"><input type="text" name="tag_edit_weight[]" class="input" size="10" maxlength="2" value="' + tag_weight + '" onkeypress="return numbersonly(event);"></td>');
}

function textonly(e){
var code;
if (!e) var e = window.event;
if (e.keyCode) code = e.keyCode;
else if (e.which) code = e.which;
var character = String.fromCharCode(code);
    var AllowRegex  = /^[\ba-zA-Z\s-]$/;
    if (AllowRegex.test(character)) return true;     
    return false; 
}

function numbersonly(evt) {
  var theEvent = evt || window.event;
  var key = theEvent.keyCode || theEvent.which;
  key = String.fromCharCode( key );
  var regex = /[0-9]|\./;
  if( !regex.test(key) ) {
    theEvent.returnValue = false;
    if(theEvent.preventDefault) theEvent.preventDefault();
  }
}

</script>


<div class="wrap"><h2>Silk Tags Admin Page</h2></div><br /><a class='button-secondary' href='#' id="add_table_row" title='Create New Tag'>Create New Tag</a><br /><br />
<form name="insert_new_tag" id="insert_new_tag" class="wrap" method="post" action="<?php echo bloginfo('wpurl'); ?>/wp-admin/admin.php?page=silk_tags/admin_area.php">
<table id="silk_tag_table" class="widefat" width="100%" cellpadding="3" cellspacing="3">
    <thead>
    	<tr>
			<th><?php _e('Tag ID','silk_tags') ?></th>
			<th><?php _e('Tag Name','silk_tags') ?></th>
			<th><?php _e('Tag Weight (1-99)','silk_tags') ?></th>
			<th><?php _e('Creation Date','silk_tags') ?></th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
    	</tr>
    </thead>
    <tbody>
    	<?php 
    	$the_tags = $wpdb->get_results("SELECT * FROM " . SILK_TAGS_TABLE . " ORDER BY tag_id ASC"); 
    		foreach($the_tags as $tag){ ?>
    			<tr class="tag_number_<?php echo $tag->tag_id; ?>">
					<td><?php echo $tag->tag_id; ?></td>
					<td width="55%" class="tag_name"><?php echo $tag->tag_name; ?></td>
					<td class="tag_weight"><?php echo $tag->tag_weight; ?></td>
					<td><?php $fix_date = strtotime($tag->tag_creation_date); echo date("d/m/Y", $fix_date);?></td>
					<td width="25"><a href="#" onclick="edit_btn(<?php echo $tag->tag_id; ?>)"><img src="<?php echo plugin_dir_url(); ?>silk_tags/images/edit.png"></a></td>
					<td width="25"><a href="?page=silk_tags/admin_area.php&action=delete&delete_id=<?php echo $tag->tag_id; ?>" class="delete_btn"><img src="<?php echo plugin_dir_url(); ?>silk_tags/images/delete.png"></a></td>
    			</tr>
    	<?php
    		}
    	?>
    </tbody>
    <tfoot>
    	<tr>
			<th><?php _e('Tag ID','silk_tags') ?></th>    		
			<th><?php _e('Tag Name','silk_tags') ?></th>
			<th><?php _e('Tag Weight (1-99)','silk_tags') ?></th>
			<th><?php _e('Creation Date','silk_tags') ?></th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>			
    	</tr>
    </tfoot>      
</table>
<br />
<input class='button-primary' type='submit' name='Save' value='<?php _e('Save Options'); ?>' id='submitbutton' />
</form>