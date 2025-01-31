import { useBlockProps } from '@wordpress/block-editor';

/**
 *
 * @param root0
 * @param root0.attributes
 */
export default function save( { attributes } ) {
    const blockProps = useBlockProps.save();
    return <div { ...blockProps }></div>;
}
