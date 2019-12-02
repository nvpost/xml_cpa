<?PHP
header('Access-Control-Allow-Origin: *');
include 'vars.php';
    $fp_json=file($xml_name."/img.json");
    print_r(json_encode($fp_json, JSON_UNESCAPED_SLASHES));
?>