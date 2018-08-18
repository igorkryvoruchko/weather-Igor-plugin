<?php
/**
 * Plugin Name: Weather-Igor-plugin
 * Description: weather plugin || Paste the following shortcode anywhere in your site to display the weather [weather city_name="Your city name"] where "Your city name" is exemple Kiev || EXEMPLE [weather city_name="Kiev"]
 * Plugin URI:  
 * Author URI:  https://github.com/igorkryvoruchko/
 * Author:      Igor Kryvoruchko
 * Version:     1.0
 *
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * Network:     true
 */

function weather_styles() {
	wp_register_style('my_stylesheet', plugins_url('css/style.css', __FILE__));
  	wp_enqueue_style('my_stylesheet');
}
add_action('wp_enqueue_scripts', 'weather_styles');

add_shortcode( 'weather', 'weather_show' );

function weather_show( $args ){
	$token = 'befc17187fe6bcf5b0e8678a17caa894';
	$city_name = $args['city_name'];
	$url = 'https://api.openweathermap.org/data/2.5/weather?q='.$city_name.'&units=metric&APPID='.$token;

 	$response = wp_remote_get( $url );
	$body = wp_remote_retrieve_body( $response );
	
	$json = json_decode( $body, TRUE );
	
	$city_id = $json['id'];
	$city = $json['name'];
	$temp = $json['main']['temp'];
	$icon = $json['weather'][0]['icon'];
	$icon_url = "http://openweathermap.org/img/w/$icon.png";
	$description = $json['weather'][0]['description'];
	
	$forecast_token = '316a797b30d80472368fd43929bca5d5';
	$forecast_url = 'https://api.openweathermap.org/data/2.5/forecast/?q='. $city_name .'&units=metric&APPID='.$token;
	$responce_forecast = wp_remote_get( $forecast_url );
	$body_forecast = wp_remote_retrieve_body( $responce_forecast );
	$json_forecast = json_decode( $body_forecast, TRUE );
	
	$result = "<div class='weather_box'>
					<h1 class='weather_title'>Weather</h1>
					<ul class='weather_list'>
						<li>City: $city</li>
						<li><img src='$icon_url' alt='weather icon'></li>
						<li>Temperature: $temp °C</li>
						<li>Description: $description</li>
					</ul>
			   </div>";
	$result .= '<div class="weather_box"><h3 class="weather_title">Forecast of 5 days / 3 hours</h3>';
	$count = count($json_forecast['list']);
	
	for($i = 0; $i <= $count - 1; $i++ ){
		$result .= '<ul>';
		$result .= '<hr><li><img src="http://openweathermap.org/img/w/'.$json_forecast['list'][$i]['weather'][0]['icon'] .'.png" alt="weather icon"></li>';
		$result .= '<li>Temperature: '.$json_forecast['list'][$i]['main']['temp'] .'</li>';
		$result .= '<li>Description: '.$json_forecast['list'][$i]['weather'][0]['description'] .'</li>';
		$result .= '<li>Day and time: '.$json_forecast['list'][$i]['dt_txt'] .'</li></ul>';
	}
	$result .= '<hr></div>';
	return $result;
}
