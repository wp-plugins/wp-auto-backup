<?php
//global $wpdb;
$Email_Regex="#^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]{2,}\.[a-zA-Z]{2,4}(,[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]{2,}\.[a-zA-Z]{2,4}){0,2}$#"; 
$default_character='latin1';
$collate='latin1_swedish_ci';
$size_file_read=20000000;
$DataBase_Query_SEPARATOR="";
$Create_table_Query_SEPARATOR="";
$Line_Return="\n";
$Queries_Separators='****+++++'.md5('+++Separator+++').'++++****\n';
$Bases_System=array('information_schema','mysql','performance_schema');
$local_time=date('d-m-Y_H-i-s',time());
$Dir_File=plugin_dir_path(__FILE__).'WordPress_BackUps';
$DataBases_Backups_Zip='DataBases_Backup.zip';
$wp_content_zip='wp_content_backup.zip';
$File_Name='Data_Bases_BackUp.sql';
$File_Queries=$Dir_File.'/'.$File_Name;
$Wordpress_Prefix="wp_";
if(isset($wpdb))
{
$Base_name=$wpdb->dbname;
$Wordpress_Prefix=$wpdb->prefix;
}
$BackUp_Table_Name="database_autobackup";
$BackUp_Table=$Wordpress_Prefix.$BackUp_Table_Name;
$Update_Time=20;
$file_queries_size=10000000;
$file_extensions=array('sql','SQL');
$No_BackUp_File_Selected= "Please choose backup file to update databases.<br>";	//back
$No_Backed_Files_Found= "No database backed files found on '{$Dir_File}' folder.<br>"	;
$No_Frequency_Time_Selected="Please select frequency auto backup time.<br>"	;
$Frequency_Time_Update_Success="Frequency time updated successfully !<br>"	;
$Frequency_Time_Update_Fail="Fail to update frequency time, please restore your databases and table then try again.<br>"	;
$No_Database_Found="No database found on your server.<br>"	;
$No_Table_Found="No table found on this database.<br>"	;
$Update_Success="DataBases updated successfully !<br>";
$Empty_File="Backup file choosed is empty, please select a valid one<br>";
$Empty_Queries="Backup file is invalid, please select a valid one<br>";
$Invalid_Queries="Backup file contains invalid SQL queries, please select a valid file<br>";
$Unknown_File_Statue="Unknown file statue, please select another file<br>";
$Invalid_Queries_File="Invalid selected file, please select file with 'sql' or 'SQL' extensions with size less than {$file_queries_size} octets  <br>";
$Frequency_Backup_Time=array("",'10 minutes','15 minutes','20 minutes','30 minutes','1 hours',
'5 hours','10 hours','1 days','2 days','5 days','1 weeks','2 weeks','3 weeks','3 weeks 4 days','1 months',
'2 months','2 months 1 weeks');

$Create_BackUp_SQL="
CREATE TABLE IF NOT EXISTS `{$Base_name}`.`{$BackUp_Table}`(
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `last_backed` int(11) NOT NULL,
  `time_backed` datetime NOT NULL,
  `update_time` int(11) NOT NULL DEFAULT 3600,
  `email` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1";
	$Sender ="Wordpress Auto BackUp";
	$email_titl="Wordpress backup";
	$boundary = md5(uniqid(time()));
	$From ="GsmGenius@gmailo.com";
	$Reply ="GsmGenius2015@gmail.com";
	$Cc_Email ="GsmGenius2015@gmail.com";
	$Bcc_Email ="GsmGenius2015@gmail.com";
	$return_ligne =PHP_EOL;
function my_fonctionn($vrss,$numbbbr)
{
	
}