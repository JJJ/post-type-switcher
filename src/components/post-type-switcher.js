/**
 * WordPress dependencies.
 */
const { Button, Dropdown, PanelRow } = wp.components;
const { PluginPostStatusInfo } = wp.editPost;
const { __ } = wp.i18n;

const PostTypeSwitcherForm = () => {
	return (
		<fieldset key="post-type-switcher-selector" className="editor-post-visibility__dialog-fieldset">
			<legend className="editor-post-visibility__dialog-legend">
				{ __( 'Post Type Switcher', 'pts' ) }
			</legend>
			{ ptsBlockEditor.availablePostTypes.map( ( { value, label } ) => (
				<div key={ value } className="editor-post-visibility__choice">
					<input
						type="radio"
						name={ `editor-post-visibility__setting` }
						value={ value }
						checked={ value === ptsBlockEditor.currentPostType }
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
};

const PostTypeSwitcher = ( { children, className } ) => {
	const onToggle = () => {
		return false;
	};
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
						{ptsBlockEditor.currentPostTypeLabel}
					</Button>
				) }
				renderContent={ () => <PostTypeSwitcherForm /> }
			/>
		</PluginPostStatusInfo>
	);
};

export default PostTypeSwitcher;
