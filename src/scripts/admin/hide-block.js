import { addFilter } from '@wordpress/hooks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { Fragment } from '@wordpress/element';
import { BlockControls, InspectorControls } from '@wordpress/block-editor';
import {
    Button,
    Dashicon,
    PanelBody,
    ToggleControl,
    ToolbarGroup,
    ToolbarItem,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Add hideBlock attribute to all blocks.
 *
 * @param {Object} props Original block properties.
 *
 * @returns {Object} Filtered block properties.
 */
const addAttribute = ( props ) => {
    // These blocks cause "invalid attributes" errors in editor when hideBlock attribute is added to them.
    const EXCLUDED_BLOCKS = [
        'core/archives',
        'core/calendar',
        'core/latest-comments',
        'core/tag-cloud',
    ];

    if (
        ! EXCLUDED_BLOCKS.includes( props.name ) &&
        Object.hasOwn( props, 'attributes' )
    ) {
        props.attributes.hideBlock = {
            type: 'boolean',
            default: false,
        };
    }
    return props;
};

addFilter(
    'blocks.registerBlockType',
    'emergency-info/hideblock-attribute',
    addAttribute
);

/**
 * Add hide block control to all blocks.
 */
const addControls = createHigherOrderComponent( ( BlockEdit ) => {
    const component = ( props ) => {
        const { hideBlock } = props.attributes;
        const { setAttributes } = props;

        return (
            <Fragment>
                <div className={ hideBlock ? 'hidden-block' : '' }>
                    <BlockEdit { ...props } />
                </div>

                <BlockControls>
                    <ToolbarGroup>
                        <ToolbarItem
                            as={ Button }
                            onClick={ () => {
                                {
                                    true === hideBlock
                                        ? setAttributes( {
                                              hideBlock: false,
                                          } )
                                        : setAttributes( {
                                              hideBlock: true,
                                          } );
                                }
                            } }
                        >
                            { true === hideBlock ? (
                                <Dashicon icon="hidden" />
                            ) : (
                                <Dashicon icon="visibility" />
                            ) }
                        </ToolbarItem>
                    </ToolbarGroup>
                </BlockControls>
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

addFilter(
    'editor.BlockEdit',
    'emergency-info/hideblock-controls',
    addControls
);
