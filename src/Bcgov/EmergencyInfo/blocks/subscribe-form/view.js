import $ from 'jquery';

$(document).ready(() => {
    // Add listener on changes to post type checkboxes. Toggles taxonomy checkboxes.
    $('.post-type-section .post-type-checkbox').on('change', ($event) => {
        const target = $event.target;
        const taxonomyFieldSets = $(target)
            .parents('.post-type-section')
            .find('fieldset');
        const animationSpeed = 'fast';
        if (target.checked) {
            taxonomyFieldSets.hide(animationSpeed);
        } else {
            taxonomyFieldSets.show(animationSpeed);
        }
    });
});
