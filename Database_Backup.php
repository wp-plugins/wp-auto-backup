<?php
include_once (plugin_dir_path(__FILE__)."File.class.php");
include_once (plugin_dir_path(__FILE__)."Store.class.php");
include_once (plugin_dir_path(__FILE__)."General_functions.php");
include_once (plugin_dir_path(__FILE__)."Data.php");
class dj_operation
{
public function Build_CREATE_DATABASE_Query($database_name)
{
	global $default_character,$collate,$Line_Return,$Queries_Separators;
	$create_table_query="CREATE DATABASE IF NOT EXISTS `{$database_name}` DEFAULT CHARACTER SET {$default_character} COLLATE {$collate}{$Queries_Separators}{$Line_Return} USE `{$database_name}`{$Queries_Separators}";//{$Line_Return}
return $create_table_query;
}
public function Build_CREATE_TABLE_Query($table,$database)
{
	global $Line_Return,$Queries_Separators;
$store=new dj_store(); 
$Sql_Operation = "SELECT ENGINE,TABLE_COLLATION,AUTO_INCREMENT,TABLE_COMMENT FROM information_schema.TABLES WHERE TABLE_NAME='{$table}' AND TABLE_SCHEMA='{$database}'";
$Data=$store->Sql_Operation($Sql_Operation);
$engine=$Data[0]['ENGINE'];
$charset=$Data[0]['TABLE_COLLATION'];
$charset=explode("_",$charset);
$charset=$charset[0];
$auto_increment=$Data[0]['AUTO_INCREMENT'];
    if($auto_increment===NULL OR $auto_increment=='1')
    {
    $auto_increment=""	;
    }
    else
    {
    $auto_increment=" AUTO_INCREMENT={$auto_increment}"	;
	$Not_Empty_Table=TRUE;
    }
$Table_Comment=$Data[0]['TABLE_COMMENT'];
$Table_Comment=str_replace("'","''",$Table_Comment);
    if($Table_Comment=='')
    {
    $Table_Comment=""	;
    }
    else
    {
    $Table_Comment=" COMMENT='{$Table_Comment}'"	;
    }
$create_table_query_arguments=$this->Create_Table_Query_Arguments($table,$database);	
$create_table_query="CREATE TABLE IF NOT EXISTS `{$database}`.`{$table}` ({$Line_Return}";
$create_table_query.=substr($create_table_query_arguments, 1);
$create_table_query.=") ENGINE={$engine} DEFAULT CHARSET={$charset}{$auto_increment}{$Table_Comment}{$Queries_Separators}{$Line_Return}";
$create_table_query.="TRUNCATE table `{$database}`.`{$table}`{$Queries_Separators}{$Line_Return}";
	if(isset($Not_Empty_Table))
    {
    $Insert_Into_Text=$this->Build_INSERT_INTO_Query($table,$database);
    $create_table_query.=$Insert_Into_Text;
    $create_table_query.=$Queries_Separators;
	}
return $create_table_query;	
}
public function Create_Table_Query_Arguments($tables,$databases)
{
	global $Line_Return,$Queries_Separators;
$store=new dj_store(); 
$list_tables_fields=$store->List_Tables_Fields($tables,$databases);
    $table_query_arguments='';
    for($i=0;$i<sizeof($list_tables_fields);$i++)
    {
	$Field=  $list_tables_fields[$i]['Field'];
	$Field_Text=$Field;
	$table_query_arguments.=",`{$Field_Text}` ";
	$Sql_Operation = "SELECT COLUMN_COMMENT FROM information_schema.COLUMNS WHERE COLUMN_NAME='{$Field}' AND TABLE_NAME='{$tables}' AND TABLE_SCHEMA='{$databases}'";
	$Data=$store->Sql_Operation($Sql_Operation);
	$Column_Comment=$Data[0]['COLUMN_COMMENT'];
	$Column_Comment=str_replace("'","''",$Column_Comment);
    	if($Column_Comment=='')
		{
		$Column_Comment="";	
		}
		else
		{
		$Column_Comment=" COMMENT '{$Column_Comment}'";	
		}
	$Type=  $list_tables_fields[$i]['Type'];
	$Type_Text=$Type;
	$table_query_arguments.="{$Type_Text} ";
	$Null=  $list_tables_fields[$i]['Null'];
	    if($Null=='NO')
		{
		$Null_Text='NOT NULL';
		}
        elseif( $Null=='YES')
		{
		$Null_Text='DEFAULT NULL';
		}
		else
		{
		$Null_Text='';
		}
	$table_query_arguments.="{$Null_Text}";
	$Key=  $list_tables_fields[$i]['Key'];
        switch($Key)
	    {
		case "PRI":
        $PRI_Key=TRUE;
		$PRI_Field=$Field;
        break;		
		case "UNI":
        $UNI_Key=TRUE;
		$UNI_Field=$Field;
        break;		
		case "MUL":
        $MUL_Key=TRUE;
		$MUL_Field=$Field;
        break;		
	    }
	$Default=  $list_tables_fields[$i]['Default'];
	    if($Default!==null)
		{
		$Default_Text=$Default;
		$table_query_arguments.=" DEFAULT '{$Default_Text}'";
		}
	$Extra=  $list_tables_fields[$i]['Extra'];
		if($Extra=="auto_increment")
		{
		$Extra_Text	=" AUTO_INCREMENT";
		$Auto_Increment_Key=TRUE;
		$Auto_Increment_Field=$Field;
		}
		else
		{	$Extra_Text	="";
    	}
	$table_query_arguments.="{$Extra_Text}{$Column_Comment}{$Line_Return}";//
    }
	if(isset($Auto_Increment_Key))
	{
	$table_query_arguments.=", PRIMARY KEY (`{$Auto_Increment_Field}`){$Line_Return}"	;
	}
	if(isset($PRI_Key) AND !isset($Auto_Increment_Key))
	{
	$table_query_arguments.=",PRIMARY KEY (`{$PRI_Field}`){$Line_Return}"	;//
	}
	if(isset($UNI_Key))
	{
	$table_query_arguments.=",UNIQUE KEY `{$UNI_Field}` (`{$UNI_Field}`){$Line_Return}"	;
	}
	if(isset($MUL_Key))
	{
	$table_query_arguments.=",KEY `{$MUL_Field}` (`{$MUL_Field}`){$Line_Return}"	;
	}
return $table_query_arguments;	
}
public function Build_INSERT_INTO_Query($table,$database)
{
	global $Line_Return,$Queries_Separators;
$store=new dj_store(); 

$List_Tables_Fields=	$store->List_Tables_Fields($table,$database);	
$Insert_Into_Text="INSERT INTO `{$database}`.`{$table}` (`";
    for($i=0; $i<sizeof($List_Tables_Fields); $i++)
	{
	$List_Tables[$i]=$List_Tables_Fields[$i]['Field'];
	}
$Insert_Into_Text.=implode("`, `",$List_Tables);
$Insert_Into_Text.="`) ";
$Insert_Into_Text.="VALUES {$Line_Return}";
$Values=$this->Build_Insert_Into_VALUES($table,$database);
$Insert_Into_Text.=$Values;
return $Insert_Into_Text;	
}
public function Build_Insert_Into_VALUES($table,$database)
{
	global $Line_Return,$Queries_Separators;
	$store=new dj_store(); 
$Results=$store->Sql_Operation("SELECT * FROM {$database}.{$table}")	;
$Values=""  ;
    for($i=0;$i<sizeof($Results);$i++)
    {
	$Values.="("	;
        foreach($Results[$i] as $vals)
		{
		$vals=str_replace("'","''",$vals);
		$Values.="'{$vals}', "	;
		}
	$Values=substr($Values, 0, -2); 
	$Values.="),{$Line_Return}"	;
	}
	$Values=substr($Values, 0, -2); 
return $Values;	
}
public function Build_Create_Database_BUFFER($database_name)
{global $Line_Return,$Queries_Separators;
$Results=$this->Build_CREATE_DATABASE_Query($database_name).$Line_Return	;
return $Results;	

}
public function Build_Create_Table_BUFFER(array $table_list,$database)
{  	global $Line_Return,$Queries_Separators;
$CREATE_TABLE_Query='';
    for($i=0;$i<sizeof($table_list);$i++)
	{
		$Tab=$table_list[$i];
	$CREATE_TABLE_Query.= $this->Build_CREATE_TABLE_Query($Tab,$database).$Line_Return	;
	}
	return $CREATE_TABLE_Query;
}
public function Build_All_Buffers()
{
	$store=new dj_store(); 
$Databases=$store->List_Databases();
$Buffer='';
    if(!empty($Databases))
	{
	  for($i=0;$i<sizeof($Databases);$i++)
	  {
	  $table_list=$store->List_Tables($Databases[$i]);
	  $Buffer.=$this->Build_Create_Database_BUFFER($Databases[$i]);
	  
	    if(!empty($table_list))
	    {
	    $Buffer.=$this->Build_Create_Table_BUFFER($table_list,$Databases[$i]);
	    }
	  }
	}
return $Buffer;
}
public function add_directory(&$item1, $keyk, $prefixk)
{
    $item1 = "$prefixk/$item1";
}
public function Register_Queries_File($File_Queries) 
{
	global $Dir_File,$DataBases_Backups_Zip;//
	global $Base_name,$BackUp_Table,$wp_content_zip;
$Buffer=$this->Build_All_Buffers();
if(!empty($Buffer))
{
	$file=new File($File_Queries,'w+');
    $write_stat=$file->Write_File($Buffer,sizeof($Buffer));
	$File_Queries_name=basename($File_Queries);
	$File_Queries_zip=$DataBases_Backups_Zip;
	$New_zip_file=$Dir_File.'/'.$File_Queries_zip;
	$op_stat=fopen($New_zip_file,'w+');
	fclose($op_stat);
	$zip = new ZipArchive;
	$zip->open($New_zip_file);
	$zip->addFile($File_Queries, $File_Queries_name);
	$zip->close();
	unset($zip);
    $store=new dj_store(); 
	$email=$store->Sql_Operation("SELECT email FROM {$Base_name}.{$BackUp_Table}");
	$email=$email[0]['email'];
	
	if(!empty($email))
	{
	$message_PLAIN='Databases backup on:'.date('Y:m:d H:i:s',time());
	$message_HTML='<h2>Databases backup on:</h2>'.date('Y:m:d H:i:s',time());
	
	$attachement_buffer=file_get_contents($New_zip_file);
	$email_titl="DataBase backup";
    $email_sent= send_mail_attachment($New_zip_file, $email, $email_titl, $message_PLAIN,$File_Queries_zip)  ;	// File_Queries_name
	}
	$non_backable=array(
	get_theme_root().'/twentyfifteen',
	get_theme_root().'/twentyfourteen',
	get_theme_root().'/twentythirteen',
	/*
	get_theme_root().'/twentyten',
	get_theme_root().'/twentytwelve',
	get_theme_root().'/twentyeleven',
	*/
	plugin_dir_path(__FILE__).'WordPress_BackUps');
	$main_dir=WP_CONTENT_DIR;
	$paths_and_files=array($main_dir);
	$elements_size=1;
	for($i=sizeof($paths_and_files)-$elements_size;$i<sizeof($paths_and_files);$i++)
	{
		$file_dir=$paths_and_files[$i];
		if(is_dir($file_dir) AND !in_array ($file_dir , $non_backable ))
		{
    	$elements=scandir(	$paths_and_files[$i]);
		$elements=array_slice($elements,2,sizeof($elements));
		array_walk($elements,  array($this,'add_directory'), $paths_and_files[$i]);
		$elements_size=sizeof($elements);
		$paths_and_files=array_merge($paths_and_files,$elements);
		$folders_found=TRUE;
		}
	}
	
	$New_zip_file=$Dir_File.'/'.$wp_content_zip;
	$op_stat=fopen($New_zip_file,'w+');
	fclose($op_stat);
	$zip = new ZipArchive;
	$zip->open($New_zip_file);
    foreach($paths_and_files as $paths)
	{
        if(is_file($paths))
        {
		$zip_paths=str_replace($main_dir,basename($main_dir),$paths);
        $zip->addFile($paths,$zip_paths);
        }
	}
	unset($zip_paths);
	$zip->close();

	if(!empty($email))
	{
	$message_PLAIN='WP_CONTENT backup on:'.date('Y:m:d H:i:s',time());
	$message_HTML='<h2>WP_CONTENT backup on:</h2>'.date('Y:m:d H:i:s',time());
	$attachement_buffer=file_get_contents($New_zip_file);
	$email_titl="wp content backup";
    $email_sent= send_mail_attachment($New_zip_file, $email, $email_titl, $message_PLAIN,$wp_content_zip)  ;	
	$_SESSION['dj_email_sent_stat']=$email_sent;
	}

}
return $email_sent;
}
public function Update_Database_From_File($File_Queries) 
{
	global $Queries_Separators;
$store=new dj_store(); 
$Read_File=    new File($File_Queries,'r');
if(!empty($Read_File))
{	
  $queries_chunks=$Read_File->Split_Buffer_File($Queries_Separators);
  $queries_chunks=array_slice($queries_chunks,0,sizeof($queries_chunks)-1);
  if(!empty($queries_chunks))
  {
    for($i=0;$i<sizeof($queries_chunks);$i++)
    {
	  if(!empty($queries_chunks[$i]))
      {
      $Stat=$store->Sql_Operation_WR($queries_chunks[$i]);
	  $results= "UPDATE_SUCCESS:TRUE";
		if(!$Stat)
		{
	    $results="INVALID_SQL_QUERIES:FALSE";
		break;
		}
	  }
	  else
	  {
	  $results="EMPTY_SQL_QUERY";
	  break;
	  }
    }
  }
  else
  {
  $results="EMPTY_FILE";
  }
}
else
{
$results="EMPTY_FILE";
}

return $results;
}
public function Create_BackUp_Table() 
{
global $Create_BackUp_SQL;
$store=new dj_store(); 
$Write= $store->Sql_Operation_WR($Create_BackUp_SQL);
return 	$Write;
}
public function Update_Current_Time() 
{
global $Base_name,$BackUp_Table,$Update_Time;
$store=new dj_store(); 
$current_time=time();
$current_date=date("Y-m-d H:i:s"); 
$Table_Data= $store->Sql_Operation("SELECT id FROM {$Base_name}.{$BackUp_Table}");
if(empty($Table_Data))
{
	$SQL="INSERT INTO {$Base_name}.{$BackUp_Table}(last_backed,time_backed) VALUES ('{$current_time}','{$current_date}')";
	$Write= $store->Sql_Operation_WR($SQL);
}
else
{
$Data= $store->Sql_Operation("SELECT last_backed+update_time as time_decal FROM {$Base_name}.{$BackUp_Table}");
	if($Data[0]['time_decal']<=$current_time)
	{
    $SQL="UPDATE {$Base_name}.{$BackUp_Table} SET last_backed={$current_time}, time_backed='{$current_date}'";
    $Write= $store->Sql_Operation_WR($SQL);
	}
	else
	{
	$Write="TIME_NOT_REACHED";
	}
}
return 	$Write;
}
public function Create_Send_BackUp_File() 
{
global $File_Queries,$Base_name,$BackUp_Table,$Email_Regex,$Dir_File;
if(!file_exists( $Dir_File))
{
mkdir ($Dir_File,0755);
}
$files=scandir( $Dir_File );
$files=array_slice($files,2,sizeof($files));
$Update= $this->Update_Current_Time();
    if($Update===TRUE OR sizeof($files)==0 OR isset($_POST['Manual_backup']))
    {
	$Update=$this->Register_Queries_File($File_Queries);
    }
return $Update;
}

}