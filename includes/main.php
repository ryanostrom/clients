<?php

function echo_bootstrap($html, $options = array()) {
	$page_js = isset($options['js']) ? "<script src=\"../resources/js/{$options['js']}.js\"></script>" : '';
  echo <<<HTML
		<html>
			<head>
				<title>Praxent Intranet</title>
				<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
				{$page_js}
				<link rel="stylesheet" href="../resources/css/main.css" type="text/css">
				<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
			</head>
			<body>
				<div id="wrapper">
					{$html}
				</div>
			</body>
		</html>
HTML;
}