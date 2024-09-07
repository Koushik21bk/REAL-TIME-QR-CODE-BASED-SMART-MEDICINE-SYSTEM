<?php 
	
	//Constants for database connection
	define('DB_HOST','localhost');
	define('DB_USER','root');
	define('DB_PASS','');
	define('DB_NAME','QR_Medicine');

	//connecting to database 
	$conn = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME) or die('Unable to connect');
	
	$response = array();
	
	if(isset($_GET['apicall'])){
		
		switch($_GET['apicall']){
			
			case 'user_reg':
				if(isTheseParametersAvailable(array('name','email','password','mobile'))){
					$name = $_POST['name']; 
					$email = $_POST['email'];
					$password = $_POST['password'];
					$mobile = $_POST['mobile']; 
					$email=$_POST['email'];
				
										
					$stmt = $conn->prepare("SELECT id FROM user_reg WHERE email = ? OR password = ?");
					$stmt->bind_param("ss", $email, $email);
					$stmt->execute();
					$stmt->store_result();

					
					
					if($stmt->num_rows > 0){
						$response['error'] = true;
						$response['message'] = 'User already registered';
						$stmt->close();
					}
					
					else{
					
					$stmt = $conn->prepare("INSERT INTO user_reg (name, email, password, mobile) VALUES (?,?,?,?)");
					$stmt->bind_param("ssss", $name ,$email ,$password ,$mobile);
					$stmt->execute();
					$stmt->store_result();
					
					if($stmt->num_rows > 0){
						$response['error'] = true;
						$response['message'] = 'Registration success';
						$stmt->close();
					}
					}
					
				}else{
					$response['error'] = true; 
					$response['message'] = 'required parameters are not available'; 
				}
				
			break;
			
			

			case 'user_login':
				if(isTheseParametersAvailable(array('email','password'))){
					$email = $_POST['email'];
					$password = $_POST['password'];
					
					$stmt = $conn->prepare("SELECT id, name, email, password, mobile FROM user_reg WHERE email=? AND password=?;");
					$stmt->bind_param("ss", $email, $password);
					
					$stmt->execute();
					$stmt->store_result();
					if($stmt->num_rows > 0){
						$stmt->bind_result($id, $name, $email, $password, $mobile );
	
						$details = array(); 
	
						//traversing through all the result 
						while($stmt->fetch()){
						$temp = array();
						$temp['id'] = $id; 
						$temp['name'] = $name; 
						$temp['email'] = $email;
						$temp['password'] = $password;						
						$temp['mobile'] = $mobile;
						
						array_push($details, $temp);
						}
	
						//displaying the result in json format 
						echo json_encode($details);
					}else{
					
						$response['error'] = false; 
						$response['message'] = 'Invalid username or password';
					}
					
				}
				
				
			
			
			break;		
			
			case 'addmed':
				if(isTheseParametersAvailable(array('name','uses','manu'))){
					$scan_id = $_POST['scan_id'];
					$name = $_POST['name']; 
					$uses = $_POST['uses'];
					$manu = $_POST['manu'];
					$expiry = $_POST['expiry'];
					$compo = $_POST['compo'];
					$side = $_POST['side'];
					
					$stmt = $conn->prepare("INSERT INTO medicine (scan_id, name, uses, compo,manufacture_date,expiry_date,side_effects) 
					VALUES (?,?,?,?,?,?,?)");
					$stmt->bind_param("sssssss", $scan_id ,$name ,$uses ,$compo ,$manu ,$expiry ,$side );
					$stmt->execute();
					$stmt->store_result();
					
					if($stmt){
						$response['error'] = true;
						$response['message'] = 'Successful';
						$stmt->close();
					}
					
					
				}else{
					$response['error'] = true; 
					$response['message'] = 'required parameters are not available'; 
				}
				
			break;
			
			case 'update_count':
				
				$username = $_POST['username'];
				$count = $_POST['count']; 
				$tabname = $_POST['tabname']; 
				

                        
		

				$mysqli_stmt = $conn->prepare("UPDATE tab_counts SET count=? WHERE username = ? AND tab_name=?");
				$mysqli_stmt->bind_param("sss", $count,$username,$tabname);
				$mysqli_stmt->execute();
				$mysqli_stmt->store_result();
				$mysqli_stmt->fetch(); 
				//printf("Number of rows: %d.\n", $mysqli_stmt->num_rows);
 	
				if($mysqli_stmt)
				{	
					$response['error'] = true;
					$response['message'] = 'Successful';

				}else{
					$response['error'] = true; 
					$response['message'] = 'Error'; 
				}
			
			break;
			
			case 'addmedcount':
				if(isTheseParametersAvailable(array('name','count','username','tab','date'))){
					$tabname = $_POST['name']; 
					$count = $_POST['count'];
					$username = $_POST['username'];
					$date = $_POST['date'];
					$tab = $_POST['tab'];

					$tabc = intval($tab);
        				$countc = intval($count);

				
					
			
                               if($tabc <= $countc){

					$stmt = $conn->prepare("INSERT INTO tab_count (tab_name, count, username, added_date, tab) 
					VALUES (?,?,?,?,?)");
					$stmt->bind_param("sssss", $tabname ,$count ,$username ,$date ,$tab);
					$stmt->execute();
					$stmt->store_result();
					
                             
				     }
					
					if($stmt){
						$response['error'] = true;
						$response['message'] = 'Successful';
						$stmt->close();
					}
					
					
				}else{
					$response['error'] = true; 
					$response['message'] = 'required parameters are not available'; 
				}
				
			break;

			case 'addmedcounts':
				if(isTheseParametersAvailable(array('name','count','username','date'))){
					$tabname = $_POST['name']; 
					$count = $_POST['count'];
					$username = $_POST['username'];
					$date = $_POST['date'];

		

					$stmt = $conn->prepare("INSERT INTO tab_counts (tab_name, count, username, added_date) 
					VALUES (?,?,?,?)");
					$stmt->bind_param("ssss", $tabname ,$count ,$username ,$date );
					$stmt->execute();
					$stmt->store_result();
					
					
					if($stmt){
						$response['error'] = true;
						$response['message'] = 'Successful';
						$stmt->close();
					}
					
					
				}else{
					$response['error'] = true; 
					$response['message'] = 'required parameters are not available'; 
				}
				
			break;
			
			case 'fetch_count':
				
				$tabname = $_POST['tabname'];
				$username = $_POST['username'];
				
				$query = mysqli_query($conn,"SELECT * FROM tab_counts WHERE tab_name='$tabname'  AND username='$username' ");

				$details = array(); 

				while($row = mysqli_fetch_assoc($query)) {
   //echo    $row["price"];
				$temp = array();
				$temp['id'] = $row["id"];
				$temp['tab_name'] = $row["tab_name"];
				$temp['count'] = $row["count"];
				$temp['username'] = $row["username"];
				$temp['added_date'] = $row["added_date"];

				array_push($details, $temp);


				}
				echo json_encode($details);
			
			break;
			
			case 'show_description':
				
				$email = $_POST['email'];
				
				$query = mysqli_query($conn,"SELECT * FROM user_reg WHERE email='$email' ");

				$details = array(); 

				while($row = mysqli_fetch_assoc($query)) {
   //echo    $row["price"];
				$temp = array();
				$temp['id'] = $row["id"];
				$temp['des'] = $row["des"];

				array_push($details, $temp);


				}
				echo json_encode($details);
			
			break;
			
			case 'update_med':
				
				$scan_id = $_POST['scan_id'];
					$name = $_POST['name']; 
					$uses = $_POST['uses'];
					$manu = $_POST['manu'];
					$expiry = $_POST['expiry'];
					$compo = $_POST['compo'];
					$side = $_POST['side'];

				$mysqli_stmt = $conn->prepare("UPDATE medicine SET name=?,uses=?,compo=?,manufacture_date=?,expiry_date=?,side_effects=? WHERE scan_id = ?");
				$mysqli_stmt->bind_param("sssssss", $name,$uses,$compo, $manu,$expiry,$side,$scan_id);
				$mysqli_stmt->execute();
				$mysqli_stmt->store_result();
				$mysqli_stmt->fetch(); 
				//printf("Number of rows: %d.\n", $mysqli_stmt->num_rows);
 	
				if($mysqli_stmt)
				{	
					$response['error'] = true;
					$response['message'] = 'Successful';

				}else{
					$response['error'] = true; 
					$response['message'] = 'Error'; 
				}
			
			break;
			
			
			
			case 'show_med':
				
				$scan_id = $_POST['scan_id'];
				
				$query = mysqli_query($conn,"SELECT * FROM medicine WHERE scan_id='$scan_id' ");

				$response = array(); 

				while($row = mysqli_fetch_assoc($query)) {
   //echo    $row["price"];
				$temp = array();
				$temp['name'] = $row["name"];
				$temp['uses'] = $row["uses"];
				$temp['manufacture_date'] = $row["manufacture_date"];
				$temp['expiry_date'] = $row["expiry_date"];
				$temp['side_effects'] = $row["side_effects"];
				$temp['compo'] = $row["compo"];
				$temp['image'] = $row["image"];
				

				array_push($response, $temp);


				}
			//	echo json_encode($details);
			
			break;
			
			
			
			
			
			
			case 'image_upload':
			
			$scanid = $_POST['scanid'];
			$target_path = "uploads/";
			$response = array();
			$server_ip = gethostbyname(gethostname());
			$file_upload_url = 'http://' . $server_ip . '/' . 'QRMed' . '/' . $target_path;
			if (isset($_FILES['image']['name'])) {
	
				$target_path = $target_path . basename($_FILES['image']['name']);
				$response['file_name'] = basename($_FILES['image']['name']);
				$fn = basename($_FILES['image']['name']);
			try {
				$mysqli_stmt = $conn->prepare("UPDATE medicine SET image=? WHERE scan_id = ?");
				$mysqli_stmt->bind_param("ss", $fn,$scanid);
				$mysqli_stmt->execute();
				$mysqli_stmt->store_result();
				$mysqli_stmt->fetch();
			//throw exception if can't move the file
			if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
			$response['error'] = true;
			$response['message'] = 'Could not move the file!';
        
				}
			$response['message'] = 'File uploaded successfully!';
			$response['error'] = false;
			$response['file_path'] = '';
    
		} catch (Exception $e) {
			$response['error'] = true;
			$response['message'] = $e->getMessage();
			}
			} else {
			// File parameter is missing
				$response['error'] = true;
				$response['message'] = 'Not received any file!F';
			}
			
			break;
			
			
			
			case 'user_login':
				if(isTheseParametersAvailable(array('email','password'))){
					$email = $_POST['email'];
					$password = $_POST['password'];
					
					$stmt = $conn->prepare("SELECT id, name, email, password, number FROM user_reg WHERE email=? AND password=?;");
					$stmt->bind_param("ss", $email, $password);
					
					$stmt->execute();
					$stmt->store_result();
					if($stmt->num_rows > 0){
						$stmt->bind_result($id, $name, $email, $password, $number );
	
						$details = array(); 
	
						//traversing through all the result 
						while($stmt->fetch()){
						$temp = array();
						$temp['id'] = $id; 
						$temp['name'] = $name; 
						$temp['email'] = $email;
						$temp['password'] = $password;						
						$temp['number'] = $number;
						
						array_push($details, $temp);
						}
	
						//displaying the result in json format 
						echo json_encode($details);
					}else{
					
						$response['error'] = false; 
						$response['message'] = 'Invalid username or password';
					}
					
				}
				
				
			
			
			break;			
			
			
			default: 
				$response['error'] = true; 
				$response['message'] = 'Invalid Operation Called';
		}
		
		}else{
		$response['error'] = true; 
		$response['message'] = 'Invalid API Call';
	}
	
	echo json_encode($response);
	
	function isTheseParametersAvailable($params){
		
		foreach($params as $param){
			if(!isset($_POST[$param])){
				return false; 
			}
		}
		return true; 
	}
	?>
			
			