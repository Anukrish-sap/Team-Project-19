<?php
if (isset($_SESSION['userID'])) {
    include __DIR__ . '/footer_1.php';      // logged-in footer
} else {
    include __DIR__ . '/footer_guest.php';  // guest footer
}
?>