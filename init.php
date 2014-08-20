<?php
/**
 * Loads all our files as needed
 * @since 1.0.0
*/

if ( is_admin() ) {
	include( dirname( __FILE__ ) . '/lib/settings.php' );
}
