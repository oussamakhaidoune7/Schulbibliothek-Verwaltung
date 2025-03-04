CREATE TABLE IF NOT EXISTS buecher (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titel VARCHAR(255) NOT NULL,
    autor VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS schueler (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vorname VARCHAR(255) NOT NULL,
    nachname VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS ausleihen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    buch_id INT,
    schueler_id INT,
    status ENUM('Verfügbar', 'Ausgeliehen', 'Überfällig') DEFAULT 'Verfügbar',
    rueckgabedatum DATE,
    FOREIGN KEY (buch_id) REFERENCES buecher(id),
    FOREIGN KEY (schueler_id) REFERENCES schueler(id)
);
