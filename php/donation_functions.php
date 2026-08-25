<?php
// ============================================
// Reusable functions for donations
// Member 2 will call insertDonation() from the form
// Member 4 will call getAllDonations() for the admin report
// ============================================

include "db_connect.php";

// Insert a new donor (or reuse if email already exists)
function getOrCreateDonor($conn, $name, $email) {
    $stmt = $conn->prepare("SELECT donor_id FROM donors WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['donor_id'];
    }

    $stmt = $conn->prepare("INSERT INTO donors (name, email) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $email);
    $stmt->execute();
    return $conn->insert_id;
}

// Insert a donation record
function insertDonation($conn, $donor_id, $cause_id, $amount, $payment_method, $payment_status) {
    $stmt = $conn->prepare("INSERT INTO donations (donor_id, cause_id, amount, payment_method, payment_status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iidss", $donor_id, $cause_id, $amount, $payment_method, $payment_status);
    return $stmt->execute();
}

// Retrieve all donations with donor & cause names joined in
function getAllDonations($conn) {
    $donations = [];
    $sql = "SELECT d.donation_id, dn.name AS donor_name, c.cause_name, 
                   d.amount, d.donation_date, d.payment_status
            FROM donations d
            JOIN donors dn ON d.donor_id = dn.donor_id
            JOIN causes c ON d.cause_id = c.cause_id
            ORDER BY d.donation_date DESC";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $donations[] = $row;
        }
    }
    return $donations;
}
?>