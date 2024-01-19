<?php
$word = in_array($_GET['w'] ?? '', ['感恩']) || mb_strlen($_GET['w'] ?? '', 'UTF-8') == 1 ? $_GET['w'] : '好';
$exp = isset($_GET['e']) ? (is_array($_GET['e']) ? $_GET['e'] : [$_GET['e']]) : ['，', '。'];
$exp = count(array_filter($exp, function($v){ return mb_strlen($v, 'UTF-8') > 1; })) > 0 ? ['，', '。'] : $exp;
$end = isset($_GET['d']) && mb_strlen($_GET['d'], 'UTF-8') == 1 ? $_GET['d'] : '。';

function e($t, $c) {
	for($x = 0; $x < $c; $x++){
		echo $t;
	}
}
?>
<html>
	<head>
		<meta charset="UTF-8" />
		<meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport" />
		<title><?=e($word, rand(3, 20)); ?></title>
		
		<style>
		body{ background: #f00; margin: 15px; }
		.good{ max-width: 1000px; margin:0 auto; background: #000; border: 5px solid #00f; padding: 20px; color:#0f0; }
		h1{ text-align: center; margin-top:0; }
		p{ text-indent: 2em; word-break: break-all; }
		</style>
	</head>
	<body>
		<div class="good">
			<h1><?=e($word, rand(3, 20)); ?></h1>
			<?php for($x = 0; $x < rand(50, 100); $x++){ ?>
			<p><?php for($y = 0; $y < rand(10, 20); $y++){ echo e($word, rand(10, 20), ); echo $exp[rand(0, count($exp) - 1)]; } echo e($word, rand(3, 20)) . $end; ?></p>
			<?php } ?>
		</div>
	</body>
</html>