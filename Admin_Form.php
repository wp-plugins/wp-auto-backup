<div class='main_form'>
<?php
	global $Email_Regex,$No_Table_Found,$Base_name,$BackUp_Table,$Dir_File,
	$Update_Success,$Empty_File,$Empty_Queries,$No_Database_Found,$Invalid_Queries,
	$Unknown_File_Statue,$No_BackUp_File_Selected,	$No_Backed_Files_Found, $Frequency_Backup_Time,
	$Frequency_Time_Update_Success,	$Frequency_Time_Update_Fail,$No_Frequency_Time_Selected,$file_queries_size,
	$file_extensions,$Invalid_Queries_File,$send_backups;
$store=new dj_store();
$operation=new dj_operation();
$email_Data= $store->Sql_Operation("SELECT email FROM {$Base_name}.{$BackUp_Table}");
$email=$email_Data[0]['email'];
if(empty($email) OR isset($_POST['update_email'])) //Back
{
?>
<br><fieldset>
<legend><h1>Email update & registration</h1></legend>
This section allow to register or update Email address to send wordPress backups
<form action="" method="post">
<br><label for="email"><b>Enter 1 to 3 Emails separated with comma "," without spaces:</b></label>  <br> <input type="text"  size="100" maxlength="300" name="email" id="email"/><br><br>
<?php
$SQL="UPDATE {$Base_name}.{$BackUp_Table} SET email=''";
$Write= $store->Sql_Operation_WR($SQL);

if(isset($_POST['email']))
{
$email=	htmlspecialchars($_POST['email']);
    if(!empty($email))
	{
	    if(preg_match($Email_Regex,$email))//"#^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]{2,}\.[a-zA-Z]{2,4}$#",
	    {
        $SQL="UPDATE {$Base_name}.{$BackUp_Table} SET email='{$email}'";
        $Write= $store->Sql_Operation_WR($SQL);
		    if(!$Write)
		    {
		    echo '<span class=\'fail_message\' >Fail to register Email, no registration table present, please reactive plugin or contact author</span><br>';
		    }
		    else
		    {
		    echo '<span class=\'success_message\' >Email registered successfully, be patient to redirect to Admin page</span><br>';
			$uri=$_SERVER['REQUEST_URI'];
		    echo '<META HTTP-EQUIV="Refresh" Content="0; URL='.$uri.'">';   
		    }
	    }
	    else
	    {
	    echo '<span class=\'fail_message\'>Email entered is invalid or empty, re-enter a valid one</span><br>';
	    }
	}
	else
	{
	echo '<span  class=\'fail_message\'>Fill Email input</span><br>';
	}
}
?>
	<input type="submit" value="Register email"/><br>
</form>
<?php

?>
</fieldset>
<?php
}
else
{
?>
<br><fieldset>
<legend><h1>Plugin definition</h1></legend>
 This security plugin detect and backup current WordPress database<br>
 and WP-CONTENT folder with all its contents(themes, plugins, ...) <br>
 without native themes,  then send them to Email inbox that's registered<br>
 in admin interface, this done automatically in function to frequency <br>
 time that is adjusted  <a href="#Settings">below</a>, without need to backup them manually<br>
 every time, these backups made when visitor access to your site web pages or when<br>
 you access to your Wordpress administrator interface, so this plugin useful<br>
 in case you have loosed or modified your databases or WP contents either deleted<br>
   accidentally or hacked, so you have a chance 100% to recover them safely.
</fieldset><br>
<br><fieldset>
<legend><h1>Database & tables list</h1></legend>
<?php
$List_Databases=$store->List_Databases();
if(!empty($List_Databases))
{
	?>
    <ul>
	<?php
	$i=1;
    foreach($List_Databases as $Databases)
    {
	?><ul><?php	echo "<br><b>DATABASE n ".$i.": </b>".$Databases;?></ul><?php
	$List_Tables=$store->List_Tables($Databases);
	    if(!empty($List_Tables))
		{
			?>
    		<ul>
			<?php
			$j=1;
			foreach($List_Tables as $Tables)	
			{
			?><ul><?php	echo "<b>----->TABLE n ".$j.": </b>".$Tables;?></ul><?php		
			$j++;
			}
			?>
    		</ul>
			<?php
		}
		else
		{
		echo '<span class=\'fail_message\'>'.$No_Table_Found.'</span>'	;
		}
	$i++;	
    }
	?>
    </ul>
	<?php
}
else
{
	echo '<span class=\'fail_message\'>'.$No_Database_Found.'</span>'	;
}	
?>
</legend><h2>
</fieldset><br>
<br><fieldset>
<legend><h1>Restore & Update DataBase</h1></legend>
<?php
$backups_files=scandir($Dir_File);
$backups_files=array_splice($backups_files , 2 , sizeof($backups_files));
$clone_array=array();
foreach($backups_files as $files)
{
	$info_path_files=pathinfo($files);
	if($info_path_files['extension']=='sql')
	{
	$clone_array[]=	$files;
	}
}
$backups_files=$clone_array;
if(!empty($backups_files))
{
?>
<form action="#Update_database" method="post" enctype="multipart/form-data" >
<span class='info_message'>Browse Backup file,then click 'Restore database' :</span ><br><br>
	<?php
    for($i=0;$i<sizeof($backups_files);$i++)
    {
    $backups=	$backups_files[$i];
	$timestp=filectime($Dir_File.'/'.$backups);
	$modification_date=date("Y-m-d H:i:s",$timestp);
    }
	?>
<input type="file" name="database_back"  /><br>
<input type="submit" name="submit_button" value="Restore database" id="Update_database"/><br>
<?php
	if(isset($_POST['submit_button']) )
	{
			$database_back=	$_FILES['database_back'];
			if(!empty($database_back['name']))
			{
			$backup_file=$database_back['tmp_name'];
			
		    $file_infos=pathinfo($database_back['name']);
				if($database_back['error']==0 AND $database_back['size']<=$file_queries_size AND in_array( $file_infos['extension'],$file_extensions)  )//
				{
				
				$Update_stat=$operation->Update_Database_From_File($backup_file)   ;
					switch($Update_stat)
					{
					case 'UPDATE_SUCCESS:TRUE';
					$Update_Reslt=$Update_Success;
					$message_style='success_message';
					break;	
					case 'EMPTY_FILE':
					$Update_Reslt=$Empty_File;
					$message_style='fail_message';
					break;	
					case 'EMPTY_SQL_QUERY':
					$Update_Reslt=$Empty_Queries;
					$message_style='fail_message';
					break;	
					case 'INVALID_SQL_QUERIES:FALSE':
					$Update_Reslt=$Invalid_Queries;
					$message_style='fail_message';
					break;
					default:
					$Update_Reslt=$Unknown_File_Statue;
					$message_style='fail_message';
					}
				}
				else
				{
				$Update_Reslt= $Invalid_Queries_File;
				$message_style='fail_message';
				}
			}
			else
			{
			$Update_Reslt= $No_BackUp_File_Selected;
			$message_style='fail_message';
			}
	}

    if(isset($Update_Reslt)) 
	{
    ?>
	<span class='<?php echo $message_style?>' ><?php echo $Update_Reslt?></span>
    <?php 
	}
    ?>
</form >
<?php
}
else
{
echo '<span  class=\'fail_message\'>'.$No_Backed_Files_Found.'</span>'	;	
}
?>
</fieldset><br>
<br><fieldset>
<legend id="Settings"><h1>Automatic Backup state</h1></legend>
<form action="#save_settings" method="post">
 <?php 
 $dj_email_sent_stat=$_SESSION['dj_email_sent_stat'];
	if($dj_email_sent_stat===TRUE)
	{
	echo '<span class=\'success_message\'  >Wordpress backup created and sent by Email successfully!!</span><br>';
	}
	elseif($dj_email_sent_stat==='TIME_NOT_REACHED')
	{
	echo '<span class=\'info_message\' >Time not reached to send WordPress backup</span><br>';
	}
	else
	{
	echo '<span class=\'fail_message\' >Fail to send Backup by Email, please reinstall plugin</span><br>';
	}
	
 ?>
<input type="submit" class='buttons' name="Manual_backup" value="Manual backup" id=""/><br>
</fieldset>
<br><fieldset>
<legend ><h1>Settings</h1></legend>
    <h2 id="Settings">Update time:</h2>
	  <label for="frequency_time" >Choose frequency time </label>
        <select name="frequency_time" id="frequency_time">
		   
		   <?php
		   for($i=0;$i<sizeof($Frequency_Backup_Time);$i++)
		   {
			$Time=$Frequency_Backup_Time[$i];
			   if(!empty($Time))
			   {
			   $secnds=strtotime($Time)-time();
			   }
			   else
			   {
				$secnds='';   
			   }
		   ?>
		   <option value="<?php echo $secnds;  ?>"><?php echo $Time?></option>
		   <?php
		   }
		   ?>
        </select><br>
		<?php
		if(isset($_POST["frequency_time"]) AND isset($_POST["save_settings"]))
		{
			$frequency_time=$_POST["frequency_time"];
		  if($frequency_time!=='')
		  {
		   $Sql="UPDATE {$Base_name}.{$BackUp_Table} SET update_time={$frequency_time}";
		   $Update_statue=$store->Sql_Operation_WR($Sql);
		    if($Update_statue)
			{
	 		echo '<span class=\'success_message\'>'.$Frequency_Time_Update_Success.'</span>'	;	
			}
			else
            {
	 		echo '<span  class=\'fail_message\'>'.$Frequency_Time_Update_Fail.'</span>'	;	
			}				
		  }
		  else
		  {
	 		echo '<span  class=\'fail_message\'>'.$No_Frequency_Time_Selected.'</span>'	;	
		  }
		}
		?>
    <h4>Frequency Automatic DataBase Backup time :</h4>
    When this time reached, Plugin make Wordpress backup automatically<br>
	when accessing your site web pages or administrator interface page.<br>
	
	<?php
	$Qer="SELECT update_time FROM {$Base_name}.{$BackUp_Table}";
	$Current_update_time=$store->Sql_Operation($Qer);
	$up_time=$Current_update_time[0]['update_time'];
	$periods=array("years","months","days","hours","minutes","seconds");
	$time=date("Y_m_d_H_i_s",$up_time);
	$Final_Per="";
	$time=explode("_",$time);
	$time[0]=$time[0]-1970;
	$time[1]=$time[1]-1;
	$time[2]=$time[2]-1;
   	  	$i=0;
      	foreach($time  as $timess)
      	{
		  	if($timess>0)
		  	{
		  	$Final_Per.= $timess.' '.$periods[$i].' ';
		  	}
      	$i++;
	  	}
    ?>
    <h3>Current frequency Backup time: <span class='info_message'><?php echo $Final_Per ?></span> </h3>
	<input type="submit" class='buttons' name="save_settings" value="Update settings" id="save_settings"/><br>
	
    <br><h2>Change Email & update:</h2>
	This section allow to add or change new Email address <br>
	<input type="submit" name="update_email" value="Update email" id=""/><br>
</form>
</fieldset><br>
<?php
}
?>
</div>