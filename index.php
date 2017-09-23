<?php
/**
 * NIC.IR Released Domain
 * @author Parsa Kafi
 * @version 1.0
 * Website: http://parsa.ws
 * */

require_once "simpleHtmlDom.php";
require_once "functions.php";

if (!save_cookie() || !$cookie = get_cookie())
    die("Unable to get cookie form nic.ir!");

$message = '';
if (isset($_POST['submit'])) {
    $ch = curl_init('http://www.nic.ir/Just_Released?captcha=' . $_POST['captcha']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Cookie: IRNIC={$cookie}"));
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.1 Safari/537.11');
    $result = curl_exec($ch);
    if (!empty($result) && (mb_strpos($result, "فیلدهای مشخص شده را با دقت و با حروف و اعداد مجاز مجدداً پر") !== false || mb_stripos($result, "متن نمایش داده شده در عکس (CAPTCHA) غلط وارد شده است") !== false))
        $message = "Your captcha is invalid!";
    elseif (!empty($result)) {
        $html = str_get_html($result);
        $table = $html->find('.listing-table', 1);
        if ($table) {
            $table_a = array();
            if (isset($table->innertext))
                foreach ($table->find('tr') as $tr) {
                    $tds = $tr->find("td");
                    foreach ($tds as $td)
                        if (!is_numeric($td->innertext))
                            $table_a[] = $td->innertext;
                }

            $domains = array();
            for ($i = 0; $i <= count($table_a) - 1; $i = $i + 2) {
                $originalDate = str_replace("- ", "-", trim(strip_tags($table_a[$i + 1])));
                $domains[] = array(
                    'domain' => trim(strip_tags($table_a[$i])),
                    'expire_date' => $originalDate,
                );
            }
        } else {
            $message = "HTML Parse with Error!";
        }
    } else {
        $message = "Unable request to nic.ir";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>NIC.IR Released Domain</title>
</head>
<body>
<h1>NIC.IR Released Domain</h1>
<h3><?php echo $message; ?></h3>
<form action="" method="post">
    <img src="captcha.php?cookie=<?php echo $cookie ?>"><br>
    <input type="text" name="captcha" placeholder="Captcha Code" autocomplete="off" autofocus>
    <input type="submit" name="submit" value="submit">
</form>
<?php
if (isset($domains) && count($domains)) {
    $i = 0; ?>
    <table>
        <thead>
        <tr>
            <th>#</th>
            <th><?php echo implode('</th><th>', array_keys(current($domains))); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($domains as $row) { ?>
            <tr>
                <td><?php echo ++$i ?></td>
                <td><?php echo implode('</td><td>', $row); ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
<?php } ?>
</body>
</html>