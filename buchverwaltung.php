<?php
require 'db.php';

// Den Status der Bücher automatisch aktualisieren
$update_sql = "UPDATE ausleihen SET status = 'Überfällig' WHERE rueckgabedatum < CURDATE() AND status = 'Ausgeliehen'";
$conn->exec($update_sql);

// Ein Buch ausleihen
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['buch_id'], $_POST['schueler_id'])) {
    $buch_id = $_POST['buch_id'];
    $schueler_id = $_POST['schueler_id'];

    $check_sql = "SELECT status FROM ausleihen WHERE buch_id = :buch_id ORDER BY id DESC LIMIT 1";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bindParam(':buch_id', $buch_id);
    $check_stmt->execute();
    $buch = $check_stmt->fetch();

    if (!$buch || $buch['status'] == 'Verfügbar') {
        $ausleihdatum = date("Y-m-d");
        $rueckgabedatum = date('Y-m-d', strtotime('+4 weeks'));

        $insert_sql = "INSERT INTO ausleihen (buch_id, schueler_id, ausleihdatum, rueckgabedatum, status) 
                       VALUES (:buch_id, :schueler_id, :ausleihdatum, :rueckgabedatum, 'Ausgeliehen')";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bindParam(':buch_id', $buch_id);
        $insert_stmt->bindParam(':schueler_id', $schueler_id);
        $insert_stmt->bindParam(':ausleihdatum', $ausleihdatum);
        $insert_stmt->bindParam(':rueckgabedatum', $rueckgabedatum);
        $insert_stmt->execute();

        echo "<p class='success'>✅ Buch erfolgreich ausgeliehen! Rückgabe am: $rueckgabedatum</p>";
    } else {
        echo "<p class='error'>⚠️ Dieses Buch ist bereits ausgeliehen oder überfällig!</p>";
    }
}

// Bücher abrufen
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$sql = "SELECT b.id, b.titel, b.autor, 
        COALESCE(a.status, 'Verfügbar') AS status, 
        a.rueckgabedatum, s.vorname, s.nachname 
        FROM buecher b
        LEFT JOIN ausleihen a ON b.id = a.buch_id AND a.id = (
            SELECT MAX(id) FROM ausleihen WHERE buch_id = b.id
        )
        LEFT JOIN schueler s ON a.schueler_id = s.id";

if (!empty($search)) {
    $sql .= " WHERE b.titel LIKE :search OR b.autor LIKE :search";
}

$stmt = $conn->prepare($sql);
if (!empty($search)) {
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
}
$stmt->execute();
$buecher = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buchverwaltung</title>
    <style>
        body {
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
            margin: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid black;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        .success {
            color: green;
            font-weight: bold;
        }

        .error {
            color: red;
            font-weight: bold;
        }

        .badge {
            padding: 5px;
            border-radius: 5px;
            font-size: 14px;
        }

        .verfuegbar {
            background-color: green;
            color: white;
        }

        .ausgeliehen {
            background-color: orange;
            color: black;
        }

        .ueberfaellig {
            background-color: red;
            color: white;
        }

        .button {
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            color: white;
            font-size: 14px;
        }

        .button-ausleihen {
            background-color: blue;
        }

        .button-disabled {
            background-color: grey;
        }

        .search-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .search-container input {
            width: 250px;
            padding: 10px;
            border: 2px solid #007BFF;
            border-radius: 5px;
            font-size: 16px;
            outline: none;
            transition: 0.3s;
        }

        .search-container input:focus {
            border-color: #0056b3;
            box-shadow: 0 0 8px rgba(0, 91, 187, 0.5);
        }

        .search-container button {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px 15px;
            margin-left: 5px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        .search-container button:hover {
            background-color: #0056b3;
        }

        .logout-button {
            background-color: #ff4d4d;
            color: white;
            border: none;
            padding: 10px 15px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .logout-button:hover {
            background-color: #cc0000;
        }

        .back-button {
            background-color: #ff9800;
            color: white;
            border: none;
            padding: 10px 15px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 10px;
            transition: 0.3s;
        }

        .back-button:hover {
            background-color: #e68900;
        }

        /*Responive*/
        @media screen and (max-width: 768px) {
            body {
                margin: 10px;
                font-size: 13px;
            }

            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            .search-container {
                flex-direction: column;
                align-items: center;
            }

            .search-container input {
                width: 100%;
                font-size: 14px;
            }

            .search-container button {
                width: 100%;
                margin-top: 5px;
            }

            .logout-button,
            .back-button {
                width: 30%;
                font-size: 13px;
                padding: 8px;
            }
        }
    </style>
</head>

<body>
    <h2>📚 Buchverwaltung</h2>
    <form method="GET" class="search-container">
        <input type="text" name="search" placeholder="🔍 Nach Titel oder Autor suchen..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
        <button type="submit">Suchen</button>
    </form>

    <?php if (!empty($_GET['search'])): ?>
        <form method="GET">
            <button type="submit" class="back-button">🔙 Alle Bücher</button>
        </form>
    <?php endif; ?>

    <form action="logout.php" method="post">
        <button type="submit" class="logout-button">🚪 Logout</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Titel</th>
                <th>Autor</th>
                <th>Status</th>
                <th>Rückgabedatum</th>
                <th>Aktion</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($buecher as $buch): ?>
                <tr>
                    <td><?= $buch['id'] ?></td>
                    <td><?= htmlspecialchars($buch['titel']) ?></td>
                    <td><?= htmlspecialchars($buch['autor']) ?></td>
                    <td>
                        <?php if ($buch['status'] == 'Überfällig'): ?>
                            <span class="badge ueberfaellig"><?= $buch['status'] ?></span>
                        <?php elseif ($buch['status'] == 'Ausgeliehen'): ?>
                            <span class="badge ausgeliehen"><?= $buch['status'] ?></span>
                        <?php else: ?>
                            <span class="badge verfuegbar">Verfügbar</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $buch['rueckgabedatum'] ? $buch['rueckgabedatum'] : '-' ?></td>
                    <td>
                        <?php if ($buch['status'] == 'Verfügbar'): ?>
                            <form action="buchverwaltung.php" method="POST">
                                <input type="hidden" name="buch_id" value="<?= $buch['id'] ?>">
                                <input type="hidden" name="schueler_id" value="1">
                                <button type="submit" class="button button-ausleihen">📚 Ausleihen</button>
                            </form>
                        <?php else: ?>
                            <button class="button button-disabled" disabled>❌ Nicht verfügbar</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>