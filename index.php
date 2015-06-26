<!DOCTYPE html>
<html>

	<head>
		<title>What's My IP Address?</title>
		<meta charset="utf-8">
		<meta name="keywords" content="Whats my IP address">
		<link href="main.css" type="text/css" rel="stylesheet">
	</head>

	<body>
		<?php
		/**
		 * Collect real ip of the visitor
		 */
		function get_ip() {
			// IP if internet share
			if (isset($_SERVER['HTTP_CLIENT_IP'])) {
				return $_SERVER['HTTP_CLIENT_IP'];
			}
			// IP under proxy
			elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				return $_SERVER['HTTP_X_FORWARDED_FOR'];
			}
			// Else : normal IP
			else {
				return (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
			}
		}
		?>

		<!-- Print ip adress -->
			<h1>
				Your IP adress is : <br/><?php echo get_ip(); ?>
			</h1>
	</body>
</html>
