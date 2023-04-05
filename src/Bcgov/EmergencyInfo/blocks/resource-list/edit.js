import ServerSideRender from '@wordpress/server-side-render';
import { useSelect } from '@wordpress/data';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { CheckboxControl, PanelBody } from '@wordpress/components';
import { withState } from '@wordpress/compose';
import { __ } from '@wordpress/i18n';

const Edit = (props) => {
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
    })(({ hazard_types: hazardTypes }) =>
        hazardTerms.map((option) => (
            <CheckboxControl
                key={option.id}
                label={option.name}
                checked={hazardTypes[option.id]}
                onChange={(check) => {
                    if (check) {
                        hazardTypes[option.id] = option.slug;
                    } else {
                        delete hazardTypes[option.id];
                    }
                    props.setAttributes({ hazard_types: hazardTypes });
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

export default Edit;
