<?php


namespace themenamespace;


if ( ! defined( 'ABSPATH' ) ) { exit; };


/**
 * Регистрируем страницу настроек для 
 */
function register_custom_sidebars_submenu() {
	add_theme_page(
		__( 'Дополнительные сайдбары', TEXTDOMAIN ),
		__( 'Дополнительные сайдбары', TEXTDOMAIN ),
		'manage_options',
		SLUG . '_custom_sidebars',
		'resume_render_custom_sidebars_submenu'
	);
}

add_action( 'admin_menu', 'themenamespace\register_custom_sidebars_submenu' );



function render_custom_sidebars_submenu() {
	// определяем текущую вкладку
	$current_tab = 'table';
	if ( isset( $_REQUEST[ 'tab' ] ) && in_array( $_REQUEST[ 'tab' ], [ 'table', 'add', 'edit' ] ) ) {
		$current_tab = $_REQUEST[ 'tab' ];
	}
	// получаем список колонок
	$register_sidebars = get_theme_mod( 'register_sidebars', [] );
	// создаём уникальный защитный ключ для обеспечения безопасности
	$nonce = wp_create_nonce( plugin_basename( __FILE__ ) );
	// получаем url текущей страницы
	$page_url = 'themes.php?page=' . THEME_SLUG . '_custom_sidebars';
	// если есть "предупреждения", то віводим их
	$page_content = ( isset( $_REQUEST[ 'notice' ] ) ) ? $_REQUEST[ 'notice' ] : '';
	// получаем заголовок страницы
	$page_title = get_admin_page_title();
	// поскульку содержимое страницы нужно передать в переменной - включаем буфер
	ob_start();
	// выводим контент вкладки добавления нового сайдбара
	if ( 'add' == $current_tab || empty( $register_sidebars ) ) {
		/**
		 * подключаем форму добавления сайдбара
		 */
		include get_theme_file_path( 'views/form-add-sidebar.php' );
	} elseif ( 'edit' == $current_tab && isset( $_REQUEST[ 'id' ] ) ) {
		/**
		 * вкладка редактирование сайдбара
		 */
		include get_theme_file_path( 'views/form-edit-sidebar.php' );
	} else {
		/**
		 * Вкладка со списокм добавленных сайдбаров. На этой вкладке выведем весь
		 * список (тиблицу) добавленных сайдаров и дополнительные свойства.
		 * для удобства добавим кнопку на форму регистрации нового сайдара рядом
		 * с заголовком страницы
		 */
		$page_title .= ' <a class="button button-primary" href="' . $page_url . '&tab=add">' . __( 'Добавить', TEXTDOMAIN ) . '</a>';
		include get_theme_file_path( 'views/sidebars-table.php' );
	}
	// сохраним данные из буфера в переменную
	$page_content = $page_content . ob_get_contents();
	// завершим работу буфера и очистим его
	ob_end_clean();
	// подключим шаблон админ страница
	include get_theme_file_path( 'views/admin/menu-page.php' );
}



/**
 * Выполнение пользовательских действий над списком сайдбаров
 * @param  WP_Screen $current_screen [description]
 */
function action_for_custom_sidebars( $current_screen ) {
	// определяем на нужно ли мы странице находимся, т.к. по задумке действия будут совершаться только на одной
	if ( 'appearance_page_' . SLUG . '_custom_sidebars' == $current_screen->id ) {
		// проверяем права пользователя, соответствие кода безопасности и наличие необходимых для
		if (
			current_user_can( 'manage_options' )
			 && isset( $_REQUEST[ 'action' ] )
			 && in_array( $_REQUEST[ 'action' ], [ 'add', 'edit', 'delete' ] )
			 && isset( $_REQUEST[ 'nonce' ] )
			 && wp_verify_nonce( $_REQUEST[ 'nonce' ], plugin_basename( __FILE__ ) )
		) {
			// получаем массив всех зарегистрированных пользовательских колонок
			$register_sidebars = get_theme_mod( 'register_sidebars', [] );
			// определяем какую операцию нужно соверщить
			switch ( $_REQUEST[ 'action' ] ) {
				// добавление/регистрация новой колонки
				case 'add':
					$register_sidebars = add_custom_sidebar( $register_sidebars );
					break;
				// редактирование пользовательской колонки
				case 'edit':
					$register_sidebars = edit_custom_sidebar( $register_sidebars );
					break;
				// удаление пользовательской колонки
				case 'delete':
					$register_sidebars = delete_custom_sidebar( $register_sidebars );
					break;
			}
			// сохраняем изменившийся список пользовательских колонок в базе данных
			set_theme_mod( 'register_sidebars', $register_sidebars );
			// редректим назад на страницу настроек
			wp_safe_redirect( get_admin_url( null, 'themes.php?page=' . RESUME_SLUG . '_custom_sidebars', null ), 302 );
			exit();
		}
	}
}

add_action( 'current_screen', 'themenamespace\action_for_custom_sidebars', 10, 1 );




/**
 * Функция для очистки массива параметров
 * @param  array $default           разрешённые параметры и стандартные значения
 * @param  array $args              неочищенные параметры
 * @param  array $sanitize_callback одномерный массив с именами функция, с помощью которых нужно очистить параметры
 * @param  array $required          обязательные параметры
 * @return array                    возвращает очищенный массив разрешённых параметров
 */
