<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=agenda_contactos', 'root', '');
    echo "Conexión exitosa a la base de datos.";
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}
