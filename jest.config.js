const defaultConfig = require( '@wordpress/scripts/config/jest-unit.config' );

const config = {
	...defaultConfig,
    moduleDirectories: ['node_modules', 'src/scripts'],
    /**
     * Needed to remedy issue: https://github.com/WordPress/gutenberg/issues/43132
     */
	transformIgnorePatterns: [ 'node_modules/(?!(is-plain-obj))' ],
    globals: {
        wp: {
            i18n: true, // Fixes issues with unit tests thinking that wp.i18n is undefined.
        },
    },
};

module.exports = config;