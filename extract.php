<?php

$data = file_get_contents("shell.php");
$line = explode("\r\n", $data);

$store = "<?php\r\n\r\n\$collect = \"\";\r\n";
foreach($line as $fak){
	$store .= "\$collect .= \"".addslashes($fak)."\";\r\n";
}
file_put_contents("ext.php", $store);


?>