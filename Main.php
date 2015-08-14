<?PHP
/*
Plugin Name: WordPress Auto Backup
Plugin URI: http://www.sl3-unlocking.com/plugins.php
Description: This security plugin detects and backup wordpress database and WP CONTENT folder automatically in function to frequency time that is adjusted in admin panel, without need to back up them manually every time, these backed up automatically when visitor access to your web site pages or when you get access to your Wordpress administrator interface, so this plugin useful in case you have loosed or modified your databases either deleted accidentally or hacked, so you have a chance 100% to recover them safely!
Version: 1.0
Author: Ahmed KHABER
Author URI:  http://www.sl3-unlocking.com/plugins.php
License: GPLv2
*/
include_once (plugin_dir_path(__FILE__)."Database_Backup.php");
include_once (plugin_dir_path(__FILE__)."File.class.php");
include_once (plugin_dir_path(__FILE__)."Data.php");
include_once (plugin_dir_path(__FILE__)."Store.class.php");
class dj_databases_backup 
{
    public function __construct()
    {
	add_action('init', array($this, 'create_options_table'),1);
	add_action('init', array($this,'myStartSession'), 2);
	add_action('init', array($this, 'send_backups_by_email'),3);
	add_action('wp_loaded', array($this, 'register_plugin_styles'));
    add_action('admin_menu', array($this, 'add_admin_menu')); 
	register_uninstall_hook(__FILE__, 	'deactivate_uninstall');
	register_deactivation_hook(__FILE__, array($this,	'deactivate_uninstall'));
    }	
    public function myStartSession() 
	{
		if(!session_id()) 
		{
		session_start();
		}
	}
	
    public function send_backups_by_email() 
	{
include_once (plugin_dir_path(__FILE__)."Database_Backup.php");
    $operation=new dj_operation();
	$sending=$operation->Create_Send_BackUp_File() ;
	$_SESSION['dj_email_sent_stat']=$sending;	
	
	}
    public function register_plugin_styles() 
	{
		$dir=plugin_dir_url(__FILE__).'style.css';
		wp_register_style( 'database-plugin',$dir  );
		wp_enqueue_style( 'database-plugin' );
	}
	public function add_admin_menu()
	{
	add_menu_page('Databases plugin', 'WordPress Backup',
	'manage_options', 'Back_Up', array($this, 'admin_html'),plugin_dir_url(__FILE__).'/icons/Database.png');
	}
	public function admin_html()
	{
	include_once('Admin_Form.php');	
	}
	public function create_options_table()
	{
	$operation=new dj_operation();
    $stat=$operation->Create_BackUp_Table() ;
	}
	public function deactivate_uninstall()
	{
	global $Base_name,$BackUp_Table;
	$storen=new dj_store();
	$Sql_Operation="DROP TABLE IF EXISTS `{$Base_name}`.`{$BackUp_Table}`";
	$ope_stat=$storen->Sql_Operation_WR($Sql_Operation);
	return $ope_stat;
	}
}
$databases_backup =new dj_databases_backup();
?>