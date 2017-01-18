<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;
use App\PartNumber;

class ImportController extends Controller
{
    public function index()
    {
        if (\Gate::denies('importView', PartNumber::class)) {
            abort(404);
        }

        return view('import');
    }

    private function cekSize(){
    	$file_max = ini_get('upload_max_filesize');
	    $file_max_str_leng = strlen($file_max);
	    $file_max_meassure_unit = substr($file_max,$file_max_str_leng - 1,1);
	    $file_max_meassure_unit = $file_max_meassure_unit == 'K' ? 'kb' : ($file_max_meassure_unit == 'M' ? 'mb' : ($file_max_meassure_unit == 'G' ? 'gb' : 'unidades'));
	    $file_max = substr($file_max,0,$file_max_str_leng - 1);
	    $file_max = intval($file_max);

	    //handle second case
	    if((empty($_FILES) && empty($_POST) && isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) == 'post'))
	    {
	    	return false;
	    }
	    return true;
    }

    public function upload(Request $request)    {
    	if (\Gate::denies('importImport', PartNumber::class)) {
            abort(404);
        }

        $this->validate($request, [
            'document' => 'required|mimes:xlsx,ods',
        ]);
        $fileName = time().'.'.$request->document->getClientOriginalExtension();
        if($request->document->move(storage_path('app/uploads'), $fileName)){
        	return array('file' => $fileName);
        }            
    }

    private function readSpreadSheet($filename){
    	$ext = pathinfo($filename, PATHINFO_EXTENSION);

    	if(strtolower($ext == 'ods')){
        	$reader = ReaderFactory::create(Type::ODS);
        }else{
        	$reader = ReaderFactory::create(Type::XLSX);
        }

        $reader->open(storage_path('app/uploads/' . $filename));
		return $reader;
    }    

    public function read($filename){	
    	if (\Gate::denies('importImport', PartNumber::class)) {
            abort(404);
        }

    	$reader = $this->readSpreadSheet($filename);
    	foreach ($reader->getSheetIterator() as $sheet) {
			$i = 1;
			$table_name ='';
			foreach ($sheet->getRowIterator() as $rows) {
				if($i++ == 1){
					$table_name = strtoupper(trim($rows[0]));
				}
			}
		}
		
		if($table_name == 'PART NUMBER'){
			$status = array();
			foreach ($reader->getSheetIterator() as $sheet) {
				$i = 1;
				foreach ($sheet->getRowIterator() as $rows) {
					if($i++ == 2){
						if(strtoupper(trim($rows[0])) == 'INC'){
							$status[] = 1;
						}else{
							$status[] = 0;
						}

						if(strtoupper(trim($rows[1])) == 'ITEM NAME'){
							$status[] = 1;
						}else{
							$status[] = 0;
						}

						if(strtoupper(trim($rows[2])) == 'SHORT DESCRIPTION'){
							$status[] = 1;
						}else{
							$status[] = 0;
						}

						if(strtoupper(trim($rows[3])) == 'MAN CODE'){
							$status[] = 1;
						}else{
							$status[] = 0;
						}

						if(strtoupper(trim($rows[4])) == 'MANUFACTURER NAME'){
							$status[] = 1;
						}else{
							$status[] = 0;
						}

						if(strtoupper(trim($rows[5])) == 'PART NUMBER'){
							$status[] = 1;
						}else{
							$status[] = 0;
						}

						if(strtoupper(trim($rows[6])) == 'PO TEXT'){
							$status[] = 1;
						}else{
							$status[] = 0;
						}
					}
				}				
			}
			
			if (in_array(0, $status)) {
			    echo "<span class='text-danger'>TABLE COLUMN DIDN'T MATCH</span>";
			}else{
				echo "<div id='uploaded_area'>";
				echo "<hr/>";
				$table = "<div id='message' style='margin-top:10px;'>READY TO IMPORT YOUR <strong><span id='counter'></span> OF PART NUMBER</strong> DATA</div>";
				$table .= "<table id='datatables' class='table table-striped table-bordered table-condensed' width='100%'>";
				foreach ($reader->getSheetIterator() as $sheet) {
					$i = 1;
					$first = true;
					$urut = 3;

					$cek_already_in_db = [];
					$warn_already_in_db = [];

					$check_duplicate_row = [];

					$max_str = [];
					$warn_max_str = [];

					$check_empty_item_name = [];
					$check_empty_short_desc = [];
					$check_empty_man_code = [];
					$check_empty_part_number = [];
					$check_empty_po_text = [];

					$data_counter = [];
					foreach ($sheet->getRowIterator() as $rows) {
						if($i++ > 1){
							if($first){
								$table .= "<thead><tr>";
								$table .= "<th width='3%'>#</th>";
								$table .= "<th width='10%'>".strtoupper(trim($rows[0]))."</th>";
								$table .= "<th width='14%'>".strtoupper(trim($rows[1]))."</th>";
								$table .= "<th width='16%'>".strtoupper(trim($rows[2]))."</th>";
								$table .= "<th width='10%'>".strtoupper(trim($rows[3]))."</th>";
								$table .= "<th width='14%'>".strtoupper(trim($rows[4]))."</th>";
								$table .= "<th width='19%'>".strtoupper(trim($rows[5]))."</th>";
								$table .= "<th width='14%'>".strtoupper(trim($rows[6]))."</th>";
								$table .= "</tr></thead>";
								$table .= "<tbody>";
								$first = false;
							}else{

								// VALIDATION
								// ===============================================
								$adakah_di_db = PartNumber::select('id')
									->where('item_name', trim($rows[1]))
									->where('short_desc', trim($rows[2]))
									->where('man_code', trim($rows[3]))
									->where('part_number', trim($rows[5]))
									->where('po_text', trim($rows[6]))
									->first();

								if(count($adakah_di_db)>0){
									$cek_already_in_db[] = 0;
									$warn_already_in_db[] = '<b>ITEM NAME:</b> '.strtoupper(trim($rows[1])).' WITH <b>SHORT DESCRIPTION:</b> '.strtoupper(trim($rows[2])).' WITH <b>MAN CODE:</b> '.strtoupper(trim($rows[3])).' WITH <b>PART NUMBER: </b> '.strtoupper(trim($rows[5])).' WITH <b>PO TEXT: </b> '.strtoupper(trim($rows[6]));
								}else{
									$cek_already_in_db[] = 1;
								}

								$check_duplicate_row[] .= '<b>ITEM NAME:</b> '.strtoupper(trim($rows[1])).' WITH <b>SHORT DESCRIPTION:</b> '.strtoupper(trim($rows[2])).' WITH <b>MAN CODE:</b> '.strtoupper(trim($rows[3])).' WITH <b>PART NUMBER: </b> '.strtoupper(trim($rows[5])).' WITH <b>PO TEXT: </b> '.strtoupper(trim($rows[6]));

								if(strlen(trim($rows[0])) > 5){
									$max_str[] = 0;
									$warn_max_str[] = '<b>INC</b> "'.strtoupper(trim($rows[0])).'" LENGTH MAY NOT BE GREATER THAN 5';
								}else{
									$max_str[] = 1;
								}

								if(strlen(trim($rows[1])) > 255){
									$max_str[] = 0;
									$warn_max_str[] = '<b>ITEM NAME</b> "'.strtoupper(trim($rows[1])).'" LENGTH MAY NOT BE GREATHER THAN 255';
								}else{
									$max_str[] = 1;
								}

								if(strlen(trim($rows[2])) > 255){
									$max_str[] = 0;
									$warn_max_str[] = '<b>SHORT DESCRIPTION</b> "'.strtoupper(trim($rows[2])).'" LENGTH MAY NOT BE GREATHER THAN 255';
								}else{
									$max_str[] = 1;
								}

								if(strlen(trim($rows[3])) > 10){
									$max_str[] = 0;
									$warn_max_str[] = '<b>MAN CODE</b> "'.strtoupper(trim($rows[3])).'" LENGTH MAY NOT BE GREATHER THAN 10';
								}else{
									$max_str[] = 1;
								}

								if(strlen(trim($rows[4])) > 255){
									$max_str[] = 0;
									$warn_max_str[] = '<b>MANUFACTURER NAME</b> "'.strtoupper(trim($rows[4])).'" LENGTH MAY NOT BE GREATHER THAN 255';
								}else{
									$max_str[] = 1;
								}

								if(strlen(trim($rows[5])) > 255){
									$max_str[] = 0;
									$warn_max_str[] = '<b>PART NUMBER</b> "'.strtoupper(trim($rows[5])).'" LENGTH MAY NOT BE GREATHER THAN 255';
								}else{
									$max_str[] = 1;
								}

								$check_empty_item_name[] .= $rows[1];
								$check_empty_short_desc[] .= $rows[2];
								$check_empty_man_code[] .= $rows[3];
								$check_empty_part_number[] .= $rows[5];
								$check_empty_po_text[] .= $rows[6];

								// TABLE
								// ====================================================
								$table .= "<tr><td>".$urut++."</td>";
								$table .= "<td>".strtoupper(trim($rows[0]))."</td>";
								$table .= "<td>".strtoupper(trim($rows[1]))."</td>";
								$table .= "<td>".strtoupper(trim($rows[2]))."</td>";
								$table .= "<td>".strtoupper(trim($rows[3]))."</td>";
								$table .= "<td>".strtoupper(trim($rows[4]))."</td>";
								$table .= "<td>".strtoupper(trim($rows[5]))."</td>";
								$table .= "<td>".strtoupper(trim($rows[6]))."</td></tr>";

								$data_counter[] = 1;
							}							
						}						
					}					
					
				}
				$table .= "</tbody></table>";

				$row_check_duplicate = array_count_values($check_duplicate_row);
				$chek_dupl_row = array();
				foreach ($row_check_duplicate as $key => $value) {
					if($value > 1) {
						$chek_dupl_row[] = 0;
					}else{
						$chek_dupl_row[] = 1;
					}
				}

				$empty_item_name = array();
				foreach ($check_empty_item_name as $value) {
					 if(is_null($value) || $value == ''){
					 	$empty_item_name[] = 0;
					 }else{
					 	$empty_item_name[] = 1;
					 }
				}

				$empty_short_desc = array();
				foreach ($check_empty_short_desc as $value) {
					 if(is_null($value) || $value == ''){
					 	$empty_short_desc[] = 0;
					 }else{
					 	$empty_short_desc[] = 1;
					 }
				}

				$empty_man_code = array();
				foreach ($check_empty_man_code as $value) {
					 if(is_null($value) || $value == ''){
					 	$empty_man_code[] = 0;
					 }else{
					 	$empty_man_code[] = 1;
					 }
				}

				$empty_part_number = array();
				foreach ($check_empty_part_number as $value) {
					 if(is_null($value) || $value == ''){
					 	$empty_part_number[] = 0;
					 }else{
					 	$empty_part_number[] = 1;
					 }
				}

				$empty_po_text = array();
				foreach ($check_empty_po_text as $value) {
					 if(is_null($value) || $value == ''){
					 	$empty_po_text[] = 0;
					 }else{
					 	$empty_po_text[] = 1;
					 }
				}

				if(count($data_counter) < 1){
					echo '<span class="text-danger">YOU TRY TO UPLOAD SPREADSHEET WITH EMPTY INC DATA</span>';
				}elseif(
					in_array(0, $cek_already_in_db) ||
					in_array(0, $chek_dupl_row) ||
					in_array(0, $max_str) || 
					in_array(0, $empty_item_name) ||
					in_array(0, $empty_short_desc) ||
					in_array(0, $empty_man_code) ||
					in_array(0, $empty_part_number) ||
					in_array(0, $empty_po_text)
				){
					
					echo "<span><strong>PLEASE CHECK YOUR PART NUMBER SPREADSHEET</strong></span>";
					
					$already = '';
					if(in_array(0, $cek_already_in_db)){
						$validasi = "<br><br><strong class='text-danger'><u>ALREADY IN DATABASE:</u> </strong>";
						foreach ($warn_already_in_db as $value) {
							$validasi .= '<br/>'.$value;
						}
						$already = $validasi;
					}

					$max_length = '';
					if(in_array(0, $max_str)){
						$validasi = "<br><br><strong class='text-danger'><u>CHARACTER LENGTH MORE THAN ALLOWED:</u> </strong>";
						foreach ($warn_max_str as $value) {
							$validasi .= '<br/>'.$value;
						}
						$max_length = $validasi;
					}

					$dupl_row_ = '';
					$dupl_row = '';
					if(in_array(0, $chek_dupl_row)){
						$validasi = '';
						
						$check_duplicate_row_again = array_count_values($check_duplicate_row);

						foreach($check_duplicate_row_again as $key=>$value){
						    if(is_null($key) || $key == '')
						        unset($check_duplicate_row_again[$key]);
						}

						$ada = array();
						foreach ($check_duplicate_row_again as $key => $value) {
							if($value > 1) {
								$validasi .= '<br/>'.$key;
								$ada[] = 1;
							}else{
								$ada[] = 0;
							}
						}
						$dupl_row_ = $validasi;

						if(in_array(1, $ada)){
							$dupl_row  = "<br><br><strong class='text-danger'><u>DUPLICATE :</u></strong> ";
							$dupl_row .= $dupl_row_;
						}else{
							$dupl_row = '';
						}
					}

					$item_name_empty = '';
					if(in_array(0, $empty_item_name)){
						$validasi = "<br><br><strong class='text-danger'><u>EMPTY ITEM NAME:</u> </strong> ";
						$i = 3;
						foreach ($check_empty_item_name as $value) {
							 if(is_null($value) || $value == ''){
							 	$validasi .= '<br/>ON LINE <b>#'.$i++.'</b> IN YOUR SPREADSHEET.';
							 }else{
							 	$i++;
							 }
						}
						$item_name_empty = $validasi;
					}

					$short_desc_empty = '';
					if(in_array(0, $empty_short_desc)){
						$validasi = "<br><br><strong class='text-danger'><u>EMPTY SHORT DESCRIPTION:</u> </strong> ";
						$i = 3;
						foreach ($check_empty_short_desc as $value) {
							 if(is_null($value) || $value == ''){
							 	$validasi .= '<br/>ON LINE <b>#'.$i++.'</b> IN YOUR SPREADSHEET.';
							 }else{
							 	$i++;
							 }
						}
						$short_desc_empty = $validasi;
					}

					$man_code_empty = '';
					if(in_array(0, $empty_man_code)){
						$validasi = "<br><br><strong class='text-danger'><u>EMPTY MAN CODE:</u> </strong> ";
						$i = 3;
						foreach ($check_empty_man_code as $value) {
							 if(is_null($value) || $value == ''){
							 	$validasi .= '<br/>ON LINE <b>#'.$i++.'</b> IN YOUR SPREADSHEET.';
							 }else{
							 	$i++;
							 }
						}
						$man_code_empty = $validasi;
					}

					$part_number_empty = '';
					if(in_array(0, $empty_part_number)){
						$validasi = "<br><br><strong class='text-danger'><u>EMPTY PART NUMBER:</u> </strong> ";
						$i = 3;
						foreach ($check_empty_part_number as $value) {
							 if(is_null($value) || $value == ''){
							 	$validasi .= '<br/>ON LINE <b>#'.$i++.'</b> IN YOUR SPREADSHEET.';
							 }else{
							 	$i++;
							 }
						}
						$part_number_empty = $validasi;
					}

					$po_text_empty = '';
					if(in_array(0, $empty_po_text)){
						$validasi = "<br><br><strong class='text-danger'><u>EMPTY PO TEXT:</u> </strong> ";
						$i = 3;
						foreach ($check_empty_po_text as $value) {
							 if(is_null($value) || $value == ''){
							 	$validasi .= '<br/>ON LINE <b>#'.$i++.'</b> IN YOUR SPREADSHEET.';
							 }else{
							 	$i++;
							 }
						}
						$po_text_empty = $validasi;
					}	

					echo $already;
					echo $max_length;
					echo $dupl_row;
					echo $item_name_empty;
					echo $short_desc_empty;
					echo $man_code_empty;
					echo $part_number_empty;
					echo $po_text_empty;
				}else{
					echo "<span id='data_counter'>".number_format(count($data_counter))."</span>";
					echo "<input type='button' class='import_to_db import_part_number btn btn-sm btn-primary' value='IMPORT PART NUMBER DATA'>";
					echo $table;
				}
				echo "</div>";
			}
		}else{
			echo 'Wrong Spreadsheet.';
		}
	}

	public function importPartNumber($file){
		if (\Gate::denies('importImport', PartNumber::class)) {
            abort(404);
        }
        
    	$reader = $this->readSpreadSheet($file);
    	foreach ($reader->getSheetIterator() as $sheet) {			
			$rows = $sheet->getRowIterator();
			\DB::transaction(function () use($rows){
				$i = 1;
				foreach ($rows as $cel) {
					$key = $i++;
					if($key > 2){

						$item_code 		= 1;
						$inc 			= strtoupper(trim($cel[0]));
						$item_name 		= strtoupper(trim($cel[1]));
						$short_desc 	= strtoupper(trim($cel[2]));
						$man_code 		= strtoupper(trim($cel[3]));
						$man_name 		= strtoupper(trim($cel[4]));
						$part_number 	= strtoupper(trim($cel[5]));
						$po_text 		= strtoupper(trim($cel[6]));
						$date 			= \Carbon\Carbon::now();

						$data = [
							'item_code' 	=> $item_code,
							'inc' 			=> $inc,
							'item_name' 	=> $item_name,
							'short_desc'	=> $short_desc,
							'man_code' 		=> $man_code,
							'man_name'		=> $man_name,
							'part_number' 	=> $part_number,
							'po_text'		=> $po_text,
			        		'created_at'	=> $date,
	     					'updated_at'	=> $date
						];

						PartNumber::create($data);		
					}
				}				
			});
		};
    }
}
