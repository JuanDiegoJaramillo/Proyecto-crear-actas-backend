<?php
$mensaje_invitado = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Invitado</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #ffffff; max-width: 600px; margin: 0 auto; border: 1px solid #dddddd; padding: 20px;">
        <tr>
            <td style="text-align: center; padding-bottom: 20px;">
                <h2 style="color: #111517;">Bienvenido a nuestra plataforma, ' . htmlspecialchars($Nombre) . '!</h2>
                <p style="font-size: 16px; color: #555555;">Has sido registrado como **Invitado** para participar en futuras reuniones.</p>
            </td>
        </tr>
        <tr>
            <td style="padding-bottom: 20px;">
                <p style="font-size: 16px; color: #555555;">Tus credenciales temporales son:</p>
                <ul style="list-style: none; padding: 0; color: #333333; font-size: 16px;">
                    <li><strong>Email:</strong> ' . htmlspecialchars($usuario) . '</li>
                    <li><strong>Contraseña:</strong> ' . htmlspecialchars($password) . ' <span style="color: #ff0000;">(Temporal)</span></li>
                </ul>
                <p style="font-size: 16px; color: #555555;">Por favor, inicia sesión y cambia tu contraseña en la sección de perfil para garantizar la seguridad de tu cuenta.</p>
            </td>
        </tr>
        <tr>
            <td style="text-align: center; padding: 20px 0;">
                <a href="' . htmlspecialchars($urlLogin) . '" style="background-color: #007BFF; color: #ffffff; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-size: 16px;">Iniciar Sesión</a>
            </td>
        </tr>
        <tr>
            <td style="font-size: 14px; color: #777777; text-align: center; padding-top: 20px; border-top: 1px solid #dddddd;">
                <p>Si tienes preguntas o necesitas ayuda, contáctanos.</p>
                <p>Gracias,</p>
                <p>Equipo de Soporte</p>
            </td>
        </tr>
    </table>
</body>
</html>
';

