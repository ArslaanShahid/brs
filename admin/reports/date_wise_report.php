<?php
require_once "views/header.php";
require_once "../../models/Booking.php";
$from_date = $_POST['from_date'];
$to_date = $_POST['to_date'];

if($from_date > $to_date) {
    $_SESSION['error'] = "Invalid Date";
    header("Location:".BASE_URL."admin/report.php");
}
?>
<h4 class="text-center">Date Wise Booking Report</h4>
<br>
<table class="table table-striped">
    <thead>
        <tr>
            <td>Sr.No</td>
            <td>Name</td>
            <td>Contact No</td>
            <td>CNIC</td>
            <td>Gender</td>
            <td>Total Fare</td>
            <td>Date</td>
            <td>Seats</td>
        </tr>
    </thead>
    <?php
    $i = 1;
    $sum=0;
    $bookings = Booking::DateWiseBooking($from_date , $to_date);
    // echo("<pre>");
    // print_r($bookings);
    // echo('</pre>');
    // die;
    if(count($bookings) == 0) {
    echo("<tr><td colspan='7' class='text-center'>No Booking Found</td></tr>");
    }else foreach ($bookings as $booking) {
        $sum += $booking['total_fare'];
        echo ("<tr>");
        echo ("<td>" . $i . "</td>");
        echo ("<td>" . $booking['name'] . "</td>");
        echo ("<td>" . $booking['contact_no'] . "</td>");
        echo ("<td>" . $booking['cnic'] . "</td>");
        echo ("<td>" . $booking['gender'] . "</td>");
        echo ("<td>" . $booking['total_fare'] . "</td>");
        echo ("<td>" . $booking['date'] . "</td>");
        $i++;
        echo("<td>");
        foreach($booking['seats'] as $seat) {
            echo($seat['seat_no'].',&nbsp;');
        }
        echo("</td>");
    }
    echo("<tr><td colspan='14' class='h6 font-weight-bold'>Total Booking: $sum PKR </td></tr>");

    ?>
</table>

<?php
require_once "views/footer.php";
?>