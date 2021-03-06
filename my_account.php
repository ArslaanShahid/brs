<?php
// session_start();
require_once('init.php');
require_once('views/header.php');
if (isset($_SESSION['obj_user'])) {
    $obj_user = unserialize($_SESSION['obj_user']);
} else {
    $obj_user = new User();
}
$obj_user->profile();


?>
<body class="body-class bc blog">
<section id="breadcrumb">
    <div class="overly"></div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10 text-center">
                <div class="breadcrumbinfo">
                    <article>
                        <h2>My Account</h2>
                        <a href="index.php">Home</a> <span>/</span>
                        <a class="active" href="<?php echo (BASE_URL); ?>login.php">My Account</a>
                    </article>
                </div>
            </div>
        </div>
    </div>
</section>


        <section id="choose-us">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-7 col-md-10 text-center">
                        <h2 class="text-center">
                            User Profile Information
                        </h2>
                        <br>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-lg-6 col-md-6">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <tr class="">
                                    <td colspan="2">
                                        <center class="font-weight-bold">My Account Data</center>
                                    </td>
                                </tr>
                                <tr>
                                    <td>First Name</td>
                                    <td>
                                        <?php
                                        if ($obj_user->first_name == "") {
                                            echo ("N/A");
                                        } else {
                                            echo ($obj_user->first_name);
                                        }
                                        ?>
                                    </td>
                                </tr>

                                <td>Last Name</td>
                                <td>
                                    <?php
                                    if ($obj_user->last_name == "") {
                                        echo ("N/A");
                                    } else {
                                        echo ($obj_user->last_name);
                                    }
                                    ?>
                                </td>
                                </tr>
                                <tr>
                                    <td>Contact No</td>
                                    <td>
                                        <?php
                                        if ($obj_user->mobile_no == "") {
                                            echo ("N/A");
                                        } else {
                                            echo ($obj_user->mobile_no);
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Email</td>
                                    <td>
                                        <?php
                                        if ($obj_user->email == "") {
                                            echo ("N/A");
                                        } else {
                                            echo ($obj_user->email);
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>CNIC</td>
                                    <td>
                                        <?php
                                        if ($obj_user->cnic == "") {
                                            echo ("N/A");
                                        } else {
                                            echo ($obj_user->cnic);
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Gender</td>
                                    <td>
                                        <?php
                                        if ($obj_user->gender == "") {
                                            echo ("N/A");
                                        } else {
                                            echo ($obj_user->gender);
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Date Of Birth</td>
                                    <td>
                                        <?php
                                        if ($obj_user->date_of_birth == "") {
                                            echo ("N/A");
                                        } else {
                                            echo ($obj_user->date_of_birth);
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>State</td>
                                    <td>
                                        <?php
                                        if ($obj_user->state_name == "") {
                                            echo ("N/A");
                                        } else {
                                            echo ($obj_user->state_name);
                                        }
                                        ?>
                                    </td>
                                </tr>


                                <tr>
                                    <td>City</td>
                                    <td>
                                        <?php
                                        if ($obj_user->city_name == "") {
                                            echo ("N/A");
                                        } else {
                                            echo ($obj_user->city_name);
                                        }
                                        ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <center><a href="<?php echo (BASE_URL); ?>change_password.php" class="btn btn-danger fa fa-repeat"> Reset Password</a></center>
                                    </td>
                                    <td>
                                        <center><a href="<?php echo (BASE_URL); ?>update_account.php" class="btn btn-success fa fa-user "> Update Account</a></center>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    

<?php

require_once('views/footer.php')

?>