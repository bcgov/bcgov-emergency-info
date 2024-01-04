<?php

namespace Bcgov\EmergencyInfo;

/**
 * EarthquakeEarlyWarning class.
 */
class EarthquakeEarlyWarningTest extends \WP_UnitTestCase {

    /**
     * Sets up unit test suite, only runs once.
     *
     * @return void
     */
    public static function set_up_before_class() {
        parent::set_up_before_class();
        register_post_type( 'event' );
        register_post_type( 'custom-pattern' );
        register_taxonomy(
            'hazard_type',
            [ 'event', 'custom-pattern' ],
            [
                'public'       => true,
                'hierarchical' => true,
            ]
        );
        wp_insert_term( 'earthquake', 'hazard_type' );
        wp_insert_term( 'flood', 'hazard_type' );
    }

    /**
     * Tests create_event action.
     *
     * @dataProvider create_event_provider
     * @param array $args Arguments for the action.
     * @param array $expected_result The expected result of the action.
     * @param array $patterns Custom patterns to insert.
     * @return void
     */
    public function test_create_event( array $args, array $expected_result, array $patterns = [] ) {
        foreach ( $patterns as $pattern ) {
            $id = wp_insert_post( $pattern );
            wp_set_object_terms( $id, $pattern['tax_input']['hazard_type'], 'hazard_type' );
        }

        new EarthquakeEarlyWarning();

        do_action(
            'eibc_create_event',
            $args['title'],
            $args['excerpt'],
            $args['hazard_type'],
            $args['time_occurred'],
            $args['card_value_1'],
            $args['card_value_2']
        );

        $latest_event = get_posts(
            [
				'post_type'   => 'event',
				'post_status' => 'any',
			]
        );
        $post_id      = $latest_event[0]->ID;
        $result       = get_post( $post_id );
        $this->assertEquals( $expected_result['post_content'], $result->post_content );
        $this->assertEquals( $expected_result['post_title'], $result->post_title );
        $this->assertStringContainsString( $expected_result['post_excerpt'], $result->post_excerpt );
        $this->assertEquals( $expected_result['post_status'], $result->post_status );
        $this->assertEquals( $expected_result['updated_date'], get_post_meta( $post_id, 'updated_date' )[0] );
        $this->assertEquals( $expected_result['updated_time'], get_post_meta( $post_id, 'updated_time' )[0] );
        $this->assertEquals( $expected_result['card_value_1'], get_post_meta( $post_id, 'card_value_1' )[0] );
        $this->assertEquals( $expected_result['card_value_2'], get_post_meta( $post_id, 'card_value_2' )[0] );
        // This should be a post term, not a meta field but the post term is not being set in testing environment.
        $this->assertEquals( $expected_result['terms'], get_post_meta( $post_id, '_hazard_type' )[0] );
    }