function parse_only_allowed_args( $default, $args, $sanitize_callback = [], $required = [] ) {
	// неочищенные параметры обязательно должны быть в виде массива
	$args = ( array ) $args;
	// инициализируем возвращаемые данные, так же в виде массива
	$result = [];
	// счетчик-указатель на текущий параметр
	$count = 0;
	// проходим по массиву с белым списком параметров
	// получаем текущую пару ключ => значение в переменную
	while ( ( $value = current( $default ) ) !== false ) {
		// получаем имя текущего разрешённого параметра
		$key = key( $default );
		// проверяем есть ли в неочищенном массиве есть такое элемент
		if ( array_key_exists( $key, $args ) ) {
			// если есть, то добавляем это значение к результату
			$result[ $key ] = $args[ $key ];
			// проверяем есть ли функция для очистки значения
			if ( isset( $sanitize_callback[ $count ] ) && ! empty( $sanitize_callback[ $count ] ) ) {
				// если есть, то очищаем значение
				$result[ $key ] = $sanitize_callback[ $count ]( $result[ $key ] );
			}
		// если в неочищенном массиве НЕ передали нужное нужное значение, то проверяем обязательные ли это параметр и если обязателен, то в результат записываем значение по умолчанию
		} elseif ( in_array( $key, $required ) ) {
			return null;
		} else {
			$result[ $key ] = $value;
		}
		// передвигаем указатели на следующий элемент
		$count = $count + 1;
		next( $default );
	}
	// возвращаем результат
	return $result;
}


/**
 * Добавление пользовательское колонки
 * @param    array    $register_sidebars    зарегистрированные сайдбары
 * @return   arry
 */
function add_custom_sidebar( $register_sidebars = [] ) {
	if ( isset( $_REQUEST[ 'new_column' ] ) ) {
		// очищаем полученных данные и сохраняем во временную переменную
		$new_column = parse_only_allowed_args(
			[ 'name' => '', 'description' => '', 'class' => '' ],
			$_REQUEST[ 'new_column' ],
			[ 'sanitize_text_field', 'sanitize_text_field', 'sanitize_text_field' ],
			[ 'name' ]
		);
		// проверяем успешность очистки данных
		if ( null !== $new_column ) {
			// если всё нормально, то создаём идентификатор для новой колонки
			$new_column[ 'id' ] = 'column_' . md5( $new_column[ 'name' ] );
			// проверяем существует ли такой же идентификатор в базе
			if ( empty( count( wp_list_filter( $register_sidebars, [ 'id' => $new_column[ 'id' ] ], 'AND' ) ) ) ) {
				// если нет, то добавляем новую колонку в массив
				$register_sidebars[] = $new_column;
			}
		}
	}
	return $register_sidebars;
}


/**
 * Редактирование параметров сайдбара
 * @param    array    $register_sidebars    зарегистрированные сайдбары
 * @return   arry
 */
function edit_custom_sidebar( $register_sidebars = [] ) {
	// проверяем есть ли данные для обновления
	if ( isset( $_REQUEST[ 'new_value' ] ) ) {
		// если есть, то очищаем их
		$new_value = parse_only_allowed_args(
			[ 'name' => '', 'id' => '', 'description' => '', 'class' => '' ],
			$_REQUEST[ 'new_value' ],
			[ 'sanitize_text_field', 'sanitize_text_field', 'sanitize_text_field', 'sanitize_text_field' ],
			[ 'name', 'id', 'description', 'class' ]
		);
		// проверяем результат после очистки
		if ( null !== $new_value ) {
			// находим колонку, которую нужно редактировать
			foreach ( $register_sidebars as &$register_column ) {
				// колонка найдена - записываем новые данные и прерываем работу цикла
				if ( $new_value[ 'id' ] == $register_column[ 'id' ] ) {
					$register_column = $new_value;
					break;
				}
			}
		}
	}
	return $register_sidebars;
}


/**
 * Удаление сайдбара
 * @param    array    $register_sidebars    зарегистрированные сайдбары
 * @return   arry
 */
function delete_custom_sidebar( $register_sidebars = [] ) {
	// проверяем передан ли идентификатор колонки
	if ( isset( $_REQUEST[ 'id' ] ) ) {
		// очищаем данные перед использованием
		$id = sanitize_text_field( $_REQUEST[ 'id' ] );
		// проверяем поле после очистки
		if ( ! empty( $id ) ) {
			// ищем в цикле нужную колонку 
			for ( $i = 0; $i < count( $register_sidebars ); $i++ ) { 
				// если колонка с идентификатором найдена - начинаем её удаление
				if ( $id == $register_sidebars[ $i ][ 'id' ] ) {
					// получим список страниц на которых используется колонка
					$pages = get_pages( [
						'number'     => -1,
						'meta_key'   => '_custom_sidebars',
						'meta_value' => $id,
					] );
					// если страницы найдены - удаляем метаданные в цикле
					if ( is_array( $pages ) && ! empty( $pages ) ) {
						foreach ( $pages as $page ) {
							delete_post_meta( $page->ID, '_custom_sidebars' );
						}
					}
					// получим список категорий на которых используется колонка
					$categories = get_categories( [
						'taxonomy'    => 'category',
						'hide_empty'  => false,
						'meta_key'    => '_custom_sidebars',
						'meta_value'  => $id,
					] );
					// если категории найдены - удаляем метаданные в цикле
					if ( is_array( $categories ) && ! empty( $categories ) ) {
						foreach ( $categories as $category ) {
							delete_term_meta( $category->ID, '_custom_sidebars' );
						}
					}
					// удаляем данные колонки со списка
					array_splice( $register_sidebars, $i, 1 );
					break;
				}
			}
		}
	}
	return $register_sidebars;
}