import ServerSideRender from '@wordpress/server-side-render';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, FormTokenField } from '@wordpress/components';
import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

export const Edit = ( { attributes: { excludedTerms }, setAttributes } ) => {
    const blockProps = useBlockProps();
    const [ terms, setTerms ] = useState( [] );

    // Get array of terms to use as options in FormTokenField input.
    useEffect( () => {
        // Get array of all terms.
        wp.apiFetch( {
            path: 'wp/v2/region',
        } ).then( ( data ) => {
            const terms = data.map(
                ( term ) => `${ term.name } (ID:${ term.id })`
            );
            setTerms( terms );
        } );
    }, [] );

    return (
        <div { ...blockProps }>
            <InspectorControls>
                <PanelBody title={ __( 'Settings' ) }>
                    <FormTokenField
                        label="Excluded Terms"
                        help="Selected terms will not appear as checkbox options in the subscribe form."
                        value={ excludedTerms }
                        suggestions={ terms }
                        onChange={ ( tokens ) =>
                            setAttributes( { excludedTerms: tokens } )
                        }
                    />
                    <PanelBody
                        title={ __( 'All Terms' ) }
                        initialOpen={ false }
                    >
                        <ul>
                            { terms.map( ( term, index ) => {
                                return <li key={ index }>{ term }</li>;
                            } ) }
                        </ul>
                    </PanelBody>
                </PanelBody>
            </InspectorControls>
            <ServerSideRender
                block="emergency-info/subscribe-form"
                attributes={ {
                    excludedTerms: excludedTerms ?? [],
                } }
            />
        </div>
    );
};
