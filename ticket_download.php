<?php
require_once('init.php');
require_once('views/header.php');
require_once('models/user.php');
require_once 'models/Booking.php';
$result = Booking::PrintTicket($_GET['unique_id']);
$date =date('Y-m-d',strtotime($result['booking']->date));
// echo("<pre>");
// print_r($result);
// die;
// echo ("</pre>");

?>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<h1 class="align-center"></h1>
<div class="row">
  <div class="col-md-8 offset-2">
    <div class="card mb-3">
      <div class="card-header text-center bg-primary">
        <center><img src="<?php echo(BASE_URL);?>assets/images/logo/logo.png" alt="logo"></center>
        <h3>Smart BRS Ticket</h3>
      </div>
      <div class="card-body text-success">
        <h3 class="card-title">Ticket Route: <span class="text-danger"><?php echo ($result['booking']->departure . " To " . $result['booking']->arrival); ?></span></h3>
        <h4><strong>Ticket No:</strong> <?php echo ($result['booking']->Ticket_No); ?></h4>
        <h4><strong>Name:</strong> <?php echo ($result['booking']->customer); ?></h4>
        <h4><strong>Bus No:</strong><?php echo ($result['booking']->bus_no);?>
        <h4><strong>Seat No:</strong>
        <?php
            foreach($result['seats'] as $seat)
            {
              echo($seat->seat_no.",");
            }
        ?>
        </h4>
        <h4><strong>Date:</strong> <?php echo $date ?></h4>
        
        <h4><strong>Departure Time:</strong> <?php echo ($result['booking']->departure_time); ?></h4>
      </div>
      <div class="card-footer bg-disabled"><strong class="text-danger">Note:</strong> This Ticket is Generated by System Error May Be Expected.<br>
      
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-12">
    <div class="d-flex justify-content-center">
      <input class="btn btn-primary" value="Print Ticket" type="button" onClick="window.print()">
    </div>
  </div>
</div>
   
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<?php
require_once('views/footer.php');
?>
