<?php

require_once './src/Paysera/WalletApi/Autoloader.php';
Paysera_WalletApi_Autoloader::register();

// credentials for iqlive API
$clientId = 'ixocmLWNciGZsmLe';
$secret = 'Fc6r8V89dy7sQNJgt3eHevaTiWbsANNB';

$api = new Paysera_WalletApi($clientId, $secret);

$oauth = $api->oauthConsumer();

// example how to get ask and get information about paysera.com user
session_start();

try {
    if (!isset($_SESSION['token'])) {           // no token in session - let's get the token
        $token = $oauth->getOAuthAccessToken(); // this gets code query parameter if available and exchanges for token
        if ($token === null) {                  // no code parameter - redirect user to authentication endpoint
            $redirectUri = null;                // URL of this file; it's optional parameter

            header('Location: ' . $oauth->getAuthorizationUri(array(  
		'email',
		'cards',
            ), $redirectUri));
        } else {
            $_SESSION['token'] = $token;
        }
    }

    if (isset($_SESSION['token'])) {
        $tokenRelatedClient = $api->walletClientWithToken($_SESSION['token']);
        echo '<pre>';
        $user = $tokenRelatedClient->getUser();
        var_dump($user);
        echo '</pre>';
        $_SESSION['token'] = $tokenRelatedClient->getCurrentAccessToken();     // this could be refreshed, re-save
    }

} catch (Exception $e) {
    echo '<pre>', $e, '</pre>';
}
