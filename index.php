<?php
echo "<div class='welcome-message'>📚 Willkommen in der Schulbibliothek!</div>";
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Schulbibliothek</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
            background: url('Images/background_image.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
            text-align: center;
            height: 100vh;
            display: flex;
            justify-content: center;
            flex-direction: column;
            align-items: center;
        }
        .welcome-message {
            font-size: 1.8rem;
            color: rgb(228, 231, 228);
            font-weight: bold;
            animation: fadeIn 2s ease-in-out, bounce 3s infinite;
            max-width: 90%;
            text-align: center;
            margin: 20px;
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        @keyframes bounce {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-10px);
            }

            60% {
                transform: translateY(-5px);
            }
        }

        button {
            margin-top: 30px;
            padding: 15px 30px;
            font-size: 1.2rem;
            background-color:rgb(84, 175, 87);
            color: white;
            border: none;
            border-radius: 15px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            max-width: 90%;
        }
        button:hover {
            background-color:rgb(105, 179, 108);
            transform: scale(1.07);
        }

        @media (max-width: 768px) {
            .welcome-message {
                font-size: 1.5rem;
                margin: 10px;
            }

            button {
                font-size: 1rem;
                padding: 12px 20px;
            }
        }

        @media (max-width: 480px) {
            .welcome-message {
                font-size: 1.2rem;
                margin: 5px;
            }

            button {
                font-size: 0.9rem;
                padding: 10px 15px;
            }
        }
    </style>
</head>

<body>
    <div class="welcome-message">
        Melde dich jetzt an, um alle Bücher zu entdecken und auszuleihen.<br>
        <a href="register.php">
            <button>Jetzt registrieren</button>
        </a>
    </div>
</body>

</html>