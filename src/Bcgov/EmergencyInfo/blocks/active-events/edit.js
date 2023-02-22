import ServerSideRender from '@wordpress/server-side-render';
import { useBlockProps } from '@wordpress/block-editor';

const edit = (props) => {
	const blockProps = useBlockProps();
	return (
		<div {...blockProps}>
			<ServerSideRender
				block="emergency-info/active-events"
				attributes={props.attributes}
			/>
		</div>
	);
};

export default edit;
