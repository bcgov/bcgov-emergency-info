<?php
// phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter.FoundBeforeLastUsed
/**
 * Renders the `emergency-info/event-meta` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 * @return string Returns the Event Meta block markup.
 */
function render_block_event_meta(
    array $attributes,
    string $content,
    WP_Block $block
): string {
    // phpcs:enable
    if ( 'event' !== $block->context['postType'] ) {
        return '';
    }
    // Get Event.
    $post_id     = $block->context['postId'];
    $event       = get_post( $post_id );
    $is_detailed = $attributes['detailed'];

    // Get ACF status meta field.
    $event_status = get_field( 'status', $post_id ) ?? [
		'value' => false,
		'label' => 'N/A',
	];

    // Get Hazard Type taxonomy terms. Uses the hazard_types attribute if it exists.
    $hazard_types = [];
    if ( ! empty( $attributes['hazard_types'] ) ) {
        $hazard_types = [ get_term( $attributes['hazard_types'][0] ) ];
    }
    if ( empty( $hazard_types ) ) {
        $hazard_types = get_the_terms( $event, 'hazard_type' );
    }

    // Get Hazard Type image.
    $hazard_type     = $hazard_types[0];
    $hazard_image    = get_field( 'hazard_image', 'hazard_type_' . $hazard_type->term_id );
    $hazard_image_id = $hazard_image['id'];
    if ( $hazard_image_id ) {
        $hazard_image_srcset = wp_get_attachment_image_srcset( $hazard_image_id );
        $hazard_image_sizes  = wp_get_attachment_image_sizes( $hazard_image_id );
    }

    $wrapper_attributes = get_block_wrapper_attributes(
        [
			'class' => 'hazard_type-' . $hazard_type->slug,
		]
    );

    // Build header and post title html.
    $header_html     = '';
    $post_title_html = '<a href="' . get_post_permalink( $event ) . '">' . $event->post_title . '</a>';
    if ( $is_detailed ) {
        $header_html     = sprintf(
            '
            <div class="text-center mb-2 hazard-background">
                <h3 class="text-white p-2 my-0">%s</h3>
            </div>
            ',
            esc_html( $event_status['label'] )
        );
        $post_title_html = $event->post_title;
    }

    // Build hazard image html.
    $hazard_img_html = '';
    if ( isset( $hazard_image_srcset ) ) {
        $hazard_img_html = sprintf(
            '
            <img class="img-fluid hazard-image" loading="lazy" decoding="async" alt="%s" srcset="%s" sizes="%s">
            ',
            $hazard_type->name,
            $hazard_image_srcset,
            $hazard_image_sizes
        );
    }

    // Build final block html.
    return sprintf(
        '
        <div %5%s>
            <div class="p-0 w-100 bg-white">
                %1$s
                <div class="p-3 d-flex flex-column flex-sm-row align-items-center hazard-background-secondary">
                    %2$s
                    <div class="p-2 w-75">
                        <h1><strong>%3$s</strong></h1>
                        <h2>%4$s</h2>
                    </div>
                </div>
            </div>
        </div>
        ',
        $header_html,
        $hazard_img_html,
        $is_detailed ? esc_html( $hazard_type->name ) : '',
        $post_title_html,
        $wrapper_attributes
    );
};

/**
 * Registers the `emergency-info/event-meta` block on the server.
 */
register_block_type_from_metadata(
    $path . '/event-meta',
    array(
        'render_callback' => 'render_block_event_meta',
    )
);
