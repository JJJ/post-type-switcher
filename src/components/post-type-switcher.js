/* global wp */

/**
 * WordPress dependencies.
 */
const { Button, Dropdown, PanelRow } = wp.components;
const { PluginPostStatusInfo } = wp.editor;
const { Component } = wp.element;
const { sprintf, __ } = wp.i18n;

/**
 * The PostTypeSwitcherForm component.
 */
class PostTypeSwitcherForm extends Component {

	/**
	 * Constructor.
	 *
	 * @param {*} props 
	 */
	constructor( props ) {
		super( ...arguments );

		this.state = {
			currentPostType: window.ptsBlockEditor.currentPostType,
		};
	}

	/**
	 * Render the form for the post type switcher.
	 *
	 * @returns {Object} The rendered component.
	 */
	render() {
		return (
			<fieldset key="post-type-switcher-selector" className="editor-post-type__dialog-fieldset">
				<legend className="editor-post-type__dialog-legend">
					{ __( 'Post Type', 'post-type-switcher' ) }
				</legend>
				{ window.ptsBlockEditor.availablePostTypes.map( ( { value, label } ) => (
					<div key={ value } className="editor-post-type__choice">
						<input
							type="radio"
							className="editor-post-type__dialog-radio"
							name={ `editor-post-type__setting` }
							value={ value }
							onChange={ () => {
								const oldPostType = this.state.currentPostType;
								this.setState({
									currentPostType: value,
								});
								const message = sprintf( __( "Are you sure you want to change this from a '%s' to a '%s'?", 'post-type-switcher' ), oldPostType, value );
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
							className="editor-post-type__dialog-label"
						>
							{ label }
						</label>
					</div>
				) ) }
			</fieldset>
		);
	}
}

/**
 * Define the PostTypeSwitcher component.
 *
 * @param {*} props
 */
const PostTypeSwitcher = () => {

	/**
	 * Render the post type switcher.
	 *
	 * @returns {Object} The rendered component.
	 */
	return(
		<PluginPostStatusInfo>
			<div className="edit-post-post-type">
				<div className="editor-post-panel__row-label">{ __( 'Post Type', 'post-type-switcher' ) }</div>
				<div className="editor-post-panel__row-control">
					<Dropdown
						popoverProps={ { placement: 'left-start', offset: 138, shift: true } }
						contentClassName="edit-post-post-type__dialog"
						renderToggle={ ( { isOpen, onToggle } ) => (
							<Button
								type="button"
								aria-expanded={ isOpen }
								aria-label={ sprintf( __( 'Change post type: %s', 'post-type-switcher' ), window.ptsBlockEditor.currentPostType ) }
								className="edit-post-post-type__toggle is-compact is-tertiary"
								onClick={ onToggle }
							>
								{window.ptsBlockEditor.currentPostTypeLabel}
							</Button>
						) }
						renderContent={ () => <PostTypeSwitcherForm /> }
					/>
				</div>
			</div>
		</PluginPostStatusInfo>
	);
};

export default PostTypeSwitcher;
