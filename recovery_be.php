<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

include 'conexion_be.php';

if (isset($_POST['email'])) {
$email = $_POST['email'];

    // Depuración: Verificar si el correo se recibe correctamente
    if (empty($email)) {
        die("El correo electrónico está vacío.");
    }

$query = "SELECT * FROM usuarios WHERE email = '$email'";
$result = mysqli_query($enlace, $query);

    if ($result && $result->num_rows > 0) {
$row2 = $result->fetch_assoc();

// Cambiar 'id' por 'ID' para que coincida con la estructura de la tabla
if (isset($row2['ID'])) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'dara12336448@gmail.com';
        $mail->Password   = 'zvwtlnlntjkzfbje';
        $mail->Port       = 587;

        $mail->setFrom('dara12336448@gmail.com', 'Dara');
        $mail->addAddress($email);

        $mail->CharSet = 'UTF-8'; // Asegura la codificación correcta
        $mail->Subject = "Recuperación de contraseña";
        $mail->isHTML(true); // Si el mensaje es HTML

        // Generar token seguro
        $token = bin2hex(random_bytes(32));
        $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Guardar token y expiración en la base de datos (puedes crear una tabla password_resets)
        mysqli_query($enlace, "INSERT INTO password_resets (user_id, token, expires_at) VALUES ('{$row2['ID']}', '$token', '$expira')");

        // Enlace con token
        $link = "http://localhost/AppRegistroyControl/change_password.php?token=$token";
        $mail->Body = 'Hola, si te llegó este correo es porque has solicitado recuperar tu contraseña.<br>
        <br>Si no has solicitado este correo, por favor ignora este mensaje.<br><br>
        Entra al siguiente link para cambiar la contraseña: <a href="' . $link . '">Recuperar Contraseña</a>';
        $mail->AltBody = 'Este es el mensaje en texto plano para clientes que no soportan HTML.';

        if ($mail->send()) {
            header("Location: index.php?toast_tipo=exito&toast_titulo=Éxito&toast_descripcion=Correo+de+recuperación+enviado+correctamente.");
            exit();
        } else {
            echo "Error al enviar el correo: {$mail->ErrorInfo}";
        }
    } catch (Exception $e) {
        echo "Error al enviar el correo: {$mail->ErrorInfo}";
            }
        } else {
            echo "No se encontró el ID del usuario.";
        }
    } else {
        header("Location: index.php?toast_tipo=error&toast_titulo=Error&toast_descripcion=No+se+pudo+enviar+el+correo.+Inténtalo+nuevamente.");
        exit();
    }
} else {
    header("Location: index.php?message=not_found");
}
?>
