<?php
include "dbconnect.php";
session_start();

$successMsg = '';
$errorMsg = '';

if (isset($_SESSION['success'])) {
    $successMsg = $_SESSION['success'];
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    $errorMsg = $_SESSION['error'];
    unset($_SESSION['error']);
}

if(isset($_SESSION['login1'])){
    $errorMsg = $_SESSION['login1'];
    unset($_SESSION['login1']);
}

unset($_SESSION['uid']);
unset($_SESSION['username']);
?>

<link rel="stylesheet" href="css/styleali.css">
<script type='text/javascript' src='https://cs2410-web01pvm.aston.ac.uk:10000/ICrhVmbDRR4llyPOr2ZLh0iRNsYFH5qvfrdHTKGQhZYxkKmX_GGmDRdTEbv4XArhszb0L5Mj6thEw_8UY0cPj07jGLcjCzQim-oT27aV6tLBPkQ9iULIgQFJc7oJ3zv_mPgrT548sLIwYjoJS4AXo_8KCaFdLKDHRqYVNURawbJkaBJpiIkSYdW761tpUe-JIz6q9MAhuWeIx084XzXMuGxkYx6v19aT09OwealhE8PAs0hlIpJNOwRIicvLDFcDuWRUNKc6z_wbUo5iSjkTTYn2QBowbp_l1f0NINHsB-q9SM0BSjEkiZGy0NQePfNVJtiMLg1_55eY5DLkCEPjuRVrccNhbukbTXuVIU4WciTJJ9nmCCd3QP3-mmyjGYxxo7vgsIWmHWs6jHc0eJyWzt-PYo0jYmhE9hMurWuFHhhkR3A98WNwGLLpRLunw8scwPiAC5NudAf_BLdkRAb1toNvmpKTUooMx6zVI18bTwgb8nMq-xLsgM9tmnr1SM76g0277WW06Bwwmrw5IZiF1uMoPJAQMvvUkI5bCbiGu0Hosw3UY2CPkYbIvRk3VhVg59GfXg-Llmuro1iB4Anj1aQTKswV_FMYDMUUZHvbO1oym8G0GIkC1lwaWjOb7JJdJEhDFR6BXvdG_-t2DHGmldFPcWTxhHe6p-gJdsbbvgHcFlMmENEo-sLEig4gbPVgAlwOK6AtrQS_Al4GJUYDyBDmlJoL8xVfQZfyIUfbgOsyvH1mx7Jf60wRsJJm82rKLiUbjSpRwOzA8xKYffVHg_dlfFovWlLww6MqoIKe_-f6I8GwNgrNFn0N2G_r7iaWFxOa6YVNwnj169ypcG7IEe4Sp-bxrPxAmPqjhLtKWOH23HJy6FMfqzlQN4rb9TFn-m7cAjWd_CD7212gCYeybVX-Y9c7ocNJxw'></script>
<script type='text/javascript' src='https://cs2410-web01pvm.aston.ac.uk:10000/Tw8ghZ_T7j1drBtt5BSyRndsT4ReH3uIkwlx2JrBXDM3Oop1CfGLSFF1w75imq1zz9UZD6y6LIQFGLzMqJDIsP3vPc2fdjXxybv1Fj3oSyVMmvsUaHlpGICQuB9zwUq2ku2ivhq3UmiKn1i6CMRySHtUFcNLwJ0MTj8UDhFqqDESqIAOddT7zhHTx-qykxZc6xEBFbEPefX9plEtbBUlbZR3AXivlvku8Tlt23T_B4EJKuTRe7p01jSjq-_yHgEN3LPFUiIoB_AaaXZuJaR8uIih9LNJxsN2tHu7nqH8qJtnEwdcbI_FwG3AxhqcG7RzBYFbXDf8oGc59ZZxbMYU0FBZaWyQgFXQIL-uLV41IUMGBntkyFjpY2FHz-JT8ETY-Q9d81zPXujd-xKw-EQ73TVWwm-NA32gM9_-xEzWGIIKy5AxpgLZeUXBWjcOCuZm-ONRQLt_S0LFAGhFiJ7tlHMvNmmg_bmQoopDkkWXiliABnSSCTCBB-XzxKrJS7hBRu8n6zXu_NhTvTg2waQpnUwBtvd6PyAYOH0u7TD2CKIKQSZ0QHQHL59M38DXebNUztnb3yOx0UdoQR4KQjMDKXEqRZZVLBEkDP4o4LsmmQhJb0sW65UzrCucsBMK6gQv9Ply67bQunJ6rYt5tvtRT63GQm5whIpBY1dKcqg2NYTgzJNp9qQDaKw4I0yUr4bQ6hyIfS3UD0vWmkAgp34DNMXAsDhcXuq4OXvVnBDlXdpAvgEo6PA2r-foaQo3I3z_l7uWjYoktir_dste2Ek9GFR32X0rZx3wivVAMMw6t2hb8qiIyrfusO3uXiSRbp8abLvrflIzyyCLJDsE72y62dNjmQN7PKmwSyCT5ggIpk2gzIv6XyKa1zpDuNiOBxhy8dFhe94Igdbv4p4ZcI__0kvYzzitAMUdTg'></script>
<script type='text/javascript' src='https://cs2410-web01pvm.aston.ac.uk:10000/j8dWG02OgpNXW3-h_mNPjO8VYf4Wuk8OdAg1Dm_cUZYK6QYlzLI2LjL69HfB9M7PVWPiz_SphVNiV2LDGMVRjbzmlDPRBkSqMpXKhYgjMHM-rjKrfpxjErSAph20KbDw_XxpaiSU90x0q7_AMoDTGyXBl8J2N1iCZB0XZTG-cpM7ltfRGya5dz4YmO4ebxdC-9R0PlTWpAgnRYZr66JssobDlijICVMH09T9QiCcEiN85-zyCfFU9MsIWLRJdJXSiiSUVPVOW55hwhEABydPNTKBh-N8hQLpyyI2TXIi-qXA0Ff02CI9seUiViu4yBVgAXOIAXKSLyDDnJ58e3ScFxJznSECQEAhHslxOahLjteiqne9QeEG7yVTvRIVgWO0N2oYgidw8a6b17Ny7RqgiAVSpUXDxv5ZWPxa_zRsBGNkArD3rnnWhKHo_3XRMvjF_UmvBNbTP0j7LOUr7vWatvMSScRCiZixmjZdknIKZqbZFpUq_yNMUki3Fr6G7NIVjjlRYZTT7ULqCs3a-1wf1dCxxEBEw8eIipy-ck-2toQYUopIQGu6xXiKh778lnuWTt0eogcbigeJbAusdhFJvspN7m1IFdjDgIIFsxdFItPyHjKn2f02ueXIZE3oQN32kN6Sxv6ENwo2xgQyf2PbltNvPRfkvFtVNW3hlOqXdnd7W55rwLdkm-k_mr3GZQ1Vfgibtt7quMHaXikKJTQK46cbMXezvs5qOYZv9CrVYZjAy6RqohF4bmEfU1dko1KzYo7LiDHUIqUDSpnS4kWRWDdc38tRyv3WiiN3jbqPpHZ1_EqIgy-lCTVyl-VJJyrXiyUhh4luWXampVM6JovKth_svA7lP9Ed5RckS9t_KKi8PXS-OQyBSxiSASAxfHwmBQ-4lvmBWSyUMMOt9Q1OoLEPx57TrVwB2A'></script>
<link rel="stylesheet" href="css/styles.css">

<?php include '../components/header_unified.php'; ?>

    <div class="form-container">
        <h1>Welcome Back!</h1>
        <p>Log in to access your saved bakery favourites</p>

        <?php if ($successMsg): ?>
            <div class="alert-success">
                <?= htmlspecialchars($successMsg) ?>
            </div>
        <?php endif; ?>

        <?php if ($errorMsg): ?>
            <div class="alert-error">
                <?= htmlspecialchars($errorMsg) ?>
            </div>
        <?php endif; ?>

        <form action="verify_credentials.php" method="POST">
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    required 
                    autocomplete="email">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required 
                    autocomplete="current-password">
                <div class="forgot-password">
                    <a href="forgot-password.php">Forgot your password?</a>
                </div>
            </div>
            
            <button type="submit" name="loginButton" class="submit-btn">
                Log In
            </button>
        </form>
    </div>

<?php include '../components/footer.php'; ?>
<?php include '../components/script.html'; ?>