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
                <button id="share-menu" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" aria-label="%3$s">
                    <i class="bi-send-fill me-3 align-middle" style="font-size: 24px;"></i>
                    %3$s
                </button>
                <div class="dropdown-menu" role="menu" aria-labelledby="share-menu">
                    <a class="dropdown-item" role="menuitem" href="https://twitter.com/intent/tweet?url=%2$s">		
                        <span class="areoi-icon"><i class="text-dark bi-twitter-x"></i></span>
                        %4$s
                    </a>
                    <a class="dropdown-item" role="menuitem" href="https://www.facebook.com/sharer/sharer.php?u=%2$s">
                        <span class="areoi-icon"><i class="text-dark bi-facebook"></i></span>	
                        %5$s
                    </a>
                    <a class="dropdown-item copy-link" role="menuitem" href="#" data-url="%2$s">
                        <span class="areoi-icon"><i class="text-dark bi-clipboard-plus-fill link-copied-status"></i></span>
                        %6$s
                    </a>
                </div>
            </div>
        </div>
        ',
        $wrapper_attributes,
        $link,
        __( 'Share' ),
        __( 'Share on X (Twitter)' ),
        __( 'Share on Facebook' ),
        __( 'Copy link' ),
    );
}

/**
 * Registers the `emergency-info/post-social-share` block on the server.
 */
register_block_type_from_metadata(
    $path . '/post-social-share',
    array(
        'render_callback' => 'render_block_post_social_share',
    )
);
