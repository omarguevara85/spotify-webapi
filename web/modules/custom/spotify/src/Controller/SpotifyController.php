<?php
//https://developer.spotify.com/console/
//https://developer.spotify.com/dashboard/applications
namespace Drupal\spotify\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Component\Utility\Html ;
use Symfony\Component\HttpFoundation\JsonResponse;
use GuzzleHttp\Client;
use Drupal\Component\Serialization\Json;

/**
 * Returns responses for spotify routes.
 */
class SpotifyController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function launch() {
    $token = $this->getToken()['access_token'];
    $endpoint_launch = $this->getConfig('spotify_endpoint_launch');
    $method = 'GET';
    $arg = [
      'query' => [
        'limit' => 10,            
      ],
      'headers' => [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
        'Authorization' =>  'Bearer '.$token
      ],
    ];

    try {
      $launch = $this->send($endpoint_launch, $method, $arg);
    }catch (\Exception $e) {
      \Drupal::logger('spotify')->error((string) "Error al intentar traer los lanzamientos, @error", [
        '@method' => 'spotify',
        '@error' => print_r($e->getMessage(), TRUE),
      ]);
    }

    return [
      '#theme' => 'launch',
      '#launch' => $launch,
      '#attached' => [
        'library' => [
          'spotify/spotify',
        ],
      ],
    ];
  }

   /**
   * Builds the response.
   */
  public function artist($id) {
    $token = $this->getToken()['access_token'];
    $endpoint_artist = $this->getConfig('spotify_endpoint_artist').$id;
    $method = 'GET';
    $arg = [
      'headers' => [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
        'Authorization' =>  'Bearer '.$token
      ],
    ];

    try {
      $artist = $this->send($endpoint_artist, $method, $arg);
    }catch (\Exception $e) {
      \Drupal::logger('spotify')->error((string) "Error al intentar traer los artistas, @error", [
        '@method' => 'spotify',
        '@error' => print_r($e->getMessage(), TRUE),
      ]);
    }
    $tracks = $this->tracks($id, $token);
    
    return [
      '#theme' => 'artist',
      '#artist' => $artist,
      '#tracks' => $tracks,
      '#attached' => [
        'library' => [
          'spotify/spotify',
        ],
      ],
    ]; 
  }

  /**
   * Builds the response.
   */
  public function tracks($id, $token) {
    $endpoint_artist = $this->getConfig('spotify_endpoint_artist').$id.'/top-tracks';
    $method = 'GET';
    $arg = [            
      'query' => [
        'country'=> 'CO',        
      ],
      'headers' => [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
        'Authorization' =>  'Bearer '.$token
      ],
    ];

    try {
      $tracks = $this->send($endpoint_artist, $method, $arg);
    }catch (\Exception $e) {
      \Drupal::logger('spotify')->error((string) "Error al intentar traer los albunes, @error", [
        '@method' => 'spotify',
        '@error' => print_r($e->getMessage(), TRUE),
      ]);
    }

    return $tracks;
  }

  /**
   * {@inheritdoc}
   */   
  public function getToken(){
    $client_id = $this->getConfig('spotify_client_id'); 
    $client_secret = $this->getConfig('spotify_client_secret'); 
    $endpoint_token = $this->getConfig('spotify_endpoint_token');
    $method = 'POST';

    $arg = [
      'form_params' => [
        'grant_type'=> 'client_credentials',
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'header' => array('Authorization: Basic '.base64_encode($client_id.':'.$client_secret))
      ]
    ];

    return $this->send($endpoint_token, $method, $arg);
  }

  /**
   * {@inheritdoc}
   */
  public function send($end_point, $method, $arg){
    $request = \Drupal::service('http_client_factory');
    $api_client = $request->fromOptions(['verify' => FALSE]);
    $pay_load = $api_client->request($method, $end_point, $arg);
    $code = $pay_load->getStatusCode();
    if ($code == 200) {
      $body = $pay_load->getBody()->getContents();
      return Json::decode($body);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig($data) {
    return \Drupal::config('spotify.settings')->get($data);         
  }

}
