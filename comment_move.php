<?php

/*
Plugin Name: Comment Move
Plugin URI: http://naatan.com/category/wordpress/plugins/comment-move/
Description: Adds the ability to move comments between posts / pages to the comment edit page.
Version: 1.0
Author: Nathan Rijksen
Author URI: http://naatan.com/
*/

add_action('admin_menu', 'comment_move_define_metabox');
add_action('admin_head', 'comment_move_define_javascript');
add_action('edit_comment', 'comment_move_update_comment');

function comment_move_update_comment($id) {
	
	if (empty($_POST['comment_move_new_pid']) OR !is_numeric($_POST['comment_move_new_pid'])) return;
	
	global $wpdb;
	$pid = $wpdb->get_var("SELECT comment_post_ID FROM $wpdb->comments WHERE comment_id='".$id."'");
	$wpdb->query("UPDATE $wpdb->posts SET comment_count=comment_count-1 WHERE ID='".$pid."'");
	$wpdb->query("UPDATE $wpdb->posts SET comment_count=comment_count+1 WHERE ID='".$_POST['comment_move_new_pid']."'");
	$wpdb->query("UPDATE $wpdb->comments SET comment_post_ID='".$_POST['comment_move_new_pid']."' WHERE comment_ID='".$id."'");
	
}

function comment_move_define_metabox() {
	add_meta_box('commentmovediv', 'Move Comment', 'comment_move_add_sidebar', 'comment','normal');
}

function comment_move_define_javascript() {
	?>
	<script type="text/javascript">
	
		function cm_selectPost() {
			
			if (jQuery("#cm_selPost").val()!='-1' && jQuery("#cm_selPost").val()!='')
				jQuery("#comment_move_new_pid").val(jQuery("#cm_selPost").val());
			
		}
		
		jQuery(document).ready(function() {
			
			jQuery("#cm_selPost").change( function() {
				
				cm_selectPost();
				
			});
			
			jQuery("#cm_selPost_click").click( function() {
				
				cm_selectPost();
				
			});
			
		});
	
	</script>
	<?php
}

function comment_move_add_sidebar() {
	?>
	
	<label for="comment_move_new_pid">
		New Post/Page ID: <input type="text" id="comment_move_new_pid" name="comment_move_new_pid" value="" />
	</label>
	&nbsp; <big><a href="javascript:" id="cm_selPost_click">«</a></big> &nbsp;
	<label for="cm_selPost">
	<select name="cm_selPost" id="cm_selPost">
		<option></option>
	<?php
		global $post;
		$posts = get_posts('numberposts=-1&post_type=post');
		echo '<option value="-1">POSTS</option>';
		echo '<option value="-1">===============</option>';
		foreach($posts as $post) {
			echo '<option value="'.$post->ID.'">'.the_title('','',FALSE).'</option>';
		}
		$posts = get_posts('numberposts=-1&post_type=page');
		echo '<option value="-1"></option>';
		echo '<option value="-1">PAGES</option>';
		echo '<option value="-1">===============</option>';
		foreach($posts as $post) {
			echo '<option value="'.$post->ID.'">'.the_title('','',FALSE).'</option>';
		}
	?>
	</select>
	</label>
	<?php
}

?>