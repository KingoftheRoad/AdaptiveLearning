<?php

namespace App\Http\Repositories;

use Illuminate\Http\Request;
use App\Constants\DbConstant as cn;
use App\Traits\Common;
use App\Traits\ResponseFormat;
use Exception;
use Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class CSVFileRepository
{
    use Common, ResponseFormat;

    public function GetCSVfileData(Request $request, $InputFileName, $FilePath){
        $response = array();
        ini_set('max_execution_time', 1800); // 30 Minutes

        $file = $request->file($InputFileName);

        // File Details 
        $filename = $file->getClientOriginalName();
        $fileName_without_ext = \pathinfo($filename, PATHINFO_FILENAME);
        $fileName_with_ext = \pathinfo($filename, PATHINFO_EXTENSION);      
        $filename = $fileName_without_ext.time().'.'.$fileName_with_ext;

        $extension = $file->getClientOriginalExtension();
        $tempPath = $file->getRealPath();
        $fileSize = $file->getSize();
        $mimeType = $file->getMimeType();

        // Valid File Extensions
        $valid_extension = array("csv");

        // 2MB in Bytes
        $maxFileSize = 2097152;

        // Check file extension
        if(in_array(strtolower($extension),$valid_extension)){
            // Check file size
            if($fileSize <= $maxFileSize){
                // File upload location
                $location = 'uploads/import_schools';
                
                // Upload file
                $file->move(public_path($location), $filename);

                // Import CSV to Database
                $FilePath = public_path($location."/".$filename);
                                                            
                // Reading file
                $file = fopen($FilePath,"r");
                $CSVData = array();
                $i = 0;
                
                while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                    $num = count($filedata );
                    // Skip first row (Remove below comment if you want to skip the first row)
                    if($i != 0){
                        for ($c=0; $c < $num; $c++) {
                            $CSVData[$i][] = $filedata [$c];
                        }   
                    }
                    $i++;
                }
                // close the file
                fclose($file);
                if(isset($CSVData) && !empty($CSVData)){
                    $response['status'] = true;
                    $response['CSVData'] = $CSVData ?? [];
                }
            }else{
                $response['status'] = false;
                $response['error'] = 'File size is very large';
            }
        }else{
            $response['status'] = false;
            $response['error'] = 'Allowed only CSV file';
        }

        return $response;
    }
}