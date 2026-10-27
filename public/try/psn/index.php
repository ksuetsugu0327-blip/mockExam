<!DOCTYPE html>
<html><head>
<meta http-equiv="content-language" content="ja"
<meta charset="UTF-8">
<meta name="robots" content="noindex,nofollow" />
<meta name="googlebot" content="noindex,nofollow" />
<link rel="stylesheet" href="../style.css">
<title>「解答解説」のダウンロード(高校)</title>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-140481385-1"></script>
<script>
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag('js', new Date());

	gtag('config', 'UA-140481385-1');
</script>
</head>

<?php
$passes = [
    '4' => '0316',
    '5' => '0406',
    '6' => '0518',
    '7' => '0615',
    '8' => '0705',
    '9' => '0817',
    '10' => '0914',
    '11' => '1005',
    '12' => '1026',
    '1' => '1207',
    '2' => '0117'];
$terms = [
    '4' => '5/1(月)～3/31(日)',
    '5' => '5/29(月)～3/31(日)',
    '6' => '7/3(月)～3/31(日)',
    '7' => '7/31(月)～3/31(日)',
    '8' => '9/4(月)～3/31(日)',
    '9' => '10/2(月)～3/31(日)',
    '10' => '10/30(月)～3/31(日)',
    '11' => '11/20(月)～3/31(日)',
    '12' => '12/11(月)～3/31(日)',
    '1' => '1/29(月)～3/31(日)',
    '2' => '3/4(月)～3/31(日)'];
$froms = [
    '4' => '2023-05-01 00:00:00',
    '5' => '2023-05-29 00:00:00',
    '6' => '2023-07-03 00:00:00',
    '7' => '2023-07-31 00:00:00',
    '8' => '2023-09-04 00:00:00',
    '9' => '2023-10-02 00:00:00',
    '10' => '2023-10-30 00:00:00',
    '11' => '2023-11-20 00:00:00',
    '12' => '2023-12-11 00:00:00',
    '1' => '2024-01-29 00:00:00',
    '2' => '2024-03-04 00:00:00'];
$tos = [
    '4' => '2024-03-31 23:59:59',
    '5' => '2024-03-31 23:59:59',
    '6' => '2024-03-31 23:59:59',
    '7' => '2024-03-31 23:59:59',
    '8' => '2024-03-31 23:59:59',
    '9' => '2024-03-31 23:59:59',
    '10' => '2024-03-31 23:59:59',
    '11' => '2024-03-31 23:59:59',
    '12' => '2024-03-31 23:59:59',
    '1' => '2024-03-31 23:59:59',
    '2' => '2024-03-31 23:59:59'];

$hit = 0;
date_default_timezone_set('Asia/Tokyo');
$time = time();

foreach ($passes as $mkey => $pass) {
    if ($_REQUEST['txtPass'] == $pass) {
        $term = $terms[$mkey];
        $from = strtotime($froms[$mkey]);
        $to = strtotime($tos[$mkey]);

        if ($time >= $from && $time <= $to) {
            $on = 1;
        } elseif ($time >= $from && $time >= $to) {
            $on = 2;
        } else {
            $on = 0;
        }

        if ($on != 0) {
            ?>
<body>
<img border="0" alt="header" src="../img/header.jpg" width="800">
<dl class="scrn_title900">
<dt style="width:22em;"><span>「解答解説」のダウンロード(高校)</span> 
</dt><dd>
<div class="frame">
<br>
<table>
<tbody><tr height="30">
<td align="center" width="400"><table><tbody><tr><td class="title_tblr" align="center" width="310"><b><?php echo $mkey; ?>月テスト　解答解説</b></td></tr></tbody></table></td>
<td align="center" width="400"></td>
</tr>
<tr height="50">
<td align="center">公開期間：<?php echo $term; ?></td>
<td align="center"></td>
</tr>
<tr>
<td align="center">
	<table>
	<tbody>
	<tr>
		<td class="title_tblr" align="center" colspan="2">高　校</td>
	</tr>
	<tr>
		<td class="item_tblr" align="center" width="100">１年</td>
		<td class="item_tblr2" align="center" width="200">
<?php
if ($on == 1) {
    echo '<a href="./kai_23'.sprintf('%02s', $mkey).'31.pdf" target="_blank">解答解説</a>';
} elseif ($on == 2) {
    echo '解答解説';
}
            ?>
		</td>
	</tr>
	<tr>
		<td class="item_tblr" align="center">２年</td>
		<td class="item_tblr2" align="center">
<?php
if ($on == 1) {
    echo '<a href="./kai_23'.sprintf('%02s', $mkey).'32.pdf" target="_blank">解答解説</a>';
} elseif ($on == 2) {
    echo '解答解説';
}
            ?>
		</td>
	</tr>
	<tr>
		<td class="item_tblr" align="center">３年</td>
		<td class="item_tblr2" align="center">
<?php
if ($on == 1 && $mkey >= 4) {
    echo '<a href="./kai_23'.sprintf('%02s', $mkey).'33.pdf" target="_blank">解答解説</a>';
} elseif ($on == 1) {
    echo '－';
} elseif ($on == 2 && $mkey >= 4) {
    echo '解答解説';
} elseif ($on == 2) {
    echo '－';
}
            ?>
		</td>
	</tr>
	</tbody>
	</table>
<br>
</div>
</dd>
</dl>
<!--<div class="address">Copyright &#169; 2019 Seihoku Publishing Company All rights reserved.</div>-->
</body>
<?php
                        $hit = 1;
        }
        break;
    }
}
if ($hit == 0) {
    echo 'パスワードが違います。';
}
?>
</html>
