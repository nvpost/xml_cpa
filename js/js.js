var json_mes
function send_xml(e){
    var name=$('#name').val()
    var url=$('#url').val()
	var adm=$('#adm').val()
	var php_url = 'http://localhost/xml/xml.php'	
	if($('#adm_gde').prop('checked')){
		php_url='http://localhost/xml/admitad_xml.php'
	}

    data={'name':name, 'url':url}
    startParse(php_url, data)
	console.log(data)
	console.log(url)
}
function startParse(php_url, data){
    $.ajax({
        type: 'POST',
        url: php_url,
        data: data,
        success: function(msg){
            json_mes=JSON.parse(msg)
            console.log(json_mes)
            drow_res(json_mes)
      },
    })
}
function drow_res(data){
    $('.res').css("display", "flex")
    $('.count').text("Офферов: "+data.count)
    $('.xml').html("<div class='btn btn-default' onclick='down("+data.xml_file+")'>Скачать XML</div>")
    $('.json').html("<div class='btn btn-default' onclick='down("+data.json_file+")'>Скачать json</div>")
    $('.img_download').html("<div class='btn btn-default' onclick=get_arr('"+data.json_file+"')>Cкачать rартинки</div>")
   //$('.script').html("<script src="+data.json_file+"></script>")
    
}
var img_arr=[]
var count=0
function get_arr(url){
     $.ajax({
        type: 'POST',
        url: 'http://localhost/xml/get_img_data.php',
        data: data,
        success: function(msg){
            img_arr=JSON.parse(msg)
            //download_img(img_arr)
            //console.log(img_arr[0])
            copi_img(img_arr[0])
      },
    })
}

function download_img(img_arr){  
    for (i in img_arr){
        img_arr_row=img_arr[i].split(' ^%^ ')
        //console.log(img_arr_row[0].replace('"', ''))
        in_img=img_arr_row[0].replace('"', '')
        out_img=img_arr_row[1].replace('"', '')

        /*copi_img(in_img, out_img)
        count++;
        console.log("Загружено "+count)*/

    }
}

function copi_img(img_row){

    img_arr_row=img_row.split(' ^%^ ')
    in_img=img_arr_row[0].replace('"', '')
    out_img=img_arr_row[1].replace('"', '')
    out_img=out_img.replace(',', '')
    out_img=out_img.replace(' ', '')
    out_img=out_img.replace('\r', '')
    out_img=out_img.replace('\n', '')

    var data={'in':in_img,'out':out_img}
    console.log(data)
    $.ajax({
        type: 'POST',
        url: 'http://localhost/xml/copy.php',
        data: data,
        success: function(msg){
            console.log(msg);
            if(count<img_arr.length-1){
                count++;
                copi_img(img_arr[count])
                console.log("Загружено "+count) 
            }
      },
    })
}
