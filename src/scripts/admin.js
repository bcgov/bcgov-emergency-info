import '../styles/admin.scss';
import '../styles/public.scss';
import './admin/hazard-type-select';
import './admin/hide-block';
import './admin/region-group';

/**
 * All the scripting for the admin area goes here.
 */

import { registerBlockVariation } from '@wordpress/blocks';

/**
 * Block variation to create a paragraph block named Recommended Actions
 * that has the block bindings pre-configured.
 */
registerBlockVariation( 'core/paragraph', {
    name: 'recommended_actions',
    title: 'Recommended Actions',
    attributes: {
        metadata: {
            bindings: {
                content: {
                    source: 'core/post-meta',
                    args: {
                        key: 'recommended_actions',
                    },
                },
            },
        },
    },
} );
