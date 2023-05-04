<?php
// phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter.FoundBeforeLastUsed
/**
 * Renders the `emergency-info/post-social-share` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 * @return string Returns the Post Social Share block markup.
 */
function render_block_post_social_share(
    array $attributes,
    string $content,
    WP_Block $block
): string {
    // phpcs:enable
    if ( ! isset( $block->context['postId'] ) ) {
        return '';
    }

    $post               = get_post( $block->context['postId'] );
    $link               = get_the_permalink( $post );
    $wrapper_attributes = get_block_wrapper_attributes();

    if ( ! $link ) {
        return '';
    }

    return sprintf(
        '
        <div %1$s>
            <div class="dropdown">	
                <a class="btn areoi-has-url position-relative btn-outline-dark dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                    <i class="bi-send-fill me-3 align-middle" style="font-size: 24px;"></i>
                    Share
                </a>
                <div class="dropdown-menu" aria-labelledby="">
                    <a class="dropdown-item" href="https://twitter.com/intent/tweet?url=%2$s">		
                        <span class="areoi-icon"><i class="text-dark bi-twitter"></i></span>
                        Share on Twitter
                    </a>
                    <a class="dropdown-item" href="https://www.facebook.com/sharer/sharer.php?u=%2$s">
                        <span class="areoi-icon"><i class="text-dark bi-facebook"></i></span>	
                        Share on Facebook
                    </a>
                </div>
            </div>
        </div>
        ',
        $wrapper_attributes,
        $link
    );
};

/**
 * Registers the `emergency-info/post-social-share` block on the server.
 */
register_block_type_from_metadata(
    $path . '/post-social-share',
    array(
        'render_callback' => 'render_block_post_social_share',
    )
);
