<?php
header('Access-Control-Allow-Origin: *');
$answ;
if(isset($_POST['url']) && !empty($_POST['url']) ){
//Скачиваем файл по ссылке
$myXml=$_POST['url'];
$xml_name=$_POST['name'];


//$myXml=$dir.'/'.$tmp_xml_name;
$xml=simplexml_load_file($myXml);
$cat=$xml->shop->categories;



$idArray=array();
$pidArray=array();
$nameArray=array();
//Собираем массив категорий
foreach($cat->category as $i){
	array_push($pidArray, (string)$i['parent_id']);
	array_push($idArray, (string)$i['id']);
	array_push($nameArray, (string)$i);

};
//Собираем зависимость категорий
	$catIndex=array();
	$catBlock=array();
foreach($cat->category as $i){
	//Определяем изначальную позицию Родителя (представим что их не больше 4)
	$index1=$index2=$index3=$index4=0;
	$index1=array_search($i['parent_id'], $idArray);
	$str=(string)$i;
	for($d=1; $d<5; $d++){
		$in='index'.$d;
		$in2='index'.($d+1);
		if($$in>0){
			$str.=' -> '.$nameArray[$$in];
			$$in2=array_search($pidArray[$$in], $idArray);
		}
	}
	$catRowTmp=array();
	$catRowTmp=explode(' -> ', $str); //

	array_push($catIndex, (string)$i['id']);
	array_push($catBlock, $catRowTmp);

};
//После того как соберем блоки категорий, проверяем в массиве слвподение по $catIndex 
//и в цикле выводим $catBlock соответсвующей позици, в обратном порядке

function translit($str) {
    $rus = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я', ' ', '/', '\\');
    $lat = array('A', 'B', 'V', 'G', 'D', 'E', 'E', 'Gh', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Sch', 'Y', 'Y', 'Y', 'E', 'Yu', 'Ya', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'gh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', 'y', 'y', 'y', 'e', 'yu', 'ya', '_', '_', '_');
    return str_replace($rus, $lat, $str);
  };

$offer=$xml->shop->offers->offer;
//echo '<hr>'.count($offer).'<hr>';

$count = array('count' => count($offer));


//$answ=array('test'=>count($offer));
if(!file_exists($xml_name)){
	mkdir($xml_name);
	$im_dir=$xml_name."/img";
	mkdir($im_dir);
}
//создадим файл с переменными
$vars=fopen("vars.php","w");
$vars_value='<?PHP $xml_name="'.$xml_name.'";'.PHP_EOL.'?>';
fwrite($vars, $vars_value);  
fclose($vars);


$xml_file = array('xml_file' => $xml_name."/".$xml_name.".xml");
$json_file = array('json_file' => $xml_name."/"."img.json");
$answ = array_merge($count, $xml_file, $json_file);

$obj;
$fp = fopen($xml_name."/"."img.json","w");
fclose($fp);
foreach ($offer as $i){	
global $obj;
	$name=$i->name;
	$catN=$i->categoryId;
	$origPicUrl=$i->picture;
	
	$imgName=preg_replace("/[^a-z0-9._-]/iu", '',translit($i->name));//убираем все знаки кроме латиницы цифр и подчеркивания

	$i->addChild('ownImg', $imgName.'.jpg');
	$in=array_search($catN, $catIndex);
	//echo $catN.'<br>';
	$reversCatRow=array_reverse($catBlock[$in]);
	$catStr='';
	$catCount=0;
	foreach($reversCatRow as $r){
		$catStr.=" - ".$r;
		$i->addChild('category'.$catCount, htmlspecialchars($r));
		$catCount++;
	}
	//echo $name.' Категория: '.$catStr.'<br>';
	//Сохраняем изображения
	$img_path=$im_dir."/".$imgName.'.jpg';
	
	//$obj_row=array('in'=>"http:".$origPicUrl, 'out'=>$imgName.'.jpg');
	/*
	if(!preg_match("/http:/", $origPicUrl)){
		$origPicUrl="http:".$origPicUrl;
	}
	*/
	//$origPicUrl="http:".$origPicUrl;
	$imgName=$imgName.".jpg";
	$obj_row=$origPicUrl." ^%^ ".$imgName;
	//array_push($obj, $obj_row);
	$fp_json=fopen($xml_name."/"."img.json","a"); 
	fwrite($fp_json, json_encode($obj_row, JSON_UNESCAPED_SLASHES).','.PHP_EOL);  
	fclose($fp_json);
	
}



//Сохраняем в папку если есть название
//echo "url - ".$xml_name;
if($xml_name){
	$xml->asXML($xml_name."/".$xml_name.".xml");
	}
else{
	$fname='xml';
	$xml->asXML($fname.".xml");
	}


//тут мы собственно название картинки переводим
//в данном случае для xml (хотя нужно подумать, насчет перенести это в imgDown.php (вместе с переназываением) $i->addChild('ownImg', $name);)

//unlink($dir."/".$tmp_xml_name);
//rmdir($dir);
$file = 'xml.xml';
//unlink('myXml.zip'); //удаляем архив
//echo "<div style='position: absolute; left: 300px; top: 150px; width: 300px; height: 300px; padding: 100px; background-color: white'><a href='".$file."'>Скачать файл</a></div>";
//$answ_obj=(array)$answ;
//print_r($answ_obj);
print_r(json_encode($answ, JSON_UNESCAPED_SLASHES));
}
else{
	?>
	<style>
	body{
		margin:10px;
	}
	input{
		width: 100%;
		margin-bottom: 10px;
		font-size: 2em;
	}
	</style>
	
	<form method="POST">
		<input type="text" name="name" class="inp" placeholder="как назовем?" required="required"><br>
		<input type="text" name="url" class="inp" placeholder="урл" required="required"><br>
		<input type="submit" value="Отправить">	
	</form>	
	<?php } ?>

