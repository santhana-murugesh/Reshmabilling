<?php
session_start();
ini_set('display_errors', 1);
Class Action {
	private $db;

	public function __construct() {
		ob_start();
   	include 'db_connect.php';
    
    $this->db = $conn;
	}
	function __destruct() {
	    $this->db->close();
	    ob_end_flush();
	}

	function login(){
		extract($_POST);		
		$qry = $this->db->query("SELECT * FROM users where username = '".$username."' and password = '".md5($password)."' ");
		if($qry->num_rows > 0){
			foreach ($qry->fetch_array() as $key => $value) {
				if($key != 'passwors' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
				return 1;
		}else{
			return 3;
		}
	}
	function login2(){
		
		extract($_POST);		
		$qry = $this->db->query("SELECT * FROM complainants where email = '".$email."' and password = '".md5($password)."' ");
		if($qry->num_rows > 0){
			foreach ($qry->fetch_array() as $key => $value) {
				if($key != 'passwors' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
				return 1;
		}else{
			return 3;
		}
	}
	function logout(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:login.php");
	}
	function logout2(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:../index.php");
	}

	public function save_user(){
    extract($_POST);

    // Fallback/defaults
    $type = isset($type) && $type !== '' ? (int)$type : 1;

    $data = " name = '$name' ";
    $data .= ", username = '$username' ";
    $data .= ", type = $type ";

    if (!empty($password)) {
        $data .= ", password = md5('$password') ";
    }

    if(empty($id)){
        $save = $this->db->query("INSERT INTO users SET $data");
    } else {
        $save = $this->db->query("UPDATE users SET $data WHERE id = $id");
    }

    if($save){
        return 1;
    }

    return 0;
}

	function delete_user(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM users where id = ".$id);
		if($delete)
			return 1;
	}
	function signup(){
		extract($_POST);
		$data = " name = '$name' ";
		$data .= ", email = '$email' ";
		$data .= ", address = '$address' ";
		$data .= ", contact = '$contact' ";
		$data .= ", password = '".md5($password)."' ";
		$chk = $this->db->query("SELECT * from complainants where email ='$email' ".(!empty($id) ? " and id != '$id' " : ''))->num_rows;
		if($chk > 0){
			return 3;
			exit;
		}
		if(empty($id))
			$save = $this->db->query("INSERT INTO complainants set $data");
		else
			$save = $this->db->query("UPDATE complainants set $data where id=$id ");
		if($save){
			if(empty($id))
				$id = $this->db->insert_id;
				$qry = $this->db->query("SELECT * FROM complainants where id = $id ");
				if($qry->num_rows > 0){
					foreach ($qry->fetch_array() as $key => $value) {
						if($key != 'password' && !is_numeric($key))
							$_SESSION['login_'.$key] = $value;
					}
						return 1;
				}else{
					return 3;
				}
		}
	}
	function update_account(){
		extract($_POST);
		$data = " name = '".$firstname.' '.$lastname."' ";
		$data .= ", username = '$email' ";
		if(!empty($password))
		$data .= ", password = '".md5($password)."' ";
		$chk = $this->db->query("SELECT * FROM users where username = '$email' and id != '{$_SESSION['login_id']}' ")->num_rows;
		if($chk > 0){
			return 2;
			exit;
		}
			$save = $this->db->query("UPDATE users set $data where id = '{$_SESSION['login_id']}' ");
		if($save){
			$data = '';
			foreach($_POST as $k => $v){
				if($k =='password')
					continue;
				if(empty($data) && !is_numeric($k) )
					$data = " $k = '$v' ";
				else
					$data .= ", $k = '$v' ";
			}
			if($_FILES['img']['tmp_name'] != ''){
							$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
							$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
							$data .= ", avatar = '$fname' ";

			}
			$save_alumni = $this->db->query("UPDATE alumnus_bio set $data where id = '{$_SESSION['bio']['id']}' ");
			if($data){
				foreach ($_SESSION as $key => $value) {
					unset($_SESSION[$key]);
				}
				$login = $this->login2();
				if($login)
				return 1;
			}
		}
	}

	function save_settings(){
		extract($_POST);
		$data = " name = '".str_replace("'","&#x2019;",$name)."' ";
		$data .= ", email = '$email' ";
		$data .= ", contact = '$contact' ";
		$data .= ", about_content = '".htmlentities(str_replace("'","&#x2019;",$about))."' ";
		if($_FILES['img']['tmp_name'] != ''){
						$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
						$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
					$data .= ", cover_img = '$fname' ";

		}
		
		// echo "INSERT INTO system_settings set ".$data;
		$chk = $this->db->query("SELECT * FROM system_settings");
		if($chk->num_rows > 0){
			$save = $this->db->query("UPDATE system_settings set ".$data);
		}else{
			$save = $this->db->query("INSERT INTO system_settings set ".$data);
		}
		if($save){
		$query = $this->db->query("SELECT * FROM system_settings limit 1")->fetch_array();
		foreach ($query as $key => $value) {
			if(!is_numeric($key))
				$_SESSION['system'][$key] = $value;
		}

			return 1;
				}
	}
	function save_category(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id')) && !is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		$check = $this->db->query("SELECT * FROM categories where name ='$name' ".(!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if($check > 0){
			return 2;
			exit;
		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO categories set $data");
		}else{
			$save = $this->db->query("UPDATE categories set $data where id = $id");
		}

		if($save)
			return 1;
	}
	function delete_category(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM categories where id = ".$id);
		if($delete){
			return 1;
		}
	}
	function save_product(){
	extract($_POST);
	$data = "";
	foreach($_POST as $k => $v){
		if(!in_array($k, array('id','status')) && !is_numeric($k)){
			if($k == 'price'){
				$v= str_replace(',', '', $v);
			}
			if(empty($data)){
				$data .= " $k='" . addslashes($v) . "' ";
			}else{
				$data .= ", $k='" . addslashes($v) . "' ";
			}
		}
	}

	// ✅ Handle status
	if(isset($status)){
		$data .= ", status=1 ";
	}else{
		$data .= ", status=0 ";
	}

	// ✅ Handle image upload
	if(isset($_FILES['product_image']) && $_FILES['product_image']['tmp_name'] != ''){
		$folder = 'uploads/products/';
		if (!is_dir($folder)) {
			mkdir($folder, 0777, true);
		}
		$filename = time() . '_' . basename($_FILES['product_image']['name']);
		$filepath = $folder . $filename;

		if(move_uploaded_file($_FILES['product_image']['tmp_name'], $filepath)){
			$data .= ", image_path = '$filename' ";
		}
	}

	// ✅ Check duplicate name
	$check = $this->db->query("SELECT * FROM products WHERE name ='$name' ".(!empty($id) ? " AND id != {$id} " : ''))->num_rows;
	if($check > 0){
		return 2;
		exit;
	}

	// ✅ Insert or update
	if(empty($id)){
		$save = $this->db->query("INSERT INTO products SET $data");
	}else{
		$save = $this->db->query("UPDATE products SET $data WHERE id = $id");
	}

	if($save)
		return 1;
}

	function delete_product(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM products where id = ".$id);
		if($delete){
			return 1;
		}
	}
	function save_order(){
		extract($_POST);
		$data = " total_amount = '$total_amount' ";
		$data .= ", amount_tendered = '$total_tendered' ";
		$data .= ", order_number = '$order_number' ";
		if(empty($id)){
			$i = 0;
			while($i == 0){
				$ref_no  = mt_rand(1,999999999999);
				$ref_no = sprintf("%'012d", $ref_no);
				$chk = $this->db->query("SELECT * FROM orders where ref_no ='$ref_no' ");
				if($chk->num_rows <= 0){
					$i = 1;
				}
			}
			$data .= ", ref_no = '$ref_no' ";
			$save = $this->db->query("INSERT INTO orders set $data");
			if($save){
				$id = $this->db->insert_id;
			}
		}else{
			$save = $this->db->query("UPDATE orders set $data where id = $id");
		}
		if($save){
			$ids = array_filter($item_id);
			$ids = implode(',',$ids);
			if(!empty($ids))
			$this->db->query("DELETE FROM order_items where order_id = $id and id not ($ids) ");
		foreach($item_id as $k=>$v){
			$data = " order_id = $id ";
			$data .= ", product_id = '{$product_id[$k]}' ";
			$data .= ", qty = '{$qty[$k]}' ";
			$data .= ", price = '{$price[$k]}' ";
			$data .= ", amount = '{$amount[$k]}' ";
			if(empty($v)){
				$this->db->query("INSERT INTO order_items set $data");
			}else{
				$this->db->query("UPDATE order_items set $data where id = $v");
			}
		}
		return $id;
		}
		
	}
	function delete_order(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM orders where id = ".$id);
		$delete2 = $this->db->query("DELETE FROM order_items where order_id = ".$id);
		if($delete){
			return 1;
		}
	}
}
