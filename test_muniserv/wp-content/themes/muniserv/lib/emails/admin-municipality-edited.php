<?php 
	global $fieldNames;
	$folders = wp_upload_dir();

?><html>
<head>
<title>muniSERV</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="580" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td style="font-family:Arial, Helvetica, sans-serif; font-size:12px; color:#000000;">
        <h3>Edit Approval Required</h3>
        <p>A municipality profile edit request has been received. Please review the changes below.</p>
        <?php foreach($_POST as $key => $value){ 
				if(empty($value) || empty($fieldNames[$key])){
					continue;
				}
				if($key == 'municipality_profile_image'){
					$imageURL = trailingslashit($folders['baseurl']).'municipal-pics/'. rawurlencode($value);
					$value = '<br /><img src="'.$imageURL.'" />';	
				}
		?>
        	<p><strong><?php echo $fieldNames[$key]; ?>:</strong> <?php echo $value; ?></p>
        <?php } ?>
    </td>
  </tr>
</table>
</body>
</html>