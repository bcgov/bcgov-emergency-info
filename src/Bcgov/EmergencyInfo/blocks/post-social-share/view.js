import $ from 'jquery';

$(document).ready(() => {
    /**
     * Updates icon to indicate that the post link has been copied to their clipboard.
     * @param {bool} isCopied Whether the link has been copied.
     */
    const showLinkCopiedState = (isCopied) => {
        $('.link-copied-status')
            .toggleClass('bi-clipboard-check-fill', isCopied)
            .toggleClass('bi-clipboard-plus-fill', !isCopied);
    };

    // Copy post link to clipboard on click.
    $('.copy-link').on('click', () => {
        const link = $('.copy-link').data('url');
        navigator.clipboard.writeText(link);

        showLinkCopiedState(true);
    });

    // Initialize link copied state to uncopied.
    showLinkCopiedState(false);
});
