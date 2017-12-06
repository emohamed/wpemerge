<?php
/**
 * Template used to override the loaded template file by WordPress when a route is handled
 */
// @codeCoverageIgnoreStart
$response = apply_filters( 'wp_emerge.response', null );
if ( $response !== null ) {
	WPEmerge::respond( $response );
}
// @codeCoverageIgnoreEnd
