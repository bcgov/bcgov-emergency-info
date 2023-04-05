import ServerSideRender from '@wordpress/server-side-render';
import { useBlockProps } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { useEffect } from '@wordpress/element';

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
            <ServerSideRender
                block="emergency-info/event-meta"
                attributes={props.attributes}
            />
        </div>
    );
};

export default Edit;
