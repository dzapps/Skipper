<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || !isset($_POST['action']) || !isset($_POST['url'])) {
	$Output = array (
		'Code' => '403',
		'Status' => 'Error',
		'Message' => 'Erişim Engellendi.'
	);
	echo json_encode($Output);
	exit();
}

$opt = '';
$raw = '';
$url = $_POST['url'];

if (strpos($url, 'adf.ly') !== false) {
	$opt = 'ly';
	$raw = $url;
}
elseif (strpos($url, 'link.tl') !== false) {
	$opt = 'tl';
	$ext = substr($url, strrpos($url, '/') +1);
	$raw = 'http://link.tl/fly/go.php?to='.$ext;
}
else {
	$Output = array (
		'Code' => '415',
		'Status' => 'Error',
		'Message' => 'Desteklenmeyen Link Yapısı.',
		'HTMLGui' => '<div class="alert alert-danger">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
		<i class="fa fa-exclamation fa-fw"></i>Desteklenmeyen Link Yapısı.
		</div>'
	);
	echo json_encode($Output);
	exit();
}
$curl = curl_init();
$useragent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36';

curl_setopt($curl, CURLOPT_URL, $raw);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_USERAGENT, $useragent);

$exec = curl_exec($curl); curl_close($curl);

function DecodeYsmm($Ysmm) {
	$A = '';
	$B = '';
	for ($j = 0; $j < strlen($Ysmm); $j++) {
		if ($j % 2 == 0) $A .= $Ysmm[$j];
		else $B = $Ysmm[$j].$B; 
    } 
    return substr(base64_decode($A.$B), 2); 
}

if ($opt == 'ly') {
	if (preg_match("#var ysmm = '([a-zA-Z0-9+/=]+)'#", $exec, $m)) {
		$m[0] = str_replace("var ysmm = '",'',$m[0]);
		$last = substr_replace($m[0], "", -1);
		$cleanlink = DecodeYsmm($last);
		$Output = array (
			'Code' => '200',
			'Status' => 'Success',
			'Message' => $cleanlink,
			'HTMLGui' => '<div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
			<i class="fa fa-check fa-fw"></i><a href="'.$cleanlink.'" target="_blank">Siteye Gitmek İçin Tıklayın.</a>
			</div>'
		);
		echo json_encode($Output);
		exit();
	}else {
		$Output = array (
			'Code' => '500',
			'Status' => 'Error',
			'Message' => 'Link Hatalı Veya Geçersiz.',
			'HTMLGui' => '<div class="alert alert-danger">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
			<i class="fa fa-exclamation fa-fw"></i>Link Hatalı Veya Geçersiz.
			</div>'
		);
		echo json_encode($Output);
		exit();
	}
}elseif ($opt == 'tl') {
	if (preg_match_all('/<a\s+href=["\']([^"\']+)["\']/i', $exec, $m, PREG_PATTERN_ORDER)) {
		$last = array_unique($m[1]);
		$cleanlink = $last[2];
		$Output = array (
			'Code' => '200',
			'Status' => 'Success',
			'Message' => $cleanlink,
			'HTMLGui' => '<div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
			<i class="fa fa-check fa-fw"></i><a href="'.$cleanlink.'" target="_blank">Siteye Gitmek İçin Tıklayın.</a>
			</div>'
		);
		echo json_encode($Output);
		exit();
	}else {
		$Output = array (
			'Code' => '500',
			'Status' => 'Error',
			'Message' => 'Link Hatalı Veya Geçersiz.',
			'HTMLGui' => '<div class="alert alert-danger">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
			<i class="fa fa-exclamation fa-fw"></i>Link Hatalı Veya Geçersiz.
			</div>'
		);
		echo json_encode($Output);
		exit();
	}
}
?>
