import { Component } from '@wordpress/element';
import { compose } from '@wordpress/compose';
import { SelectControl } from '@wordpress/components';
import { withSelect, withDispatch } from '@wordpress/data';
import { addFilter } from '@wordpress/hooks';
import apiFetch from '@wordpress/api-fetch';

/**
 * Replaces the default Hazard Type taxonomy input (text or checkboxes) with a select input
 * in the block editor, allowing only one hazard type per post to be selected.
 */
class HazardType extends Component {
    constructor() {
        super(...arguments);

        this.state = {
            selected: 0,
            options: [],
            message: '',
        };
    }

    componentDidMount() {
        const { hazardType } = this.props;

        if (hazardType && hazardType[0]) {
            this.setState({
                selected: hazardType[0],
            });
        }

        apiFetch({ path: '/wp/v2/hazard_type?per_page=-1' }).then(
            (terms) => {
                const options = terms.map((term) => {
                    return { label: term.name, value: term.id };
                });

                this.setState({ options, message: '' });
            },
            (error) => {
                this.setState({ message: error.message });
            }
        );
    }

    onUpdateHazardType(selected) {
        const { onChangeHazardType } = this.props;

        this.setState({
            selected: parseInt(selected.option, 10),
        });

        onChangeHazardType(selected.option);
    }

    render() {
        const { selected, options, message } = this.state;
        const { taxonomy } = this.props;
        let selectControl;

        if (options && options.length > 1) {
            selectControl = (
                <SelectControl
                    value={selected}
                    options={options}
                    onChange={(option) => {
                        this.onUpdateHazardType({ option });
                    }}
                />
            );
        }

        return (
            <div>
                {taxonomy && '' !== taxonomy.description && (
                    <p>{taxonomy.description}</p>
                )}
                {selectControl}
                {message && <p className="error">{message}</p>}
            </div>
        );
    }
}

const HazardTypeWrapper = compose([
    withSelect((select) => {
        const ret = {
            hazardType:
                select('core/editor').getEditedPostAttribute('hazard_type'),
        };
        // Get currently selected hazard_type slug.
        const hazard = select('core').getEntityRecord(
            'taxonomy',
            'hazard_type',
            ret.hazardType[0]
        );
        // Add the hazard_type's slug to body classes, allows hazard-specific styling.
        if (hazard) {
            document.body.className = document.body.className.replace(
                /(^|\s)hazard_type-\S+/g,
                ''
            );
            document.body.classList.add('hazard_type-' + hazard.slug);
        }
        return ret;
    }),
    withDispatch((dispatch) => ({
        onChangeHazardType: (hazardTypeId) => {
            dispatch('core/editor').editPost({ hazard_type: [hazardTypeId] });
        },
    })),
])(HazardType);

const hazardTypeFilter = (OriginalComponent) => {
    return (props) => {
        const { slug } = props;

        if ('hazard_type' === slug) {
            return <HazardTypeWrapper {...props} />;
        }

        return <OriginalComponent {...props} />;
    };
};

addFilter('editor.PostTaxonomyType', 'emergency-info', hazardTypeFilter);
