<?php
require_once "DbTrait.php";
class Booking
{
    use DbTrait;
    public static function store($data)
    {
        // echo("<pre>");
        // print_r($data);
        // echo ("</pre>");
        // die;
        extract($data);
        $obj_db = self::obj_db();
        $seats = preg_split('/,/', $seat_number);

        $unique_ticket_id = substr(str_shuffle("0123456789"), 5, 5);
        $query_booking = "INSERT INTO bookings "
            . "(`id`,`date`,`route_id`,`total_fare`,`name`,`contact_no`,`cnic`,`gender`,`unique_ticket_id`) "
            . " values "
            . " (NULL,'{$booking_date}','{$route_id}',{$total_fare},'{$name}','{$contact_no}','{$cnic}','{$gender}' ,'{$unique_ticket_id}')";
        $obj_db->query($query_booking);
        if ($obj_db->errno) {
            die($obj_db->error);
        }
        $response = [];
        $booking_id = $obj_db->insert_id;
        $response['booking_id'] = $booking_id;
        $response['ticket_id'] = $unique_ticket_id;
        for ($i = 0; $i < sizeof($seats); $i++) {
            if (!empty($seats[$i])) {
                $query_seat_booking = " INSERT INTO booked_seats "
                    . " (`id`,`seat_no`,`booking_id`) "
                    . " values "
                    . " (NULL,$seats[$i],$booking_id) ";
                $obj_db->query($query_seat_booking);
                if ($obj_db->errno) {
                    die($obj_db->error);
                }
            }
        }
            $username = "923237553458";///Your Username
            $password = "ripazha1@";///Your Password
            $mobile = "$contact_no";///Recepient Mobile Number
            $sender = "SmartBRs";
            $message = "Greetings, Welcome To Smart BRS .$name. Thanks For Choosing Our Services here is the Ticket ID .$unique_ticket_id. You can Download the Ticket From Our Website or Android App. Thank You.";
            ////sending sms
            $url = "https://sendpk.com/api/sms.php?username=".$username."&password=".$password."&mobile=".$mobile."&sender=".urlencode($sender)."&message=".urlencode($message)."&format=json";
            $ch = curl_init();
            $timeout = 30; // set to zero for no timeout
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)');
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_POST, 1);
            //
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $result = curl_exec($ch); 
            /*Print Responce*/
            return $response;
            echo $result; 

    }

    public static function showBooking()
    {
        $obj_db = self::obj_db();
        $query = " SELECT * FROM bookings ";
        $result = $obj_db->query($query);
        if ($obj_db->errno) {
            throw new Exception("db Select Error" . $obj_db->errno . $obj_db->error);
        }
        $query = [];
        while ($data = $result->fetch_object()) {
            $query[] = $data;
        }
        return $query;
    }

    public static function getSeats($id)
    {
        $obj_db = self::obj_db();
        $query = " SELECT b.name, b.id, b.route_id, r.fare, bs.seat_no, bs.booking_id FROM bookings b"
            . " JOIN routes r ON b.route_id = r.id"
            . " JOIN booked_seats bs ON b.id = bs.booking_id"
            . " WHERE b.id = '$id'";

        $result = $obj_db->query($query);
        if ($obj_db->errno) {
            throw new Exception("db Select Error" . $obj_db->errno . $obj_db->error);
        }
        $query = [];

        while ($data = $result->fetch_object()) {
            $query[] = $data;
        }
        $response = [];

        $response['data'] = $query;
        $response['success'] = true;
        // echo ("<pre>");
        // print_r($query);
        // echo ("</pre>");
        // die;
        return $response;
    }

    public static function getRoute($id)
    {
        $obj_db = self::obj_db();
        $query = "SELECT r.id, cd.name as departure, ca.name as arrival,"
            . " r.fare, r.duration, r.departure_time, r.distance, b.seats, b.bus_no, d.day"
            . " from routes r"
            . " JOIN cities cd ON (cd.id = r.departure)"
            . " JOIN cities ca ON (ca.id = r.arrival)"
            . " JOIN buses b ON b.id = r.bus_id"
            . " JOIN days d ON d.id = r.day"
            . " WHERE r.id = '$id'";

        $result = $obj_db->query($query);
        if ($obj_db->errno) {
            throw new Exception("db Select Error" . $obj_db->errno . $obj_db->error);
        }
        $response = [];
        $response['data'] = $result->fetch_object();
        $response['success'] = true;

        return $response;
    }
    public static function dailyBooking()
    {
        $current = date('Y-m-d');
        // die($current);
        $obj_db = self::obj_db();
        $query = " SELECT date, ba.bus_no as bus, b.id, b.name , b.gender , b.cnic ,b.contact_no, b.total_fare,  b.date, r.departure_time, cd.name as departure, ca.name as arrival FROM bookings b  "
        . "JOIN routes r ON r.id = b.route_id "
        . "JOIN cities cd ON (cd.id = r.departure) "
        . "JOIN cities ca ON (ca.id = r.arrival) "
        . "JOIN buses ba ON (ba.id = r.bus_id) "
        . "WHERE date ='$current'";
        $result = $obj_db->query($query);
        if ($obj_db->errno) {
            throw new Exception("db Select Error" . $obj_db->errno . $obj_db->error);
        }
        $result = $obj_db->query($query);
        if ($obj_db->errno) {
            throw new Exception("db Select Error" . $obj_db->errno . $obj_db->error);
        }
        $bookings = [];
        while ($data = $result->fetch_object()) {
            $rows = [];
            $rows['name'] = $data->name;
            $rows['contact_no'] = $data->contact_no;
            $rows['cnic'] = $data->cnic;
            $rows['gender'] = $data->gender;
            $rows['total_fare'] = $data->total_fare;
            $rows['date'] = $data->date;
            $rows['arrival'] = $data->arrival;
            $rows['departure'] = $data->departure;
            $query_seat = "SELECT * from booked_seats "
                . "where booking_id = '$data->id'";
            $res = $obj_db->query($query_seat);

            $seats = [];
            while ($d = $res->fetch_object()) {
                $seat_rows = [];
                $seat_rows['seat_no'] = $d->seat_no;
                $seats[] = $seat_rows;
            }

            $rows['seats'] = $seats;
            $bookings[] = $rows;
        }
        return $bookings;
    }

    public static function weeklyBooking()
    {
        $obj_db = self::obj_db();
        $monday = strtotime("last monday");
        $monday = date('w', $monday) == date('w') ? $monday + 7 * 86400 : $monday;
        $sunday = strtotime(date("Y-m-d H:i:s", $monday) . " +6 days");
        $start_date = date("Y-m-d H:i:s", $monday);
        $end_date = date("Y-m-d H:i:s", $sunday);

        $query = " SELECT date, ba.bus_no as bus, b.id, b.name , b.gender , b.cnic ,b.contact_no, b.total_fare,  b.date, r.departure_time, cd.name as departure, ca.name as arrival FROM bookings b  "
        . "JOIN routes r ON r.id = b.route_id "
        . "JOIN cities cd ON (cd.id = r.departure) "
        . "JOIN cities ca ON (ca.id = r.arrival) "
        . "JOIN buses ba ON (ba.id = r.bus_id) "
        . " where date between '$start_date' AND '$end_date' ";
        $result = $obj_db->query($query);
        if ($obj_db->errno) {
            throw new Exception("db Select Error" . $obj_db->errno . $obj_db->error);
        }
        // print_r($result);
        // die;
        $bookings = [];
        while ($data = $result->fetch_object()) {
            $rows = [];
            $rows['name'] = $data->name;
            $rows['contact_no'] = $data->contact_no;
            $rows['cnic'] = $data->cnic;
            $rows['gender'] = $data->gender;
            $rows['total_fare'] = $data->total_fare;
            $rows['date'] = $data->date;
            $rows['arrival'] = $data->arrival;
            $rows['departure'] = $data->departure;

            $query_seat = "SELECT * from booked_seats "
                . "where booking_id = '$data->id'";
            $res = $obj_db->query($query_seat);

            $seats = [];
            while ($d = $res->fetch_object()) {
                $seat_rows = [];
                $seat_rows['seat_no'] = $d->seat_no;
                $seats[] = $seat_rows;
            }

            $rows['seats'] = $seats;
            $bookings[] = $rows;
        }
        return $bookings;
    }

    public static function bookingHistory($cnic)
    {
        $obj_db = self::obj_db();
        $query = " SELECT b.cancel_status, b.name , b.gender , b.cnic ,b.contact_no, b.total_fare,  b.date, r.departure_time, cd.name as departure, ca.name as arrival FROM bookings b  "
            . "JOIN routes r ON r.id = b.route_id "
            . "JOIN cities cd ON (cd.id = r.departure) "
            . "JOIN cities ca ON (ca.id = r.arrival) "
            . "WHERE cnic = '$cnic' ";
        $result = $obj_db->query($query);
        if ($obj_db->errno) {
            throw new Exception("db Select Error" . $obj_db->errno . $obj_db->error);
        }
        $query = [];
        while ($data = $result->fetch_object()) {
            $query[] = $data;
        }
        return $query;
    }

    public static function getTicketInfo($booking_id)
    {
        $obj_db = self::obj_db();
        $query = "SELECT b.name as customer, b.unique_ticket_id as Ticket_No ,bs.bus_no ,r.bus_id, b.date, r.departure_time, cd.name as departure, ca.name as arrival FROM bookings b "
            . "JOIN routes r ON r.id = b.route_id "
            . "JOIN cities cd ON (cd.id = r.departure) "
            . "JOIN cities ca ON (ca.id = r.arrival) "
            . "JOIN buses bs ON (bs.id = r.bus_id) "
            . "WHERE b.id = '$booking_id'";

        $booking = $obj_db->query($query);
        if ($obj_db->errno) {
            throw new Exception("db Select Error" . $obj_db->errno . $obj_db->error);
        }

        $booking_info = $booking->fetch_object();

        $query = "SELECT seat_no from booked_seats "
            . "WHERE booking_id = '$booking_id'";

        $seat = $obj_db->query($query);
        if ($obj_db->errno) {
            throw new Exception("db Select Error" . $obj_db->errno . $obj_db->error);
        }

        $seats = [];
        while ($data = $seat->fetch_object()) {
            $seats[] = $data;
        }

        $ticket = [];
        $ticket['booking'] = $booking_info;
        $ticket['seats'] = $seats;

        return $ticket;
    }
    public static function count_bookings()
    {
        $obj_db = self::obj_db();
        $query = "SELECT * FROM bookings b Where b.cancel_status =0";
        $result = $obj_db->query($query);
        $count = mysqli_num_rows($result);
        return $count;
    }
    public static function count_cancelBookings()
    {
        $obj_db = self::obj_db();
        $query = "SELECT * FROM bookings b Where b.cancel_status =1";
        $result = $obj_db->query($query);
        $count = mysqli_num_rows($result);
        return $count;
    }
    public static function count_Booked()
    {
        $obj_db = self::obj_db();
        $query = "SELECT * FROM bookings";
        $result = $obj_db->query($query);
        $count = mysqli_num_rows($result);
        return $count;
    }
    public static function Daily_Earning()
    {
        $current = date('Y-m-d');
        $obj_db = self::obj_db();
        $query = "SELECT SUM(total_fare)as daily FROM bookings where date = '$current'";
        $result = $obj_db->query($query);
        $row = mysqli_fetch_array($result);
        $sum = $row['daily'];
        return $sum;
        // die($query);
        // $row = mysqli_fetch_array($query);
        // die($row);
        // $sum = $row['daily_earning'];
        // return $sum;
        // print_r($sum);
    }
    public static function Weekly_Earning()
    {
        $obj_db = self::obj_db();
        $monday = strtotime("last monday");
        $monday = date('w', $monday) == date('w') ? $monday + 7 * 86400 : $monday;
        $sunday = strtotime(date("Y-m-d", $monday) . " +6 days");
        $start_date = date("Y-m-d", $monday);
        $end_date = date("Y-m-d", $sunday);
        $obj_db = self::obj_db();
        $query = "SELECT SUM(total_fare) as weekly_earning FROM bookings where date between '$start_date' AND '$end_date'";
        $query = $obj_db->query($query);
        $row = mysqli_fetch_array($query);
        $sum = $row['weekly_earning'];
        return $sum;
    }
    public static function AllTime_Earning()
    {
        $obj_db = self::obj_db();
        $query = "SELECT SUM(total_fare) as earning FROM bookings";
        $query = $obj_db->query($query);
        $row = mysqli_fetch_array($query);
        $sum = $row['earning'];
        return $sum;
    }

    public static function monthlyBooking()
    {
        $obj_db = self::obj_db();
        $first_date = date('Y-m-01');
        $last_date = date('Y-m-t');

        $query = " SELECT * FROM bookings where date between "
            . " '$first_date' AND '$last_date' ";
        $result = $obj_db->query($query);
        if ($obj_db->errno) {
            throw new Exception("db Select Error" . $obj_db->errno . $obj_db->error);
        }
        // print_r($result);
        // die;
        $bookings = [];
        while ($data = $result->fetch_object()) {
            $rows = [];
            $rows['name'] = $data->name;
            $rows['contact_no'] = $data->contact_no;
            $rows['cnic'] = $data->cnic;
            $rows['gender'] = $data->gender;
            $rows['total_fare'] = $data->total_fare;
            $rows['date'] = $data->date;

            $query_seat = "SELECT * from booked_seats "
                . "where booking_id = '$data->id'";
            $res = $obj_db->query($query_seat);

            $seats = [];
            while ($d = $res->fetch_object()) {
                $seat_rows = [];
                $seat_rows['seat_no'] = $d->seat_no;
                $seats[] = $seat_rows;
            }

            $rows['seats'] = $seats;
            $bookings[] = $rows;
        }
        return $bookings;
    }
    public static function DateWiseBooking($from_date, $to_date)
    {
        $obj_db = self::obj_db();

        $query = " SELECT * FROM bookings where date between "
            . " '$from_date' AND '$to_date' ";
        $result = $obj_db->query($query);
        if ($obj_db->errno) {
            throw new Exception("db Select Error" . $obj_db->errno . $obj_db->error);
        }
        // print_r($result);
        // die;
        $bookings = [];
        while ($data = $result->fetch_object()) {
            $rows = [];
            $rows['name'] = $data->name;
            $rows['contact_no'] = $data->contact_no;
            $rows['cnic'] = $data->cnic;
            $rows['gender'] = $data->gender;
            $rows['total_fare'] = $data->total_fare;
            $rows['date'] = $data->date;

            $query_seat = "SELECT * from booked_seats "
                . "where booking_id = '$data->id'";
            $res = $obj_db->query($query_seat);

            $seats = [];
            while ($d = $res->fetch_object()) {
                $seat_rows = [];
                $seat_rows['seat_no'] = $d->seat_no;
                $seats[] = $seat_rows;
            }

            $rows['seats'] = $seats;
            $bookings[] = $rows;
        }
        return $bookings;
    }
    public static function CancelBooking()
    {
        $obj_db = self::obj_db();
        $query = " SELECT b.cancel_status, ba.bus_no as bus, b.id, b.name , b.gender , b.cnic ,b.contact_no, b.total_fare,  b.date, r.departure_time, cd.name as departure, ca.name as arrival FROM bookings b  "
            . "JOIN routes r ON r.id = b.route_id "
            . "JOIN cities cd ON (cd.id = r.departure) "
            . "JOIN cities ca ON (ca.id = r.arrival) "
            . "JOIN buses ba ON (ba.id = r.bus_id) "
            . "WHERE b.cancel_status = 1 ";
        $result = $obj_db->query($query);
        if ($obj_db->errno) {
            throw new Exception("db Select Error" . $obj_db->errno . $obj_db->error);
        }
        $bookings = [];
        while ($data = $result->fetch_object()) {
            $rows = [];
            $rows['name'] = $data->name;
            $rows['contact_no'] = $data->contact_no;
            $rows['cnic'] = $data->cnic;
            $rows['gender'] = $data->gender;
            $rows['total_fare'] = $data->total_fare;
            $rows['date'] = $data->date;
            $rows['departure'] = $data->departure;
            $rows['arrival'] = $data->arrival;
            $rows['bus'] = $data->bus;
            $rows['departure_time'] = $data->departure_time;
            $query_seat = "SELECT * from booked_seats "
                . "where booking_id = '$data->id'";
            $res = $obj_db->query($query_seat);

            $seats = [];
            while ($d = $res->fetch_object()) {
                $seat_rows = [];
                $seat_rows['seat_no'] = $d->seat_no;
                $seats[] = $seat_rows;
            }

            $rows['seats'] = $seats;
            $bookings[] = $rows;
        }
        return $bookings;
    }
    public static function DateWiseCancelBooking($from_date, $to_date)
    {
        $obj_db = self::obj_db();
        $query = " SELECT date, b.cancel_status, ba.bus_no as bus, b.id, b.name , b.gender , b.cnic ,b.contact_no, b.total_fare,  b.date, r.departure_time, cd.name as departure, ca.name as arrival FROM bookings b  "
            . "JOIN routes r ON r.id = b.route_id "
            . "JOIN cities cd ON (cd.id = r.departure) "
            . "JOIN cities ca ON (ca.id = r.arrival) "
            . "JOIN buses ba ON (ba.id = r.bus_id) "
            . "WHERE b.cancel_status = 1 AND date BETWEEN '$from_date' AND '$to_date'";
        $result = $obj_db->query($query);
        if ($obj_db->errno) {
            throw new Exception("db Select Error" . $obj_db->errno . $obj_db->error);
        }
        $bookings = [];
        while ($data = $result->fetch_object()) {
            $rows = [];
            $rows['name'] = $data->name;
            $rows['contact_no'] = $data->contact_no;
            $rows['cnic'] = $data->cnic;
            $rows['gender'] = $data->gender;
            $rows['total_fare'] = $data->total_fare;
            $rows['date'] = $data->date;
            $rows['departure'] = $data->departure;
            $rows['arrival'] = $data->arrival;
            $rows['bus'] = $data->bus;
            $rows['departure_time'] = $data->departure_time;
            $query_seat = "SELECT * from booked_seats "
                . "where booking_id = '$data->id'";
            $res = $obj_db->query($query_seat);

            $seats = [];
            while ($d = $res->fetch_object()) {
                $seat_rows = [];
                $seat_rows['seat_no'] = $d->seat_no;
                $seats[] = $seat_rows;
            }

            $rows['seats'] = $seats;
            $bookings[] = $rows;
        }
        return $bookings;
    }
    public static function PrintTicket($unique_id)
    {
        $obj_db = self::obj_db();
        $query = "SELECT b.name as customer, b.id as booking_id, b.unique_ticket_id as Ticket_No ,bs.bus_no ,r.bus_id, b.date, r.departure_time, cd.name as departure, ca.name as arrival FROM bookings b "
            . "JOIN routes r ON r.id = b.route_id "
            . "JOIN cities cd ON (cd.id = r.departure) "
            . "JOIN cities ca ON (ca.id = r.arrival) "
            . "JOIN buses bs ON (bs.id = r.bus_id) "
            . "WHERE b.unique_ticket_id = '$unique_id'  ";

        $booking = $obj_db->query($query);
        if ($obj_db->errno) {
            throw new Exception("db Select Error" . $obj_db->errno . $obj_db->error);
        }

        $booking_info = $booking->fetch_object();
        // echo ("<pre>");
        // print_r($booking);
        // echo ("</pre>");
        // die;
        $query = "SELECT seat_no from booked_seats  "
            . "WHERE booking_id = '$booking_info->booking_id'";
        $seat = $obj_db->query($query);
        if ($obj_db->errno) {
            throw new Exception("db Select Error" . $obj_db->errno . $obj_db->error);
        }

        $seats = [];
        while ($data = $seat->fetch_object()) {
            $seats[] = $data;
        }

        $ticket = [];
        $ticket['booking'] = $booking_info;
        $ticket['seats'] = $seats;

        return $ticket;
    }
    //     public static function count_bookings(){
    //         $obj_db = self::obj_db();
    //         $query = "SELECT * FROM bookings b Where b.cancel_status =0";
    //         $result = $obj_db->query($query);
    //         $count = mysqli_num_rows($result);
    //         return $count; 
    // }
    public static function BusWiseBooking($from_date, $to_date, $bus_id)
    {
        $obj_db = self::obj_db();
        $query = " SELECT date, b.cancel_status, ba.id , ba.bus_no as bus, b.id, b.name , b.gender , b.cnic ,b.contact_no, b.total_fare,  b.date, r.departure_time, cd.name as departure, ca.name as arrival FROM bookings b  "
            . "JOIN routes r ON r.id = b.route_id "
            . "JOIN cities cd ON (cd.id = r.departure) "
            . "JOIN cities ca ON (ca.id = r.arrival) "
            . "JOIN buses ba ON (ba.id = r.bus_id) "
            . "WHERE ba.id= '$bus_id' AND date BETWEEN '$from_date' AND '$to_date'";
        $result = $obj_db->query($query);
        // print_r($result);
        // die;
        if ($obj_db->errno) {
            throw new Exception("db Select Error" . $obj_db->errno . $obj_db->error);
        }
        $bookings = [];
        while ($data = $result->fetch_object()) {
            $rows = [];
            $rows['name'] = $data->name;
            $rows['contact_no'] = $data->contact_no;
            $rows['cnic'] = $data->cnic;
            $rows['gender'] = $data->gender;
            $rows['total_fare'] = $data->total_fare;
            $rows['date'] = $data->date;
            $rows['departure'] = $data->departure;
            $rows['arrival'] = $data->arrival;
            $rows['bus'] = $data->bus;
            $rows['departure_time'] = $data->departure_time;
            $query_seat = "SELECT * from booked_seats "
                . "where booking_id = '$data->id'";
            $res = $obj_db->query($query_seat);

            $seats = [];
            while ($d = $res->fetch_object()) {
                $seat_rows = [];
                $seat_rows['seat_no'] = $d->seat_no;
                $seats[] = $seat_rows;
            }

            $rows['seats'] = $seats;
            $bookings[] = $rows;
        }
        return $bookings;
    }
    public static function cancelBookingPending()
    {
        $obj_db = self::obj_db();
        $query = " SELECT b.cancel_status, b.request_status as status, ba.bus_no as bus, b.id, b.name , b.gender , b.cnic ,b.contact_no, b.total_fare,  b.date, r.departure_time, cd.name as departure, ca.name as arrival FROM bookings b  "
            . "JOIN routes r ON r.id = b.route_id "
            . "JOIN cities cd ON (cd.id = r.departure) "
            . "JOIN cities ca ON (ca.id = r.arrival) "
            . "JOIN buses ba ON (ba.id = r.bus_id) "
            . "WHERE b.cancel_status = 0 AND b.request_status = 1 ";
        $result = $obj_db->query($query);
        if ($obj_db->errno) {
            throw new Exception("db Select Error" . $obj_db->errno . $obj_db->error);
        }
        $bookings = [];
        while ($data = $result->fetch_object()) {
            $rows = [];
            $rows['name'] = $data->name;
            $rows['contact_no'] = $data->contact_no;
            $rows['cnic'] = $data->cnic;
            $rows['gender'] = $data->gender;
            $rows['total_fare'] = $data->total_fare;
            $rows['date'] = $data->date;
            $rows['departure'] = $data->departure;
            $rows['arrival'] = $data->arrival;
            $rows['bus'] = $data->bus;
            $rows['departure_time'] = $data->departure_time;
            $rows['status'] = $data->status;
            $query_seat = "SELECT * from booked_seats "
                . "where booking_id = '$data->id'";
            $res = $obj_db->query($query_seat);

            $seats = [];
            while ($d = $res->fetch_object()) {
                $seat_rows = [];
                $seat_rows['seat_no'] = $d->seat_no;
                $seats[] = $seat_rows;
            }

            $rows['seats'] = $seats;
            $bookings[] = $rows;
        }
        return $bookings;
    }
    public static function DateWisePendingCancelBooking($from_date, $to_date)
    {
        $obj_db = self::obj_db();
        $query = " SELECT date, b.cancel_status, b.request_status as status, ba.bus_no as bus, b.id, b.name , b.gender , 
               b.cnic ,b.contact_no, b.total_fare,  b.date, r.departure_time, cd.name as departure, ca.name as arrival FROM bookings b  "
            . "JOIN routes r ON r.id = b.route_id "
            . "JOIN cities cd ON (cd.id = r.departure) "
            . "JOIN cities ca ON (ca.id = r.arrival) "
            . "JOIN buses ba ON (ba.id = r.bus_id) "
            . "WHERE b.cancel_status = 0 AND b.request_status = 1 AND date BETWEEN '$from_date' AND '$to_date'";
        $result = $obj_db->query($query);
        if ($obj_db->errno) {
            throw new Exception("db Select Error" . $obj_db->errno . $obj_db->error);
        }
        $bookings = []; 
        while ($data = $result->fetch_object()) {
            $rows = [];
            $rows['name'] = $data->name;
            $rows['contact_no'] = $data->contact_no;
            $rows['cnic'] = $data->cnic;
            $rows['gender'] = $data->gender;
            $rows['total_fare'] = $data->total_fare;
            $rows['date'] = $data->date;
            $rows['departure'] = $data->departure;
            $rows['arrival'] = $data->arrival;
            $rows['bus'] = $data->bus;
            $rows['departure_time'] = $data->departure_time;
            $rows['status'] = $data->status;
            $query_seat = "SELECT * from booked_seats "
                . "where booking_id = '$data->id'";

            $res = $obj_db->query($query_seat);
            
            $seats = [];
            while ($d = $res->fetch_object()) {
                $seat_rows = [];
                $seat_rows['seat_no'] = $d->seat_no;
                $seats[] = $seat_rows;
            }

            $rows['seats'] = $seats;
            $bookings[] = $rows;
        }
        return $bookings;
    }
    public static function CityWiseBooking($from_date, $to_date, $city_id)
    {
        $obj_db = self::obj_db();
        $query = " SELECT date, b.cancel_status, ba.id , ba.bus_no as bus, b.id, b.name , b.gender , b.cnic ,b.contact_no, b.total_fare,  b.date, r.departure_time, cd.name as departure, ca.name as arrival FROM bookings b  "
            . "JOIN routes r ON r.id = b.route_id "
            . "JOIN cities cd ON (cd.id = r.departure) "
            . "JOIN cities ca ON (ca.id = r.arrival) "
            . "JOIN buses ba ON (ba.id = r.bus_id) "
            . "WHERE cd.id= '$city_id' AND date BETWEEN '$from_date' AND '$to_date'";
        $result = $obj_db->query($query);
        // print_r($result);
        // die;
        if ($obj_db->errno) {
            throw new Exception("db Select Error" . $obj_db->errno . $obj_db->error);
        }
        $bookings = [];
        while ($data = $result->fetch_object()) {
            $rows = [];
            $rows['name'] = $data->name;
            $rows['contact_no'] = $data->contact_no;
            $rows['cnic'] = $data->cnic;
            $rows['gender'] = $data->gender;
            $rows['total_fare'] = $data->total_fare;
            $rows['date'] = $data->date;
            $rows['departure'] = $data->departure;
            $rows['arrival'] = $data->arrival;
            $rows['bus'] = $data->bus;
            $rows['departure_time'] = $data->departure_time;
            $query_seat = "SELECT * from booked_seats "
                . "where booking_id = '$data->id'";
            $res = $obj_db->query($query_seat);

            $seats = [];
            while ($d = $res->fetch_object()) {
                $seat_rows = [];
                $seat_rows['seat_no'] = $d->seat_no;
                $seats[] = $seat_rows;
            }

            $rows['seats'] = $seats;
            $bookings[] = $rows;
        }
        return $bookings;
    }
    public static function Api_bookingHistory($cnic)
    {
        $obj_db = self::obj_db();
        $query = " SELECT b.cancel_status, b.name , b.gender , b.cnic ,b.contact_no, b.total_fare,  b.date, r.departure_time, cd.name as departure, ca.name as arrival FROM bookings b  "
            . "JOIN routes r ON r.id = b.route_id "
            . "JOIN cities cd ON (cd.id = r.departure) "
            . "JOIN cities ca ON (ca.id = r.arrival) "
            . "WHERE cnic = '$cnic' ";
        $result = $obj_db->query($query);
        if ($obj_db->errno) {
            throw new Exception("db Select Error" . $obj_db->errno . $obj_db->error);
        }
        $query = [];
        while ($data = $result->fetch_object()) {
            $query[] = $data;
        }
        return $query;
    }
}
