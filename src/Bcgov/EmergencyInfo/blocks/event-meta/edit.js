import ServerSideRender from '@wordpress/server-side-render';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { CheckboxControl, PanelBody } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

const Edit = (props) => {
    const blockProps = useBlockProps();

    // Get current value of Hazard Type taxonomy from editor.
    // This allows the block to re-render in the editor when taxonomy is changed.
    const { hazardTypes } = useSelect((select) => {
        return {
            hazardTypes:
                select('core/editor').getEditedPostAttribute('hazard_type'),
        };
    });

    useEffect(() => {
        props.setAttributes({ hazard_types: hazardTypes });
    }, [hazardTypes, props]);

    return (
        <div {...blockProps}>
            <InspectorControls>
                <PanelBody title={__('Detailed display')}>
                    <CheckboxControl
                        label={__("Show details")}
                        checked={props.attributes.detailed}
                        onChange={(check) => {
                            props.setAttributes({ detailed: check });
                        }}
                    />
                </PanelBody>
            </InspectorControls>
            <ServerSideRender
                block="emergency-info/event-meta"
                attributes={props.attributes}
            />
        </div>
    );
};

export default Edit;
