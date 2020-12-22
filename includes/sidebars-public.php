<?php


namespace themenamespace;


if ( ! defined( 'ABSPATH' ) ) { exit; };


/**
 * Заменяем стандартную колонку на пользовательскую
 */
function replace_default_sidebar() {
	$custom_sidebars = '';
	// определяем какой шаблон темы сейчас будет генерироваться
	if ( is_singular( 'page' ) ) {
		// если это постоянная страница, то информация об отдельной пользовательской колонке хранится в метаданных для страницы
		$custom_sidebars = get_post_meta( get_the_ID(), '_custom_sidebars', true );
	} elseif ( is_singular( 'post' ) ) {
		// если просматривается пост, то информация о пользовательской колонке хранится в метаднных категории
		// получим список категорий текущего поста
		$categories = get_terms( [
			'taxonomy'   => 'category',
			'object_ids' => get_the_ID(),
			'fields'     => 'ids',
			'meta_key'   => '_custom_sidebars',
		] );
		if ( is_array( $categories ) && ! empty( $categories ) ) {
			// если пост в категории, то получим информацию о колонке
			$custom_sidebars = get_term_meta( $categories[ 0 ], '_custom_sidebars', true );
		}
	} elseif ( is_category() ) {
		// если просматривается страница категории, то информация хранится в метаданных категорий
		$custom_sidebars = get_term_meta( get_queried_object()->term_id, '_custom_sidebars', true );
	}
	if ( ! empty( $custom_sidebars ) ) {
		// если необходима замена колонки, то регистрируем хук
		add_filter( 'sidebars_widgets', function( $sidebars ) use ( $custom_sidebars ) {
			// если сайдбар с таким идентификатом существует, то произведём замену
			if ( array_key_exists( $custom_sidebars, $sidebars ) ) {
				$sidebars[ 'column' ] = $sidebars[ $custom_sidebars ];
			}
			return $sidebars;
		}, 5, 1 );
	}
}

add_action( 'wp', 'themenamespace\replace_default_sidebar' );