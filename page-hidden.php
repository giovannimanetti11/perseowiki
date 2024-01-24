<?php
/* Template Name: Hidden Page */

// PHP code for Mailchimp integration.
require_once 'inc/config.php';

// Check if the user's email is already stored in a cookie
$showContent = false;
if (isset($_COOKIE['user_email'])) {
    $email = filter_var($_COOKIE['user_email'], FILTER_VALIDATE_EMAIL);
    if ($email) {
        // If the cookie contains a valid email, set a flag to show the content directly
        $showContent = true;
    }
}

// Handle the AJAX request.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $email = isset($data['email']) ? filter_var($data['email'], FILTER_VALIDATE_EMAIL) : false;

    if (!$email) {
        echo json_encode(['success' => false, 'message' => 'Per favore, inserisci un indirizzo email valido.']);
        exit;
    }

    // Mailchimp API integration
    $apiKey = MAILCHIMP_API_KEY;
    $listId = MAILCHIMP_LIST_ID;
    $memberId = md5(strtolower($email));
    $dataCenter = substr($apiKey,strpos($apiKey,'-')+1);
    $url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $listId . '/members/' . $memberId;

    $json = json_encode([
        'email_address' => $email,
        'status'        => 'subscribed', 
    ]);

    // Setup cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); 
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    
    // Execute cURL request
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $response = json_decode($result, true);
    curl_close($ch);

    // Response handling
    if ($httpCode == 200) {
        // Set a cookie with the email
        setcookie('user_email', $email, time() + (86400 * 30), "/"); // 30 days expiration
        echo json_encode(['success' => true]);
    } else {
        $errorMessage = 'Errore nella registrazione.';
        if (isset($response['title']) && $response['title'] == 'Member Exists') {
            $errorMessage = 'Questa email è già registrata.';
        } elseif (isset($response['title']) && $response['title'] == 'Invalid Resource') {
            $errorMessage = 'Indirizzo email non valido.';
        }
        echo json_encode(['success' => false, 'message' => $errorMessage]);
    }
    exit;
}

get_header();
?>

<div id="popup-overlay" <?php if($showContent) echo 'style="display:none;"'; ?>>
    <div id="email-popup">
        <form id="email-form">
            <label for="user-email">INSERISCI LA TUA EMAIL</label>
            <input type="email" id="user-email" name="email" required>
            <div id="email-error" class="alert alert-danger" style="display:none;"></div>
            <button type="submit">VISITA LA PAGINA</button>
        </form>
    </div>
</div>

<main id="content" <?php if(!$showContent) echo 'style="display:none;"'; ?>>
    <h1>Welcome to the page!</h1>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var form = document.getElementById('email-form');
        var emailError = document.getElementById('email-error');

        form.addEventListener('submit', function(event) {
            event.preventDefault();
            var email = document.getElementById('user-email').value;

            if(validateEmail(email)) {
                sendDataToPHP(email);
            } else {
                displayError('Per favore, inserisci un indirizzo email valido.');
            }
        });

        function sendDataToPHP(email) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "", true);
            xhr.setRequestHeader("Content-Type", "application/json");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    var response = JSON.parse(xhr.responseText);
                    if(xhr.status === 200 && response.success) {
                        document.cookie = "user_email=" + email + ";path=/;max-age=" + (86400 * 30);
                        document.getElementById('popup-overlay').style.display = 'none';
                        document.getElementById('content').style.display = 'block';
                    } else {
                        displayError(response.message || 'Si è verificato un errore nell\'invio dell\'email.');
                    }
                }
            };
            var data = JSON.stringify({"email": email});
            xhr.send(data);
        }

        function validateEmail(email) {
            var re = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
            return re.test(email.toLowerCase());
        }

        function displayError(message) {
            emailError.textContent = message;
            emailError.style.display = 'block';
        }
    });
</script>


<style>
    /* CSS styles for the popup and background blur */
    #popup-overlay {
        position: fixed;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        background: rgba(0, 0, 0, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    #email-popup {
        background: linear-gradient(135deg, #6e8efb, #0A2944);
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        text-align: center;
        animation: popin 0.5s ease-out forwards;
        transform: scale(0);
        max-width: 400px;
        width: 100%;
    }

    #email-form label, #email-form input, #email-form button {
        display: block;
        margin: 10px auto;
        width: 80%;
        color: white;
    }

    #email-form input, #email-form button {
        border: 2px solid rgba(255, 255, 255, 0.7);
        padding: 10px 15px;
        border-radius: 5px;
        background: transparent;
        outline: none;
    }

    #email-form button {
        background-color: #fff;
        color: #0A2944;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.3s, color 0.3s;
    }

    #email-form button:hover {
        background-color: #0A2944;
        color: white;
    }

    main#content {
        text-align: center;
        padding: 20px;
    }

    #email-error {
        width: 80%;
        margin: 0 auto;
        max-width: 400px;
    }

    @keyframes popin {
        from {
            transform: scale(0);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }
</style>

<?php get_footer(); ?>
