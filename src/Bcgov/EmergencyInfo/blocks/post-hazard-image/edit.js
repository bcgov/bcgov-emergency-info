import { useSelect } from '@wordpress/data';
import { useBlockProps } from '@wordpress/block-editor';
import { Spinner } from '@wordpress/components';

const getMediaSourceUrlBySizeSlug = (media, slug) => {
    return media?.media_details?.sizes?.[slug]?.source_url || media?.source_url;
};

const Edit = ({ context: { postType, postId } }) => {
    const blockProps = useBlockProps();
    const image = useSelect((select) => {
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
            hazardId = post.hazard_type[0];
        }

        // Get the term data using the current hazard_type term id.
        const hazard = select('core').getEntityRecord(
            'taxonomy',
            'hazard_type',
            hazardId
        );

        // Get the media attachment data for the hazard's image.
        let media = null;
        if (hazard) {
            media = select('core').getMedia(hazard.acf.hazard_image.value, {
                context: 'view',
            });
        }
        return media;
    });

    const imageUrl = getMediaSourceUrlBySizeSlug(image, 'medium');

    return (
        <div {...blockProps}>
            {image ? (
                <img
                    className={'hazard-image'}
                    src={imageUrl}
                    alt={image.alt_text}
                />
            ) : (
                <Spinner />
            )}
        </div>
    );
};

export default Edit;
