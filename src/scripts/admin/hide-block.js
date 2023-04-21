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
 * @return {Object} Filtered block settings
 */
function addAttributes(settings) {
    settings.attributes.hideBlock = {
        type: 'boolean',
        default: false,
    };
    return settings;
}

addFilter('blocks.registerBlockType', 'emergency-info', addAttributes);

/**
 * Add hide block control to all blocks.
 */
const addInspectorControl = createHigherOrderComponent((BlockEdit) => {
    return (props) => {
        const { hideBlock } = props.attributes;
        const { setAttributes } = props;

        return (
            <Fragment>
                <BlockEdit {...props} />
                <InspectorControls>
                    <PanelBody title={__('Visibility')}>
                        <ToggleControl
                            label={__('Hide block')}
                            checked={hideBlock}
                            onChange={(check) =>
                                setAttributes({ hideBlock: check })
                            }
                        />
                    </PanelBody>
                </InspectorControls>
            </Fragment>
        );
    };
}, 'withInspectorControl');

addFilter('editor.BlockEdit', 'emergency-info', addInspectorControl);

/**
 * Add class to indicate hidden blocks in editor.
 */
const withCustomAttributeClass = createHigherOrderComponent(
    (BlockListBlock) => {
        return (props) => {
            const { attributes } = props;
            const { hideBlock } = attributes;
            const className = hideBlock ? 'hidden-block' : '';

            return <BlockListBlock {...props} className={className} />;
        };
    },
    'withCustomAttributeClass'
);

addFilter('editor.BlockListBlock', 'emergency-info', withCustomAttributeClass);
