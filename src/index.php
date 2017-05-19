<?php

require 'vendor/autoload.php';
require 'config.php';

use Lametric\Lametric;

$lametric = new Lametric(array(
    'pushURL' => $LAMETRIC_PUSHURL,
    'token' => $LAMETRIC_TOKEN,
));


$session = new SpotifyWebAPI\Session(
    $CLIENT_ID,
    $CLIENT_SECRET,
    $REDIRECT_URI
);

$api = new SpotifyWebAPI\SpotifyWebAPI();

function getLastPlayedTrack(){
  global $session,$api;
  $tracks = $api->getMyRecentTracks();
  return $tracks->items[0];
}

function sendMessageToLametric($text, $icon = 7990){
  global $lametric;
  echo $text;
  $lametric->setIcon(7990);
  $lametric->push($text);
}

function saveTokenFiles(){
  global $session;
  $token = $session->getAccessToken();
  $refresh = $session->getRefreshToken();
  file_put_contents('./token.txt',$token);
  file_put_contents('./refresh.txt',$refresh);
}

$token = file_get_contents('./token.txt');
if( $token !== false){
  // si la connexion est deja faite
  $refresh = file_get_contents('./refresh.txt');

  $api->setAccessToken($token);
  $session->refreshAccessToken($refresh);
  saveTokenFiles();

  if( isset($_GET['get_titre']) ){
    //recuperation de la derniere chanson
    $last = getLastPlayedTrack();
    $titre = 'LAST: '.$last->track->album->artists[0]->name." - ".$last->track->name;
    sendMessageToLametric($titre);
  }
  elseif( isset($_GET['add_titre']) ){
    //recuperation de la derniere chanson
    $last = getLastPlayedTrack();
    $titre = 'ADD: '.$last->track->album->artists[0]->name." - ".$last->track->name;
    sendMessageToLametric($titre);
    $api->addMyTracks($last->track->id);
  }
  else{
    sendMessageToLametric("Aucune action choisie");
  }

}
elseif (isset($_GET['code'])) {
  // retour du ouath, on stoques les token de connexion
    $code = $_GET['code'];
    $session->requestAccessToken($code);
    saveTokenFiles();
    sendMessageToLametric("Connexion Oauth réussi!");
} else {
  // la premiere connexion doit etre faite avec un navigateur pour avoir oauth
    $options = [
        'scope' => [
          'playlist-read-private',
          'playlist-read-collaborative',
          'playlist-modify-public',
          'playlist-modify-private',
          'streaming',
          'user-follow-modify',
          'user-follow-read',
          'user-library-read',
          'user-library-modify',
          'user-read-private',
          'user-read-birthdate',
          'user-read-currently-playing',
          'user-read-recently-played',
          'user-read-email',
          'user-top-read'
        ],
    ];

    sendMessageToLametric("No Oauth connexion...");

    header('Location: ' . $session->getAuthorizeUrl($options));
}
