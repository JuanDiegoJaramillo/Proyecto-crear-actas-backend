<?php

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
//Load Composer's autoloader

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';


class Enviar_correo{


public function EnviarDatosUsuarioE($Nombre1,$Para,$usuario, $contrasena){

include 'mensajes/bienvenidaUE.php';


//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug =0; //SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'gomezreginojhonatan@gmail.com';                     //SMTP username
    $mail->Password   = 'mhhguhevkbyqpvjm';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('gomezreginojhonatan@gmail.com', 'Eventos univiversidad');
    $mail->addAddress($Para, $Nombre1);     //Add a recipient
   // $mail->addAddress('ellen@example.com');               //Name is optional
    //$mail->addReplyTo('info@example.com', 'Information');
    //$mail->addCC('cc@example.com');
    //$mail->addBCC('bcc@example.com');

    //Attachments
   // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
   // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

    //Content
    $mail->isHTML(true);  
    $mail->CharSet='UTF-8';                                //Set email format to HTML
    $mail->Subject = 'Cuenta Administrador de Eventos';
    $mail->Body    .=$mensaje_bienveniaUE;
   // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
   $respu = 4;
} catch (Exception $e) {
   $respu = 5;
}
return $respu;
}

public function Validarcorreo($Nombre1,$Para, $pin){

   include 'mensajes/mensajepin.php';
   
   
   //Create an instance; passing `true` enables exceptions
   $mail = new PHPMailer(true);
   
   try {
       //Server settings
       $mail->SMTPDebug =0; //SMTP::DEBUG_SERVER;                      //Enable verbose debug output
       $mail->isSMTP();                                            //Send using SMTP
       $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
       $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
       $mail->Username   = 'gomezreginojhonatan@gmail.com';                     //SMTP username
       $mail->Password   = 'mhhguhevkbyqpvjm';                               //SMTP password
       $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
       $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
   
       //Recipients
       $mail->setFrom('gomezreginojhonatan@gmail.com', 'Registro vendedor');
       $mail->addAddress($Para, $Nombre1);     //Add a recipient
      // $mail->addAddress('ellen@example.com');               //Name is optional
       //$mail->addReplyTo('info@example.com', 'Information');
       //$mail->addCC('cc@example.com');
       //$mail->addBCC('bcc@example.com');
   
       //Attachments
      // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
      // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
   
       //Content
       $mail->isHTML(true);  
       $mail->CharSet='UTF-8';                                //Set email format to HTML
       $mail->Subject = 'confirmación de correo';
       $mail->Body    .=$mensaje_pin;
      // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
   
       $mail->send();
      $respu = 1;
   } catch (Exception $e) {
      $respu = 0;
   }
   return $respu;
   }



public function DatosLoginUsuario($Nombre,$Para, $usuario ,$password,$urlLogin,$rol)
{
   include BASE_PATH .  '/app/Email/mensajes/NotificacionUsuario.php';
   //Create an instance; passing `true` enables exceptions
   $mail = new PHPMailer(true);
   try {
       //Server settings
       $mail->SMTPDebug =0; //SMTP::DEBUG_SERVER;                      //Enable verbose debug output
       $mail->isSMTP();                                            //Send using SMTP
       $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
       $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
       $mail->Username   = 'gomezreginojhonatan@gmail.com';                     //SMTP username
       $mail->Password   = 'mhhguhevkbyqpvjm';                               //SMTP password
       $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
       $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
   
       //Recipients
       $mail->setFrom('gomezreginojhonatan@gmail.com', 'Registro '.$rol);
       $mail->addAddress($Para, $Nombre);     //Add a recipient
      // $mail->addAddress('ellen@example.com');               //Name is optional
       //$mail->addReplyTo('info@example.com', 'Information');
       //$mail->addCC('cc@example.com');
       //$mail->addBCC('bcc@example.com');
   
       //Attachments
      // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
      // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
   
       //Content
       $mail->isHTML(true);  
       $mail->CharSet='UTF-8';                                //Set email format to HTML
       $mail->Subject = 'Bienvenido a nuestra plataforma';
       $mail->Body    .=$mensaje_Usuario;
      // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
       
       $mail->send();
      $respu = 200;
   } catch (Exception $e) {
      $respu = 500;
   }
   return $respu;
   }
}


?>