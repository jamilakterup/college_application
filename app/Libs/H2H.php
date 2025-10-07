<?php
/**
 * @author Rajib, cse,Ru.
 *
 * @category file
 * @copyright 2016
 */

?>


<?php
   

class H2H {
    public static $file;  // it is the name of the file as a array $file=$_FILES['name']
    public static $extension;  //array which extension file we want to upload
    public static $directory;  //in which directory we want to uploaded. 
    public static $new_file_name='ok';  // what is the modified name of the file
    public static $table_name;     //table name for in which table csv file will be uploaded
    public static $attribute=array();       //table attributr name for csv upload
	  public static $show='no';
    public static $comma="";
    public static $line="";
    public static $file_size='';
    public static $extra_field=array();
    public static $extra_value=array();
    function __construct(){
        
    }
    

    
    public static function getExtension($file_name) {
         $i = strrpos($file_name,".");
         if (!$i) { return ""; }
         $l = strlen($file_name) - $i;
         $ext = substr($file_name,$i+1,$l);
         
         return $ext;
 }
 
 
    private static function chek_extension($ext){
        return in_array($ext,self::$extension);
        
        
    }



// public static function csv_upload(){
//     global $database;
//     self::$extension=array('csv');
    
   
//    $copy=self::upload_file();
 
   
   
//    if($copy){
//     $row=implode(",",self::$attribute);
//  $upload_path= ROOT_PATH.DS.ROOT_FOLDER.DS.self::$directory.self::$new_file_name.'.csv';
//   $table=self::$table_name; 
  

//   $set_string='';
//   if(!empty(self::$extra_field)){
//  	$count_row=count(self::$extra_field);
// 	$column=self::$extra_field;
// 	$column_value=self::$extra_value;
// 	$set_string="SET ";
	
// 	for($j=0;$j<$count_row;$j++){
// 		$set_string.="{$column[$j]}='{$column_value[$j]}',";
// 	}
		
//   }
  
// 	$set_string=rtrim($set_string,",");
	
  
  
// $query="LOAD DATA LOCAL INFILE '$upload_path'
//   INTO TABLE $table 
//   FIELDS TERMINATED BY ',' 
//   LINES TERMINATED BY '\n' 
//   ($row) {$set_string};";
  
//   $database->query($query);
//   echo "Insert Successfully";
//   }
  
//   else{
//   	 echo " Sorry Couldn't upload";
//   }
  
  
    
// }


	public static function make_head($header=array()){
		self::$comma="";
		foreach($header as $head){
			self::make_line($head);
		}
		self::end_line();
	}
	
	
	public static function make_line($string){
		 self::$line .= self::$comma . '"' . str_replace('"', '""', $string) . '"';
         self::$comma = ",";
		}
		
	public static function end_line(){
		 self::$line .="\n";
		}

	public static function make_csv($file_name){
		$fp = fopen($file_name, 'w');
 		fputs($fp,self::$line);
 		fclose($fp);
	}
	

}


?>