<style>
	.logo {
    margin: auto;
    font-size: 20px;
    background: white;
    padding: 7px 11px;
    border-radius: 50% 50%;
    color: #000000b3;
}
</style>
<?php
include 'db_connect.php';
$qry = $conn->query("SELECT * from system_settings limit 1");
if($qry->num_rows > 0){
	foreach($qry->fetch_array() as $k => $val){
		$meta[$k] = $val;
	}
}
 ?>
<nav class="navbar navbar-light fixed-top bg-white">
  <div class="container-fluid mt-2 mb-2">
  	<div class="col-lg-12">
  		<div class="col-md-1 float-left" style="display: flex;">
  		
  		</div>
      <div class="col-md-2 float-left text-dark">
        <!-- <large><b><?php echo isset($_SESSION['system']['name']) ? $_SESSION['system']['name'] : '' ?></b></large> -->
         <img src="<?php echo isset($meta['cover_img']) ? 'assets/uploads/'.$meta['cover_img'] :'' ?>" alt="" id="cimg" style="width: 10rem; height: 4rem;" width="10rem" height="4rem">
      </div>
       <div class="col-md-8 float-left text-dark mt-3">
      </div>
      <div id="google_translate_element"></div>
	  	<div class="float-right mt-3">
        <div class=" dropdown mr-4">
            <a href="#" class="text-dark dropdown-toggle"  id="account_settings" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $_SESSION['login_name'] ?> </a>
              <div class="dropdown-menu" aria-labelledby="account_settings">
                <a class="dropdown-item" href="javascript:void(0)" id="manage_my_account"><i class="fa fa-cog"></i> Manage Account</a>
                <a class="dropdown-item" href="ajax.php?action=logout"><i class="fa fa-power-off"></i> Logout</a>
              </div>
        </div>
      </div>
  </div>
  
</nav>
<style>
  .VIpgJd-ZVi9od-ORHb-OEVmcd .skiptranslate .VIpgJd-ZVi9od-ORHb{
    display: none !important;
  }
   .VIpgJd-ZVi9od-ORHb-OEVmcd {

    display: none !important;
  }
  .VIpgJd-ZVi9od-ORHb{
    display: none !important;
  }
 
</style>
<script>
  $('#manage_my_account').click(function(){
    uni_modal("Manage Account","manage_user.php?id=<?php echo $_SESSION['login_id'] ?>&mtype=own")
  })
</script>