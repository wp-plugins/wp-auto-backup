<?php
include_once (plugin_dir_path(__FILE__)."Data.php");
class File
{
    private $filename;
    private $openMode;
    private $file_Handle;
    public function __construct($filename,$openMode)
    {
    $this->file_Handle=fopen (  $filename , $openMode);
	return $this->file_Handle;
    }
    public function Read_File()
    {	global $size_file_read;
    $length  =$size_file_read;
	$fread_status=fread (  $this->file_Handle ,   $length  );
	return $fread_status;	
    }
    public function Write_File($string)
    {
    $length  =strlen($string);
	$fwrite_status=fwrite (  $this->file_Handle , $string ,  $length  );
	return $fwrite_status;	
    }
    public function Split_Buffer_File($delimiter)
    {
	 $Main_Buffer=$this->	Read_File();
	 $final_chunks=explode($delimiter,$Main_Buffer);
	return $final_chunks;	
    }
    public function Close_File()
    {
	$fclose=fclose (  $this->file_Handle   );
	return $fclose;	
    }
    public function __destruct()
    {
	$this->Close_File();
    }
}