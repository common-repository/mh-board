<?php
if (function_exists('curl_init')) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, MH_BOARD_UPDATE_URL);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_TIMEOUT, 600);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0');
	$data = curl_exec($ch);
	$data = simplexml_load_string($data);
	
	curl_close($ch);
} else {
	// curl library is not installed so we better use something else
	$xml = wp_remote_get(MH_BOARD_UPDATE_URL);
	$data = simplexml_load_string($xml['body']);
}

if($data->version != MH_BOARD_VERSION){
	echo "<p>".__('Installed version:','mhboard').MH_BOARD_VERSION." | ".__('Current version','mhboard').":{$data->version}</p>";
	echo "<p>".__('Read more:','mhboard')." <a href='{$data->download}'>$data->download</a></p>";
}else{
	//echo "현재 MH Board의 버전은 ".MH_BOARD_VERSION."으로 최신버전입니다.";
	echo "<p>".__('Installed version:','mhboard').MH_BOARD_VERSION." | ".__('Current version','mhboard').":{$data->version}</p>";
	echo "<p>".__('Read more:','mhboard')." <a href='{$data->download}'>$data->download</a></p>";
}else{
}
?>