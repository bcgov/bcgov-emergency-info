<?php

namespace Bcgov\Theme\FoodDirectory;

/**
 * A comfortably padded section in which to place elements.
 */
return [
	'title'      => __( 'Event (Query Loop)', 'emergency-info' ),
	'categories' => [ 'emergency-info-bc-general' ],
	'blockTypes' => [ 'core/query' ],
	'content'    => '
        <!-- wp:query {"queryId":1,"query":{"perPage":"9","pages":0,"offset":0,"postType":"event","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false,"parents":[]},"displayLayout":{"type":"list"},"className":"is-style-default"} -->
        <div class="wp-block-query is-style-default"><!-- wp:post-template {"lock":{"move":false,"remove":false},"className":"event-query-loop"} -->
        <!-- wp:group {"lock":{"move":false,"remove":false},"style":{"spacing":{"padding":{"top":"0","right":"0","bottom":"0","left":"0"}},"border":{"top":{"width":"10px"},"radius":"10px"}},"backgroundColor":"white","className":"hazard-border soft-shadow h-100","layout":{"type":"default"}} -->
        <div class="wp-block-group hazard-border soft-shadow h-100 has-white-background-color has-background" style="border-radius:10px;border-top-width:10px;padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:group {"style":{"border":{"radius":{"bottomLeft":"10px","bottomRight":"10px"},"right":{"color":"var:preset|color|support-gray-02"},"bottom":{"color":"var:preset|color|support-gray-02"},"left":{"color":"var:preset|color|support-gray-02"}},"spacing":{"padding":{"top":"0","right":"0","bottom":"0","left":"0"}}},"className":"h-100","layout":{"type":"default"}} -->
        <div class="wp-block-group h-100" style="border-bottom-left-radius:10px;border-bottom-right-radius:10px;border-right-color:var(--wp--preset--color--support-gray-02);border-bottom-color:var(--wp--preset--color--support-gray-02);border-left-color:var(--wp--preset--color--support-gray-02);padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|20","right":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|20"}}},"backgroundColor":"support-gray-01","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"left"}} -->
        <div class="wp-block-group has-support-gray-01-background-color has-background" style="padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--20);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--20)"><!-- wp:group {"style":{"spacing":{"padding":{"top":"0","right":"0","bottom":"0","left":"0"}}},"layout":{"type":"constrained"}} -->
        <div class="wp-block-group" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:emergency-info/post-hazard-image /--></div>
        <!-- /wp:group -->
        
        <!-- wp:post-title {"isLink":true,"style":{"spacing":{"padding":{"right":"var:preset|spacing|20","left":"var:preset|spacing|20"}}},"fontSize":"medium"} /--></div>
        <!-- /wp:group -->
        
        <!-- wp:group {"style":{"spacing":{"padding":{"top":"0","right":"var:preset|spacing|30","bottom":"0","left":"var:preset|spacing|30"}},"border":{"radius":{"bottomLeft":"10px","bottomRight":"10px"}}},"backgroundColor":"white","layout":{"type":"default"}} -->
        <div class="wp-block-group has-white-background-color has-background" style="border-bottom-left-radius:10px;border-bottom-right-radius:10px;padding-top:0;padding-right:var(--wp--preset--spacing--30);padding-bottom:0;padding-left:var(--wp--preset--spacing--30)"><!-- wp:group {"style":{"border":{"top":{"color":"var:preset|color|support-gray-02"}}},"layout":{"type":"default"}} -->
        <div class="wp-block-group" style="border-top-color:var(--wp--preset--color--support-gray-02)"><!-- wp:emergency-info/post-emergency-alert {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20","right":"0","bottom":"var:preset|spacing|20","left":"0"}}}} /-->
        
        <!-- wp:emergency-info/post-meta-display {"style":{"spacing":{"margin":{"top":"var:preset|spacing|20","right":"0","bottom":"var:preset|spacing|20","left":"0"}}}} /-->
        
        <!-- wp:emergency-info/post-meta-display {"valueNum":2,"style":{"spacing":{"margin":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20"}}}} /--></div>
        <!-- /wp:group --></div>
        <!-- /wp:group --></div>
        <!-- /wp:group --></div>
        <!-- /wp:group -->
        <!-- /wp:post-template -->
        
        <!-- wp:query-no-results -->
        <!-- wp:group {"style":{"spacing":{"padding":{"top":"0","right":"var:preset|spacing|30","bottom":"0","left":"var:preset|spacing|30"}},"border":{"radius":"10px"}},"backgroundColor":"white","className":"soft-shadow","layout":{"type":"default"}} -->
        <div class="wp-block-group soft-shadow has-white-background-color has-background" style="border-radius:10px;padding-top:0;padding-right:var(--wp--preset--spacing--30);padding-bottom:0;padding-left:var(--wp--preset--spacing--30)"><!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|20","right":"0","bottom":"var:preset|spacing|20","left":"0"}}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
        <div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--20);padding-right:0;padding-bottom:var(--wp--preset--spacing--20);padding-left:0"><!-- wp:image {"id":1637,"sizeSlug":"full","linkDestination":"none","className":"d-none d-sm-block"} -->
        <figure class="wp-block-image size-full d-none d-sm-block"><img src="https://test.vanity.blog.gov.bc.ca/app/uploads/sites/1187/2023/04/Current_Emergencies.png" alt="" class="wp-image-1637" title=""/></figure>
        <!-- /wp:image -->
        
        <!-- wp:paragraph -->
        <p>There are no active emergencies at this time</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:group {"style":{"layout":{"selfStretch":"fill","flexSize":null}},"className":"text-end","layout":{"type":"constrained","justifyContent":"center"}} -->
        <div class="wp-block-group text-end"><!-- wp:areoi/button {"block_id":"0320081d-d84e-4aa7-b20b-6cdcba503157","style":"btn-warning","text_wrap":"text-nowrap","text":"Get Notified","include_icon":true,"icon":"bi-bell-fill","icon_position":"prepend","className":"text-primary"} /--></div>
        <!-- /wp:group --></div>
        <!-- /wp:group --></div>
        <!-- /wp:group -->
        <!-- /wp:query-no-results -->
        
        <!-- wp:query-pagination {"paginationArrow":"chevron","className":"pt-2","layout":{"type":"flex","justifyContent":"center"},"fontSize":"small"} -->
        <!-- wp:query-pagination-previous /-->
        
        <!-- wp:query-pagination-numbers /-->
        
        <!-- wp:query-pagination-next /-->
        <!-- /wp:query-pagination --></div>
        <!-- /wp:query -->
    ',
];
