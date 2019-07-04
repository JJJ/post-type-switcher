/* global wp */

/**
 * WordPress dependencies.
 */
const { Button, Dropdown, PanelRow } = wp.components;
const { PluginPostStatusInfo } = wp.editPost;
const { Component } = wp.element;
const { sprintf, __ } = wp.i18n;

class PostTypeSwitcherForm extends Component {
	constructor( props ) {
		super( ...arguments );

		this.state = {
			currentPostType: window.ptsBlockEditor.currentPostType,
		};
	}
	render() {
		return (
			<fieldset key="post-type-switcher-selector" className="editor-post-visibility__dialog-fieldset">
				<legend className="editor-post-visibility__dialog-legend">
					{ __( 'Post Type Switcher', 'post-type-switcher' ) }
				</legend>
				{ window.ptsBlockEditor.availablePostTypes.map( ( { value, label } ) => (
					<div key={ value } className="editor-post-visibility__choice">
						<input
							type="radio"
							className="editor-visibility__dialog-radio"
							name={ `editor-post-visibility__setting` }
							value={ value }
							onChange={ () => {
								const oldPostType = this.state.currentPostType;
								this.setState({
									currentPostType: value,
								});
								const message = sprintf( __( "Are you sure you want to change this from a '%s' to a '%s'?", 'pts' ), oldPostType, value );
								if ( window.confirm( message ) ) {
									window.location.href = window.ptsBlockEditor.changeUrl + '&pts_post_type=' + value;
								} else {
									this.setState({
										currentPostType: oldPostType,
									});
								}
							} }
							checked={ value === this.state.currentPostType }
							id={ `editor-post-type-switcher-${ value }` }
						/>
						<label
							htmlFor={ `editor-post-type-switcher-${ value }` }
							className="editor-post-visibility__dialog-label"
						>
							{ label }
						</label>
					</div>
				) ) }
			</fieldset>
		);
	}
}

const PostTypeSwitcher = ( { children, className } ) => {
	return(
		<PluginPostStatusInfo>
			<span>{ __( 'Post Type' ) }</span>
			<Dropdown
				position="bottom left"
				contentClassName="edit-post-post-visibility__dialog"
				renderToggle={ ( { isOpen, onToggle } ) => (
					<Button
						type="button"
						aria-expanded={ isOpen }
						className="edit-post-post-visibility__toggle"
						onClick={ onToggle }
						isLink
					>
						{window.ptsBlockEditor.currentPostTypeLabel}
					</Button>
				) }
				renderContent={ () => <PostTypeSwitcherForm /> }
			/>
		</PluginPostStatusInfo>
	);
};

export default PostTypeSwitcher;
