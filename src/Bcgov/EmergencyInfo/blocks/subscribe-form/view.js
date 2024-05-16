import $ from 'jquery';

$( () => {
    // The hidden multiselect input containing selected regions.
    const regionSelect = $( '#region-select' );

    // The input for entering autocomplete search.
    const regionAutocomplete = $( '#region-autocomplete-input' );

    // The list of available terms to autocomplete from. Passed in from PHP.
    // eslint-disable-next-line no-undef
    const availableTerms = regions;
    // Whether the form is an update or a new subscription. Passed in from PHP.
    // eslint-disable-next-line no-undef
    const isUpdate = update;

    /**
     * Updates region select input validity.
     * Necessary because if the form is made invalid due to this input its validity won't be updated
     * because it isn't directly changed by the user.
     */
    const updateRegionSelectValidity = () => {
        const autocomplete = document.getElementById(
            'region-autocomplete-input'
        );
        autocomplete.setCustomValidity( '' );
    };

    // When the all regions radio value is changed, toggle the region section.
    const selectAllRegions = $( 'input[name="tax_region_all"]' );
    selectAllRegions.on( 'change', ( event ) => {
        const value = event.target.value;
        if ( '1' === value ) {
            // Hide all region inputs, disable the underlying select input.
            $( '.region-section' ).hide();
            $( '.all-region-section' ).show();
            regionSelect.prop( 'disabled', true );
        } else {
            // Show all region inputs, enable the underlying select input.
            $( '.region-section' ).show();
            $( '.all-region-section' ).hide();
            regionSelect.prop( 'disabled', false );
        }
        updateRegionSelectValidity();
    } );
    $( 'input[name="tax_region_all"]:checked' ).trigger( 'change' );

    /**
     * Adds a term to the currently selected terms.
     * @param {string} id The term id.
     */
    const addSelectedTerm = ( id ) => {
        const selectedTerms = regionSelect.val();
        selectedTerms.push( id );
        regionSelect.val( selectedTerms );
        updateRegionSelectValidity();
    };

    /**
     * Removes a term from the currently selected terms.
     * @param {string} id The term id.
     */
    const removeSelectedTerm = ( id ) => {
        const selectedTerms = regionSelect.val();
        const index = selectedTerms.indexOf( id );
        if ( index !== -1 ) {
            selectedTerms.splice( index, 1 );
            regionSelect.val( selectedTerms );
        }
        updateRegionSelectValidity();
    };

    /**
     * Renders the currently selected terms.
     */
    const renderTerms = () => {
        let regionList = '';
        let regionGroupList = '';
        let regionGroups = [];
        let excludeRegionGroups = [];
        regionSelect.val().forEach( ( termId ) => {
            const term = availableTerms.findLast( ( t ) => {
                return t.value === +termId;
            } );
            // Build region pill with remove button.
            regionList += `<li aria-label="${ term.label }" tabindex="0">
                <span class="term-pill region">
                    <span>
                        ${ term.label }
                    </span>
                    <button class="btn btn-secondary" aria-label="Remove ${ term.label }" data-id="${ term.value }" tabindex="0"><i class="bi-x-circle-fill"></i></button>
                </span>
            </li>`;

            // If the region is also a region group (eg. Capital (Regional District)), don't show its region group.
            if ( term.isRegionGroupTerm ) {
                excludeRegionGroups = excludeRegionGroups.concat(
                    term.regionGroups
                );
            }
            regionGroups = regionGroups.concat( term.regionGroups );
        } );

        // Remove duplicate and redundant region groups.
        const seen = {};
        regionGroups = regionGroups.filter( ( regionGroup ) => {
            if ( excludeRegionGroups.includes( regionGroup ) ) {
                return false;
            }
            return Object.hasOwn( seen, regionGroup )
                ? false
                : ( seen[ regionGroup ] = true );
        } );

        // Build region group pill.
        regionGroups.forEach( ( groupName ) => {
            regionGroupList += `<li aria-label="${ groupName }" tabindex="0">
                <span class="term-pill region-group">
                    <span>
                        ${ groupName }
                    </span>
                </span>
            </li>`;
        } );

        // Add region pills to HTML.
        if ( ! regionList ) {
            // If no terms selected, remove message.
            $( '.region-autocomplete-label' ).html( '' );
        } else {
            $( '.region-autocomplete-label' ).html( 'Your locations:' );
        }
        $( '.region-list' ).html( regionList );

        // Add region group pills to HTML.
        if ( ! regionGroupList ) {
            // If no terms selected, remove message.
            $( '.region-group-autocomplete-label' ).html( '' );
        } else {
            $( '.region-group-autocomplete-label' ).html(
                'You will also get email updates for:'
            );
        }
        $( '.region-group-list' ).html( regionGroupList );

        // Add listener for clicks of the remove button on each term.
        $( '.region-list button' ).on( 'click', ( event ) => {
            const removeId = event.currentTarget.dataset.id;
            removeSelectedTerm( removeId );
            renderTerms();
        } );
    };

    regionAutocomplete
        // Don't navigate away from the field on tab when selecting an item.
        .on( 'keydown', function ( event ) {
            if (
                event.keyCode === $.ui.keyCode.TAB &&
                $( this ).autocomplete( 'instance' ).menu.active
            ) {
                event.preventDefault();
            }
        } )
        .autocomplete( {
            minLength: 0,
            source: ( request, response ) => {
                // If no search term given, return early.
                if ( '' === request.term ) {
                    response( availableTerms );
                    return;
                }

                const selectedValues = regionSelect.val();

                // Create regex string of the search term, escaping any special characters.
                const term = new RegExp(
                    `(${ request.term.replace(
                        /[/\-\\^$*+?.()|[\]{}]/g,
                        '\\$&'
                    ) })`,
                    'gi'
                );

                let results = [];
                for ( const option of availableTerms ) {
                    // Skip terms that have already been selected.
                    if ( selectedValues.includes( option.value.toString() ) ) {
                        continue;
                    }

                    // Get number of matches in the label.
                    const labelMatches = option.label.match( term );
                    const labelRelevancy = labelMatches
                        ? labelMatches.length
                        : 0;

                    // Get number of matches in the region groups.
                    const regionGroupsRelevancy = option.regionGroups.reduce(
                        ( accumulator, currentValue ) => {
                            const matches = currentValue.match( term );
                            const count = matches ? matches.length : 0;
                            return count + accumulator;
                        },
                        0
                    );

                    // Set weighted search result relevancy.
                    option.relevancy =
                        labelRelevancy * 5 + regionGroupsRelevancy;

                    // Only add to the results if non-zero relevancy.
                    if ( option.relevancy > 0 ) {
                        results.push( option );
                    }
                }

                // Sort by descending relevancy.
                results.sort( ( a, b ) => b.relevancy - a.relevancy );

                // Return results in response.
                response( results );
            },
            position: {
                of: '.region-autocomplete',
            },
            appendTo: '#listbox-wrapper',
            classes: { 'ui-autocomplete': 'soft-shadow' },
            focus: () => {
                // Update aria attributes when focusing on an item.
                const id = $( '#listbox-wrapper' )
                    .find( '.ui-state-active' )
                    .attr( 'id' );
                regionAutocomplete.attr( 'aria-activedescendant', id );
                $( '.ui-autocomplete div' ).attr( 'aria-selected', 'false' );
                $( '.ui-autocomplete div.ui-state-active' ).attr(
                    'aria-selected',
                    'true'
                );
                return false;
            },
            select: ( event, ui ) => {
                const termId = ui.item.value;
                // If the term isn't already selected, select it.
                if ( ! regionSelect.val().includes( termId ) ) {
                    addSelectedTerm( termId );
                    renderTerms();
                }

                regionAutocomplete.val( '' );
                return false;
            },
            response: ( event, ui ) => {
                // Show no results message if the search returned no hits.
                if ( 0 === ui.content.length ) {
                    ui.content.push( {
                        value: '',
                        label: 'No results found.',
                    } );
                }
            },
        } );

    // Override jQueryUI Autocomplete renderItem() to add aria role.
    $.ui.autocomplete.prototype._renderItem = ( ul, item ) => {
        const renderedItem = $( '<li></li>' );
        renderedItem.append( '<div role="option">' + item.label + '</div>' );
        // Display region groups if the item has any.
        if ( ! item.isRegionGroupTerm && item.regionGroups ) {
            renderedItem.append(
                '<div class="region-groups">' +
                    item.regionGroups.join( ', ' ) +
                    '</div>'
            );
        }
        return renderedItem.appendTo( ul );
    };

    // Override jQueryUI Autocomplete renderMenu() to add aria role.
    // eslint-disable-line prefer-arrow/prefer-arrow-functions
    $.ui.autocomplete.prototype._renderMenu = function ( ul, items ) {
        const $this = this;
        $.each( items, ( index, item ) => {
            $this._renderItemData( ul, item );
        } );
        $( ul ).attr( 'role', 'listbox' );
    };

    // Initialize terms display in case terms were passed to the page.
    renderTerms();

    // Validate region select input on submit.
    const forms = document.querySelectorAll( '.needs-validation' );
    Array.from( forms ).forEach( ( form ) => {
        form.addEventListener(
            'submit',
            ( event ) => {
                const regionSelect = document.getElementById( 'region-select' );
                const autocomplete = document.getElementById(
                    'region-autocomplete-input'
                );

                // If the autocomplete input is hidden, don't attempt to validate.
                if ( autocomplete.checkVisibility() ) {
                    // Region select input must not be empty.
                    if ( regionSelect.value.length < 1 ) {
                        autocomplete.setCustomValidity(
                            'You must select at least one location.'
                        );
                    } else {
                        autocomplete.setCustomValidity( '' );
                    }
                    autocomplete.reportValidity();
                }

                // If the form is not valid, prevent submission.
                if ( ! form.checkValidity() ) {
                    event.preventDefault();
                    event.stopPropagation();
                }
            },
            false
        );
    } );

    // If the user is updating their preferences, scroll to the form.
    if ( isUpdate ) {
        const element = document.getElementById( 'subscribe-form' );
        element.scrollIntoView( { behavior: 'instant' } );
    }
} );
