<?php
	//one way to get the final octet of a given server
	//i'm sure there are others, but i've tested and used this method
	$ip = gethostbyname(php_uname('n'));
	echo "$ip\n";
	$octet = trim(strrchr($ip, "."), ".");
	echo "$octet\n";
?>