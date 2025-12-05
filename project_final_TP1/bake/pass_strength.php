<?php
function checkPassStrength($password) {
    $length = strlen($password) >= 8;
    $hasUppercase = preg_match('/[A-Z]/', $password);
    $hasLowercase = preg_match('/[a-z]/', $password);
    $hasNumber = preg_match('/[0-9]/', $password);
    $hasSpecialChar = preg_match('/[!@#$%^&*(),.?":{}|<>_]/', $password);

    return $length && $hasUppercase && $hasLowercase && $hasNumber && $hasSpecialChar;
}
?>