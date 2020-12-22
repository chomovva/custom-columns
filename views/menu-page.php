<?php


namespace themenamespace;


if ( ! defined( 'ABSPATH' ) ) { exit; };


?>


<div class="wrap">

	<?php if ( isset( $page_title ) && ! empty( $page_title ) ) : ?>
		<h2><?php echo $page_title; ?></h2>
	<?php endif; ?>

	<?php
		if ( isset( $page_content ) && ! empty( $page_content ) ) {
			echo $page_content;
		}
	?>

</div>