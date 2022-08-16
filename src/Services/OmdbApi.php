<?php
namespace App\Services;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
class OmdbApi
{
    public static $defaultUrlPoster = 'https://amc-theatres-res.cloudinary.com/amc-cdn/static/images/fallbacks/DefaultOneSheetPoster.jpg';
    private $client;
    private $params;
    private $apiKey = 'a93b767b';
    public function __construct(HttpClientInterface $client, ContainerBagInterface $params)
    {
        $this->client = $client;
        $this->apiKey = $params->get('app.omdbapi.key');
    }
    public function fetchOmdbData(string $titre): array
    {
        $response = $this->client->request(
            'GET',
            'https://www.omdbapi.com/?t=' . $titre . '&apikey=' . $this->apiKey
        );

        $statusCode = $response->getStatusCode();
        $contentType = $response->getHeaders()['content-type'][0];
        $content = $response->getContent();
        $content = $response->toArray();

        return $content;
    }

    public function fetchPoster($titre)
    {
        $content = $this->fetchOmdbData($titre);
        /**
         * {
            "Response": "False",
            "Error": "Movie not found!"
            }
         */ 
        return $content['Poster'];
    }

    public function fetch($titre)
    {
        $content = $this->fetchOmdbData($titre);
        /**
         * {
            "Response": "False",
            "Error": "Movie not found!"
            }
         */ 
        return $content;
    }
}