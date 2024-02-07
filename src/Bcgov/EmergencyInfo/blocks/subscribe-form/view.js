import $ from 'jquery';

$(() => {
    // The hidden multiselect input containing selected regions.
    const regionSelect = $('#region-select');
    regionSelect.hide();

    // The input for entering autocomplete search.
    const regionAutocomplete = $('#region-autocomplete');

    // The list of available terms to autocomplete from. Passed in from PHP.
    // eslint-disable-next-line no-undef
    const availableTerms = terms;

    /**
     * Adds a term to the currently selected terms.
     * @param {string} id The term id.
     */
    const addSelectedTerm = (id) => {
        const selectedTerms = regionSelect.val();
        selectedTerms.push(id);
        regionSelect.val(selectedTerms);
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
            termList += `<li>
                <span class="badge rounded-pill bg-primary">
                    ${term.label}
                    <button type="button" class="btn btn-link" data-id="${term.value}"><i class="text-light bi-x"></i></button>
                </span>
            </li>`;
        });
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
            minLength: 3,
            source: availableTerms,
            focus: () => {
                // Prevent value inserted on focus.
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
        });

    // Initialize terms display in case terms were passed to the page.
    renderTerms();
});
