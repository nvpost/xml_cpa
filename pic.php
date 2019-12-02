<?PHP
	$fp=fopen("img.txt","r+");  
	echo fgets($fp);
	$vals=explode("\t", $fp);
	copy($vals[0], $vals[1])	
	fclose($fp);

?>