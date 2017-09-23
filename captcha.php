<?php
header("Content-Type: image/png");
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://www.nic.ir/Show_CAPTCHA');
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Cookie: IRNIC={$_GET['cookie']}"));
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.1 Safari/537.11');
$res = curl_exec($ch);
curl_close($ch);
echo $res;