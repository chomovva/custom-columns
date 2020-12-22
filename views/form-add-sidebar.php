<?php


namespace themenamespace;


if ( ! defined( 'ABSPATH' ) ) { exit; };


?>

<h3><?php _e( 'Добавление', TEXTDOMAIN ); ?></h3>

<form method="post">

	<!-- скрытое поле с одноразовым кодом для безопасности -->
	<input type="hidden" name="nonce" required="required" value="<?php echo $nonce; ?>">

	<!-- идентификатор выполняемого действия (добавление) -->
	<input type="hidden" name="action" required="required" value="add">

	<p>
		<!-- название новой колонки -->
		<input type="text" name="new_column[name]" required="required" placeholder="<?php esc_attr_e( 'Название', TEXTDOMAIN ); ?>">

		<!-- краткое описание колонки -->
		<input type="text" name="new_column[description]" placeholder="<?php esc_attr_e( 'Описание', TEXTDOMAIN ); ?>">

		<!-- класс контейнера колонки -->
		<input type="text" name="new_column[class]" placeholder="<?php esc_attr_e( 'CSS-класс', TEXTDOMAIN ); ?>">

		<!-- кнопка отправки формы (добавления колонки) -->
		<button class="button button-primary" type="submit"><?php _e( 'Добавить', TEXTDOMAIN ); ?></button>

		<!-- если ни одного сайдбара ещё не добавлено, то отменять действие добавление нет смысла -->
		<?php if ( ! empty( $register_columns ) && isset( $page_url ) ) : ?>
			<a class="button" href="<?php echo esc_attr( $page_url ); ?>"><?php _e( 'Отмена', TEXTDOMAIN ); ?></a>
		<?php endif; ?>
	</p>

</form>