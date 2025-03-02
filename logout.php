<?php
session_start(); // Die Sitzung starten
session_destroy(); // Alle Sitzungsdaten beenden
header("Location: register.php"); // Weiterleitung zur Registrierungsseite
exit();
?>
