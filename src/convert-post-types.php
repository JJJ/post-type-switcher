<?php
/*
Plugin Name: Convert Post Types
Plugin URI: http://stephanieleary.com/plugins/convert-post-types
Version: 1.5
Author: Stephanie Leary
Author URI: http://stephanieleary.com
Description: A bulk conversion utility for post types.
License: GPL2
*/

add_action( 'admin_menu', 'bulk_convert_posts_add_pages' );

function bulk_convert_posts_add_pages() {
	$css = add_management_page( __( 'Convert Post Types', 'convert-post-types' ), __( 'Convert Post Types', 'convert-post-types' ), 'manage_options', 'convert-post-types', 'bulk_convert_post_type_options' );
	add_action( 'admin_head-'.$css, 'bulk_convert_post_type_css' );
}

function bulk_convert_post_type_css() { ?>
	<style type="text/css">
		div.categorychecklistbox { float: left; margin: 1em 1em 1em 0; }
		ul.categorychecklist { height: 15em; width: 20em; overflow-y: scroll; border: 1px solid #dfdfdf; padding: 0 1em; background: #fff; border-radius: 4px; -moz-border-radius: 4px; -webkit-border-radius: 4px; }
		ul.categorychecklist ul.children { margin-left: 1em; }
		p.taginput { float: left; margin: 1em 1em 1em 0; width: 22em; }
		p.taginput input { width: 100%; }
		p.filters select { width: 24em; margin: 1em 1em 1em 0; }
		p.submit { clear: both; }
		p.msg { margin-left: 1em; }
	</style>
	<?php
}

function bulk_convert_post_type_options() {
	if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {  
		$hidden_field_name = 'bulk_convert_post_submit_hidden';
		if( isset( $_POST[ $hidden_field_name ] ) && $_POST[ $hidden_field_name ] == 'Y' ) {
			bulk_convert_posts();
		} // $hidden_field_name ?>
	
    <div class="wrap">
    <?php if( !isset( $_POST[ $hidden_field_name ] ) || $_POST[ $hidden_field_name ] != 'Y' ) { ?>
		<form method="post">
	    <h2><?php _e( 'Convert Post Types', 'convert-post-types' ); ?></h2>
		<p><?php _e( 'With great power comes great responsibility. This process could <strong>really</strong> screw up your database. Please make a backup before proceeding.', 'convert-post-types' ); ?></p>
		<input type="hidden" name="<?php echo esc_attr( $hidden_field_name ); ?>" value="Y">
		<p class="filters">
		<?php
		$typeselect = array();
		if ( isset( $_POST['convert_cat'] ) ) 
			$convert_cat = (int) $_POST['convert_cat']; 
		else 
			$convert_cat = '';
		$post_types = get_post_types( array( 'public' => true ) );
		foreach ( $post_types as $type ) {
			$typeselect[] = sprintf( '<option value="%s">%s</option>', esc_attr( $type ), esc_html( $type ) );
		}
		$typeselect = implode( "\n", $typeselect );
		?>
			<select name="old_post_type">
			<option value="-1"><?php _e( "Convert from...", 'convert-post-types' ); ?></option>
			<?php echo $typeselect; ?>
			</select>
		
			<select name="new_post_type">
			<option value="-1"><?php _e( "Convert to...", 'convert-post-types' ); ?></option>
			<?php echo $typeselect; ?>
			</select>
		
		<?php wp_dropdown_categories( array(
				'name' => 'convert_cat',
				'show_option_none' => __( 'Limit posts to category...', 'convert-post-types' ),
				'hide_empty' => 0,
				'hierarchical' => 1,
				'selected' => $convert_cat,
			)
		 ); ?>
	
		<?php wp_dropdown_pages( array(
		 				'name' => 'page_parent',
						'show_option_none' => __( 'Limit pages to children of...', 'convert-post-types' ),
		 			) );
		 ?>
	
		</p>
		<?php 
			global $wp_taxonomies; 
			$nonhierarchical = '';
		?>
		<?php if ( is_array( $wp_taxonomies ) ) : ?>
		<h4><?php _e( 'Assign custom taxonomy terms', 'convert-post-types' ); ?></h4>
				<?php foreach ( $wp_taxonomies as $tax ) :
				if ( !in_array( $tax->name, array( 'nav_menu', 'link_category', 'podcast_format' ) ) ) : ?>
					<?php 
					if ( !is_taxonomy_hierarchical( $tax->name ) ) :
					// non-hierarchical
						$nonhierarchical .= '<p class="taginput"><label>'.esc_html( $tax->label ).'<br />';
						$nonhierarchical .= '<input type="text" name="'.esc_attr( $tax->name ).'" class="widefloat" /></label></p>';
					else:
					// hierarchical 
					?>
					 	<div class="categorychecklistbox">
							<label><?php echo esc_html( $tax->label ); ?></label><br />
				        <ul class="categorychecklist">
				     	<?php
						wp_terms_checklist( 0, array( 
							           'descendants_and_self' => 0,
							           'selected_cats' => false,
							           'popular_cats' => false,
							           'walker' => null,
							           'taxonomy' => $tax->name,
							           'checked_ontop' => true,
							       )
							 ); 
					?>
					</ul>  </div>
					<?php
					endif;
				    ?>
				<?php
				endif;
				endforeach; 
				echo '<br class="clear" />'.$nonhierarchical;
				?>
		
		<?php endif; ?>

		<p class="submit">
		<input type="submit" name="submit" class="primary button" value="<?php _e( 'Convert &raquo;', 'convert-post-types' ); ?>" />
		</p>
		</form>
		
    <?php } // if $hidden_field_name ?>

    </div>
    
<?php } // if user can
} 

function bulk_convert_posts() {
	// check for invalid post type choices
	if ( $_POST['new_post_type'] == -1 || $_POST['old_post_type'] == -1 ) {
		echo '<p class="error">'.__( 'Could not convert posts. One of the post types was not set.', 'convert-post-types' ).'</p>';
		return;
	}
	if ( !post_type_exists( $_POST['new_post_type'] ) || !post_type_exists( $_POST['old_post_type'] ) ) {
		echo '<p class="error">'.__( 'Could not convert posts. One of the selected post types does not exist.', 'convert-post-types' ).'</p>';
		return;
	}
	
	$query = array( 
		'posts_per_page'	=> -1,
		'post_status' 		=> 'any',
		'post_type'			=> $_POST['old_post_type'],
	 );
	
	if ( !empty( $_POST['convert_cat'] ) && $_POST['convert_cat'] > 1 )
		$query['cat'] = $_POST['convert_cat'];
	
	if ( !empty( $_POST['page_parent'] ) && $_POST['page_parent'] > 0 ) 
		$query['post_parent'] = $_POST['page_parent'];
	
	$items = get_posts( $query );
	
	if ( !is_array( $items ) ) {
		echo '<p class="error">'.__( 'Could not find any posts matching your criteria.', 'convert-post-types' ).'</p>';
		return;
	}
	
	global $wp_taxonomies;
	
	foreach ( $items as $post ) {
		
		// Update the post into the database
		$update['ID'] = $post->ID;
		if ( ! $new_post_type_object = get_post_type_object( $_POST['new_post_type'] ) ) {
			echo '<p class="error">' . sprintf( __( 'Could not convert post #%d. %s', 'convert-post-types' ), $post->ID, __( 'The new post type was not valid.', 'convert-post-types' ) ) . '</p>';
		}
		else {
			// handle post categories now; otherwise all posts will receive the default
			if ( 'post' == $new_post_type_object->name && isset( $_POST['post_category'] ) && !empty( $_POST['post_category'] ) ) {
				wp_set_post_terms( $post->ID, $_POST['post_category'], 'post_category', false );
			}
			
			// set post type
			$post->post_type = $new_post_type_object->name;	
			
			// fix attachment status
			if ( $post->post_status == 'inherit' && $_POST['old_post_type'] == 'attachment' ) {
				$post->post_status = 'publish';	
			}
			
			// clear post parent, if converting children of a certain page
			if ( !empty( $_POST['page_parent'] ) && $_POST['page_parent'] > 0 ) {
				$post->post_parent = 0;
			}
			
			wp_update_post( $post );
			
			// WPML support
			if ( function_exists( 'icl_object_id' ) ) {
			// adjust field 'element_type' in table 'wp_icl_translations'
			// from 'post_OLDNAME' to 'post_NEWNAME'
			// the post_id you look for is in column: 'element_id'

			    if ( $post->post_type == 'revision' ) {
					if ( is_array( $post->ancestors ) ) {
						$ID = $post->ancestors[0];
					}
			    } 
				else {
					$ID = $post->ID;
				}
			    global $wpdb;

				$wpdb->update( 
			          $wpdb->prefix.'icl_translations',
				  		array( 'element_type' => 'post_' . $new_post_type_object->name ),
				  		array( 'element_id' => $ID,'element_type' => 'post_' . $post->post_type )
				 	  );

			    $wpdb->print_error();
			}
		}
		
		// set new taxonomy terms
		foreach ( $wp_taxonomies as $tax ) :
			
			// hierarchical custom taxonomies
			if ( isset( $_POST['tax_input'][$tax->name] ) && !empty( $_POST['tax_input'][$tax->name] ) && is_array( $_POST['tax_input'][$tax->name] ) ) {
				wp_set_post_terms( $post->ID, $_POST['tax_input'][$tax->name], $tax->name, false );
				echo '<p class="msg">'.sprintf( __( 'Set %s to %s', 'convert-post-types' ), $tax->label, $term->$name ).'</p>';
			}
			// all flat taxonomies
			if ( isset( $_POST[$tax->name] ) && !empty( $_POST[$tax->name] ) && 'post_category' != $tax->name ) {
				wp_set_post_terms( $post->ID, $_POST[$tax->name], $tax->name, false );
				if ( 'post_category' == $tax->name )
					echo '<p class="msg">'.sprintf( __( 'Set %s to %s', 'convert-post-types' ), $tax->label, join( ', ', $_POST[$tax->name] ) ).'</p>';
				else
					echo '<p class="msg">'.sprintf( __( 'Set %s to %s', 'convert-post-types' ), $tax->label, $_POST[$tax->name] ).'</p>';
			}
		endforeach;
	}
	echo '<div class="updated"><p><strong>' .__( 'Posts converted.', 'convert-post-types' ). '</strong></p></div>';
}

// i18n
load_plugin_textdomain( 'convert-post-types', '', plugin_dir_path( __FILE__ ) . '/languages' );