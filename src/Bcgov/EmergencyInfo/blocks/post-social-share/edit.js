import { useEntityProp } from '@wordpress/core-data';
import { useBlockProps } from '@wordpress/block-editor';

export const Edit = ({ context: { postType, postId } }) => {
    const blockProps = useBlockProps();
    const [link] = useEntityProp('postType', postType, 'link', postId);

    return (
        <div {...blockProps}>
            <div className="dropdown">
                <button
                    className="btn position-relative btn-outline-primary dropdown-toggle"
                    data-bs-toggle="dropdown"
                    data-bs-auto-close="true"
                    aria-expanded="false"
                    href="##"
                >
                    <i
                        className="bi-send-fill me-3 align-middle"
                        style={{ 'font-size': '24px' }}
                    ></i>
                    Share
                </button>
                <div className="dropdown-menu" aria-labelledby="">
                    <a
                        className="dropdown-item"
                        href={'#https://twitter.com/intent/tweet?url=' + link}
                    >
                        <span className="areoi-icon">
                            <i className="text-dark bi-twitter"></i>
                        </span>
                        Share on Twitter
                    </a>
                    <a
                        className="dropdown-item"
                        href={
                            '#https://www.facebook.com/sharer/sharer.php?u=' +
                            link
                        }
                    >
                        <span className="areoi-icon">
                            <i className="text-dark bi-facebook"></i>
                        </span>
                        Share on Facebook
                    </a>
                </div>
            </div>
        </div>
    );
};
