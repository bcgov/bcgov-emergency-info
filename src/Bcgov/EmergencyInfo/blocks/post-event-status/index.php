<?php
// phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter.FoundBeforeLastUsed

use Bcgov\EmergencyInfo\Plugin;

/**
 * Renders the `emergency-info/post-event-status` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 * @return string Returns the Post Event Status block markup.
 */
function render_block_post_event_status(
    array $attributes,
    string $content,
    WP_Block $block
): string {
    // phpcs:enable
    // Get Event.
    $post_id            = $block->context['postId'];
    $wrapper_attributes = get_block_wrapper_attributes();
    $status             = Plugin::get_field( 'status', $post_id );

    // Based on the status, set the correct message content.
    $message = '';
    switch ( $status['value'] ?? '' ) {
        case 'referred':
            // If the event is in "referred" state, look for the referred_link field.
            $referred_link = Plugin::get_field( 'referred_link', $post_id );
            if ( $referred_link ) {
                // Referred link exists, add a link in the message.
                $message = sprintf( 'Information about this event <a href="%s">has moved</a>.', $referred_link );
            } else {
                // Referred link does not exist, plain text message.
                $message = 'Information about this event has moved.';
            }
            break;
        case 'expired':
            $message = 'This event has expired.';
            break;
        default:
            return '<span></span>';
    }

    // Build final block html.
    return sprintf(
        '
        <div %s>
            <div class="alert alert-primary status-alert">
                <p><strong>%s</strong></p>
            </div>
        </div>
        ',
        $wrapper_attributes,
        $message
    );
}

/**
 * Registers the `emergency-info/post-event-status` block on the server.
 */
register_block_type_from_metadata(
    $path . '/post-event-status',
    array(
        'render_callback' => 'render_block_post_event_status',
    )
);
