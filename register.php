<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';

include('config.php');

// define variables and initialize with empty values
$username = $password = $usertype = $email = $phone = $city = "";
$username_err = $password_err = $usertype_err = $email_err = $phone_err = $city_err = "";

// check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        // check if username already exists in database
        $sql = "SELECT id FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $param_username);
        $param_username = trim($_POST["username"]);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) == 1) {
            $username_err = "This username is already taken.";
        } else {
            $username = trim($_POST["username"]);
        }
    }

    // validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // validate user type
    if (empty(trim($_POST["usertype"]))) {
        $usertype_err = "Please select a user type.";
    } else {
        $usertype = trim($_POST["usertype"]);
    }

    // validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email address.";
    } else {
        $email = trim($_POST["email"]);
        if (!preg_match('/@gmail\.com$/', $email)) {
            $email_err = "Please enter a valid Gmail address.";
        }
        // check if email already exists in database
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $param_email);
        $param_email = $email;
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) == 1) {
            $email_err = "This email address is already taken.";
        }
    }

    // validate phone number
    if (empty(trim($_POST["phone"]))) {
        $phone_err = "Please enter a phone number.";
    } else {
        $phone = trim($_POST["phone"]);
        // check if phone number already exists in database
        $sql = "SELECT id FROM users WHERE phone = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $param_phone);
        $param_phone = $phone;
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (!preg_match('/^\d{10,11}$/', $phone)) {
            $phone_err = "Please enter a valid phone number.";
        }
        if (mysqli_stmt_num_rows($stmt) == 1) {
            $phone_err = "This phone number is already taken.";
        }
    }

    // validate city
    if (empty(trim($_POST["city"]))) {
        $city_err = "Please select a city.";
    } else {
        $city = trim($_POST["city"]);
    }

    // if no errors, insert user into database and send verification email
    if (empty($username_err) && empty($password_err) && empty($usertype_err) && empty($email_err) && empty($phone_err) && empty($city_err)) {
        // Set the user status to "pending"
        $status = "pending";
        $verification_token = uniqid();

        // Insert user details into database
        $sql = "INSERT INTO users (username, password, usertype, email, phone, city, status, verification_token) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssssss", $param_username, $param_password, $param_usertype, $param_email, $param_phone, $param_city, $param_status, $param_verification_token);
        $param_username = $username;
        $param_password = password_hash($password, PASSWORD_DEFAULT); // Hashed password for security
        $param_usertype = $usertype;
        $param_email = $email;
        $param_phone = $phone;
        $param_city = $city;
        $param_status = $status;
        $param_verification_token = $verification_token;
        
        mysqli_stmt_execute($stmt);

        // Send verification email using PHPMailer
        try {
            $mail = new PHPMailer(true);

            //Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';  // Specify SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'nasiryt.827@gmail.com'; // SMTP username
            $mail->Password = 'htswyfarodgtsiov';   // SMTP password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            //Recipients
            $mail->setFrom('nasiryt.827@gmail.com', 'KitcheWare Project');
            $mail->addAddress($email);     // Add a recipient

            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'Confirm Your Registration';
            $mail->Body    = 'Click the link below to confirm your registration:
                              <a href="http://localhost/kitchenware/verify.php?token=' . $verification_token . '">Verify Email</a>';

            $mail->send();
            echo '<div class="alert alert-success" role="alert">User registered successfully. Check your email for verification.</div>';
        } catch (Exception $e) {
            echo '<div class="alert alert-danger" role="alert">Message could not be sent. Mailer Error: ' . $mail->ErrorInfo . '</div>';
        }

        // Close statement and database connection
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>User Registration</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">

</head>

<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-5">
        <h2 class="text-center mt-5">User Registration</h2>
        <p class="text-center">Please fill in your details to register.</p>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>

            <div class="form-group">
                <label>User Type</label>
                <div class="ml-5 form-check form-check-inline <?php echo (!empty($usertype_err)) ? 'is-invalid' : ''; ?>">
                    <input class="form-check-input" type="radio" name="usertype" id="buyer" value="buyer" <?php if ($usertype == "buyer") echo " checked"; ?>>
                    <label class="form-check-label" for="buyer">Buyer ( Customer )</label>
                </div>
                <span class="invalid-feedback"><?php echo $usertype_err; ?></span>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                <span class="invalid-feedback"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="number" name="phone" class="form-control <?php echo (!empty($phone_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $phone; ?>">
                <span class="invalid-feedback"><?php echo $phone_err; ?></span>
            </div>
            <div class="form-group">
                <label>City</label>
                <select name="city" class="form-control <?php echo (!empty($city_err)) ? 'is-invalid' : ''; ?>">
                    <option value="">Select a city</option>
                    <option value="Karachi" <?php if ($city == "Karachi") echo " selected"; ?>>Karachi</option>
                    <option value="Lahore" <?php if ($city == "Lahore") echo " selected"; ?>>Lahore</option>
                    <option value="Islamabad" <?php if ($city == "Islamabad") echo " selected"; ?>>Islamabad</option>
                    <!-- Add more cities as needed -->
                </select>
                <span class="invalid-feedback"><?php echo $city_err; ?></span>
            </div>
            <div class="form-group text-center">
                <input type="submit" class="btn btn-primary" value="Register">
            </div>
        </form>

        <p class="text-center">Already have an account? <a href="login.php">Login here</a></p>
        <p class="text-center">Go to <a href="index.php">Home Page</a></p>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>
