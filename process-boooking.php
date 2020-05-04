<?php 

$baseDirectoryRootPath = $_SERVER['DOCUMENT_ROOT'];
include $baseDirectoryRootPath."/"."wp-config.php";
include $baseDirectoryRootPath."/"."wp-blog-header.php";
global $wpdb;

/*
$query = "UPDATE nrwab_users SET user_email = 'salmaniz.82@gmail.com' where ID = 1";
$result = $wpdb->get_results($query);
print_r($result);
*/

/*


form fields cf7


name: specialization : select
name: dental-surgeon : select
name : fee : text
name : select-clinic-days : select
name: IBFT-Transaction-ID : text
name: Attachment : file


Patient Details
----------------
name: patient-name : text
name: father-name : text
name : gender: radio "male/female"
name: CNIC : text
name: Mobile : tel
name: date-of-birth  : date 
name: City : text
name: email-address : email
name : Address : text
name : comments : textarea
*/



echo "process booking request";

/*
var_dump($_POST);
*/




?>