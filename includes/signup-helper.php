<?php

if (isset($_POST['signup-submit'])) {
    require 'dbhandler.php';

    $username = $_POST['uname'];
    $email = $POST['email'];
    $passw = $POST['pwd'];
    $pass_rep = $POST['con-pwd'];
    $fname = $POST['fname'];
    $lname = $POST['lname'];

    if ($passw !== $pass_rep) {
        header("Location: ../signup.php?error=diffPasswords&fname=".$fname."&lname=".$lname."&uname=".$username);
        exit();
    }
    else {
        $sql = "SELECT uname FROM users WHERE uname=?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: ../signup.php?error=SQLInjection");
            exit();
        }
        else {
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            $check = mysqli_stmt_num_rows($stmt);

            if ($check > 0) {
                header("Location: ../signup.php?error=UsernameTaken");
            exit();
            }
            else {
                $sql = "INSERT INTO users (lname, fname, email, uname, password) VALUES (?, ?, ?, ? ,?)";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    header("Location: ../signup.php?error=SQLInjection");
                    exit();
                }
                else {
                    $hashedPass = password_hash($passw, PASSWORD_BCRYPT);
                    mysqli_stmt_bind_param($stmt, "sssss", $lname, $fname, $email, $username, $hashedPass);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_store_result($stmt);

                    $sqlImg = "INSERT INTO profile (uname) VALUE ('$username')";
                    mysqli_query($conn, $sqlImg);

                    header("Location: ../signup.php?signup=success");
                    exit();
                }
            }
        }
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
    }

}
else {
    header("Location: ../signup.php");
    exit();
}
?>