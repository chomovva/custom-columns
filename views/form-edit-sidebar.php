<?php


namespace themenamespace;


if ( ! defined( 'ABSPATH' ) ) { exit; };


?>

<h3><?php _e( 'Редактирование', TEXTDOMAIN ); ?></h3>

<?php
	// получаем предыдущие значения свойств сайдбара для заполнения полей формы редактирования
	// для этого сначала создадим ассоциативный массив с пустыми знаениями
	$old_value = [ 'name' => '', 'id' => '', 'description' => '', 'class' => '' ];
	// затем найдём в цикле в массиве колонок ту, значения которой будем редактировать
	for ( $i = 0;  $i < count( $register_columns );  $i++ ) { 
		if ( $_REQUEST[ 'id' ] == $register_columns[ $i ][ 'id' ] ) {
			// после того как колонка найдена - сохраним её свойства и прервём работу цыкла
			$old_value = $register_columns[ $i ];
			break;
		}
	}
?>

<form method="post">
	<p>
		<!-- скрытое поле с кодом безопасности -->
		<input type="hidden" name="nonce" required="required" value="<?php echo $nonce; ?>">

		<!-- поле с идентификатором выполняемого действия -->
		<input type="hidden" name="action" required="required" value="edit">

		<!-- идентификатор сайдбара -->
		<input type="hidden" name="new_value[id]" required="required" value="<?php echo esc_attr( $old_value[ 'id' ] ); ?>">

		<!-- новое название сайдбара -->
		<input type="text" name="new_value[name]" required="required" value="<?php echo esc_attr( $old_value[ 'name' ] ); ?>" placeholder="<?php esc_attr_e( 'Название', TEXTDOMAIN ); ?>">

		<!-- новое описание сайдбара -->
		<input type="text" name="new_value[description]" value="<?php echo esc_attr( $old_value[ 'description' ] ); ?>" placeholder="<?php esc_attr_e( 'Описание', TEXTDOMAIN ); ?>">

		<!-- новое значение аттрибута класса -->
		<input type="text" name="new_value[class]" value="<?php echo esc_attr( $old_value[ 'class' ] ); ?>" placeholder="<?php esc_attr_e( 'CSS-класс', TEXTDOMAIN ); ?>">
	</p>
	<p>
		<a class="button" href="<?php echo $page_url; ?>"><?php _e( 'Отмена', RESUME_TEXTDOMAIN ); ?></a>
		<button class="button button-primary" type="submit"><?php _e( 'Сохранить', RESUME_TEXTDOMAIN ); ?></button>
	</p>
</form>