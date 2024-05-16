import ServerSideRender from '@wordpress/server-side-render';
import { useBlockProps } from '@wordpress/block-editor';

const Edit = () => {
    const blockProps = useBlockProps();

    return (
        <div { ...blockProps }>
            <ServerSideRender block="emergency-info/post-event-status" />
        </div>
    );
};

export default Edit;
