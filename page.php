<!doctype html>
<html>
<head>
	<?php the_post() ?>
	<meta charset="utf-8">
	<title><?php the_title() ?></title>
	<meta name="viewport" content="width=device-width, user-scalable=no">
	<style>
		body {
			max-width: 800px;
			margin: 0 auto;
			padding: 20px;
			font-family: "Arial", "Helvetica", sans-serif;
		}

		body > header {
			margin-bottom: 50px;
			text-align: center;
		}

		body > header h1 {
			margin-top: 0;
		}
	</style>
</head>
<body>
	<header>
		<h1><?php the_title() ?></h1>
	</header>
	<main>
		<?php the_content() ?>
	</main>
</body>
</html>