<?php
/**
 * ImageApiController.php - Endpoints de proxy para SerpAPI
 * /api/images  → imagen de destinos
 * /api/hotels  → hoteles de destinos
 * /api/places  → lugares turísticos
 */
class ImageApiController extends Controller {

    private function getCachePath(string $prefix, string $query): string {
        $cacheDir = STORAGE_PATH . '/cache/serpapi';
        if (!is_dir($cacheDir)) @mkdir($cacheDir, 0755, true);
        return $cacheDir . '/' . $prefix . '_' . md5(strtolower(trim($query))) . '.json';
    }

    private function getCache(string $prefix, string $query, int $ttl = 86400): ?array {
        $file = $this->getCachePath($prefix, $query);
        if (file_exists($file) && (time() - filemtime($file)) < $ttl) {
            $data = json_decode(file_get_contents($file), true);
            if ($data) return $data;
        }
        return null;
    }

    private function setCache(string $prefix, string $query, array $data): void {
        @file_put_contents($this->getCachePath($prefix, $query), json_encode($data));
    }

    /**
     * GET /api/images?q=... → Proxy de imagen de destino
     */
    public function getImage(): void {
        $query = trim($_GET['q'] ?? '');
        if (empty($query)) {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => 'Query requerida']);
            return;
        }

        // Check image cache first
        $slug = preg_replace('/[^a-z0-9_-]/i', '-', strtolower(trim($query)));
        $cacheDir = STORAGE_PATH . '/cache/serpapi';
        if (!is_dir($cacheDir)) @mkdir($cacheDir, 0755, true);
        $cacheFile = $cacheDir . '/img_' . md5($query) . '.jpg';
        
        if (file_exists($cacheFile) && filesize($cacheFile) > 0) {
            $mime = mime_content_type($cacheFile) ?: 'image/jpeg';
            header('Content-Type: ' . $mime);
            header('Cache-Control: public, max-age=604800');
            readfile($cacheFile);
            return;
        }

        $serpApi = new SerpApiService();
        $imageUrl = $serpApi->getFirstImage($query);

        if ($imageUrl) {
            $ctx = stream_context_create(['http' => ['timeout' => 8, 'header' => "User-Agent: Mozilla/5.0\r\nAccept: image/*\r\n"]]);
            $imageData = @file_get_contents($imageUrl, false, $ctx);
            if ($imageData) {
                // Cache the image
                @file_put_contents($cacheFile, $imageData);
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime = $finfo->buffer($imageData);
                header('Content-Type: ' . $mime);
                header('Cache-Control: public, max-age=604800');
                echo $imageData;
                return;
            }
        }

        // Fallback: redirect to a placeholder
        header('Location: https://images.unsplash.com/photo-1526392060635-9d6019884377?w=800&q=60');
    }

    /**
     * GET /api/hotels?q=... → JSON de hoteles en destino
     */
    public function getHotels(): void {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: public, max-age=86400');
        
        $query = trim($_GET['q'] ?? '');
        if (empty($query)) {
            echo json_encode(['hotels' => []]);
            return;
        }

        // Check cache first
        $cached = $this->getCache('hotel', $query);
        if ($cached) {
            echo json_encode($cached);
            return;
        }

        $serpApi = new SerpApiService();
        $result = $serpApi->searchHotels($query);

        $hotels = [];
        if (!empty($result['hoteles'])) {
            foreach ($result['hoteles'] as $h) {
                $name = $h['nombre'] ?? $h['name'] ?? 'Hotel';
                $rating = $h['rating'] ?? $h['overall_rating'] ?? '4.0';
                $image = $h['image'] ?? $h['thumbnail'] ?? '';
                if (empty($image)) {
                    $image = Router::url('/api/images') . '?q=' . urlencode($name . ' hotel ' . $query);
                }
                $hotels[] = [
                    'name' => $name,
                    'rating' => (float)$rating,
                    'image' => $image,
                    'price' => $h['price'] ?? '',
                ];
            }
        }

        // Si no hay resultados de la API, generar hoteles sugeridos basados en el destino
        if (empty($hotels)) {
            $hotels = $this->getFallbackHotels($query);
        }

        $response = ['hotels' => $hotels, 'query' => $query];
        $this->setCache('hotel', $query, $response);
        echo json_encode($response);
    }

    /**
     * GET /api/places?q=... → JSON de lugares turísticos
     */
    public function getPlaces(): void {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: public, max-age=86400');
        
        $query = trim($_GET['q'] ?? '');
        if (empty($query)) {
            echo json_encode(['places' => []]);
            return;
        }

        // Check cache first
        $cached = $this->getCache('place', $query);
        if ($cached) {
            echo json_encode($cached);
            return;
        }

        $serpApi = new SerpApiService();
        $places = $serpApi->searchPlaces($query);

        // Fill missing images via proxy URL
        foreach ($places as &$p) {
            if (empty($p['image'])) {
                $p['image'] = Router::url('/api/images') . '?q=' . urlencode($p['name'] . ' tourist attraction');
            }
        }

        $response = ['places' => $places, 'query' => $query];
        if (!empty($places)) $this->setCache('place', $query, $response);
        echo json_encode($response);
    }

    /**
     * Hoteles fallback cuando SerpApi no devuelve resultados
     */
    private function getFallbackHotels(string $query): array {
        $q = strtolower($query);
        $fallbackDb = [
            'cusco' => [
                ['name' => 'JW Marriott El Convento Cusco', 'rating' => 4.8, 'price' => 'Desde $280/noche', 'image' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=200&q=80&fit=crop'],
                ['name' => 'Belmond Hotel Monasterio', 'rating' => 4.7, 'price' => 'Desde $350/noche', 'image' => 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=200&q=80&fit=crop'],
                ['name' => 'Palacio del Inka, Luxury Collection', 'rating' => 4.6, 'price' => 'Desde $220/noche', 'image' => 'https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=200&q=80&fit=crop'],
                ['name' => 'Aranwa Cusco Boutique Hotel', 'rating' => 4.5, 'price' => 'Desde $180/noche', 'image' => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=200&q=80&fit=crop'],
            ],
            'cancun' => [
                ['name' => 'Hyatt Ziva Cancún', 'rating' => 4.7, 'price' => 'Desde $320/noche', 'image' => 'https://images.unsplash.com/photo-1582719508461-905c673771fd?w=200&q=80&fit=crop'],
                ['name' => 'Riu Palace Las Américas', 'rating' => 4.5, 'price' => 'Desde $280/noche', 'image' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=200&q=80&fit=crop'],
                ['name' => 'Grand Fiesta Americana Coral Beach', 'rating' => 4.6, 'price' => 'Desde $250/noche', 'image' => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=200&q=80&fit=crop'],
                ['name' => 'Dreams Sands Cancún', 'rating' => 4.4, 'price' => 'Desde $200/noche', 'image' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=200&q=80&fit=crop'],
            ],
            'punta cana' => [
                ['name' => 'Hard Rock Hotel & Casino Punta Cana', 'rating' => 4.5, 'price' => 'Desde $350/noche', 'image' => 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=200&q=80&fit=crop'],
                ['name' => 'Barceló Bávaro Palace', 'rating' => 4.7, 'price' => 'Desde $300/noche', 'image' => 'https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=200&q=80&fit=crop'],
                ['name' => 'Riu Republica', 'rating' => 4.3, 'price' => 'Desde $180/noche', 'image' => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=200&q=80&fit=crop'],
                ['name' => 'Secrets Royal Beach Punta Cana', 'rating' => 4.6, 'price' => 'Desde $280/noche', 'image' => 'https://images.unsplash.com/photo-1582719508461-905c673771fd?w=200&q=80&fit=crop'],
            ],
            'paris' => [
                ['name' => 'Le Marais Boutique Hotel', 'rating' => 4.5, 'price' => 'Desde €180/noche', 'image' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=200&q=80&fit=crop'],
                ['name' => 'Hôtel Plaza Athénée', 'rating' => 4.8, 'price' => 'Desde €600/noche', 'image' => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=200&q=80&fit=crop'],
                ['name' => 'Citadines Saint-Germain-des-Prés', 'rating' => 4.3, 'price' => 'Desde €150/noche', 'image' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=200&q=80&fit=crop'],
                ['name' => 'Hôtel des Grands Boulevards', 'rating' => 4.4, 'price' => 'Desde €170/noche', 'image' => 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=200&q=80&fit=crop'],
            ],
            'roma' => [
                ['name' => 'Hotel de Russie', 'rating' => 4.7, 'price' => 'Desde €350/noche', 'image' => 'https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=200&q=80&fit=crop'],
                ['name' => 'Hotel Raphael', 'rating' => 4.5, 'price' => 'Desde €250/noche', 'image' => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=200&q=80&fit=crop'],
                ['name' => 'NH Collection Roma Centro', 'rating' => 4.3, 'price' => 'Desde €140/noche', 'image' => 'https://images.unsplash.com/photo-1582719508461-905c673771fd?w=200&q=80&fit=crop'],
                ['name' => 'Hotel Artemide', 'rating' => 4.6, 'price' => 'Desde €180/noche', 'image' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=200&q=80&fit=crop'],
            ],
            'miami' => [
                ['name' => 'Fontainebleau Miami Beach', 'rating' => 4.5, 'price' => 'Desde $280/noche', 'image' => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=200&q=80&fit=crop'],
                ['name' => 'The Setai Miami Beach', 'rating' => 4.7, 'price' => 'Desde $450/noche', 'image' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=200&q=80&fit=crop'],
                ['name' => 'Faena Hotel Miami Beach', 'rating' => 4.6, 'price' => 'Desde $380/noche', 'image' => 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=200&q=80&fit=crop'],
                ['name' => 'Hilton Miami Downtown', 'rating' => 4.2, 'price' => 'Desde $150/noche', 'image' => 'https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=200&q=80&fit=crop'],
            ],
            'bali' => [
                ['name' => 'Viceroy Bali', 'rating' => 4.8, 'price' => 'Desde $350/noche', 'image' => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=200&q=80&fit=crop'],
                ['name' => 'AYANA Resort Bali', 'rating' => 4.6, 'price' => 'Desde $200/noche', 'image' => 'https://images.unsplash.com/photo-1582719508461-905c673771fd?w=200&q=80&fit=crop'],
                ['name' => 'The Mulia Bali', 'rating' => 4.7, 'price' => 'Desde $280/noche', 'image' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=200&q=80&fit=crop'],
                ['name' => 'Alila Seminyak', 'rating' => 4.5, 'price' => 'Desde $180/noche', 'image' => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=200&q=80&fit=crop'],
            ],
            'lima' => [
                ['name' => 'Belmond Miraflores Park', 'rating' => 4.7, 'price' => 'Desde $220/noche', 'image' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=200&q=80&fit=crop'],
                ['name' => 'JW Marriott Hotel Lima', 'rating' => 4.6, 'price' => 'Desde $180/noche', 'image' => 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=200&q=80&fit=crop'],
                ['name' => 'Country Club Lima Hotel', 'rating' => 4.5, 'price' => 'Desde $160/noche', 'image' => 'https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=200&q=80&fit=crop'],
                ['name' => 'Hilton Lima Miraflores', 'rating' => 4.4, 'price' => 'Desde $130/noche', 'image' => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=200&q=80&fit=crop'],
            ],
            'orlando' => [
                ['name' => 'Disney\'s Grand Floridian Resort', 'rating' => 4.7, 'price' => 'Desde $450/noche', 'image' => 'https://images.unsplash.com/photo-1582719508461-905c673771fd?w=200&q=80&fit=crop'],
                ['name' => 'Loews Royal Pacific Resort', 'rating' => 4.5, 'price' => 'Desde $250/noche', 'image' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=200&q=80&fit=crop'],
                ['name' => 'Rosen Shingle Creek', 'rating' => 4.4, 'price' => 'Desde $180/noche', 'image' => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=200&q=80&fit=crop'],
                ['name' => 'Hilton Orlando Bonnet Creek', 'rating' => 4.3, 'price' => 'Desde $160/noche', 'image' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=200&q=80&fit=crop'],
            ],
            'londres' => [
                ['name' => 'The Savoy', 'rating' => 4.8, 'price' => 'Desde £400/noche', 'image' => 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=200&q=80&fit=crop'],
                ['name' => 'The Langham London', 'rating' => 4.6, 'price' => 'Desde £300/noche', 'image' => 'https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=200&q=80&fit=crop'],
                ['name' => 'Citizenm Tower of London', 'rating' => 4.4, 'price' => 'Desde £150/noche', 'image' => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=200&q=80&fit=crop'],
                ['name' => 'Premier Inn London City', 'rating' => 4.2, 'price' => 'Desde £100/noche', 'image' => 'https://images.unsplash.com/photo-1582719508461-905c673771fd?w=200&q=80&fit=crop'],
            ],
            'cartagena' => [
                ['name' => 'Sofitel Legend Santa Clara', 'rating' => 4.7, 'price' => 'Desde $250/noche', 'image' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=200&q=80&fit=crop'],
                ['name' => 'Hotel Charleston Santa Teresa', 'rating' => 4.6, 'price' => 'Desde $200/noche', 'image' => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=200&q=80&fit=crop'],
                ['name' => 'Casa San Agustín', 'rating' => 4.8, 'price' => 'Desde $320/noche', 'image' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=200&q=80&fit=crop'],
                ['name' => 'Hotel Almirante Cartagena', 'rating' => 4.3, 'price' => 'Desde $120/noche', 'image' => 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=200&q=80&fit=crop'],
            ],
        ];

        foreach ($fallbackDb as $key => $hotels) {
            if (strpos($q, $key) !== false) {
                return $hotels;
            }
        }

        // Si no está en la lista, generar hoteles genéricos con imágenes Unsplash
        return [
            ['name' => 'Hotel Premium ' . ucfirst($query), 'rating' => 4.5, 'price' => 'Consultar', 'image' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=200&q=80&fit=crop'],
            ['name' => 'Resort & Spa ' . ucfirst($query), 'rating' => 4.3, 'price' => 'Consultar', 'image' => 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=200&q=80&fit=crop'],
            ['name' => 'Hotel Boutique ' . ucfirst($query), 'rating' => 4.4, 'price' => 'Consultar', 'image' => 'https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=200&q=80&fit=crop'],
        ];
    }
}
