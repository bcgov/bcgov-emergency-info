import { useSelect } from '@wordpress/data';
import { useState, useEffect } from '@wordpress/element';
import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

const Edit = ({ context: { postType, postId } }) => {
    const blockProps = useBlockProps();
    const [hasEmergencyAlert, setHasEmergencyAlert] = useState(false);

    const selectPost = useSelect((select) => {
        return select('core').getEntityRecord('postType', postType, postId);
    });

    useEffect(() => {
        if (selectPost && selectPost.acf.has_emergency_alert) {
            setHasEmergencyAlert(
                selectPost.acf.has_emergency_alert.value_formatted
            );
        }
    }, [selectPost, hasEmergencyAlert]);

    return (
        <div {...blockProps}>
            {hasEmergencyAlert && (
                <div className="emergency-alert-pill badge rounded-pill">
                    <i className="bi bi-broadcast"></i>
                    <span> {__('BC Emergency Alert Issued')}</span>
                </div>
            )}
        </div>
    );
};

export default Edit;
