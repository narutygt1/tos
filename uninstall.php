<?php
// Check that code was called from WordPress with 
// uninstallation constant declared 
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit; 
}