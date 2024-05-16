import { addFilter } from '@wordpress/hooks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { Fragment } from '@wordpress/element';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Add hideBlock attribute to all blocks.
 *
 * @param {Object} settings Original block settings
 *
 * @returns {Object} Filtered block settings
 */
const addAttributes = ( settings ) => {
    settings.attributes.hideBlock = {
        type: 'boolean',
        default: false,
    };
    return settings;
};

addFilter( 'blocks.registerBlockType', 'emergency-info', addAttributes );

/**
 * Add hide block control to all blocks.
 */
const addInspectorControl = createHigherOrderComponent( ( BlockEdit ) => {
    const component = ( props ) => {
        const { hideBlock } = props.attributes;
        const { setAttributes } = props;

        return (
            <Fragment>
                <BlockEdit { ...props } />
                <InspectorControls>
                    <PanelBody title={ __( 'Visibility' ) }>
                        <ToggleControl
                            label={ __( 'Hide block' ) }
                            checked={ hideBlock }
                            onChange={ ( check ) =>
                                setAttributes( { hideBlock: check } )
                            }
                        />
                    </PanelBody>
                </InspectorControls>
            </Fragment>
        );
    };
    component.displayName = 'withInspectorControl';
    return component;
}, 'withInspectorControl' );

addFilter( 'editor.BlockEdit', 'emergency-info', addInspectorControl );

/**
 * Add class to indicate hidden blocks in editor.
 */
const withCustomAttributeClass = createHigherOrderComponent(
    ( BlockListBlock ) => {
        const component = ( props ) => {
            const { attributes } = props;
            const { hideBlock } = attributes;
            const className = hideBlock ? 'hidden-block' : '';

            return <BlockListBlock { ...props } className={ className } />;
        };
        component.displayName = 'withCustomAttributeClass';
        return component;
    },
    'withCustomAttributeClass'
);

addFilter(
    'editor.BlockListBlock',
    'emergency-info',
    withCustomAttributeClass
);
