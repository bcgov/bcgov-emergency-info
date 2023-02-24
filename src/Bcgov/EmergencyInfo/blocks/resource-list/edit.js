import ServerSideRender from '@wordpress/server-side-render';
import { useSelect } from '@wordpress/data';
import { useBlockProps } from '@wordpress/block-editor';
import { InspectorControls } from '@wordpress/block-editor';
import { CheckboxControl, PanelBody } from '@wordpress/components';
import { withState } from '@wordpress/compose';
import { __ } from '@wordpress/i18n';

const edit = (props) => {
	const blockProps = useBlockProps();
	// Get Hazard Type taxonomy terms.
	let hazardTerms = useSelect((select) => {
		return select('core').getEntityRecords('taxonomy', 'hazard_type');
	}, []);
	if (!hazardTerms) {
		hazardTerms = [];
	}

	// Build checkbox inputs for Hazard Type terms.
	const HazardTypeCheckboxes = withState({
		hazard_types: Object.assign(
			new Object(),
			props.attributes.hazard_types
		),
	})(({ hazard_types: hazard_types }) =>
		hazardTerms.map((option) => (
			<CheckboxControl
				key={option.id}
				label={option.name}
				checked={hazard_types[option.id]}
				onChange={(check) => {
					check
						? (hazard_types[option.id] = option.slug)
						: delete hazard_types[option.id];
					props.setAttributes({ hazard_types: hazard_types });
				}}
			/>
		))
	);

	return (
		<div {...blockProps}>
			<InspectorControls>
				<PanelBody title={__('Hazard Types')}>
					<HazardTypeCheckboxes />
				</PanelBody>
			</InspectorControls>
			<ServerSideRender
				block="emergency-info/resource-list"
				attributes={props.attributes}
				className={props.className}
			/>
		</div>
	);
};

export default edit;
