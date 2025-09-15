<?php
include 'PHPMailer/PHPMailer.php';
include 'PHPMailer/Exception.php';
include 'PHPMailer/SMTP.php';
// Database connection
$host = "localhost";
$user = "root";  // change if needed
$pass = "";      // change if needed
$dbname = "contactdb";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

// Validate input
$name    = trim($_POST['name']);
$mobile  = trim($_POST['mobile']);
$email   = trim($_POST['email']);
$message = trim($_POST['message']);

if (empty($name) || empty($email) || empty($message)) {
    echo "<span style='color:red'>All required fields must be filled.</span>";
    exit;
}

// Insert into DB
$stmt = $conn->prepare("INSERT INTO contact_queries (name, mobile, email, message) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $mobile, $email, $message);
$stmt->execute();

// Send Email via PHPMailer
$mail = new PHPMailer(true);
try {
    //Server settings
    $mail->isSMTP();
    $mail->Host       = 'mail.itssolutions.in';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'akhand.pratap.singh@itssolutions.in';
    $mail->Password   = 'cw8,2Iw2H1Pq]O#B'; // Use actual email password
    $mail->SMTPSecure = 'ssl'; 
    $mail->Port       = 465;

    //Recipients
    $mail->setFrom('akhand.pratap.singh@itssolutions.in', 'Website Contact Form');
    $mail->addAddress('akhand.pratap.singh@itssolutions.in');

    // Content
    $mail->isHTML(true);
    $mail->Subject = "New Contact Query from " . $name;
    $mail->Body    = "
        <h3>Contact Form Submission</h3>
        <p><b>Name:</b> {$name}</p>
        <p><b>Mobile:</b> {$mobile}</p>
        <p><b>Email:</b> {$email}</p>
        <p><b>Message:</b><br>{$message}</p>
    ";

    $mail->send();
    echo "<span style='color:green'>Message sent successfully! We will contact you soon.</span>";
} catch (Exception $e) {
    echo "<span style='color:red'>Message could not be sent. Mailer Error: {$mail->ErrorInfo}</span>";
}

$conn->close();
?>
