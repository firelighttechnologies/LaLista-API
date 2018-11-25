<?
date_default_timezone_set("America/Chicago");
require_once('../vendor/autoload.php');

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Signer\Hmac\Sha256;

$myfile = fopen("/run/secrets/api.lalista.auth.key", "r") or die("Unable to open file!");
$GLOBALS['testsecretkey'] = trim(fgets($myfile));
fclose($myfile);


function return_true_if_token_good(){
	$oauthToken = getBearerToken();
	$token = (new Lcobucci\JWT\Parser())->parse((string) $oauthToken);

	/*
	WARN: before validating the content you would probably like to verify the token signature:
	*/
	//strval(Configure::read('Security.cipherSeed'))
	if (!$token->verify(new Sha256(), $GLOBALS['testsecretkey'])) {
	    //echo "Bad Signature! <br />";
			return "false";
	}else{
		//echo "Valid Signature! <br />";
		$data = new ValidationData(); // It will use the current time to validate (iat, nbf and exp)
		//$data->setIssuer($_SERVER['SERVER_NAME']);
		//$data->setAudience($_SERVER['SERVER_NAME']);
		return($token->validate($data)); // true, if token has a good signature and is vaild`
	}
}

function return_userid_if_token_good(){
	$oauthToken = getBearerToken();
	$token = (new Lcobucci\JWT\Parser())->parse((string) $oauthToken);

	/*
	WARN: before validating the content you would probably like to verify the token signature:
	*/
	//strval(Configure::read('Security.cipherSeed'))
	if (!$token->verify(new Sha256(), $GLOBALS['testsecretkey'])) {
	    //echo "Bad Signature! <br />";
			die(http_response_code(401));
			//return "false"; // false, Signature not valid
	}else{
		//echo "Valid Signature! <br />";
		$data = new ValidationData(); // It will use the current time to validate (iat, nbf and exp)
		//$data->setIssuer($_SERVER['SERVER_NAME']);
		//$data->setAudience($_SERVER['SERVER_NAME']);
    if ($token->validate($data)){
			 error_log('user id:' . $token->getClaim('uid') . '-', 3, '/var/www/html/error.log');
      return($token->getClaim('uid')); // true, if token has a good signature and is vaild`
    }
		die(http_response_code(401));
		//return "false"; // false, token not valid
	}
}

/**
 * Get header Authorization
 * */
function getAuthorizationHeader(){
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }
/**
 * get access token from header
 * */
function getBearerToken() {
    $headers = getAuthorizationHeader();
    // HEADER: Get the access token from the header
    if (!empty($headers)) {
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }
    }
		die(http_response_code(401));
    //return null;
}

function return_access_of_user_for_list($uid, $list_id){
	include '../conn.php';
	$sql = "SELECT `permissions` FROM `user_list` WHERE `user_id` = '" . $uid . "' AND `list_id` = '" . $list_id . "' AND `status` = '1'";
	$result = mysqli_query($conn, $sql);
	if (!$result) {
	    //echo "Could not successfully run query ($sql) from DB: " . mysql_error();
	    //exit;
			return -1;
	}
	if (mysqli_num_rows($result) == 0) {
	    //echo "No rows found, nothing to print so am exiting";
	    //exit;
			return 0;
	}
	// While a row of data exists, put that row in $row as an associative array
	// Note: If you're expecting just one row, no need to use a loop
	// Note: If you put extract($row); inside the following loop, you'll
	//       then create $userid, $fullname, and $userstatus
	while ($row = mysqli_fetch_assoc($result)) {
	    return $row["permissions"];
	}
	mysqli_free_result($result);
}

function return_all_lists_of_user($uid){
	//mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	include '../conn.php';
		$sql = "SELECT * FROM `lalista`.`list` JOIN `user_list` ON `user_list`.`list_id` = `list`.`list_id` WHERE `user_id` ='" . $uid . "'";
		//		$sql = "SELECT * FROM `lalista`.`list` JOIN `user_list` ON `user_list`.`list_id` = `list`.`list_id` WHERE `user_id` = " . $user;
		//	$sql = "CALL `task_new`('due, task, category, userid, perm')";
		//	$sql = "CALL `task_new`('" . date ("Y-m-d H:i:s", $_REQUEST['new_date']) . "', '". $_REQUEST['new_task'] . "', '". $_REQUEST['new_category'] . "', '". $_REQUEST['new_user'] . "', " . 7 . ")";
		try {
			$result = mysqli_query($conn, $sql);
		}
		catch(exception $e) {
			//			echo "ex: ".$e;
			return "Database Error!";
		}
		if(mysqli_num_rows($result) > 0) {
			$list_array = array();
			// output data of each row
			while($row = mysqli_fetch_assoc($result)) {
				$list = new stdClass();
				$list->list_id = $row["list_id"];
				$list->created = $row["list_created"];
				$list->moddified = $row["list_moddified"];
				$list->name = $row["list_name"];
				$list->status = $row["status"];
				$list->permissions = $row["permissions"];
				array_push($list_array, $list);
			}
			print_r(json_encode($list_array));
		} else {
			echo "0 results";
		}

	mysqli_close($conn);
}

