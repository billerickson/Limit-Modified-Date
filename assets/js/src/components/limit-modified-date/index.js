import { withSelect, withDispatch } from '@wordpress/data';
import { PluginPostStatusInfo } from '@wordpress/edit-post';
import { CheckboxControl } from '@wordpress/components';
import { TextControl } from '@wordpress/components';
import { compose } from '@wordpress/compose';
import { Component } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { registerPlugin } from '@wordpress/plugins';

export class LimitModifiedDate extends Component {

	render() {
		// Nested object destructuring.
		const {
			meta: {
				limit_modified_date: LimitModifiedDate,
			} = {},
			updateMeta,
		} = this.props;

		return (
			<PluginPostStatusInfo>
				<CheckboxControl
					label={ __( 'Don\'t update the modified date' ) }
					checked={ LimitModifiedDate }
					onChange={ ( LimitModifiedDate ) => {
						updateMeta( { limit_modified_date: LimitModifiedDate || false } );
					} }
				/>
			</PluginPostStatusInfo>
		);
	}
}


export default compose( [
	withSelect( ( select ) => {
		const { getEditedPostAttribute, getCurrentPost } = select( 'core/editor' );

		return {
			meta: getEditedPostAttribute( 'meta' ),
			lastModified: getCurrentPost().modified,
		};
	} ),
	withDispatch( ( dispatch, { meta, lastModified } ) => {
		const { editPost } = dispatch( 'core/editor' );

		return {
			updateMeta( newMeta ) {
				newMeta.limit_modified_date = newMeta.limit_modified_date ? '1' : '';
				newMeta.last_modified_date = lastModified;
				editPost( { meta: { ...meta, ...newMeta } } ); // Important: Old and new meta need to be merged in a non-mutating way!
			},
		};
	} )
] )( LimitModifiedDate );
