<?php


namespace themenamespace;


if ( ! defined( 'ABSPATH' ) ) { exit; };


// подключим скрип и стили для модальных окон, они пригодятся для отображения дополнительных свойств
add_thickbox();

// инициализируем переменную для счетчика строк списка-таблицы
$count = 0;

// получаем список всех сайдбаров и виджетов в них
$registered_sidebars = wp_get_sidebars_widgets();

?>

<!-- Выводи шапку таблицы -->
<table class="custom-column-table">
	<thead>
		<th><?php _e( '№', TEXTDOMAIN ); ?></th>
		<th><?php _e( 'Название', TEXTDOMAIN ); ?></th>
		<th><?php _e( 'Описание', TEXTDOMAIN ); ?></th>
		<th><?php _e( 'CSS-класс', TEXTDOMAIN ); ?></th>
		<th><?php _e( 'Страницы', TEXTDOMAIN ); ?></th>
		<th><?php _e( 'Виджеты', TEXTDOMAIN ); ?></th>
	</thead>
	<tbody>

		<!-- в цикле выводи строки таблицы со свойствами колонок -->
		<?php foreach ( $register_columns as $register_column ) : ?>

			<?php

				// номер строки увеличиваем на 1
				$count = $count + 1;

				// получаем список страниц на которых используется текущая колонка
				$pages = get_pages( [
					'meta_key'    => '_custom_columns',
					'meta_value'  => $register_column[ 'id' ],
					'sort_order'  => 'ASC',
					'sort_column' => 'post_title'
				] );

				// получам список категорий на страницах которых используется текущая колонка
				$categories = get_categories( [
					'taxonomy'    => 'category',
					'orderby'     => 'name',
					'order'       => 'ASC',
					'hide_empty'  => false,
					'fields'      => 'all',
					'meta_key'    => '_custom_columns',
					'meta_value'  => $register_column[ 'id' ],
				] );

				// инициализируем переменную в которой будем хранить количество категорий и страниц

				// где используется текущая колонка
				$usage = 0;

				// получим количество виджетов в колонке
				$widgets_count = 0;

				if ( array_key_exists( $register_column[ 'id' ], $registered_sidebars ) ) {
					count( $registered_sidebars[ $register_column[ 'id' ] ] );
				}

				// создадим часть url с кодом безопасности и идентификатором колонки эта строка нужна для использования в ссылках для выполнения действий удаления и редактирования колонки
				$action_url = $page_url . '&nonce=' . $nonce . '&id=' . $register_column[ 'id' ];

			?>

			<!-- выводим строку -->
			<tr>
				<td class="count"><?php echo $count; ?></td>
				<td class="name"><?php echo $register_column[ 'name' ]; ?></td>
				<td class="description"><?php echo $register_column[ 'description' ]; ?></td>
				<td class="class"><?php echo $register_column[ 'class' ]; ?></td>

					<!-- В этой колонку будет выводится статистика по использованию колонки. Чтобы не перегружать таблицу лишней информацией часть параметров будет показываться в модальном окне. -->
				<td class="posts">

					<!-- начало модального окна -->
					<div id="usage-<?php echo $register_column[ 'id' ]; ?>" style="display:none;">

						<!-- если есть страницы с колонкой - выведем их -->
						<?php if ( is_array( $pages ) && ! empty( $pages ) ) : $usage = $usage + count( $pages ); ?>
							<h4><?php _e( 'Страницы', TEXTDOMAIN ); ?></h4>
							<ul class="list-disc">
								<?php foreach ( $pages as $page ) : ?>
									<li><a target="_blank" href="<?php echo get_permalink( $page ); ?>"><?php echo apply_filters( 'the_title', $page->post_title, $page->ID ); ?></a></li>
								<?php endforeach; ?>
							</ul>
						<?php else : ?>
							<p><?php _e( 'Колонка не используется на постоянных страницах.', TEXTDOMAIN ); ?></p>
						<?php endif; ?>

						<!-- если есть категории с текущей колонкой - выведем их -->
						<?php if ( is_array( $categories ) && ! empty( $categories ) ) : $usage = $usage + count( $categories ); ?>
							<h4><?php _e( 'Категории', TEXTDOMAIN ); ?></h4>
							<ul class="list-disc">
								<?php foreach ( $categories as $category ) : ?>
									<li><a target="_blank" href="<?php echo get_category_link( $category ); ?>"><?php echo apply_filters( 'single_cat_title', $category->name ); ?></a></li>
								<?php endforeach; ?>
							</ul>
						<?php else : ?>
							<p><?php _e( 'Колонка не используется на страницах категорий.', TEXTDOMAIN ); ?></p>
						<?php endif; ?>
					</div>
					<!-- конец модального окна -->

					<!-- теперь выведем кнопку для открытия модального окна со списком виджетов и колонок -->
					<a class="thickbox" href="/?TB_inline&inlineId=usage-<?php echo $register_column[ 'id' ]; ?>&width=300&height=200"><?php echo $usage; ?></a>
				</td>
				
				<!-- количество виджетов, которые добавили в эту колонку -->
				<td class="widgets"><?php echo $widgets_count; ?></td>

				<!-- кнопки действий (удалени/редактирования) для работы этих кнопок и была нужна строка-url в переменной $action_url -->
				<td class="text-right">

					<!-- для кнопки удаления добавим простой скрип для подтверждения действия -->
					<a class="action-button delete-button" onclick="return confirm( '<?php esc_attr_e( 'Вы уверены?', TEXTDOMAIN ); ?>' );" href="<?php echo $action_url . '&action=delete'; ?>"><?php _e( 'Удалить', TEXTDOMAIN ); ?></a>

					<!-- ссылка редактировать ведёт на текущую страницу, но только другую "вкладку" и содержит дополнительные параметры -->
					<a class="action-button edit-button" href="<?php echo $action_url . '&tab=edit'; ?>">
						<?php _e( 'Редактировать', TEXTDOMAIN ); ?>
					</a>

				</td>
			</tr>
		<?php endforeach; ?>

	</tbody>
</table>