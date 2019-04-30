/**
 * WordPress dependencies.
 */
const { Button, Dropdown, PanelRow } = wp.components;
const { PluginPostStatusInfo } = wp.editPost;
const { Component } = wp.element;
const { __ } = wp.i18n;

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
							name={ `editor-post-visibility__setting` }
							value={ value }
							onChange={ () => {
								this.setState({
									currentPostType: value,
								});
								window.location.href = window.ptsBlockEditor.changeUrl + '&pts_post_type=' + value;
							} }
							checked={ value === this.state.currentPostType }
							id={ `editor-post-type-switcher-${ value }` }
							className="editor-visibility__dialog-radio"
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
