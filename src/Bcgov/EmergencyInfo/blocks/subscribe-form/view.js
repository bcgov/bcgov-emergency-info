import $ from 'jquery';

$(() => {
    // The hidden multiselect input containing selected regions.
    const regionSelect = $('#region-select');

    // The input for entering autocomplete search.
    const regionAutocomplete = $('#region-autocomplete-input');

    // The list of available terms to autocomplete from. Passed in from PHP.
    // eslint-disable-next-line no-undef
    const availableTerms = terms;

    /**
     * Updates region select input validity.
     * Necessary because if the form is made invalid due to this input its validity won't be updated
     * because it isn't directly changed by the user.
     */
    const updateRegionSelectValidity = () => {
        const autocomplete = document.getElementById(
            'region-autocomplete-input'
        );
        autocomplete.setCustomValidity('');
    };

    // When the all regions radio value is changed, toggle the region section.
    const selectAllRegions = $('input[name="tax_region_all"]');
    selectAllRegions.on('change', (event) => {
        const value = event.target.value;
        if ('1' === value) {
            // Hide all region inputs, disable the underlying select input.
            $('.region-section').hide();
            $('.all-region-section').show();
            regionSelect.prop('disabled', true);
        } else {
            // Show all region inputs, enable the underlying select input.
            $('.region-section').show();
            $('.all-region-section').hide();
            regionSelect.prop('disabled', false);
        }
        updateRegionSelectValidity();
    });
    $('input[name="tax_region_all"]:checked').trigger('change');

    /**
     * Adds a term to the currently selected terms.
     * @param {string} id The term id.
     */
    const addSelectedTerm = (id) => {
        const selectedTerms = regionSelect.val();
        selectedTerms.push(id);
        regionSelect.val(selectedTerms);
        updateRegionSelectValidity();
    };

    /**
     * Removes a term from the currently selected terms.
     * @param {string} id The term id.
     */
    const removeSelectedTerm = (id) => {
        const selectedTerms = regionSelect.val();
        const index = selectedTerms.indexOf(id);
        if (index !== -1) {
            selectedTerms.splice(index, 1);
            regionSelect.val(selectedTerms);
        }
        updateRegionSelectValidity();
    };

    /**
     * Renders the currently selected terms.
     */
    const renderTerms = () => {
        let termList = '';
        regionSelect.val().forEach((termId) => {
            const term = availableTerms.findLast((t) => {
                return t.value === +termId;
            });
            // Create a li with a badge and a remove button.
            termList += `<li aria-label="${term.label}" tabindex="0">
                <span class="region-pill">
                    <span>
                        ${term.label}
                    </span>
                    <button class="btn btn-secondary" aria-label="Remove ${term.label}" data-id="${term.value}" tabindex="0"><i class="bi-x-circle-fill"></i></button>
                </span>
            </li>`;
        });

        if (!termList) {
            // If no terms selected, remove message.
            $('.region-autocomplete-label').html('');
        } else {
            $('.region-autocomplete-label').html('Your locations:');
        }
        $('.region-list').html(termList);

        // Add listener for clicks of the remove button on each term.
        $('.region-list button').on('click', (event) => {
            const removeId = event.currentTarget.dataset.id;
            removeSelectedTerm(removeId);
            renderTerms();
        });
    };

    regionAutocomplete
        // Don't navigate away from the field on tab when selecting an item.
        .on('keydown', function (event) {
            if (
                event.keyCode === $.ui.keyCode.TAB &&
                $(this).autocomplete('instance').menu.active
            ) {
                event.preventDefault();
            }
        })
        .autocomplete({
            minLength: 0,
            source: availableTerms,
            position: {
                of: '.region-autocomplete',
            },
            appendTo: '#listbox-wrapper',
            classes: { 'ui-autocomplete': 'soft-shadow' },
            focus: () => {
                // Update aria attributes when focusing on an item.
                const id = $('#listbox-wrapper')
                    .find('.ui-state-active')
                    .attr('id');
                regionAutocomplete.attr('aria-activedescendant', id);
                $('.ui-autocomplete div').attr('aria-selected', 'false');
                $('.ui-autocomplete div.ui-state-active').attr(
                    'aria-selected',
                    'true'
                );
                return false;
            },
            select: (event, ui) => {
                const termId = ui.item.value;
                // If the term isn't already selected, select it.
                if (!regionSelect.val().includes(termId)) {
                    addSelectedTerm(termId);
                    renderTerms();
                }

                regionAutocomplete.val('');
                return false;
            },
            response: (event, ui) => {
                // Show no results message if the search returned no hits.
                if (0 === ui.content.length) {
                    ui.content.push({ value: '', label: 'No results found.' });
                }
            },
        });

    // Override jQueryUI Autocomplete renderItem() to add aria role.
    $.ui.autocomplete.prototype._renderItem = (ul, item) => {
        return $('<li></li>')
            .append('<div role="option">' + item.label + '</div>')
            .appendTo(ul);
    };

    // Override jQueryUI Autocomplete renderMenu() to add aria role.
    // eslint-disable-line prefer-arrow/prefer-arrow-functions
    $.ui.autocomplete.prototype._renderMenu = function (ul, items) {
        const $this = this;
        $.each(items, (index, item) => {
            $this._renderItemData(ul, item);
        });
        $(ul).attr('role', 'listbox');
    };

    // Initialize terms display in case terms were passed to the page.
    renderTerms();

    // Validate region select input on submit.
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach((form) => {
        form.addEventListener(
            'submit',
            (event) => {
                const regionSelect = document.getElementById('region-select');
                const autocomplete = document.getElementById(
                    'region-autocomplete-input'
                );

                // If the autocomplete input is hidden, don't attempt to validate.
                if (autocomplete.checkVisibility()) {
                    // Region select input must not be empty.
                    if (regionSelect.value.length < 1) {
                        autocomplete.setCustomValidity(
                            'You must select at least one location.'
                        );
                    } else {
                        autocomplete.setCustomValidity('');
                    }
                    autocomplete.reportValidity();
                }

                // If the form is not valid, prevent submission.
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
            },
            false
        );
    });
});
