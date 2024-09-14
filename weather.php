<?php
function getWeather($city, $apiKey) {
    $url = "http://api.openweathermap.org/data/2.5/weather?q=$city&appid=$apiKey";
    
    try {
        $response = json_decode(file_get_contents($url), true);
        
        if ($response['cod'] === '404') {
            return null;
        }
        
        $weatherData = [
            'temperature' => round($response['main']['temp'] - 273.15, 2),
            'humidity' => $response['main']['humidity'],
            'description' => $response['weather'][0]['description']
        ];
        
        return $weatherData;
    } catch (Exception $e) {
        echo "Error fetching weather data: " . $e->getMessage();
        return null;
    }
}