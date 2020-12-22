<?php


namespace themenamespace;


if ( ! defined( 'ABSPATH' ) ) { exit; };


function register_sidebars() {
	// получаем массив с пользовательскими сайдбарами из настроек темы и добавляем из к уже имеющимся
	$sidebars = array_merge( [ [
		'name'          => __( 'Колонка', TEXTDOMAIN ),
		'id'            => 'column',
		'description'   => '',
		'class'         => '',
	] ], get_theme_mod( 'register_sidebars', [] ) );
	// проходим по полученному массив циклом
	foreach ( $sidebars as $sidebar ) {
		// регистрируем новые сайдбары
		register_sidebar( [
			'name'             => $sidebar[ 'name' ],
			'id'               => $sidebar[ 'id' ],
			'description'      => $sidebar[ 'description' ],
			'class'            => $sidebar[ 'class' ],
			'before_widget'    => '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><div id="%1$s" class="widget %2$s">',
			'after_widget'     => '</div></div>',
			'before_title'     => '<h3 class="widget__title title">',
			'after_title'      => '</h3>',
		] );
	}
}

add_action( 'widgets_init', 'themenamespace\register_sidebars' );