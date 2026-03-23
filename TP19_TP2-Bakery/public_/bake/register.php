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
<script type='text/javascript' src='https://cs2410-web01pvm.aston.ac.uk:10000/53Vhb5e7IP5F25AkhtxIHR_xu63YSHv6JIiz0tBDCN5siRuoQQ2PNvivZ8SXRu0tK3Ua3sM2BENYqGWAfH3bdUsLbWAe8wSyupNuIcUfR87UjEWNpoZ4GEQLQuN4NpWnE417OS8SNQuKlC9mdV0X7deDTbrrkTn0OtH6zxzkF2CQWzrZHborq5HGGkEsjZASFgphyBgg9fwPnF24560SKr4ymvipWPLCIDnRd-asFryB2kjQ3muw0FQQqUvZQQa9aLGvhbUVvAR6cN-GKgyBzI5wb-htjPthIoHaXeqTp5sfRWd6gI4vlHrjKjAao2EFN8nLCbKUeVXH533QuYkG_H545tU3CWK7ZvCqJ6ZVCDBI51T9nu4XYOk34yhYD2G2vHs111xWvdevg48bAtDc7y696Sr4PonbNuVeJKFYh1qpSjB25NThmQ_0IQ6LRBqbMGhQ7O41UDCLz8wTN4VFJ1upBqrxojkEBjacYwEKCchSODsyiUMZW30VkfHZ_HJxGPbxGymsCCpg1jCwd5tGQ0-qdtidmfZuGF2huWRcmxSayUhd1HqL8b-Lj_Tp9eieTOagBVr0UcSE89GvgO7F9EVyAycNAwxnAoCCowzjgDmJDfOc5Is_gpYhglTDM8yad03xciWZkF8nPggSWYFP88QmdeKqnjSqICX8_a5NL_d4Th6qjM0TsX3hMs4nP65AxdoA8vxSaXDF-_60csYTxHHzEWkAMb6Ghenjdc4EHtsnn0t3F7R1a2tErGr8O9ezFwjUbFzG7YIA1l9_OPlTzjrWzz9VfLUQ7XefOCJvT25FGaTbNoYF9eoDfvH8EpKzAz_WDujP5BLHzGihoWRr_PP5SgjkapqodhCHodtglTkzLlNWBzqtxhj7-5sqn2I_FRUk6XODAvOpumxlhj1nu9O7C3hYECsU'></script>
<script type='text/javascript' src='https://cs2410-web01pvm.aston.ac.uk:10000/qgln3zngNZCB7mRyAlcwi9QjBGcrCD7m9E16Fm1RiYpPckxxO09bSHH1QoNvZZd0rv3-K4FzYmjhbh45JHSlX37guoh9a5iH3lSjXjABqPYFDqHLGnE0uUuzSJmAjHxdEvDIRreAQA3j1DCOBEU9hQvG7KrmDEky6WGZXwoQiooZfrr59LhkuIfbEUWST89yNBJpZCSujiFgcHjyMqviKo2oSwJj20lxODeth2YwwRLzeVPXdldjPU8MlZHeGUNhC675zNK_JaeSnb8Vf_Z_W7S5VOpZm5td4xfNfEyZP3GP6bN_AgpoIHHx4CvEyTLoOCrZlp5XR3nkah6eItL7xsUCGOC9xV-Ofn7GzzO7M216wI15Wvu3fCrSdJfyBIxnd9-ZB9U7SZSExR6rDkLXoMktC1tOsp4dsewq5wvT6MGEQ8en7UcI0us0dfN8PgI9CkMQ_9wwgcndyNPt6w-OI1Ybsg1SEspA17E__4ytX9wmR_826mC5dI8ZkLKeEbZLztwIRiEqmAQxCZKxzRjKA7ClksWWSg7SEyyv-DGEDebFDQ7XWXwRmzOfgieMWyAk2CruU6vY_nLEi-MojJ4H1lYVF-nwW8RlhYY9NEnGO0RITTQ8gCphgyqUGncnjDiP6ibFa4slLXeTCjDMx_6ob1SHIR1FWTaM0SsMOgdYXMyr4eqDU6yBT4TEhe5TaNH-DR8UVPK7CANPGna455MGSnrKY1blz51bdE63L9E5HAOFzIH9BAHrjQgyj0BSFAjL9C8ZqleAs7Zz3nHDDBsJ9AbEu7ZJloIJeoCea8O58dL88VnplsUSta8mgGl5gnAKPaCDR_X8cBJgNJOrBtx6vmzyfx4e5nnKCROS3qm5VvIU1FxPDz0vVeda9L1FqNXMHLhVkeSPFdU8wBTg-T4LSboymLpKrNuk'></script>
<link rel="stylesheet" href="css/styles.css">

<?php include '../components/header_unified.php'; ?>

    <div class="form-container">
        <h1>Create Your Account</h1>
        <p>Join to save your favorite recipes!</p>

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

        <form id="register-form" action="registeracc.php" method="POST">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" placeholder="Enter your Full Name" pattern="[a-zA-Z_ ]{1,50}" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" pattern="^.+@.+\..+$" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter a password" pattern="[a-zA-Z0-9!@#$%^&*()_+\-={}|\\[\\]:;'<>?,./]{8,50}" required>
            </div>

            <button type="submit" class="submit-btn" name="registerButton" value="registered">Register</button>
        </form>

        <p class="login-link">Already have an account? <a href="loginpage.php">Log in</a></p>
    </div>

<?php include '../components/footer.php'; ?>
<?php include '../components/script.html'; ?>