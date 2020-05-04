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

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}


$payload = array(


	'patient_name'=> 'test',
	'father_name'=> 'testtwo',
	'gender'=> 'm',
	'mmi_hsp_no'=> '123456',
	'cnic'=> '4220134006666',
	'mobile'=> '03343533668',
	'dob'=> '1992-08-06',
	'email'=> 'testemail@domain.com',
	'address' => 'somewheresomecity, some land',
	'comments'=> 'this is just a test',
	'clinic_days'=> 'monday',
	'fee'=> '100',
	'user'=> 'MMIH',
	'password'=> 'Pay@online2020',
);



$_POST = $payload;



	$patient_name = test_input($_POST["patient_name"]);
    $father_name = test_input($_POST["father_name"]);
    $gender = test_input($_POST["gender"]);
    $mmi_hsp_no = test_input($_POST["mmi_hsp_no"]);
    $cnic = test_input($_POST["cnic"]);
    $patient_mobile = test_input($_POST["mobile"]);
    $dob = test_input($_POST["dob"]);
    $city = test_input($_POST["city"]);
    $patient_email = test_input($_POST["email"]);
    $patient_address = test_input($_POST["address"]);
    $comments = test_input($_POST["comments"]);
    $clinic_days=test_input($_POST["clinic_days"]);

    $comments = test_input($_POST["comments"]);

    $fee = test_input($_POST["fee"]);

    $user="MMIH";
    $password="Pay@online2020";
    $or_number="ord_".date('dmyhis');
    $amount=$fee;
    $or_date=date('d/m/Y');
    $or_due_date="";
    $or_due_date=date('d/m/Y',strtotime('+1 days'));



    $sp_id = 'specialityId';
    $dc_id = 'doctorId';
    $patient_bank="";
    $dr_name='doctornmae';
    $dr_fees=$fee;
    $oJson=
        [[
            "MerchantId"=> "$user",
            "MerchantPassword"=> "$password"
        ],
            [
                "OrderNumber"=> "$or_number",
                "OrderAmount"=> "$fee",
                "OrderDueDate"=> "$or_due_date",
                "OrderAmountWithinDueDate"=> $fee,
                "OrderAmountAfterDueDate"=> $fee,
                "OrderTypeId"=> "Service",
                "OrderType"=> "Service",
                "IssueDate"=> "$or_date",
                "OrderExpireAfterSeconds"=> 60*60*24,
                "DatePaid"=> "",
                "Reserved"=> "",
                "TransactionStatus"=> "UNPAID",
                "ReasonType"=> "",
                "Reason"=> "",
                "CustomerName"=> "$patient_name",
                "CustomerMobile"=> "$patient_mobile",
                "CustomerEmail"=> "$patient_email",
                "CustomerAddress"=> "$patient_address",
                "CustomerBank"=> "",
                "BillDetail01"=> [[
                    "LineItem"=> "$dr_name",
                    "Quantity"=> "1",
                    "UnitPrice"=> "$dr_fees",
                    "SubTotal"=> "$dr_fees"
                ]]
            ]
        ];


$url="https://www.connectpay.com.pk:446/cpay/co?oJson=".urlencode(trim(json_encode($oJson)));



$ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, trim($url));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Length: 0'));

    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 


    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array()));
    $result = curl_exec($ch);


    var_dump($result);



    if($err = curl_errno($ch))
    {
    	
    	$error_msg = curl_error($ch);
    	var_dump($error_msg);

    }
    
    $errnew = curl_error($ch);
    $inf = curl_getinfo($ch);

    curl_close($ch);

    $result_array=json_decode($result);

    echo "result dump <br>";
    var_dump($result_array);


    /*
    $ord_status=$result_array[0]->Status;

    if($ord_status=="00"){
        $cpay_id=$result_array[1]->ConnectPayId;
        $or_number=$result_array[1]->OrderNumber;
        $ord_description=$result_array[1]->Description;
    }else{
        $ord_status=$result_array[0]->Status;
        $ord_description=$result_array[1]->Description;
    }
*/

?>