<?php
// create_booking.php
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/log_api.php';

mysqli_report(MYSQLI_REPORT_OFF);
$conn->autocommit(FALSE);

log_api("Create Booking", "POST");

try {
    // 1. Validate POST
    if (!isset($_POST['user_id'], $_POST['flight_id'], $_POST['room_id'], $_POST['plan_id'], $_POST['tour_id'])) {
        throw new Exception("Missing required fields.");
    }

    $user_id      = intval($_POST['user_id']);
    $flight_id    = intval($_POST['flight_id']);
    $room_id      = intval($_POST['room_id']);
    $plan_id      = intval($_POST['plan_id']);
    $tour_id      = intval($_POST['tour_id']);
    $payment_method = $_POST['payment_method'] ?? 'Credit Card';

    // 2. Fetch user
    $stmt = $conn->prepare("SELECT first_name, last_name, contact_number FROM waks_travel.users WHERE user_id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($first_name, $last_name, $contact_number);
    $stmt->fetch();
    $stmt->close();
    if (empty($first_name)) throw new Exception("User not found.");
    $full_name = trim($first_name . ' ' . $last_name);

    // 3. Fetch flight price
    $stmt = $conn->prepare("SELECT price, seats_available FROM renz_airway.flights WHERE flight_id=? FOR UPDATE");
    $stmt->bind_param("i", $flight_id);
    $stmt->execute();
    $stmt->bind_result($flight_price, $seats_available);
    $stmt->fetch();
    $stmt->close();
    if (!is_numeric($flight_price) || $seats_available < 1) throw new Exception("Invalid flight or no seats available.");

    // 4. Fetch room info & calculate total price
    $stmt = $conn->prepare("SELECT hotel_id, price_per_night FROM rj_hotel.rooms WHERE room_id=? FOR UPDATE");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $stmt->bind_result($hotel_id, $price_per_night);
    $stmt->fetch();
    $stmt->close();
    if (!is_numeric($hotel_id)) throw new Exception("Invalid room selected.");

    $check_in = date('Y-m-d'); // Can be replaced by user input
    $check_out = date('Y-m-d', strtotime('+1 day'));
    $num_nights = (strtotime($check_out) - strtotime($check_in)) / 86400;
    $total_room_price = $price_per_night * $num_nights;

    // 5. Fetch insurance price
    $stmt = $conn->prepare("SELECT price FROM rafa_trip.insurance_plans WHERE plan_id=?");
    $stmt->bind_param("i", $plan_id);
    $stmt->execute();
    $stmt->bind_result($plan_price);
    $stmt->fetch();
    $stmt->close();
    $insurance_price = floatval($plan_price);

    // 6. Fetch tour price
    $stmt = $conn->prepare("SELECT price FROM lance_tourguide.tours WHERE tour_id=?");
    $stmt->bind_param("i", $tour_id);
    $stmt->execute();
    $stmt->bind_result($tour_price);
    $stmt->fetch();
    $stmt->close();
    $tour_price = floatval($tour_price);

    // 7. Calculate total booking price
    $total_price = floatval($flight_price) + floatval($total_room_price) + floatval($plan_price) + floatval($tour_price);

    // 8. Insert into bookings
    $stmt = $conn->prepare("
        INSERT INTO waks_travel.bookings
        (user_id, flight_id, hotel_id, plan_id, tour_id, booking_date, total_price, status)
        VALUES (?, ?, ?, ?, ?, NOW(), ?, 'confirmed')
    ");
    $stmt->bind_param("iiiiid", $user_id, $flight_id, $hotel_id, $plan_id, $tour_id, $total_price);
    $stmt->execute();
    $booking_id = $stmt->insert_id;
    $stmt->close();

    // 9. Insert payment
    $stmt = $conn->prepare("
        INSERT INTO waks_travel.payments
        (booking_id, payment_method, amount, payment_date, payment_status)
        VALUES (?, ?, ?, NOW(), 'Paid')
    ");
    $stmt->bind_param("isd", $booking_id, $payment_method, $total_price);
    $stmt->execute();
    $stmt->close();

    // 10. Insert hotel reservation
    $stmt = $conn->prepare("
        INSERT INTO rj_hotel.hotel_reservations
        (room_id, guest_name, check_in_date, check_out_date, total_price, reservation_status)
        VALUES (?, ?, ?, ?, ?, 'confirmed')
    ");
    $stmt->bind_param("isssd", $room_id, $full_name, $check_in, $check_out, $total_room_price);
    $stmt->execute();
    $reservation_id_hotel = $stmt->insert_id;
    $stmt->close();

    // --------------------------------------------------------
    // 11. Generate passport number & insert flight passenger
    // --------------------------------------------------------
    $passport_number = 'P' . mt_rand(100000000, 999999999);

    $stmt = $conn->prepare("
        INSERT INTO renz_airway.passengers
        (flight_id, full_name, contact_number, passport_number)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("isss", $flight_id, $full_name, $contact_number, $passport_number);
    $stmt->execute();
    $passenger_id = $stmt->insert_id;
    $stmt->close();

    // --------------------------------------------------------
    // 12. Insert policy_holder with same passport_number
    // --------------------------------------------------------
    $start_date = date('Y-m-d');
    $end_date   = date('Y-m-d', strtotime('+7 day'));
    $birth_date = '2000-01-01';
    $stmt = $conn->prepare("
        INSERT INTO rafa_trip.policy_holder
        (plan_id, full_name, birth_date, passport_number, contact_number, start_date, end_date)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("issssss", $plan_id, $full_name, $birth_date, $passport_number, $contact_number, $start_date, $end_date);
    $stmt->execute();
    $policy_id = $stmt->insert_id;
    $stmt->close();

    // 13. Insert claim linked to policy
    $stmt = $conn->prepare("
        INSERT INTO rafa_trip.claims
        (policy_id, claim_date, claim_reason, claim_amount, claim_status)
        VALUES (?, NOW(), 'Travel Booking', ?, 'active')
    ");
    $stmt->bind_param("id", $policy_id, $plan_price);
    $stmt->execute();
    $claim_id = $stmt->insert_id;
    $stmt->close();

    // 14. Insert flight reservation
    $seat_number = rand(1, 50);
    $stmt = $conn->prepare("
        INSERT INTO renz_airway.reservations
        (passenger_id, booking_date, seat_number, reservation_status)
        VALUES (?, NOW(), ?, 'confirmed')
    ");
    $stmt->bind_param("ii", $passenger_id, $seat_number);
    $stmt->execute();
    $reservation_id_airway = $stmt->insert_id;
    $stmt->close();

    // 15. Insert tour booking
    $stmt = $conn->prepare("SELECT guide_id FROM lance_tourguide.tour_guides LIMIT 1");
    $stmt->execute();
    $stmt->bind_result($guide_id);
    $stmt->fetch();
    $stmt->close();
    $guide_id = is_numeric($guide_id) ? intval($guide_id) : 0;

    $num_people = 1;
    $status = 'confirmed';
    $booking_date = date('Y-m-d H:i:s');
    $stmt = $conn->prepare("
        INSERT INTO lance_tourguide.tour_bookings
        (guide_id, tour_id, tourist_name, booking_date, num_people, status)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iissis", $guide_id, $tour_id, $full_name, $booking_date, $num_people, $status);
    $stmt->execute();
    $tour_booking_id = $stmt->insert_id;
    $stmt->close();

    // 16. Update flight seats
    $stmt = $conn->prepare("UPDATE renz_airway.flights SET seats_available = seats_available - 1 WHERE flight_id = ?");
    $stmt->bind_param("i", $flight_id);
    $stmt->execute();
    $stmt->close();

    // Commit
    $conn->commit();
    log_api("Create Booking", "POST", "success");

    echo json_encode([
        'status' => 'success',
        'waks_booking_id' => $booking_id,
        'reservation_id_hotel' => $reservation_id_hotel,
        'policy_id' => $policy_id,
        'claim_id' => $claim_id,
        'passenger_id' => $passenger_id,
        'reservation_id_airway' => $reservation_id_airway,
        'tour_booking_id' => $tour_booking_id,
        'total_price' => $total_price
    ]);
    exit;

} catch (Exception $e) {
    $conn->rollback();
    log_api("Create Booking", "POST", "error");
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit;
}
