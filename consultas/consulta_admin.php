<?php
include('../config/conexion.php');

$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$role = $_POST['role'];

$query = $db->prepare("SELECT * FROM usuarios WHERE email = :email");
$query->bindParam(':email', $email);
$query->execute();

if ($query->rowCount() > 0) {
    echo "<p align='center'>El usuario ya está registrado en el sistema.</p>";
} else {
    if ($role === 'profesor') {
        $role = 'admin';
    }

    $query = $db->prepare("INSERT INTO usuarios (email, password, role) VALUES (:email, :password, :role)");
    $query->bindParam(':email', $email);
    $query->bindParam(':password', $password);
    $query->bindParam(':role', $role);
    
    if ($query->execute()) {
        echo "<p align='center'>Usuario registrado con éxito como $role.</p>";
    } else {
        echo "<p align='center'>Hubo un error al registrar el usuario. Intente nuevamente.</p>";
    }
}
?>

<?php include('../templates/footer_admin.html'); ?>
