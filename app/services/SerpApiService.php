<?php
/**
 * SerpApiService.php - Integración con SerpApi para Google Flights
 */
class SerpApiService {
    
    private string $apiKey;

    public function __construct() {
        // En producción idealmente tomarlo de $_ENV o config
        $this->apiKey = '544a43ee854dfa60b1d14779cdc6f9e58f0ff02831d3ad21f11dd35dc019260b';
    }

    /**
     * Busca vuelos mediante SerpAPI
     * @param string $origen (Ej: LIM, MEX, JFK)
     * @param string $destino (Ej: CUN, MIA)
     * @param string $fecha (Y-m-d)
     * @return array
     */
    public function searchFlights(string $origen, string $destino, string $fecha, ?string $fechaRetorno = null): array {
        
        // Si no hay API key o estamos en desarrollo/demo, devolvemos un Mock realista
        if (empty($this->apiKey) || $this->apiKey === 'demo') {
            return $this->getMockResponse($origen, $destino, $fecha);
        }

        $paramsArray = [
            'engine' => 'google_flights',
            'departure_id' => strtoupper($origen),
            'arrival_id' => strtoupper($destino),
            'outbound_date' => $fecha,
            'currency' => 'USD',
            'hl' => 'en',
            'api_key' => $this->apiKey
        ];

        if ($fechaRetorno) {
            $paramsArray['type'] = '1'; // Ida y Vuelta
            $paramsArray['return_date'] = $fechaRetorno;
        } else {
            $paramsArray['type'] = '2'; // Solo Ida
        }

        $params = http_build_query($paramsArray);

        $url = "https://serpapi.com/search.json?" . $params;

        // Reintentos automáticos (hasta 2 intentos)
        $maxRetries = 2;
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Reducido de 15 a 10 segundos
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            
            $response = curl_exec($ch);
            $err = curl_error($ch);
            curl_close($ch);

            // Si tuvo éxito, procesar y retornar
            if (!$err && !empty($response)) {
                break;
            }

            // Si es el último intento y hubo error
            if ($attempt === $maxRetries) {
                error_log("[SerpApiService::searchFlights] Falló después de $maxRetries intentos. Error: $err");
                return $this->getMockResponse($origen, $destino, $fecha);
            }

            // Esperar 500ms antes de reintentar
            usleep(500000);
        }

        if ($err) {
            error_log("[SerpApiService::searchFlights] cURL Error en intento $attempt: " . $err);
            return $this->getMockResponse($origen, $destino, $fecha);
        }

        $data = json_decode($response, true);

        if (isset($data['error'])) {
             return ['success' => false, 'error' => $data['error']];
        }

        $results = [];

        // Parseamos el mejor vuelo (best_flights)
        if (!empty($data['best_flights'])) {
            foreach ($data['best_flights'] as $f) {
                if (count($results) >= 5) break;
                
                $flights_arr = $f['flights'] ?? [];
                if (empty($flights_arr)) continue;

                $airlines = array_unique(array_column($flights_arr, 'airline'));
                $airline_str = implode(', ', $airlines);
                
                $flight_numbers = array_column($flights_arr, 'flight_number');
                $flight_num_str = implode(', ', $flight_numbers);
                
                $departureTime = $flights_arr[0]['departure_airport']['time'] ?? null;
                $arrivalTime = $flights_arr[count($flights_arr)-1]['arrival_airport']['time'] ?? null;

                $results[] = [
                    'ruta' => strtoupper("{$origen} - {$destino}"),
                    'aerolinea' => $airline_str,
                    'numero' => $flight_num_str,
                    'salida_iso' => $departureTime ? date('Y-m-d\TH:i', strtotime($departureTime)) : "{$fecha}T08:00",
                    'llegada_iso' => $arrivalTime ? date('Y-m-d\TH:i', strtotime($arrivalTime)) : "{$fecha}T12:00",
                ];
            }
        }
        
        // Parseamos other_flights si no hubo 5
        if (count($results) < 5 && !empty($data['other_flights'])) {
            foreach ($data['other_flights'] as $f) {
                if (count($results) >= 5) break;
                
                $flights_arr = $f['flights'] ?? [];
                if (empty($flights_arr)) continue;

                $airlines = array_unique(array_column($flights_arr, 'airline'));
                $airline_str = implode(', ', $airlines);
                
                $flight_numbers = array_column($flights_arr, 'flight_number');
                $flight_num_str = implode(', ', $flight_numbers);
                
                $departureTime = $flights_arr[0]['departure_airport']['time'] ?? null;
                $arrivalTime = $flights_arr[count($flights_arr)-1]['arrival_airport']['time'] ?? null;

                $results[] = [
                    'ruta' => strtoupper("{$origen} - {$destino}"),
                    'aerolinea' => $airline_str,
                    'numero' => $flight_num_str,
                    'salida_iso' => $departureTime ? date('Y-m-d\TH:i', strtotime($departureTime)) : "{$fecha}T08:00",
                    'llegada_iso' => $arrivalTime ? date('Y-m-d\TH:i', strtotime($arrivalTime)) : "{$fecha}T12:00",
                ];
            }
        }

