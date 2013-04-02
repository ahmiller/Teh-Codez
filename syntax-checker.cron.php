<?php

//to use, run as a cronjob on the server you want to monitor syntax on...for example, to recursively check the root directory:
//*/20 *  *  *  * root php /var/www/html/syntax-checker.cron.php /var/www/html/source/

$start_dir = (isset($argv[1])) ? $argv[1] : '.';
$failsummary = array();

function check_syntax($path) {
	global $failsummary;
	foreach(scandir($path) as $file) {
		if($file != '.' && $file != '..') {
			if(is_dir("$path/$file")) {
				check_syntax("$path/$file");
			} else if(preg_match("/.\.(php)$/", $file)) {
				$twoWeeksAgo = strtotime("-2 week");
				if (filemtime("$path/$file") > $twoWeeksAgo) {
					
					//shell no likey spaces or parens
					if (preg_match("/\s+/", $file)) {
						$file = preg_replace("|\s+|", "\ ", $file);
					}
					if (preg_match("/\(/", $file)) {
						$file = preg_replace("|\(|","\(", $file);
					}
					if (preg_match("/\)/", $file)) {
						$file = preg_replace("|\)|","\)", $file);
					}

					$result = `php -l $path/$file 2>&1`;
					if(substr($result, 0, 25) != 'No syntax errors detected') {
						echo $result;
						array_push($failsummary, $result);
					}
				}
			}
		}
	}
}

if (is_file($start_dir)) {
	if (preg_match("/.\.(php)$/", $start_dir)) {
		$result = `php -l $start_dir`;
		if($result[0] != 'N') echo $result;			
	}
} else {
	check_syntax($start_dir);
}

$host = php_uname('n');

if (count($failsummary) > 0) {
	mail ("test@your-company.com","Syntax check failure summary on $host","The following PHP files did NOT have valid syntax:\n\n" . print_r($failsummary, true), "From: server@your-comapny.com");
} else {
	echo "Good job! All clean, syntax-wise. But don't get cocky kid, your logic might still be wrong! ;)\n";
}	

?>