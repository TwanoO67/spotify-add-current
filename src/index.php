<?php

require 'vendor/autoload.php';
require 'config.php';

$session = new SpotifyWebAPI\Session(
    $CLIENT_ID,
    $CLIENT_SECRET,
    $REDIRECT_URI
);

$api = new SpotifyWebAPI\SpotifyWebAPI();

function getLastPlayedTrack($api){
  $tracks = $api->getMyRecentTracks();
  return $tracks->items[0]->track->id;
}

if (isset($_GET['code'])) {
    $session->requestAccessToken($_GET['code']);
    $api->setAccessToken($session->getAccessToken());

    var_dump($session);exit;

    //recuperation de la derniere chanson
    $last = getLastPlayedTrack($api);
    echo "la derniere ".$last;
    //on attend que la chanson en cours soit rajouter a la liste
    do{
      sleep(30);
      $newid = getLastPlayedTrack($api);
    }
    while( $last === $newid );

    //des que l'id est different on l'ajoute
    $api->addMyTracks($newid);

    echo "la nouvelle ".$newid;


} else {
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

    header('Location: ' . $session->getAuthorizeUrl($options));
    echo "redirection vers spotify ";
    die();
}