        if (empty($results)) {
            return ['success' => false, 'error' => 'No se encontraron vuelos para esta ruta.'];
        }

        return ['success' => true, 'vuelos' => $results];
    }

    /**
     * Busca aeropuertos (Travelpayouts API en lugar de google_flights_airports que requiere plan superior)
     */
    public function searchAirports(string $query): array {
        $params = http_build_query([
            'term' => $query,
            'locale' => 'es'
        ]);

        $url = "https://autocomplete.travelpayouts.com/places2?" . $params . "&types[]=airport&types[]=city";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return ['success' => false, 'error' => "cURL Error: " . $err];
        }

        $data = json_decode($response, true);
        if (!is_array($data)) {
             return ['success' => false, 'error' => 'API falló'];
        }

        $results = [];
        foreach ($data as $place) {
            $code = $place['code'] ?? '';
            $name = $place['name'] ?? '';
            $country = $place['country_name'] ?? '';
            
            if ($code && $name) {
                $results[] = [
                    'id' => $code,
                    'name' => $name . ($country ? ", $country" : "")
                ];
            }
        }

        return ['success' => true, 'airports' => $results];
    }

    /**
     * Busca solo ciudades usando Travelpayouts Places2 (types[]=city)
     * @param string $query
     * @return array
     */
    public function searchCities(string $query): array {
        $params = http_build_query([
            'term' => $query,
            'locale' => 'es'
        ]);

        // Solo ciudades
        $url = "https://autocomplete.travelpayouts.com/places2?" . $params . "&types[]=city";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 8);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return ['success' => false, 'error' => "cURL Error: " . $err];
        }

        $data = json_decode($response, true);
        if (!is_array($data)) {
            return ['success' => false, 'error' => 'API falló'];
        }

        $results = [];
        foreach ($data as $place) {
            // Travelpayouts returns cities with "type" => "city"; include useful fields
            $type = $place['type'] ?? '';
            if ($type !== 'city') continue;

            $id = $place['code'] ?? ($place['id'] ?? '');
            $name = $place['name'] ?? '';
            $country = $place['country_name'] ?? ($place['country'] ?? '');
            $region = $place['region_name'] ?? ($place['region'] ?? '');

            if ($name) {
                $results[] = [
                    'id' => $id,
                    'name' => $name,
                    'country' => $country,
                    'region' => $region,
                    'type' => 'city'
                ];
            }
        }

        return ['success' => true, 'cities' => $results];
    }

    private function getMockResponse(string $origen, string $destino, string $fecha): array {
        // Simular retardo de red
        usleep(800000); // 800ms
        
        $opciones = ['LATAM Airlines', 'Avianca', 'Copa Airlines', 'American Airlines', 'Aeroméxico'];
        $results = [];
        
        for ($i=0; $i<3; $i++) {
            $aerolinea = $opciones[array_rand($opciones)];
            $horaSalida = str_pad(rand(6, 18), 2, '0', STR_PAD_LEFT) . ':' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT);
            $horaLlegada = str_pad(rand(12, 23), 2, '0', STR_PAD_LEFT) . ':' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT);

            $results[] = [
                'ruta' => strtoupper($origen) . ' - ' . strtoupper($destino),
                'aerolinea' => $aerolinea,
                'numero' => (string)rand(100, 9999),
                'salida_iso' => "{$fecha}T{$horaSalida}",
                'llegada_iso' => "{$fecha}T{$horaLlegada}",
            ];
        }

        return [
            'success' => true,
            'source'  => 'mock',
            'vuelos' => $results
        ];
    }

    /**
     * Busca Hoteles mediante SerpAPI (Google Hotels)
     * @param string $query Nombre del hotel o ubicación
     * @return array
     */
    public function searchHotels(string $query): array {
        if (empty($this->apiKey) || $this->apiKey === 'demo') {
            // Retornar datos mock locales cuando no hay API key (desarrollo/demo)
            usleep(300000); // Simular delay de red (300ms)
            return [
                'success' => true,
                'source' => 'mock',
                'hoteles' => [
                    ['nombre' => 'Hard Rock Hotel & Casino Punta Cana', 'rating' => '4.5'],
                    ['nombre' => 'Barceló Bávaro Palace', 'rating' => '4.7'],
                    ['nombre' => 'Riu Republica', 'rating' => '4.3'],
                    ['nombre' => 'Meliá Caribe Tropical', 'rating' => '4.4'],
                    ['nombre' => 'Secrets Royal Beach Punta Cana', 'rating' => '4.6']
                ]
            ];
        }

        $params = http_build_query([
            'engine' => 'google_hotels',
            'q' => $query,
            'hl' => 'es',
            'currency' => 'USD',
            'api_key' => $this->apiKey
        ]);

        $url = "https://serpapi.com/search.json?" . $params;

        // Reintentos automáticos
        $maxRetries = 2;
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 8); // 8 segundos para hoteles
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            $response = curl_exec($ch);
            $err = curl_error($ch);
            curl_close($ch);

            if (!$err && !empty($response)) {
                break;
            }

            if ($attempt === $maxRetries) {
                error_log("[SerpApiService::searchHotels] Falló después de $maxRetries intentos: $err. Usando mock data.");
                // Fallback a datos mock si falla
                return [
                    'success' => true,
                    'source' => 'mock_fallback',
                    'hoteles' => [
                        ['nombre' => 'Hard Rock Hotel & Casino Punta Cana', 'rating' => '4.5'],
                        ['nombre' => 'Barceló Bávaro Palace', 'rating' => '4.7'],
                        ['nombre' => 'Riu Republica', 'rating' => '4.3']
                    ]
                ];
            }

            usleep(500000); // 500ms antes de reintentar
        }

        $data = json_decode($response, true);

        if (isset($data['error'])) {
            error_log("[SerpApiService::searchHotels] API Error: " . $data['error']);
            // Fallback a mock si hay error
            return [
                'success' => true,
                'source' => 'mock_error_fallback',
                'hoteles' => [
                    ['nombre' => 'Hard Rock Hotel & Casino Punta Cana', 'rating' => '4.5'],
                    ['nombre' => 'Barceló Bávaro Palace', 'rating' => '4.7'],
                    ['nombre' => 'Riu Republica', 'rating' => '4.3'],
                    ['nombre' => 'Meliá Caribe Tropical', 'rating' => '4.4'],
                    ['nombre' => 'Secrets Royal Beach Punta Cana', 'rating' => '4.6']
                ]
            ];
        }

        $results = [];
        if (!empty($data['properties'])) {
            foreach (array_slice($data['properties'], 0, 5) as $prop) {
                $results[] = [
                    'nombre' => $prop['name'] ?? 'Desconocido',
                    'rating' => $prop['overall_rating'] ?? $prop['review_rating'] ?? ''
                ];
            }
        }

        // Si no hay resultados de la API real, usar mock data
        if (empty($results)) {
            error_log("[SerpApiService::searchHotels] Query '$query' devolvió 0 resultados. Usando mock data.");
            return [
                'success' => true,
                'source' => 'mock_no_results',
                'hoteles' => [
                    ['nombre' => 'Hard Rock Hotel & Casino Punta Cana', 'rating' => '4.5'],
                    ['nombre' => 'Barceló Bávaro Palace', 'rating' => '4.7'],
                    ['nombre' => 'Riu Republica', 'rating' => '4.3'],
                    ['nombre' => 'Meliá Caribe Tropical', 'rating' => '4.4'],
                    ['nombre' => 'Secrets Royal Beach Punta Cana', 'rating' => '4.6']
                ]
            ];
        }

        return ['success' => true, 'hoteles' => $results];
    }

    /**
     * Obtiene la primera imagen de destino usando SerpAPI (Google Images)
     * @param string $query Término de búsqueda (ej: "París Francia turismo")
     * @return string|null URL de la imagen o null si no se encontró
     */
    public function getFirstImage(string $query): ?string {
        if (empty($this->apiKey) || $this->apiKey === 'demo') {
            return null; // El controlador usará el fallback de Unsplash
        }

        $params = http_build_query([
            'engine'  => 'google_images',
            'q'       => $query,
            'hl'      => 'es',
            'safe'    => 'active',
            'num'     => '3',
            'api_key' => $this->apiKey
        ]);

        $url = "https://serpapi.com/search.json?" . $params;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return null;
        }

        $data = json_decode($response, true);

        // SerpAPI devuelve images_results con original/thumbnail
        if (!empty($data['images_results'])) {
            foreach ($data['images_results'] as $img) {
                $imgUrl = $img['original'] ?? $img['thumbnail'] ?? null;
                if ($imgUrl && filter_var($imgUrl, FILTER_VALIDATE_URL)) {
                    return $imgUrl;
                }
            }
        }

        return null;
    }

    /**
     * Busca lugares turísticos en un destino usando SerpAPI (Google Local)
     * @param string $query Nombre del destino (ej: "París Francia")
     * @return array Lista de lugares con nombre, rating e imagen
     */
    public function searchPlaces(string $query): array {
        if (empty($this->apiKey) || $this->apiKey === 'demo') {
            // Datos mock para desarrollo
            usleep(300000);
            return [
                ['name' => 'Torre Eiffel',         'rating' => '4.7', 'image' => ''],
                ['name' => 'Museo del Louvre',      'rating' => '4.8', 'image' => ''],
                ['name' => 'Arco del Triunfo',      'rating' => '4.6', 'image' => ''],
            ];
        }

        $params = http_build_query([
            'engine'  => 'google_local',
            'q'       => 'tourist attractions in ' . $query,
            'hl'      => 'es',
            'num'     => '5',
            'api_key' => $this->apiKey
        ]);

        $url = "https://serpapi.com/search.json?" . $params;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return [];
        }

        $data = json_decode($response, true);

        $results = [];
        if (!empty($data['local_results'])) {
            foreach (array_slice($data['local_results'], 0, 5) as $place) {
                $results[] = [
                    'name'   => $place['title']          ?? 'Lugar turístico',
                    'rating' => $place['rating']         ?? '',
                    'image'  => $place['thumbnail']      ?? '',
                ];
            }
        }

        return $results;
    }
}
