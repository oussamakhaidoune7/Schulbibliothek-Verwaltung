<?php
require 'db.php';
session_start();

$message = ""; // Eine Variable zum Speichern der Nachricht!

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vorname = trim($_POST['vorname']);
    $nachname = trim($_POST['nachname']);

    // Überprüfung, ob der Schüler bereits registriert ist
    $stmt_check = $conn->prepare("SELECT * FROM schueler WHERE vorname = :vorname AND nachname = :nachname");
    $stmt_check->bindParam(':vorname', $vorname);
    $stmt_check->bindParam(':nachname', $nachname);
    $stmt_check->execute();

    $_SESSION['vorname'] = $vorname;
    $_SESSION['nachname'] = $nachname;

    if ($stmt_check->rowCount() > 0) {
        // Wenn der Schüler bereits registriert ist
        $message = "👋 Willkommen zurück, $vorname! Du wirst weitergeleitet..."; 
    } else {
        // Den neuen Schüler in die Datenbank eintragen
        $stmt = $conn->prepare("INSERT INTO schueler (vorname, nachname) VALUES (:vorname, :nachname)");
        $stmt->bindParam(':vorname', $vorname);
        $stmt->bindParam(':nachname', $nachname);

        if ($stmt->execute()) {
            $message = "✅ Registrierung erfolgreich! Willkommen, $vorname!";
        }
    }

    // Weiterleitung nach 2 Sekunden
    header("refresh:2;url=buchverwaltung.php");
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrierung</title>
    <style>
        body {
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
            background: url('Images/backgr_image.png') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .form-container {
            width: 90%;
            max-width: 400px;
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: left;
        }
        .message {
            padding: 10px;
            margin-bottom: 15px;
            font-size: 16px;
            color: white;
            border-radius: 5px;
        }
        .success {
            background-color: #4CAF50;
        }
        .info {
            background-color: #3498db;
        }
        .form-container input {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-container button {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .form-container button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Registrierung für Schüler</h2>

        <!-- Die Nachricht anzeigen, wenn sie vorhanden ist-->
        <?php if (!empty($message)): ?>
            <div class="message <?= strpos($message, '✅') !== false ? 'success' : 'info' ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            Vorname:<input type="text" name="vorname" placeholder="vorname" required><br>
            Nachname:<input type="text" name="nachname" placeholder="nachname" required><br>
            <button type="submit">Registrieren</button>
        </form>
    </div>
</body>
</html>
