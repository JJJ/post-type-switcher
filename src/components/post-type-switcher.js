/**
 * WordPress dependencies.
 */
const { PanelRow } = wp.components;
const { PluginPostStatusInfo } = wp.editPost;

const PostTypeSwitcher = ( { children, className } ) => (
	<PluginPostStatusInfo>
		<PanelRow className={ className }>
			Hello World
		</PanelRow>
	</PluginPostStatusInfo>
);

export default PostTypeSwitcher;
