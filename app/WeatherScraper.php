<?php

namespace App;


use GuzzleHttp\Client;
use App\Models\Weather;
use GuzzleHttp\Exception\GuzzleException;

class WeatherScraper
{
    public static function getData(){

        //Init Guzzle
        $client = new Client();

        //Get request
        try {
            $response = $client->request(
                'GET',
                'https://api.openweathermap.org/data/2.5/weather?q=London&appid=API_TOKEN'
            );
        } catch (GuzzleException $e) {
            exit($e);
        }

        $resp = strip_tags($response->getBody()->getContents());

        if (!empty($resp)) {
            if($response->getStatusCode() === 200){

                // Determine rating and category
                $data = json_decode($resp);
                switch ($data->weather[0]->main) {
                    case 'Rainy':
                        $rating = 'Bad';
                        $category = "stay inside weather";
                        break;
                    case 'Clouds':
                        $rating = 'Reasonable';
                        $category = 'Stay inside weather';
                        break;
                    case 'Sunny':
                        $rating = 'Good';
                        $category = "Don't stay inside weather";
                        break;

                    default:
                        $rating = 'No rating available';
                        $category = 'No category available';
                        break;
                }


                // db
                $weatherTable = new Weather;

                $weatherTable->data = $resp;
                $weatherTable->rating = $rating;
                $weatherTable->category = $category;

                $weatherTable->save();
//                dd($resp);
            }
        }
    }
}
