import { useEffect } from '@wordpress/element';
import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

/**
 * Attempting to pass metadata to this block via block bindings. Not currently working.
 * @param root0
 * @param root0.attributes
 * @param root0.attributes.input
 */
export const Edit = ( { attributes: { input } } ) => {
    const blockProps = useBlockProps();

    const alerts = [];

    useEffect( () => {
        // Split the string by newline characters
        // const lines = input.split('\n');
        // Split each line by the ";" character
        // alerts = lines.map(line => {
        //     const columns = line.split(';');
        //     return {
        //         subject: columns[0],
        //         body: columns[1],
        //         date: columns[2]
        //     }
        // });
        console.log( input );
    }, [ input ] );

    return (
        <div { ...blockProps }>
            { /* {alerts.map((alert, index) => (
                <div key={index}>
                    <h3>{alert.subject}</h3>
                    <p>{alert.body}</p>
                    <small>{alert.date}</small>
                </div>
            ))} */ }
        </div>
    );
};

export default Edit;