function return_all_tasks_of_user($uid){
	//mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	include '../conn.php';
	//	$sql = "SELECT * FROM tasks";
	//	$sql = "CALL `task_new`('due, task, category, userid, perm')";
	//	$sql = "CALL `task_new`('" . date ("Y-m-d H:i:s", $_REQUEST['new_date']) . "', '". $_REQUEST['new_task'] . "', '". $_REQUEST['new_category'] . "', '". $_REQUEST['new_user'] . "', " . 7 . ")";
	//$sql = SELECT `list`.`list_name`, `tasks`.`list_id`, `tasks`.`task_id`, `tasks`.`category`, `tasks`.`task`, `tasks`.`due`, `tasks`.`status` FROM `user_list` LEFT JOIN `list` ON `user_list`.`list_id` = `list`.`list_id` LEFT JOIN `tasks` ON `list`.`list_id` = `tasks`.`list_id` WHERE `user_list`.`user_id` = userid ORDER BY `list`.`list_name`, `tasks`.`category`, `tasks`.`due`;
	$sql = "CALL `tasks_get_by_user`('" . $uid . "')";
		try {
			$result = mysqli_query($conn, $sql);
		}
		catch(exception $e) {
			echo "ex: ".$e;
		}
		if(mysqli_num_rows($result) > 0) {
			$list = array();
			// output data of each row
			while($row = mysqli_fetch_assoc($result)) {
				$task = new stdClass();
				$task->task_id = $row["task_id"];
				$task->list_id = $row["list_id"];
				$task->created = $row["created"];
				$task->category = $row["category"];
				$task->details = $row["task"];
				$task->due = $row["due"];
				$task->status = $row["status"];
				array_push($list, $task);
			}
			print_r(json_encode($list));
		} else {
			echo "0 results";
		}
	mysqli_close($conn);
}

function return_all_users_of_list($lid){
	include '../conn.php';
	$sql = "SELECT `users`.`id` AS userid,`users`.`email`,`users`.`fname`,`users`.`lname`,`user_list`.`permissions`
	 FROM `users` JOIN `user_list` ON `user_list`.`user_id` = `users`.`id` WHERE `user_list`.`list_id` ='" . $lid . "'";

		try {
			$result = mysqli_query($conn, $sql);
		}
		catch(exception $e) {
			echo "ex: ".$e;
		}

		if(mysqli_num_rows($result) > 0) {
			$user_array = array();
			// output data of each row
			while($row = mysqli_fetch_assoc($result)) {
				$user = new stdClass();
				$user->user_id = $row["user_id"];
				$user->email = $row["email"];
				$user->fname = $row["fname"];
				$user->lname = $row["lname"];
				$user->permissions = $row["permissions"];
				array_push($user_array, $user);
			}
			print_r(json_encode($user_array));
		} else {
			echo "0 results";
		}
	mysqli_close($conn);
}

function return_json_add_new_list($uid, $listname){
	include '../conn.php';
	$sql = "INSERT INTO `list` (`list_name`, `list_status`) VALUES ('" . $listname . "', 1)";
	if (mysqli_query($conn, $sql)) {
    $last_id = mysqli_insert_id($conn);
		$sql = "INSERT INTO `user_list` (`user_id`, `list_id`, `permissions`, `status`) VALUES ('" . $uid . "', '" . $last_id . "', 1,1)";
		if (mysqli_query($conn, $sql)) {
	    echo "New List created successfully";
		} else {
	    echo "Error: Adding List";
		}
	} else {
    echo "Error: Adding List";
	}
}

function return_json_update_list($uid, $listname, $status, $list_id){
	include '../conn.php';
	$sql = "UPDATE `list` SET `list_name` = '" . $listname . "', `list_status` '" . $status . "' WHERE `list_id` = '" . $list_id . "'";
	if (mysqli_query($conn, $sql)) {
	    echo "Successfull";
	} else {
    echo "Error: Updating List";
	}
}

function return_json_add_new_task($uid, $category,$task,$due,$status,$list_id){
	include '../conn.php';
	if (return_access_of_user_for_list($uid, $list_id) > 0){
		$sql = "INSERT INTO `tasks` (`category`,`task`,`due`,`status`,`list_id`) VALUES('" . $category . "','" . $task . "','" . $due . "','" . $status . "','" . $list_id . "')";
		if (mysqli_query($conn, $sql)) {
	    //$last_id = mysqli_insert_id($conn);
			echo "successful";
		} else {
			//echo $sql;
	    echo "Error: Adding tasks";
		}
	}
}

function return_json_update_task($uid, $category,$task,$due,$status,$task_id){
		include '../conn.php';
		//	if (return_access_of_user_for_list($uid, $list_id) > 0){
		if (true){
			$sql = "UPDATE `tasks` SET `category` = '" . $category . "', `task` = '" . $task . "', `due` = '" . $due . "', `status` = '" . $status . "' WHERE `task_id` = '" . $task_id . "'";
			if (mysqli_query($conn, $sql)) {
		    //$last_id = mysqli_insert_id($conn);
				echo "successful";
			} else {
				//echo $sql;
		    echo "Error: Updating task";
			}
		}else{echo "Error: Updating task";}
}
