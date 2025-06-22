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
include './db_connect.php';

// Fetch system settings if not already in session
if (!isset($_SESSION['system']['name'])) {
    $settings_qry = $conn->query("SELECT * FROM system_settings LIMIT 1");
    if ($settings_qry->num_rows > 0) {
        $_SESSION['system'] = $settings_qry->fetch_assoc();
    } else {
        // Default values if no settings found
        $_SESSION['system'] = array('name' => 'Default System Name', 'short_name' => 'DSN');
    }
}
?>
<nav class="navbar navbar-light fixed-top bg-white">
    <div class="container-fluid mt-2 mb-2">
        <div class="col-lg-12">
            <div class="col-md-1 float-left" style="display: flex;">
                <!-- Logo or icon can go here -->
            </div>
            
            <div class="col-md-2 float-left text-dark">
                <!-- Display system name from session -->
                <large><b><?php echo htmlspecialchars($_SESSION['system']['name']) ?></b></large>
                <!-- <img src="../assets/uploads/logo.png" width="200px"> -->
            </div>
            <div class="col-md-8 float-left text-dark mt-3">
                <!-- Additional content can go here -->
            </div>
            <div id="google_translate_element"></div>
            <div class="float-right mt-3">
                <div class="dropdown mr-4">
                    <a href="#" class="text-dark dropdown-toggle" id="account_settings" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php echo htmlspecialchars($_SESSION['login_name']) ?> 
                    </a>
                    <div class="dropdown-menu" aria-labelledby="account_settings">
                        <a class="dropdown-item" href="../ajax.php?action=logout"><i class="fa fa-power-off"></i> Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<style>
    .VIpgJd-ZVi9od-ORHb-OEVmcd .skiptranslate .VIpgJd-ZVi9od-ORHb {
        display: none !important;
    }
    .VIpgJd-ZVi9od-ORHb-OEVmcd {
        display: none !important;
    }
    .VIpgJd-ZVi9od-ORHb {
        display: none !important;
    }
</style>

<script>
    $('#manage_my_account').click(function() {
        uni_modal("Manage Account", "manage_user.php?id=<?php echo $_SESSION['login_id'] ?>&mtype=own");
    });
</script>