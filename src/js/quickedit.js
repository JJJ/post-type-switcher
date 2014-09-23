function pts_quick_edit() {

	var $ = jQuery;
	var _edit = inlineEditPost.edit;

	inlineEditPost.edit = function( id ) {

		var args = [].slice.call(arguments );

		_edit.apply( this, args );

		if ( typeof( id ) == 'object' ) {
			id = this.getId( id );
		}

		var

		// editRow is the quick-edit row, containing the inputs that need to be updated
		editRow   = $( '#edit-' + id ),

		// postRow is the row shown when a book isn't being edited, which also holds the existing values.
		postRow   = $( '#post-' + id ),
		
		// get the existing values
		post_type = $( '.post_type', postRow ).data( 'post-type' );
		
		// set the values in the quick-editor
		$( 'select[name="pts_post_type"] option[value="' + post_type + '"]', editRow ).attr( 'selected', 'selected' );
	};
}

// Another way of ensuring inlineEditPost.edit isn't patched until it's defined
if ( inlineEditPost ) {
	pts_quick_edit();
} else {
	jQuery( pts_quick_edit );
}