<?php


if ( ! defined( 'ABSPATH' ) ) { exit; };
 

get_template_part( 'includes/register-sidebars' );


if ( is_admin() ) {
	get_template_part( 'includes/sidebars-admin' );
} else {
	get_template_part( 'includes/sidebars-public' );
}