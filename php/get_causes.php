<?php
// ============================================
// Returns all causes from the database
// Included by index.php to build category cards
// ============================================

include "db_connect.php";

function getAllCauses($conn) {
    $causes = [];
    $sql = "SELECT cause_id, cause_name, description FROM causes";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $causes[] = $row;
        }
    }
    return $causes;
}
?>