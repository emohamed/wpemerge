<?php
/**
 * Layout template file for Whoops's pretty error output.
 */

$is_admin = function_exists( 'is_admin' ) && is_admin();
$is_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;

if ( $is_admin && ! $is_ajax ) {
	?>
	<style>
		.wpemerge-whoops {
			position: relative;
			margin: 20px 20px 0 0;
		}

		.wpemerge-whoops .stack-container {
			display: flex;
		}

		.wpemerge-whoops .left-panel {
			position: static;
			height: auto;
			overflow: visible;
		}

		.wpemerge-whoops .details-container {
			position: static;
			height: auto;
			overflow: visible;
		}

		@media (max-width: 600px) {
			.wpemerge-whoops {
				margin: 10px 10px 0 0;
			}

			.wpemerge-whoops .stack-container {
				display: block;
			}
		}
	</style>
	<script>
		jQuery(window).load(function () {
			jQuery(window).scrollTop(0);

			jQuery('.frames-container').on('click', '.frame', function() {
				jQuery(window).scrollTop(0);
			});
		});
	</script>
	<?php
	require 'wpemerge-body.html.php';
	return;
}
?>
<!DOCTYPE html><?php echo $preface; ?>
<html>
<head>
	<meta charset="utf-8">
	<meta name="robots" content="noindex,nofollow"/>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
	<title><?php echo $tpl->escape( $page_title ) ?></title>
</head>
<body>
	<?php require 'wpemerge-body.html.php'; ?>
</body>
</html>
