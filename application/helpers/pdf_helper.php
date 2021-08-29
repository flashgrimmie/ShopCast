<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if (!function_exists('create_pdf')) {

    function create_pdf($html_data) {
        require APPPATH."third_party/mpdf/mpdf.php"; 
        $mypdf = new mPDF();
        $mypdf->WriteHTML($html_data);
        $mypdf->Output();
		
    }


    function create_pdf_l($html_data) {
        require APPPATH."third_party/mpdf/mpdf.php"; 
        $mypdf = new mPDF('','A4-L');
        $mypdf->WriteHTML($html_data);
        $mypdf->Output();
		
    }

    function create_pdf_letter($html_data) {
        require APPPATH."third_party/mpdf/mpdf.php"; 
        $mypdf = new mPDF('', array(110,200), 30, '', 12.7, 12.7, 5, 5, 5, 5);
        $mypdf->WriteHTML($html_data);
        $mypdf->Output();
        
    }

}