    /**
     * Provides test case data for test_create_event().
     *
     * @return array
     */
    public function create_event_provider(): array {
        return [
            'No pattern'               => [
                [
                    'title'         => 'Earthquake Detected in B.C.',
                    'excerpt'       => 'An earthquake of magnitude 7.1 has been detected in Northern B.C.',
                    'hazard_type'   => 'earthquake',
                    'time_occurred' => 1704311951,
                    'card_value_1'  => 'Northern B.C.',
                    'card_value_2'  => '7.1',
                ],
                [
                    'terms'        => [
                        2,
                    ],
                    'post_content' => '<p>No pattern found for this hazard type. Please add Event content.</p>',
                    'post_title'   => 'Earthquake Detected in B.C.',
                    'post_excerpt' => 'An earthquake of magnitude 7.1 has been detected in Northern B.C.',
                    'post_status'  => 'draft',
                    'updated_date' => '20240103',
                    'updated_time' => '07:59:11',
                    'card_value_1' => 'Northern B.C.',
                    'card_value_2' => '7.1',
                ],
            ],
            'Non-existent hazard type' => [
                [
                    'title'         => 'Earthquake Detected in B.C. 2',
                    'excerpt'       => 'An earthquake of magnitude 7.2 has been detected in Southern B.C.',
                    'hazard_type'   => 'doesnotexist',
                    'time_occurred' => 1704311952,
                    'card_value_1'  => 'Southern B.C.',
                    'card_value_2'  => '7.2',
                ],
                [
                    'terms'        => [],
                    'post_content' => '<p>No pattern found for this hazard type. Please add Event content.</p>',
                    'post_title'   => 'Earthquake Detected in B.C. 2',
                    'post_excerpt' => 'An earthquake of magnitude 7.2 has been detected in Southern B.C.',
                    'post_status'  => 'draft',
                    'updated_date' => '20240103',
                    'updated_time' => '07:59:12',
                    'card_value_1' => 'Southern B.C.',
                    'card_value_2' => '7.2',
                ],
            ],
            'With pattern'             => [
                [
                    'title'         => 'WP',
                    'excerpt'       => 'wp',
                    'hazard_type'   => 'earthquake',
                    'time_occurred' => 0,
                    'card_value_1'  => 'cv1',
                    'card_value_2'  => 'cv2',
                ],
                [
                    'terms'        => [
                        2,
                    ],
                    'post_content' => '<p>Pattern content</p>',
                    'post_title'   => 'WP',
                    'post_excerpt' => 'wp',
                    'post_status'  => 'publish',
                    'updated_date' => '19700101',
                    'updated_time' => '12:00:00',
                    'card_value_1' => 'cv1',
                    'card_value_2' => 'cv2',
                ],
                [
                    [
                        'post_type'    => 'custom-pattern',
                        'post_content' => '<p>Should not be used</p>',
                        'post_title'   => 'Should not be used',
                        'post_status'  => 'publish',
                        'tax_input'    => [
                            'hazard_type' => [
                                2,
                            ],
                        ],
                        'meta_input'   => [
                            'use_for_automated_posts' => '0',
                        ],
                    ],
                    [
                        'post_type'    => 'custom-pattern',
                        'post_content' => '<p>Pattern content</p>',
                        'post_title'   => 'Pattern title',
                        'post_status'  => 'publish',
                        'tax_input'    => [
                            'hazard_type' => [
                                2,
                            ],
                        ],
                        'meta_input'   => [
                            'use_for_automated_posts' => '1',
                        ],
                    ],
                ],
            ],
            'No automated pattern'     => [
                [
                    'title'         => 'WP',
                    'excerpt'       => 'wp',
                    'hazard_type'   => 'earthquake',
                    'time_occurred' => 0,
                    'card_value_1'  => 'cv1',
                    'card_value_2'  => 'cv2',
                ],
                [
                    'terms'        => [
                        2,
                    ],
                    'post_content' => '<p>No pattern found for this hazard type. Please add Event content.</p>',
                    'post_title'   => 'WP',
                    'post_excerpt' => 'wp',
                    'post_status'  => 'draft',
                    'updated_date' => '19700101',
                    'updated_time' => '12:00:00',
                    'card_value_1' => 'cv1',
                    'card_value_2' => 'cv2',
                ],
                [
                    [
                        'post_type'    => 'custom-pattern',
                        'post_content' => '<p>Should not be used</p>',
                        'post_title'   => 'Should not be used',
                        'post_status'  => 'publish',
                        'tax_input'    => [
                            'hazard_type' => [
                                2,
                            ],
                        ],
                        'meta_input'   => [
                            'use_for_automated_posts' => '0',
                        ],
                    ],
                    [
                        'post_type'    => 'custom-pattern',
                        'post_content' => '<p>Should also not be used</p>',
                        'post_title'   => 'Should not be used 2',
                        'post_status'  => 'publish',
                        'tax_input'    => [
                            'hazard_type' => [
                                2,
                            ],
                        ],
                        'meta_input'   => [
                            'use_for_automated_posts' => '0',
                        ],
                    ],
                ],
            ],
            'With multiple patterns'   => [
                [
                    'title'         => 'WP',
                    'excerpt'       => 'wp',
                    'hazard_type'   => 'earthquake',
                    'time_occurred' => 0,
                    'card_value_1'  => 'cv1',
                    'card_value_2'  => 'cv2',
                ],
                [
                    'terms'        => [
                        2,
                    ],
                    'post_content' => '<p>Pattern content</p>',
                    'post_title'   => 'WP',
                    'post_excerpt' => 'wp',
                    'post_status'  => 'publish',
                    'updated_date' => '19700101',
                    'updated_time' => '12:00:00',
                    'card_value_1' => 'cv1',
                    'card_value_2' => 'cv2',
                ],
                [
                    [
                        'post_type'    => 'custom-pattern',
                        'post_content' => '<p>Pattern content</p>',
                        'post_title'   => 'Pattern title',
                        'post_status'  => 'publish',
                        'tax_input'    => [
                            'hazard_type' => [
                                2,
                            ],
                        ],
                        'meta_input'   => [
                            'use_for_automated_posts' => '1',
                        ],
                    ],
                    [
                        'post_type'    => 'custom-pattern',
                        'post_content' => '<p>Should not be used</p>',
                        'post_title'   => 'Should not be used',
                        'post_status'  => 'publish',
                        'tax_input'    => [
                            'hazard_type' => [
                                2,
                            ],
                        ],
                        'meta_input'   => [
                            'use_for_automated_posts' => '1',
                        ],
                    ],
                ],
            ],
            'No published pattern'     => [
                [
                    'title'         => 'WP',
                    'excerpt'       => 'wp',
                    'hazard_type'   => 'earthquake',
                    'time_occurred' => 0,
                    'card_value_1'  => 'cv1',
                    'card_value_2'  => 'cv2',
                ],
                [
                    'terms'        => [
                        2,
                    ],
                    'post_content' => '<p>No pattern found for this hazard type. Please add Event content.</p>',
                    'post_title'   => 'WP',
                    'post_excerpt' => 'wp',
                    'post_status'  => 'draft',
                    'updated_date' => '19700101',
                    'updated_time' => '12:00:00',
                    'card_value_1' => 'cv1',
                    'card_value_2' => 'cv2',
                ],
                [
                    [
                        'post_type'    => 'custom-pattern',
                        'post_content' => '<p>Should not be used</p>',
                        'post_title'   => 'Should not be used',
                        'post_status'  => 'trash',
                        'tax_input'    => [
                            'hazard_type' => [
                                2,
                            ],
                        ],
                        'meta_input'   => [
                            'use_for_automated_posts' => '1',
                        ],
                    ],
                    [
                        'post_type'    => 'custom-pattern',
                        'post_content' => '<p>Should also not be used</p>',
                        'post_title'   => 'Should not be used 2',
                        'post_status'  => 'draft',
                        'tax_input'    => [
                            'hazard_type' => [
                                2,
                            ],
                        ],
                        'meta_input'   => [
                            'use_for_automated_posts' => '1',
                        ],
                    ],
                ],
            ],
        ];
    }
}
