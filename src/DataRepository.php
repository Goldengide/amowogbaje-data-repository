<?php
namespace Amowogbaje\DatabaseRepository;
use DB;
use Illuminate\Support\Facades\File;
/**
* 
*/
class DataRepository
{
        private $tableName;
        private $jsonFile;
        private $jsonDumpFolder;
        public $tableFromWhichDataIsToBeDownloaded;
        public $getContentJustFromWhichColumn = null;
        public $dataToDownload;
        
       
        public function __construct()
        {
        }

       
        public function setTableName($tableName) {
                $this->tableName = $tableName;
                $jsonDumpFolder = public_path('..\database\seeds\dumps\json_dumps');
                $jsonFile = public_path('../database/seeds/dumps/json_dumps/'.$tableName.'.json');
                $this->jsonFile = $jsonFile;
                $this->jsonDumpFolder = $jsonDumpFolder;
                
        }

        public function setTableFromWhichDataIsToBeDownloaded($tableName) {
            $this->tableFromWhichDataIsToBeDownloaded = $tableName;
        }

        public function downloadTableContents() {
                $this->createJsonDumpFolder();
                $data = DB::table($this->tableName)->get();
                $jsonData = json_encode($data, JSON_PRETTY_PRINT);

                if($handle = fopen($this->jsonFile, 'w')) {

                        (fwrite($handle, $jsonData));

                }
                return true;
        }

        public function isTableUpdated() {
                if(file_exists($this->jsonFile)) {
                        $previousData = File::get($this->jsonFile);
                        $previousDataCount = count(json_decode($previousData, TRUE));
                        $currentDataCount = DB::table($this->tableName)->count();
                        if($previousDataCount < $currentDataCount) {
                                // return 1111;
                                return true;
                        }
                        else {
                                // return 1000;
                                return false;
                        }
                }
                else {
                        // return 0000;
                        return false;
                }

        }

        public function isBackupDataYetToBeCreated() {
                if(!file_exists($this->jsonFile)) {
                        return true;
                }
                else {
                        return false;
                }
        }

        public function backup() {

                if($this->isTableUpdated()) {

                        $this->downloadTableContents();
                        
                }
                else if($this->isBackupDataYetToBeCreated()) {

                        $this->downloadTableContents();

                }
        }

        public function dataTableSeederFunction() {
            if(!$this->isBackupDataYetToBeCreated()) {
                $jsonData = File::get($this->jsonFile);
                $jsonArray = json_decode($jsonData, TRUE);
                if(count($jsonArray) > 0) {
                        DB::table($this->tableName)->insert($jsonArray);
                }
            }
            else {
                return false;
            }
        }

        public function dataTableSeedAllTableFunction() {
                $tables = $this->getAllTablesInADB();
                foreach ($tables as $table) {
                        $jsonData = File::get($this->jsonFile);
                        $jsonArray = json_decode($jsonData, TRUE);
                        if(count($jsonArray) > 0) {
                                DB::table($this->tableName)->insert($jsonArray);
                        }
                }
        }

        public function createJsonDumpFolder(){
                $this->createFolderIfNotExist($this->jsonDumpFolder);
        }

        public function createFolderIfNotExist($folderPath) {
                $segment = explode("\\", $folderPath);
                foreach ($segment as $path) {
                        if (!is_dir($path)) {
                                mkdir($path, $mode=0777, $recursive = false);
                                chdir($path);
                        }
                        elseif(is_dir($path)) {
                                chdir($path);
                        }
                }
        }

        //The two consequtive functions have not been tested
        public function getAllTablesInADB() {
                $listOfTablesInDBArray = DB::select('SHOW TABLES');
                $json = json_encode($listOfTablesInDBArray, JSON_PRETTY_PRINT);
                $db = env('DB_DATABASE');
                $object = "Tables_in_".$db;
                $tableListArray = array();
                foreach ($listOfTablesInDBArray as $tableObject) {
                        if($tableObject->$object != 'migrations') {
                                $tableListArray[] = $tableObject->$object; 
                        }
                }
                return $tableListArray;
        }

        public function backupAllDatas() {
                $tableNameArray = $this->getAllTablesInADB();
                $backupData = new DataRepository();
                foreach ($tableNameArray as $tableName) {
                        $backupData->setTableName($tableName);
                        $backupData->backup();
                }
        }

       /* public function downloadAllContents() {
            $table = $this->tableFromWhichDataIsToBeDownloaded;
            $data = DB::table("$table")->get();
            return $data; 
        }


        public function filter($startDate, $endDate) {
        }*/

        public function downloadAllContents() {
            $table = $this->tableFromWhichDataIsToBeDownloaded;
            $column = $this->getContentJustFromWhichColumn;
            $dataTable = DB::table("$table");
            if (!isset($column)) {
                $data = $dataTable->get();
                $data->jampack = false;
            }
            else {
                $data = $dataTable->orderBy($column, 'desc')->pluck($column);
                $data->jampack = true;
            }

            return $data; 

        }


        public function toCSV(){

            $data = $this->dataToDownload;
            $jsonArray = json_decode(json_encode($data), TRUE);
            $cSVArray = array(); 
                foreach ($jsonArray as $number => $data) {

                    $cSVArray[] = implode(',', $data); 

                }

            $cSVData = implode("\n", $cSVArray);

            return $cSVData;
            
        }

        public function toJson(){
            $dataToDownload = $this->dataToDownload;
            $data = json_encode($dataToDownload, JSON_PRETTY_PRINT);
            return $data;
        }

        public function toTSV(){
            $data = $this->dataToDownload;
            $jsonArray = json_decode(json_encode($data), TRUE);
            $tSVArray = array(); 
                foreach ($jsonArray as $number => $data) {

                    $tSVArray[] = implode("\t", $data); 

                }

            $tSVData = implode("\n", $tSVArray);

            return $tSVData;

        }
        public function toTXT(){
            $data = $this->dataToDownload;
            $jsonArray = json_decode(json_encode($data), TRUE);
            $txtArray = array(); 
                foreach ($jsonArray as $number => $data) {

                    $txtArray[] = implode(' ', $data); 

                }

            $txtData = implode("\n", $txtArray);

            return $txtData;
        }


       

        public function getColumnsInATable($table) {}

        public function downloadData($type){
            if ($type == 'csv') {
                $data = $this->toCSV();
            }
            if ($type == 'json') {
                $data = $this->toJson();
            }
            if ($type == 'tsv') {
                $data = $this->toTSV();
            }
            if ($type == 'txt') {
                $data = $this->toTXT();
            }

            // else{
            //     $format = 'text/'.$type;
            // }
            $format = 'text/'.$type;
            // return $data;

            return response($data)
                        ->header('Content-Type', "$format")
                        ->header('Content-type', 'application/force-download')
                        ->header('X-Header-One', 'Header Value');
        }


        public function sequenceNumber($no) {
            $sort = str_split($no);
            $lastPartOfNo = $sort[count($sort) -1];
            if( $lastPartOfNo == 1 ) {
                return $no. "<sup>st</sup>";
            }
            elseif($lastPartOfNo == 2) {
                return $no. "<sup>nd</sup>";
            }
            elseif($lastPartOfNo == 3) {
                return $no. "<sup>rd</sup>";
            }
            else {
                return $no. "<sup>th</sup>";
            }
        }

        public function replaceDelimeter($string, $initialDelimeter, $finalDelimeter) {
            $break = explode($initialDelimeter, $string);
            $finalString = implode($finalDelimeter, $break);
            return $finalString;
        }

}