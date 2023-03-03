import ServerSideRender from '@wordpress/server-side-render';
import { useBlockProps } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';

const edit = (props) => {
	const blockProps = useBlockProps();

	// Get current value of Hazard Type taxonomy from editor.
	// This allows the block to re-render in the editor when taxonomy is changed.
	const { hazardTypes } = useSelect((select) => {
		return {
			hazardTypes:
				select('core/editor').getEditedPostAttribute('hazard_type'),
		};
	});

	props.setAttributes({ hazard_types: hazardTypes });

	return (
		<div {...blockProps}>
			<ServerSideRender
				block="emergency-info/event-meta"
				attributes={props.attributes}
			/>
		</div>
	);
};

export default edit;
