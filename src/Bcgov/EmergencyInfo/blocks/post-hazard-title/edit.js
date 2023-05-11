import { useSelect } from '@wordpress/data';
import { useBlockProps } from '@wordpress/block-editor';
import { Spinner } from '@wordpress/components';

const Edit = ({ context: { postType, postId } }) => {
    const blockProps = useBlockProps();
    const title = useSelect((select) => {
        // Get current value of the hazard_type input in page attributes panel.
        const editedHazard =
            select('core/editor').getEditedPostAttribute('hazard_type');
        let hazardId = 0;
        if (editedHazard) {
            // If the hazard_type has been changed in the page attributes, use it.
            hazardId = editedHazard[0];
        } else {
            // Otherwise get the current hazard_type of the event post.
            const post = select('core').getEntityRecord(
                'postType',
                postType,
                postId
            );

            if (post?.hazard_type) {
                hazardId = post?.hazard_type[0];
            }
        }

        if (!hazardId) {
            return null;
        }

        // Get the term data using the current hazard_type term id.
        const hazard = select('core').getEntityRecord(
            'taxonomy',
            'hazard_type',
            hazardId
        );

        // Get the hazard's title.
        if (hazard) {
            return hazard.name;
        }
        return null;
    });

    blockProps.className += ' hazard-text';

    return title ? (
        <h1 {...blockProps}>{title}</h1>
    ) : (
        <div>
            <Spinner />
        </div>
    );
};

export default Edit;
