import { registerPlugin } from '@wordpress/plugins';
import { default as PostTypeSwitcher } from './components/post-type-switcher';

registerPlugin( 'post-type-switcher', {
	render: PostTypeSwitcher,
} );
