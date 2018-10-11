<?php
ob_end_clean();
header("Content-type:text/html;charset:utf-8");
require_once 'application/libraries/PhpExecl/PHPExcel.php';
require_once 'application/libraries/PhpExecl/PHPExcel/IOFactory.php';
require_once 'application/libraries/PhpExecl/PHPExcel/Reader/Excel5.php';
require_once 'application/libraries/PhpExecl/PHPExcel/Reader/Excel2007.php';

class phpexecl_upload
{

    public function userDowns($data)
    {
        $objPHPExcel = new PHPExcel();
        $iofactory = new PHPExcel_IOFactory();
        // 设置excel列名
        if (! is_array($data) || empty($data)) {
            return false;
        }
        
        // 设置字段名
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', '编号');
        $objPHPExcel->getActiveSheet()
            ->getColumnDimension('B')
            ->setWidth(25);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', '用户名');
        $objPHPExcel->getActiveSheet()
            ->getColumnDimension('C')
            ->setWidth(15);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', '性别');
        $objPHPExcel->getActiveSheet()
            ->getColumnDimension('D')
            ->setWidth(25);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1', '手机号');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E1', '注射剂量');
        $objPHPExcel->getActiveSheet()
            ->getColumnDimension('F')
            ->setWidth(15);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F1', '注射时间');
        $objPHPExcel->getActiveSheet()
            ->getColumnDimension('G')
            ->setWidth(25);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G1', '目前注射胰岛素');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H1', '硬结');
        $objPHPExcel->getActiveSheet()
        ->getColumnDimension('I')
        ->setWidth(35);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I1', '产品编号');
        $objPHPExcel->getActiveSheet()
            ->getColumnDimension('I')
            ->setWidth(45);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J1', '地址');
        $objPHPExcel->getActiveSheet()
            ->getColumnDimension('J')
            ->setWidth(25);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K1', '注册时间');
        $filename = 'userList.xlsx';
        // 设置数据内容
        $ii = 2;
        
        foreach ($data as $key => $value) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $ii, $value['id']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $ii, $value['username']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $ii, $value['gender']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $ii, $value['account']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $ii, $value['injected_dose']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $ii, $value['medical_history']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $ii, $value['insulin']);
            $injected_dose = $value['injected_dose']==1?'有':'无';
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $ii, $injected_dose);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I' . $ii, $value['product_number']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . $ii, $value['address']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . $ii, $value['add_time']);
            $ii ++;
        }
        
        // $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$key,$value['id']);
        // $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$key,$value['name']);
        // $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$key,$value['age']);
        // excel保存在根目录下 如要导出文件，以下改为注释代码
        // $objPHPExcel->getActiveSheet() -> setTitle( 'qq' );
        // $objPHPExcel-> setActiveSheetIndex(0);
        // $objWriter = $iofactory -> createWriter($objPHPExcel, 'Excel2007');
        // $objWriter -> save( 'qq.xlsx');
        // 导出代码
        $objPHPExcel->getActiveSheet()->setTitle('info');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = $iofactory->createWriter($objPHPExcel, 'Excel2007');
        
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
    }

    function getalphnum($int)
    {
        $array = array(
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z'
        );
        return $array[$int];
    }

    public function customerDowns($data)
    {
        $objPHPExcel = new PHPExcel();
        $iofactory = new PHPExcel_IOFactory();
        // 设置excel列名
        if (! is_array($data) || empty($data)) {
            return false;
        }
        
        // 设置字段名
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', '编号');
        $objPHPExcel->getActiveSheet()
            ->getColumnDimension('B')
            ->setWidth(25);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', '姓名');
        $objPHPExcel->getActiveSheet()
            ->getColumnDimension('C')
            ->setWidth(25);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', '推荐医生');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1', '性别');
        $objPHPExcel->getActiveSheet()
            ->getColumnDimension('E')
            ->setWidth(25);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E1', '手机号');
        $objPHPExcel->getActiveSheet()
            ->getColumnDimension('F')
            ->setWidth(15);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F1', '注射剂量');
        $objPHPExcel->getActiveSheet()
            ->getColumnDimension('G')
            ->setWidth(25);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G1', '注射时间');
        $objPHPExcel->getActiveSheet()
            ->getColumnDimension('H')
            ->setWidth(35);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H1', '胰岛素名称');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I1', '硬结');
        $objPHPExcel->getActiveSheet()
        ->getColumnDimension('J')
        ->setWidth(45);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J1', '地址');
        $objPHPExcel->getActiveSheet()
            ->getColumnDimension('K')
            ->setWidth(25);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K1', '注册时间');
        $filename = 'customerList.xlsx';
        // 设置数据内容
        $ii = 2;
        foreach ($data as $key => $value) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $ii, $value['id']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $ii, $value['username']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $ii, $value['doctor_name']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $ii, $value['gender']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $ii, $value['mobile']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $ii, $value['injected_dose']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $ii, $value['medical_history']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $ii, $value['insulin']);
            $is_scleroma = $value['is_scleroma']=='1'?'有':'无';
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I' . $ii, $is_scleroma);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . $ii, $value['address']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . $ii, $value['add_time']);
            $ii ++;
        }
        
        // $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$key,$value['id']);
        // $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$key,$value['name']);
        // $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$key,$value['age']);
        // excel保存在根目录下 如要导出文件，以下改为注释代码
        // $objPHPExcel->getActiveSheet() -> setTitle( 'qq' );
        // $objPHPExcel-> setActiveSheetIndex(0);
        // $objWriter = $iofactory -> createWriter($objPHPExcel, 'Excel2007');
        // $objWriter -> save( 'qq.xlsx');
        // 导出代码
        $objPHPExcel->getActiveSheet()->setTitle('info');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = $iofactory->createWriter($objPHPExcel, 'Excel2007');
        
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
    }
}

