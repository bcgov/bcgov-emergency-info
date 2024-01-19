<?php
namespace Bcgov\EmergencyInfo;

use WP_REST_Request;
use WP_REST_Response;

/**
 * EarthquakeEarlyWarning class handles automatic event creation caused by NAAD alerts.
 */
class EarthquakeEarlyWarning {

    /**
     * Constructor for EarthquakeEarlyWarning.
     */
    public function __construct() {
        add_action( 'eibc_create_event', [ $this, 'create_event' ], 10, 6 );
        add_action( 'rest_api_init', [ $this, 'register_naad_alert_routes' ], 20 );
    }

    /**
     * Creates an Event post with the given parameters.
     *
     * @param string $title         The post title the Event should have.
     * @param string $excerpt       The post excerpt the Event should have.
     * @param string $hazard_type   The hazard type of the Event.
     * @param int    $time_occurred When the event occurred.
     * @param string $card_value_1  The first card value meta field. This is different for each hazard type.
     * @param string $card_value_2  The second card value meta field.
     * @return void
     */
    public function create_event( string $title, string $excerpt, string $hazard_type, int $time_occurred, string $card_value_1 = '', string $card_value_2 = '' ) {
        // Defaults for post. If no pattern exists for the hazard type, post status is draft.
        $post_content = '<p>No pattern found for this hazard type. Please add Event content.</p>';
        $post_excerpt = sprintf(
            '<p>This is an automated posting caused by an alert received from the National Public Alerting System (NAAD).</p>
            <p>%s</p>',
            $excerpt,
        );
        $post_status  = 'draft';

        // Get term id for the given hazard type.
        $terms = get_terms(
            [
				'taxonomy'   => 'hazard_type',
				'slug'       => $hazard_type,
				'fields'     => 'ids',
				'hide_empty' => false,
			]
        );
        if ( is_wp_error( $terms ) ) {
            $terms = [];
        }

        // If a hazard type term was found, get patterns for the given hazard type.
        if ( ! empty( $terms ) ) {
            $patterns = get_posts(
                [
					'post_type'   => 'custom-pattern',
                    'post_status' => 'publish',
					'tax_query'   => [
						[
							'taxonomy' => 'hazard_type',
							'terms'    => $terms[0],
						],
					],
                    'meta_query'  => [
                        'relation' => 'AND',
                        [
                            'key'   => 'use_for_automated_posts',
                            'value' => '1',
                        ],
                    ],
				]
            );
            if ( ! empty( $patterns ) ) {
                $post_content = $patterns[0]->post_content;
                $post_status  = 'publish';
            }
        }

        // Get date and time for Event metadata.
        $updated_date = gmdate( 'Ymd', $time_occurred );
        $updated_time = gmdate( 'h:i:s', $time_occurred );

        // Build post arguments.
        $post_args = [
            'post_type'      => 'event',
            'comment_status' => 'closed',
            'ping_status'    => 'closed',
            'post_status'    => $post_status,
            'post_title'     => $title,
            'post_content'   => $post_content,
            'post_excerpt'   => $post_excerpt,
            'tax_input'      => [
                'hazard_type' => $terms,
            ],
            'meta_input'     => [
                'updated_date' => $updated_date,
                'updated_time' => $updated_time,
                'card_value_1' => $card_value_1, // Location.
                'card_value_2' => $card_value_2,  // Magnitude.
                '_hazard_type' => $terms, // Hack to make unit tests work.
            ],
        ];

        // Create post.
        wp_insert_post( $post_args );
    }

    /**
     * Register REST routes used by the NAAD connector.
     *
     * @return void
     */
    public function register_naad_alert_routes() {
        register_rest_route(
            'naad/v1',
            '/alert',
            [
                'methods'             => 'POST',
                'callback'            => [ $this, 'handle_alert' ],
                'permission_callback' => [ $this, 'authenticate_alert' ],
            ]
        );
    }

    /**
     * Handles an alert request from the NAAD connector.
     *
     * @param WP_REST_Request $request
     * @return WP_Error|WP_REST_Response
     */
    public function handle_alert( WP_REST_Request $request ) {
        return new WP_REST_Response( $request->get_body() );
    }

    /**
     * Authenticates an alert request, enforcing that the user has manage_options capability.
     * Note that the request must also include a user and valid application password.
     *
     * @return bool
     */
    public function authenticate_alert() {
        return current_user_can( 'manage_options' );
    }
}
