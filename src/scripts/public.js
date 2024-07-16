import '../styles/public.scss';

/**
 * All the scripting for the frontend goes here.
 */
( function createBanner() {
    if ( ! document.body ) {
        setTimeout( createBanner, 1 ); // Retry after 100 milliseconds if body is not available
        return;
    }
    // Get event ID and URL from localized data
    var eventUrl =
        typeof eventBannerData !== 'undefined' && eventBannerData.event_url
            ? eventBannerData.event_url
            : '';
    var eventId =
        typeof eventBannerData !== 'undefined' && eventBannerData.event_id
            ? eventBannerData.event_id
            : '';

    // Check if info-banner element already exists
    var infoBanner = document.getElementById( 'info-banner' );

    if ( ! infoBanner ) {
        // Create info-banner element
        infoBanner = document.createElement( 'div' );
        infoBanner.id = 'info-banner';
        infoBanner.className = 'bc-gov-alertbanner alert-emergency d-none';
        infoBanner.setAttribute( 'role', 'alert' );
        infoBanner.setAttribute( 'aria-labelledby', 'info' );
        infoBanner.setAttribute( 'aria-describedby', 'info-desc' );
        infoBanner.setAttribute( 'data-event-id', eventId );

        // Append the banner to the body (or another appropriate container)
        document.body.prepend( infoBanner );
    }

    // Get stored event ID from localStorage
    var storedEventId = localStorage.getItem( 'infoBannerDismissed' );

    // Only show the banner if this specific event hasn't already been dismissed.
    if ( storedEventId !== eventId ) {
        infoBanner.innerHTML = `
            <div class="container">
                <i class="bi bi-exclamation-circle-fill"></i>
                <div class="content">
                    <p id="info-desc">B.C. has declared a <a href="${ eventUrl }" target="_self">provincial state of emergency</a>.</p>
                </div>
                <span class="dismiss">
                    <i class="bi bi-x-lg"></i>
                </span>
            </div>
        `;
        infoBanner.classList.remove( 'd-none' );

        // Hide the banner if the dismiss icon is clicked.
        var dismissButton = document.querySelector( '#info-banner .dismiss' );
        if ( dismissButton ) {
            dismissButton.addEventListener( 'click', function () {
                localStorage.setItem( 'infoBannerDismissed', eventId );
                infoBanner.classList.add( 'd-none' );
            } );
        }
    }
} )();
