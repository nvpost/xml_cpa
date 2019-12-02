<?PHP
header('Access-Control-Allow-Origin: *');
include 'vars.php';
    $row=$_POST;
    $in = $_POST['in'];
    $out = $_POST['out'];
    echo $in." * ".$out;
    $path=$xml_name."/img/".$out;
    //copy($in, $path);
    echo $path;
    echo gettype($path);
    //$fp=fopen($path,"a"); 
	//fclose($fp);
    $in_file=file_get_contents($in);
    $out_file=file_put_contents($path, $in_file);
?>