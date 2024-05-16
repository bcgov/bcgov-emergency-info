import { Component } from '@wordpress/element';
import { compose } from '@wordpress/compose';
import {
    CheckboxControl,
    Panel,
    PanelBody,
    PanelRow,
} from '@wordpress/components';
import { withSelect, withDispatch } from '@wordpress/data';
import { addFilter } from '@wordpress/hooks';
import apiFetch from '@wordpress/api-fetch';

/**
 * Replaces the default Region Group taxonomy input (text or checkboxes) with checkbox inputs
 * in the block editor, allowing the Region taxonomy terms to be set more easily.
 */
class RegionGroup extends Component {
    constructor() {
        super( ...arguments );

        this.state = {
            // The currently selected regionGroup Ids.
            selected: [],
            // The list of regionGroup options, for building checkboxes.
            options: {},
            // Mapping of regionGroup Ids to its array of region Ids.
            regionGroupRegionMap: {},
            // Mapping of regionGroup type slugs to their labels.
            groupTypeLabelMap: {},
            // Message displayed when an error occurs.
            message: '',
        };
    }

    componentDidMount() {
        const { regionGroups } = this.props;

        apiFetch( { path: '/wp/v2/region-group' } ).then(
            ( terms ) => {
                const options = {};
                const groupTypeLabelMap = { other: 'Other' };

                // Process terms from REST request.
                for ( const term of terms ) {
                    const type = term.group_type[ 'value' ] ?? 'other';
                    groupTypeLabelMap[ type ] = term.group_type[ 'label' ];
                    if (
                        ! Object.prototype.hasOwnProperty.call( options, type )
                    ) {
                        options[ type ] = [];
                    }
                    options[ type ].push( {
                        label: term.name,
                        value: term.term_id,
                        type: type,
                    } );
                }

                // Build regionGroupRegionMap object.
                const regionGroupRegionMap = {};
                for ( const term of terms ) {
                    regionGroupRegionMap[ term.term_id ] =
                        term.included_regions;
                }

                this.setState( {
                    selected: regionGroups,
                    options,
                    regionGroupRegionMap,
                    groupTypeLabelMap,
                    message: '',
                } );
            },
            ( error ) => {
                this.setState( { message: error.message } );
            }
        );
    }

    onUpdateRegionGroup( checked ) {
        const { onChangeRegionGroup, regions } = this.props;
        const { selected } = this.state;
        const { regionGroupId, includedRegions } = checked;
        let index = -1;
        let newRegions = [];
        let updatedSelected = selected;

        if ( ( index = selected.indexOf( regionGroupId ) ) > -1 ) {
            // The regionGroup is being unselected. Subtract its regions from the Region input.
            updatedSelected.splice( index, 1 );
            newRegions = regions.filter(
                ( x ) => ! includedRegions.includes( x )
            );
        } else {
            // The regionGroup is being selected. Add its regions to the Region input.
            updatedSelected.push( regionGroupId );
            newRegions = regions.concat( includedRegions );
            // Ensure only unique values exist in array.
            newRegions = [ ...new Set( newRegions ) ];
        }

        this.setState( {
            selected: updatedSelected,
        } );

        onChangeRegionGroup( updatedSelected, newRegions );
    }

    render() {
        const {
            selected,
            options,
            regionGroupRegionMap,
            groupTypeLabelMap,
            message,
        } = this.state;
        const { taxonomy } = this.props;
        let elements = [];

        if ( options ) {
            for ( const type in options ) {
                // Build a checkbox for each regionGroup option.
                const checkboxes = options[ type ].map( ( option ) => {
                    const regionGroupId = option.value;
                    const regionGroupLabel = option.label;
                    return (
                        <PanelRow className="mt-0" key={ regionGroupId }>
                            <CheckboxControl
                                className="region-group-checkbox-control"
                                label={ regionGroupLabel }
                                value={ regionGroupId }
                                checked={ selected.includes( regionGroupId ) }
                                onChange={ () => {
                                    const includedRegions =
                                        regionGroupRegionMap[ regionGroupId ];
                                    this.onUpdateRegionGroup( {
                                        regionGroupId,
                                        includedRegions,
                                    } );
                                } }
                            />
                        </PanelRow>
                    );
                } );

                // Add the checkboxes to a Panel to allow expanding/collapsing.
                elements.push(
                    <Panel>
                        <PanelBody
                            title={ groupTypeLabelMap[ type ] }
                            initialOpen={ 0 === elements.length }
                        >
                            { checkboxes }
                        </PanelBody>
                    </Panel>
                );
            }
        }

        return (
            <div>
                { taxonomy && '' !== taxonomy.description && (
                    <p>{ taxonomy.description }</p>
                ) }
                { elements }
                { message && <p className="error">{ message }</p> }
            </div>
        );
    }
}

const RegionGroupWrapper = compose( [
    withSelect( ( select ) => {
        const ret = {
            regionGroups:
                select( 'core/editor' ).getEditedPostAttribute(
                    'region_groups'
                ),
            regions: select( 'core/editor' ).getEditedPostAttribute( 'region' ),
        };
        return ret;
    } ),
    withDispatch( ( dispatch ) => ( {
        onChangeRegionGroup: ( regionGroupIds, regions ) => {
            // Set regions value to non-existent term, setting to an empty array doesn't refresh the UI.
            if ( 0 === regions.length ) {
                regions = [ -1 ];
            }
            dispatch( 'core/editor' ).editPost( {
                region: regions,
                // Why do the elements need to be strings here? I don't know.
                region_groups: regionGroupIds.map( ( id ) => id.toString() ),
            } );
        },
    } ) ),
] )( RegionGroup );

const regionGroupFilter = ( OriginalComponent ) => {
    const component = ( props ) => {
        const { slug } = props;

        if ( 'region_groups' === slug ) {
            return <RegionGroupWrapper { ...props } />;
        }

        return <OriginalComponent { ...props } />;
    };
    component.displayName = 'regionGroupFilter';
    return component;
};

addFilter( 'editor.PostTaxonomyType', 'emergency-info', regionGroupFilter );
