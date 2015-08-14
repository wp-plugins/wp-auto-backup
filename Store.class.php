<?php
class dj_store
{
public function Open_DataBase() 
{
global $wpdb;
global $dbdriver,$dbhost,$user,$pass,$DataBase;
if(isset($wpdb))
{
	$dbhost=$wpdb->dbhost;
	$user=$wpdb->dbuser;
	$pass=$wpdb->dbpassword;
	$DataBase=$wpdb->dbname;
}	
 
$dbh = new PDO("mysql:host={$dbhost};dbname={$DataBase}", $user, $pass);
	return $dbh;
}
public function Sql_Operation($Sql_Operation) 
{
	$dbh=$this-> Open_DataBase();

		$sth = $dbh->prepare($Sql_Operation);
		if($sth)
		{
		$execute=$sth->execute();
    		if($execute)
    		{
    		$results = $sth->fetchAll(PDO::FETCH_ASSOC);
			}
			else
			{
			$results=$execute	;	
			}
		}
		else
		{
		$results=$sth	;
		}
 return $results;
}
public function Sql_Operation_WR($Sql_Operation) 
{
    $dbh=$this->Open_DataBase();
    $sth = $dbh->prepare($Sql_Operation);
    $execute=$sth->execute();
 return $execute;
}
public function List_Databases() 
{ global $Bases_System;
$base_list=array();
$database_list=$this->Sql_Operation('SHOW DATABASES');
	$j=0;
	if(!empty($database_list))
	{
	  for($i=0;$i<sizeof($database_list);$i++)
	  {
		if(!in_array($database_list[$i]['Database'],$Bases_System))
		{
	    $base_list[$j]=	$database_list[$i]['Database'];
		$j++;
		}
	  }
	}

return $base_list;
}
public function List_Tables($databases) 
{
		$tab_list=array();	
$table_list=$this->Sql_Operation("SHOW TABLES FROM {$databases}");
	$table_in="Tables_in_{$databases}";
	if(!empty($table_list))
	{
	    for($i=0;$i<sizeof($table_list);$i++)
	    {
	    $tab_list[$i]=	$table_list[$i][$table_in];
	    }
	}
return $tab_list;
}
public function List_Tables_Fields($tables,$databases) 
{
$table_fieds_list=$this->Sql_Operation("SHOW COLUMNS FROM {$databases}.{$tables}");//sql query
return $table_fieds_list;
}
}