import { useSelect } from '@wordpress/data';
import { useState, useEffect } from '@wordpress/element';
import { PanelBody, TextControl } from '@wordpress/components';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

const Edit = ( {
    attributes,
    setAttributes,
    context: { postType, postId },
} ) => {
    const blockProps = useBlockProps();
    const { valueNum } = attributes;
    const [ icon, setIcon ] = useState( '' );
    const [ label, setLabel ] = useState( '' );
    const [ value, setValue ] = useState( '' );

    const selectPost = useSelect( ( select ) => {
        return select( 'core' ).getEntityRecord( 'postType', postType, postId );
    } );

    useEffect( () => {
        if ( selectPost ) {
            setIcon( selectPost.acf[ 'card_icon_' + valueNum ]?.value );
            setLabel( selectPost.acf[ 'card_label_' + valueNum ]?.value );
            setValue( selectPost.acf[ 'card_value_' + valueNum ]?.value );
        }
    }, [ selectPost, valueNum ] );

    return (
        <div { ...blockProps }>
            { value ? (
                <div className="is-layout-flex flex-nowrap">
                    { icon && (
                        <div className="wp-block-areoi-icon areoi-icon">
                            <i
                                className={ 'text-dark ' + icon }
                                style={ { 'font-size': '24px' } }
                            ></i>
                        </div>
                    ) }
                    <div>
                        <strong>{ label }:</strong> { value }
                    </div>
                </div>
            ) : (
                <div>{ __( 'No value' ) }</div>
            ) }
            <InspectorControls>
                <PanelBody title={ __( 'Card Value Number' ) }>
                    <TextControl
                        type="number"
                        value={ valueNum }
                        onChange={ ( val ) => {
                            setAttributes( { valueNum: parseInt( val ) } );
                        } }
                    />
                </PanelBody>
            </InspectorControls>
        </div>
    );
};

export default Edit;
