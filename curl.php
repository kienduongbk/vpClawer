<?php 

function getData($cmnd,$code){
	$curl = curl_init('http://vpbrbqa-com.stackstaging.com');
	curl_setopt($curl, CURLOPT_FAILONERROR, true);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
     curl_setopt($curl,CURLOPT_TIMEOUT,1000);
	curl_setopt($curl, CURLOPT_POSTFIELDS,
            "cmnd=$cmnd&code=$code");  
	$result = curl_exec($curl);
	curl_close ($curl);
	return $result;

}
function getCmnd($data){
     $strLength = strlen(strval($data));
     $cmd = substr($data,($strLength - 4));
     if(substr($cmd,0,1) == 0){
          $return = substr($cmd,1);
     }else{
          $return = $cmd;
     }
     return $return;
}
/*
example
$a = html_entity_decode(getData('2651','3861663'));
preg_match_all('/<table.*?>(.*?)<\/table>/si',$a, $out, PREG_PATTERN_ORDER);

preg_match_all('/<tr.*?>(.*?)<\/tr>/si',$out[0][0], $tr, PREG_PATTERN_ORDER);
preg_match_all('/<td.*?>(.*?)<\/td>/si',$tr[0][17], $td1, PREG_PATTERN_ORDER);
preg_match_all('/<td.*?>(.*?)<\/td>/si',$tr[0][18], $td2, PREG_PATTERN_ORDER);
echo "<pre>";
var_dump($td2);die();*/

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

/** Include PHPExcel */
require_once dirname(__FILE__) . '/scripts/phpExcel/PHPExcel.php';

echo date('H:i:s') , " Load from Excel2007 file" , EOL;
$callStartTime = microtime(true);
$objPHPExcel = PHPExcel_IOFactory::load("tmp/".$_FILES['uploaded_file']['name']);
$files = "tmp/".$_FILES['uploaded_file']['name'];
//Tiến hành xác thực file
$objFile = PHPExcel_IOFactory::identify($files);
$objData = PHPExcel_IOFactory::createReader($objFile);

//Chỉ đọc dữ liệu
$objData->setReadDataOnly(true);

// Load dữ liệu sang dạng đối tượng
$objPHPExcel = $objData->load($files);

//Lấy ra số trang sử dụng phương thức getSheetCount();
// Lấy Ra tên trang sử dụng getSheetNames();

//Chọn trang cần truy xuất
$sheet = $objPHPExcel->setActiveSheetIndex(0);

//Lấy ra số dòng cuối cùng
$Totalrow = $sheet->getHighestRow();
//Lấy ra tên cột cuối cùng
$LastColumn = $sheet->getHighestColumn();

//Chuyển đổi tên cột đó về vị trí thứ, VD: C là 3,D là 4
$TotalCol = PHPExcel_Cell::columnIndexFromString($LastColumn);
//Tạo mảng chứa dữ liệu
$data = [];

//Tiến hành lặp qua từng ô dữ liệu
//----Lặp dòng, Vì dòng đầu là tiêu đề cột nên chúng ta sẽ lặp giá trị từ dòng 2
for ($i = 1; $i <= $Totalrow; $i++) {
    //----Lặp cột
    for ($j = 0; $j < $TotalCol; $j++) {
        // Tiến hành lấy giá trị của từng ô đổ vào mảng
        $data[$i - 1][$j] = $sheet->getCellByColumnAndRow($j, $i)->getValue();;
    }
}
$objPHPExcel = new PHPExcel();
// Set document properties
echo date('H:i:s') , " Set document properties" , EOL;
$objPHPExcel->getProperties()->setCreator("huyenxinh")
               ->setLastModifiedBy("huyenxinh")
               ->setTitle("haha")
               ->setSubject("list400")
               ->setDescription("test curls data")
               ->setKeywords("xxx")
               ->setCategory("list4000");


// Add some data
echo date('H:i:s') , " Add some data" , EOL;
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'CIF')
            ->setCellValue('B1', 'FullName')
            ->setCellValue('C1', 'Cmd')
            ->setCellValue('D1', 'Result')
            ->setCellValue('E1', 'UPL')
            ->setCellValue('F1', 'CC');
            $i = 2;
foreach ($data as $key => $value) {
     $cmnd = getCmnd($value[2]);
     $curls = getData($cmnd,$value[0]);
     preg_match_all('/<table.*?>(.*?)<\/table>/si',$curls, $out, PREG_PATTERN_ORDER);
     $result = 0;
     $upl = '';
     $cc = '';
     if(!empty($out[1])){
          preg_match_all('/<table.*?>(.*?)<\/table>/si',$curls, $out, PREG_PATTERN_ORDER);
          preg_match_all('/<tr.*?>(.*?)<\/tr>/si',$out[0][0], $tr, PREG_PATTERN_ORDER);
          preg_match_all('/<td.*?>(.*?)<\/td>/si',$tr[0][17], $td1, PREG_PATTERN_ORDER);
          preg_match_all('/<td.*?>(.*?)<\/td>/si',$tr[0][18], $td2, PREG_PATTERN_ORDER);
          $result = 1;
          $upl = strip_tags($td1[0][1],"<td>");
          $cc = strip_tags($td2[0][1],"<td>");
          
     }
     $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $value[0])
            ->setCellValue('B'.$i, $value[1])
            ->setCellValue('C'.$i, $value[2])
            ->setCellValue('D'.$i,  $result)
            ->setCellValue('E'.$i, $upl)
            ->setCellValue('F'.$i,  $cc);
  $i++;
}

// Rename worksheet
echo date('H:i:s') , " Rename worksheet" , EOL;
$objPHPExcel->getActiveSheet()->setTitle('huyenxinh');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Save Excel 2007 file
echo date('H:i:s') , " Write to Excel2007 format" , EOL;
$callStartTime = microtime(true);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save($_FILES['uploaded_file']['name']);
$callEndTime = microtime(true);
$callTime = $callEndTime - $callStartTime;

echo date('H:i:s') , " File written to " , str_replace('.php', '.xlsx', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;
echo 'Call time to write Workbook was ' , sprintf('%.4f',$callTime) , " seconds" , EOL;
// Echo memory usage
echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB" , EOL;

// Echo memory peak usage
echo date('H:i:s') , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) , " MB" , EOL;

// Echo done
echo date('H:i:s') , " Done writing files" , EOL;
echo 'Files have been created in ' , getcwd() , EOL;
?>