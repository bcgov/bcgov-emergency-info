import { useEntityProp } from '@wordpress/core-data';
import { useBlockProps } from '@wordpress/block-editor';

export const Edit = ({ context: { postType, postId } }) => {
    const blockProps = useBlockProps();
    const [link] = useEntityProp('postType', postType, 'link', postId);

    return (
        <div {...blockProps}>
            <ul>
                <li>
                    <a
                        href={
                            '#https://www.facebook.com/sharer/sharer.php?u=' +
                            link
                        }
                    >
                        Facebook
                    </a>
                </li>
                <li>
                    <a href={'#https://twitter.com/intent/tweet?url=' + link}>
                        Twitter
                    </a>
                </li>
            </ul>
        </div>
    );
};
