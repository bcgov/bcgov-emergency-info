import { useSelect } from '@wordpress/data';
import { useBlockProps } from '@wordpress/block-editor';
import { Spinner } from '@wordpress/components';

const Edit = ({ context: { postType, postId } }) => {
    const blockProps = useBlockProps();
    const title = useSelect((select) => {
        // Get current value of the hazard_type input in page attributes panel.
        const editedHazard =
            select('core/editor').getEditedPostAttribute('hazard_type');
        const post = select('core').getEntityRecord(
            'postType',
            postType,
            postId
        );
        let hazardId = 0;
        if (editedHazard) {
            // If the hazard_type has been changed in the page attributes, use it.
            hazardId = editedHazard[0];
        } else if (post?.hazard_type) {
            hazardId = post?.hazard_type[0];
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
            // Generic hazard type should use the hazard_name override meta field value.
            if ('generic' === hazard.slug) {
                return post.acf.hazard_name.value || null;
            }
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
