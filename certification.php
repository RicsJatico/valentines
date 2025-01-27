<?php
session_start();

$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = ""; // Default XAMPP password (empty)
$dbname = "valentines"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// If no session, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

// Initialize variables
$user_name = '';
$partner = '';
$reference_number = '';

// Retrieve user data based on session user_id
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name, partner, reference_number FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($user_name, $partner, $reference_number);
$stmt->fetch();
$stmt->close();

$conn->close();

// Generate the locked URL
$locked_url = "http://localhost/?reference_number=" . urlencode($reference_number);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Valentine's Certification</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            background-color: #f4f4f4;
            color: #2f2f2f;
            text-align: center;
            padding: 5px;
        }
        .certificate-container {
            background: url('bg1.jpg') no-repeat center center; /* Background image */
            background-size: 130%; /* Zoomed out slightly by increasing the size */
            margin: 20px auto;
            padding: 60px;
            border-radius: 10px;
            border: 5px solid #d9a51f; /* Gold border */
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            max-width: 700px;
            position: relative;
            font-family: 'Garamond', serif;
            text-align: center;
        }
        .certificate-container::before {
            content: "";
            position: absolute;
            top: -15px;
            left: -15px;
            right: -15px;
            bottom: -15px;
            border: 5px solid #d9a51f;
            border-radius: 15px;
        }
        h1 {
            font-size: 36px;
            color: #5a3d32; /* Dark Brown */
            font-weight: bold;
            margin-bottom: 5px;
            margin-top: 60px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .content {
            font-size: 20px;
            margin: 5px 0;
            line-height: 1.8;
            color: #3e3e3e;
        }
        .content strong {
            color: #b05b1a; /* Goldish color */
        }
        .signature {
            margin-top: 10px;
            font-style: italic;
            font-size: 24px;
            color: #4e2a00; /* Deep brown */
        }
        .seal {
            background: url('seal.png') no-repeat center;
            background-size: 100px;
            width: 100px;
            height: 100px;
            margin: 30px auto;
        }
        #download-button {
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 16px;
            background-color: #d9a51f;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s;
        }
        #download-button:hover {
            background-color: #b05b1a;
        }
 /* URL Container Styles */
 #url-container {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #d9a51f;
            color: white;
            padding: 10px 20px;
            text-align: center;
            font-family: Arial, sans-serif;
            font-size: 16px;
            font-weight: bold;
            box-shadow: 0 -4px 8px rgba(0, 0, 0, 0.2);
        }
        #copy-btn {
            background-color: #fff;
            color: #d9a51f;
            border: 1px solid #d9a51f;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 10px;
            transition: 0.3s ease-in-out;
        }
        #copy-btn:hover {
            background-color: #d9a51f;
            color: white;
        }
        #show-url-button {
    margin-top: 20px;
    padding: 10px 20px;
    font-size: 16px;
    background-color: #d9a51f;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
    transition: background-color 0.3s;
}

#show-url-button:hover {
    background-color: #b05b1a;
}
        @media (max-width: 480px) 
        {
            .certificate-container {
    background: url('officialbg.png') no-repeat center center; /* Background image centered */
    background-size: 100% auto; /* Width takes full container, height adjusts proportionally */
    margin: 30px auto;
    padding: 60px;
    border-radius: 10px;
    border: 5px solid #d9a51f; /* Gold border */
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    max-width: 400px; /* Container width */
    height: 550px; /* Fixed height for the container */
    position: relative;
    font-family: 'Garamond', serif;
    text-align: center;
}


            body {
            font-family: 'Times New Roman', Times, serif;
            background-color: #f4f4f4;
            color: #2f2f2f;
            text-align: center;
            padding: 5px;
        }
        .certificate-container {
        padding: 20px;
        border-width: 3px;
        max-width: 90%; /* Adjusted width for smaller screens */
        background-size: 100%; /* Fit the background for small screens */
    }

    .certificate-container::before {
        top: -10px;
        left: -10px;
        right: -10px;
        bottom: -10px;
        border-width: 3px;
        border-radius: 10px;
    }

    h1 {
        font-size: 24px; /* Reduce font size for the title */
        margin-top: 40px;
    }

    .content {
        font-size: 16px; /* Adjust font size for content */
        line-height: 1.5;
    }
        .content strong {
            color: #b05b1a; /* Goldish color */
        }
        .signature {
        font-size: 18px; /* Reduce font size for signature */
    }
    .seal {
        background-size: 80px; /* Adjust seal size */
        width: 80px;
        height: 80px;
        margin: 20px auto;
    }
    #download-button {
        font-size: 14px; /* Adjust button font size */
        padding: 8px 16px; /* Adjust button padding */
    }
        #download-button:hover {
            background-color: #b05b1a;
        }

        }

        
    </style>
</head>
<body>
    <div id="certificate" class="certificate-container">
        <h1>Certificate of Love</h1>
        <p class="content">
            This certifies that on the <strong>14th of February</strong>,<br>
            <strong><?php echo htmlspecialchars($user_name); ?></strong> and <br>
            <strong><?php echo htmlspecialchars($partner); ?></strong> are united in love and commitment.
        </p>
        <p class="content">
            May your union be blessed with peace, joy, and endless affection.<br>
            This certificate is granted to honor the beautiful bond you share. 
            Wishing you a lifetime of love, happiness, and prosperity.
        </p>
        <div class="seal"></div>
        <p class="signature">
            Given this day of February, Year 2025<br>
            With all the love and affection of those who witness your beautiful bond ❤️
        </p>
    </div>
    <button id="download-button">Download Certificate</button>

    <!-- Show URL Container -->
    <button id="show-url-button" onclick="window.location.href='share.php'">Finish UP</button>
    <div id="url-container" style="display: none;">
        Copy your locked URL: 
        <span id="locked-url"><?php echo htmlspecialchars($locked_url); ?></span>
        <button id="copy-btn">Copy</button>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script>
        document.getElementById("download-button").addEventListener("click", function () {
            const certificate = document.getElementById("certificate");
            
            html2canvas(certificate).then(canvas => {
                const imgData = canvas.toDataURL("image/png");
                const pdf = new jspdf.jsPDF("p", "mm", "a4");
                const imgWidth = 190;
                const imgHeight = (canvas.height * imgWidth) / canvas.width;

                pdf.addImage(imgData, "PNG", 10, 10, imgWidth, imgHeight);
                pdf.save("certificate.pdf");
            });
        });

        // Show locked URL container
        document.getElementById("show-url-button").addEventListener("click", function () {
            const urlContainer = document.getElementById("url-container");
            urlContainer.style.display = urlContainer.style.display === "none" ? "block" : "none";
        });

        // Copy the locked URL to clipboard
        document.getElementById("copy-btn").addEventListener("click", function () {
            const lockedUrl = document.getElementById("locked-url").textContent;
            const tempInput = document.createElement("input");
            tempInput.value = lockedUrl;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand("copy");
            document.body.removeChild(tempInput);

            alert("URL copied to clipboard!");
        });
    </script>
</body>
</html>
