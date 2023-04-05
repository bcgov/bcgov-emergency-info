module.exports = {
    root: true,
    extends: ['./node_modules/@wordpress/scripts/config/.eslintrc'],
    env: {
        jest: true,
    },
    globals: {
        wp: true,
    },
};
