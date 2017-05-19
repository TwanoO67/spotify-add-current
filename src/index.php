<?php

require 'vendor/autoload.php';
require 'config.php';

use Lametric\Lametric;

$lametric = new Lametric(array(
    'pushURL' => $LAMETRIC_PUSHURL,
    'token' => $LAMETRIC_TOKEN,
));

$lametric->setIcon(7990);

$session = new SpotifyWebAPI\Session(
    $CLIENT_ID,
    $CLIENT_SECRET,
    $REDIRECT_URI
);

$api = new SpotifyWebAPI\SpotifyWebAPI();

function getLastPlayedTrack($api){
  $tracks = $api->getMyRecentTracks();
  return $tracks->items[0];
}

if (isset($_GET['code'])) {
    $session->requestAccessToken($_GET['code']);
    $api->setAccessToken($session->getAccessToken());

    //recuperation de la derniere chanson
    $last = getLastPlayedTrack($api);
    $titre = $last->track->album->artists[0]->name." - ".$last->track->name;
    echo $titre;

    $lametric->push($titre);
    $api->addMyTracks($last->track->id);

    exit;

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
