<?php
namespace Util\Controller;
import("Org.Util.PHPExcel");
/**
 * excel工具
 * @author asus
 *
 */
class ExcelUtil{
	
	private $_php_excel;
	private $_excel_active_sheet;
	private $_key_to_letter;
	function  __construct(){
		$this->_php_excel=new \PHPExcel();
		$this->_php_excel->setActiveSheetIndex(0);
		//设置居中
		$this->_php_excel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->_php_excel->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
		// 设置边框
		$styleArray = array (
				'allborders' => array (
						// 'style' => PHPExcel_Style_Border::BORDER_THICK,//边框是粗的
						'style' => \PHPExcel_Style_Border::BORDER_THIN, // 细边框
						//'color' => array ('argb' => 'FFFF0000' )
				)
		 
		);
		
		$this->_php_excel->getDefaultStyle()->getBorders()->applyFromArray($styleArray);
		$this->_excel_active_sheet=$this->_php_excel->getActiveSheet();
	}
	
	/**
	 * 设置当前的sheet
	 * @param unknown $sheet_name 工作区名字
	 * @param unknown $sheet_index 工作区下标
	 * @param string $is_new 是否新建 默认只有0这一个工作区,其它工作区都需要新建
	 */
	public function set_active_sheet($sheet_name,$sheet_index,$is_new=false){
		if($is_new){
			$this->_php_excel->createSheet($sheet_index);
		}
		$this->_php_excel->setActiveSheetIndex($sheet_index);
		$this->_php_excel->getActiveSheet()->setTitle($sheet_name);
		$this->_excel_active_sheet=$this->_php_excel->getActiveSheet();
	}
	
// 	/**
// 	 * 
// 	 * @param unknown $title_array 列名:格式如下:二维数组
// 	 *  	$title_array['column_1']="第一列标题";
// 	 *		$title_array['column_2']="第二列标题";
// 	 *		$title_array['column_3']="第三列标题";
// 	 *		$title_array['column_4']="第四列标题";
// 	 *		$title_array['column_5']="第五列标题";
// 	 *		$title_array['column_6']="第六列标题";
// 	 *
// 	 * @param unknown $data_array 数据,每一条数据格式:
// 	 * 			$data['column_1']="第一列数据";
// 	 *			$data['column_2']="第二列数据";
// 	 *			$data['column_3']="第三列数据";
// 	 *			$data_son[]=array(
// 	 *		 			"column_4"=>"第四列数据",
// 	 *		 			"column_5"=>"第五列数据",
// 	 *		 			"column_6"=>"第六列数据"
// 	 *		 		);
// 	 *			$data_son[]=array(
// 	 *					"column_4"=>"第四列数据",
// 	 *					"column_5"=>"第五列数据",
// 	 *					"column_6"=>"第六列数据"
// 	 *			);
// 	 *			$data['column_merge']=$data_son;
// 	 * 
// 	 */
// 	public function set_active_sheet_data($title_array,$data_array){

// 		$this->_excel_active_sheet->getDefaultColumnDimension()->setWidth(20);
// 		$this->_excel_active_sheet->getDefaultStyle()->getAlignment()->setWrapText(true);
		
// 		$key_array=array_keys($title_array);
// 		$this->_key_to_letter=array();//保存键名与excel列对应的记录
// 		$end_letter=chr(65+count($title_array)-1);
// 		//插入列名
// 		foreach (range('A', $end_letter) as $key=>$column_name){
// 			$this->_excel_active_sheet->setCellValue($column_name.'1',$title_array[$key_array[$key]]);
// 			$this->_key_to_letter[$key_array[$key]]=$column_name;
// 		}
		
// 		//插入数据
// 		$excel_index=1;
// 		foreach ($data_array as $data){
// 			$excel_index++;
			
			
// 		}
// 	}
	
	public function set_active_sheet_data($title_array,$data_array){
	
		$objActSheet = $this->_php_excel->getActiveSheet();
		$objActSheet->getDefaultColumnDimension()->setWidth(20);
		$objActSheet->getDefaultStyle()->getAlignment()->setWrapText(true);
	
		$key_array=array_keys($title_array);
		$column_size=count($title_array);
		$row_size=count($data_array);
		$end_letter=chr(65+$column_size-1);
		//插入列名
		foreach (range('A', $end_letter) as $key=>$column_name){
			$objActSheet->setCellValue($column_name.'1',$title_array[$key_array[$key]]);
			// 			$objActSheet->getColumnDimension($column_name)->setAutoSize(true);
		}
	
		//插入数据
		$excel_index=1;
		foreach ($data_array as $data_index=>$data){
			$excel_index++;
			foreach (range('A', $end_letter) as $key=>$column_name){
				$objActSheet->setCellValue($column_name.$excel_index,$data[$key_array[$key]]);
			}
		}
	}
	private function insert_data($excel_index,$data){
		
		foreach ($data as $data_index=>$data_value){
			if(is_array($data_value)){
					
			}
		}
	}
	
	
	/**
	 * 导出下载
	 * @param unknown $file_name
	 */
	public function export($file_name){
		$filename = $file_name.'.xlsx';
		
		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename);
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		
		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
		
		$objWriter = \PHPExcel_IOFactory::createWriter($this->_php_excel, 'Excel2007');
		$objWriter->save('php://output');
		
		//header('Location:'.U('member/members'));
		exit;
	}
   
}

?>
