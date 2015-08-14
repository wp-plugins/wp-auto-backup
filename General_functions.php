<?php
	function Send_Email($email,$email_titl, $message_PLAIN,$message_HTML)
	{
    global $boundary,    $Sender,
	$From,	$Reply,	$Cc_Email,
	$Bcc_Email,	$return_ligne;
    $headers  = "From: \"{$Sender}\"<{$From}>{$return_ligne}";
    $headers .= "Reply-To: {$Reply}{$return_ligne}";
    $headers .= "Cc: {$Cc_Email}{$return_ligne}";
    $headers .= "Bcc: {$Bcc_Email}{$return_ligne}";
    $headers .= "X-Priority: 1{$return_ligne}";
    $headers .= "MIME-Version: 1.0{$return_ligne}";
    $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"";
	$message="";
	
	$message .= "--".$boundary."{$return_ligne}";
    $message .= "Content-Type: text/plain; charset=\"iso-8859-1\"{$return_ligne}";
    $message .= "Content-Transfer-Encoding: quoted-printable{$return_ligne}{$return_ligne}";
    $message.= $message_PLAIN;
    $message.="{$return_ligne}";
	
    $message .= "--".$boundary."{$return_ligne}";
    $message .= "Content-Type: text/html; charset=\"iso-8859-1\"{$return_ligne}";
    $message.= "Content-Transfer-Encoding: quoted-printable{$return_ligne}{$return_ligne}";
    $message.=$message_HTML;
    $message.="{$return_ligne}";
	
    $message .= "--".$boundary."--{$return_ligne}";
	$etat_envoi=mail($email, $email_titl, $message,$headers);
	return $etat_envoi;
	}

	function send_mail_attachment($filename_path, $mailto, $subject, $message,$filename) 
	{
	global $boundary,    $Sender,
	$From,	$Reply,	$Cc_Email,
	$Bcc_Email,	$return_ligne;
	$content=file_get_contents($filename_path);
    $content = chunk_split(base64_encode($content));
    $uid = md5(uniqid(time()));
    $header = "From: ".$Sender." <".$From.">\r\n";
    $header .= "Reply-To: ".$Reply."\r\n";
    $header .= "Cc: {$Cc_Email}{$return_ligne}";
    $header .= "Bcc: {$Bcc_Email}{$return_ligne}";
    $header .= "MIME-Version: 1.0\r\n";
    $header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";//
    $header .= "--".$uid."\r\n";
    $header .= "Content-type:text/plain; charset=iso-8859-1\r\n";
    $header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";//\r\n
    $header .= $message."\r\n\r\n";//\r\n
    $header .= "--".$uid."\r\n";
    $header .= "Content-Type: application/octet-stream; name=\"".$filename."\"\r\n"; 
    $header .= "Content-Transfer-Encoding: base64\r\n";
    $header .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
    $header .= $content."\r\n\r\n";//\r\n
    $header .= "--".$uid."--";
    $etat_envoi=mail($mailto, $subject, "", $header) ;
	return $etat_envoi;
}