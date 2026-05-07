<?php
/**
 * DestinationHelper.php — Helper centralizado de imágenes por destino
 * 
 * Proporciona imágenes de alta calidad (Unsplash) para cualquier destino.
 * Si el destino no está en el catálogo, genera una URL de búsqueda dinámica.
 * 
 * USO:
 *   require_once __DIR__ . '/../helpers/DestinationHelper.php';
 *   $heroImg = DestinationHelper::getHeroImage('Punta Cana');
 *   $cardImg = DestinationHelper::getCardImage('Cusco');
 *   $icon    = DestinationHelper::getIcon('Cancún');
 */
class DestinationHelper {

    /**
     * Catálogo de destinos con imágenes curadas de Unsplash
     * Cada destino tiene: hero (1920px), card (800px), icon (emoji), type
     */
    private static array $catalog = [
        // ═══════════ PERÚ ═══════════
        'cusco' => [
            'hero' => 'https://images.unsplash.com/photo-1526392060635-9d6019884377?w=1920&q=90&fit=crop',
            'card' => 'https://images.unsplash.com/photo-1526392060635-9d6019884377?w=800&q=85&fit=crop',
            'icon' => '🏔️', 'type' => 'mountain',
        ],
        'machu picchu' => [
            'hero' => 'https://images.unsplash.com/photo-1587595431973-160d0d94add1?w=1920&q=90&fit=crop',
            'card' => 'https://images.unsplash.com/photo-1587595431973-160d0d94add1?w=800&q=85&fit=crop',
            'icon' => '🏛️', 'type' => 'ruins',
        ],
        'lima' => [
            'hero' => 'https://images.unsplash.com/photo-1531968455001-5c5272a67c71?w=1920&q=90&fit=crop',
            'card' => 'https://images.unsplash.com/photo-1531968455001-5c5272a67c71?w=800&q=85&fit=crop',
            'icon' => '🏙️', 'type' => 'city',
        ],
        'iquitos' => [
            'hero' => 'https://images.unsplash.com/photo-1516026672322-bc52d61a55d5?w=1920&q=90&fit=crop',
            'card' => 'https://images.unsplash.com/photo-1516026672322-bc52d61a55d5?w=800&q=85&fit=crop',
            'icon' => '🌳', 'type' => 'jungle',
        ],
        'arequipa' => [
            'hero' => 'https://images.unsplash.com/photo-1580477667995-2b94f01c9516?w=1920&q=90&fit=crop',
            'card' => 'https://images.unsplash.com/photo-1580477667995-2b94f01c9516?w=800&q=85&fit=crop',
            'icon' => '🌋', 'type' => 'mountain',
        ],

        // ═══════════ CARIBE ═══════════
        'cancun' => [
            'hero' => 'https://images.unsplash.com/photo-1510414842594-a61c69b5ae57?w=1920&q=90&fit=crop',
            'card' => 'https://images.unsplash.com/photo-1510414842594-a61c69b5ae57?w=800&q=85&fit=crop',
            'icon' => '🏖️', 'type' => 'beach',
        ],
        'punta cana' => [
            'hero' => 'https://images.unsplash.com/photo-1506929562872-bb421503ef21?w=1920&q=90&fit=crop',
            'card' => 'https://images.unsplash.com/photo-1506929562872-bb421503ef21?w=800&q=85&fit=crop',
            'icon' => '🌴', 'type' => 'beach',
        ],
        'cartagena' => [
            'hero' => 'https://images.unsplash.com/photo-1583997052301-0fc38714e428?w=1920&q=90&fit=crop',
            'card' => 'https://images.unsplash.com/photo-1583997052301-0fc38714e428?w=800&q=85&fit=crop',
            'icon' => '🏰', 'type' => 'city',
        ],
        'san andres' => [
            'hero' => 'https://images.unsplash.com/photo-1559494007-9f5847c49d94?w=1920&q=90&fit=crop',
            'card' => 'https://images.unsplash.com/photo-1559494007-9f5847c49d94?w=800&q=85&fit=crop',
            'icon' => '🏝️', 'type' => 'beach',
        ],
        'varadero' => [
            'hero' => 'https://images.unsplash.com/photo-1519046904884-53103b34b206?w=1920&q=90&fit=crop',
            'card' => 'https://images.unsplash.com/photo-1519046904884-53103b34b206?w=800&q=85&fit=crop',
            'icon' => '🏖️', 'type' => 'beach',
        ],

        // ═══════════ NORTEAMÉRICA ═══════════
        'miami' => [
            'hero' => 'https://images.unsplash.com/photo-1533106497176-45ae19e68ba2?w=1920&q=90&fit=crop',
            'card' => 'https://images.unsplash.com/photo-1533106497176-45ae19e68ba2?w=800&q=85&fit=crop',
            'icon' => '🌴', 'type' => 'beach',
        ],
        'nueva york' => [
            'hero' => 'https://images.unsplash.com/photo-1496442226666-8d4d0e62e6e9?w=1920&q=90&fit=crop',
            'card' => 'https://images.unsplash.com/photo-1496442226666-8d4d0e62e6e9?w=800&q=85&fit=crop',
            'icon' => '🗽', 'type' => 'city',
        ],
        'new york' => [
            'hero' => 'https://images.unsplash.com/photo-1496442226666-8d4d0e62e6e9?w=1920&q=90&fit=crop',
            'card' => 'https://images.unsplash.com/photo-1496442226666-8d4d0e62e6e9?w=800&q=85&fit=crop',
            'icon' => '🗽', 'type' => 'city',
        ],
        'orlando' => [
            'hero' => 'https://images.unsplash.com/photo-1575089976121-8ed7b2a54265?w=1920&q=90&fit=crop',
            'card' => 'https://images.unsplash.com/photo-1575089976121-8ed7b2a54265?w=800&q=85&fit=crop',
            'icon' => '🎢', 'type' => 'theme_park',
        ],
        'las vegas' => [
            'hero' => 'https://images.unsplash.com/photo-1605833556294-ea5c7a74f57d?w=1920&q=90&fit=crop',
            'card' => 'https://images.unsplash.com/photo-1605833556294-ea5c7a74f57d?w=800&q=85&fit=crop',
            'icon' => '🎰', 'type' => 'city',
        ],

        // ═══════════ SUDAMÉRICA ═══════════
        'rio de janeiro' => [
            'hero' => 'https://images.unsplash.com/photo-1483729558449-99ef09a8c325?w=1920&q=90&fit=crop',
            'card' => 'https://images.unsplash.com/photo-1483729558449-99ef09a8c325?w=800&q=85&fit=crop',
            'icon' => '🏖️', 'type' => 'beach',
        ],
        'buenos aires' => [
            'hero' => 'https://images.unsplash.com/photo-1589909202802-8f4aadce1849?w=1920&q=90&fit=crop',
            'card' => 'https://images.unsplash.com/photo-1589909202802-8f4aadce1849?w=800&q=85&fit=crop',
            'icon' => '💃', 'type' => 'city',
        ],
        'bogota' => [
            'hero' => 'https://images.unsplash.com/photo-1568034304837-671dd3e0e251?w=1920&q=90&fit=crop',
            'card' => 'https://images.unsplash.com/photo-1568034304837-671dd3e0e251?w=800&q=85&fit=crop',
            'icon' => '🏙️', 'type' => 'city',
        ],
        'santiago' => [
            'hero' => 'https://images.unsplash.com/photo-1591111280319-e7811a1a5a81?w=1920&q=90&fit=crop',
            'card' => 'https://images.unsplash.com/photo-1591111280319-e7811a1a5a81?w=800&q=85&fit=crop',
            'icon' => '🏔️', 'type' => 'city',
        ],

        // ═══════════ EUROPA ═══════════
        'paris' => [
            'hero' => 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=1920&q=90&fit=crop',
            'card' => 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=800&q=85&fit=crop',
            'icon' => '🗼', 'type' => 'city',
        ],
        'roma' => [
            'hero' => 'https://images.unsplash.com/photo-1552832230-c0197dd311b5?w=1920&q=90&fit=crop',
            'card' => 'https://images.unsplash.com/photo-1552832230-c0197dd311b5?w=800&q=85&fit=crop',
            'icon' => '🏛️', 'type' => 'city',
        ],
        'barcelona' => [
            'hero' => 'https://images.unsplash.com/photo-1583422409516-2895a77efded?w=1920&q=90&fit=crop',
            'card' => 'https://images.unsplash.com/photo-1583422409516-2895a77efded?w=800&q=85&fit=crop',
            'icon' => '🏗️', 'type' => 'city',
        ],
        'londres' => [
            'hero' => 'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?w=1920&q=90&fit=crop',
            'card' => 'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?w=800&q=85&fit=crop',
            'icon' => '🇬🇧', 'type' => 'city',
        ],
        'amsterdam' => [
            'hero' => 'https://images.unsplash.com/photo-1534351590666-13e3e96b5017?w=1920&q=90&fit=crop',
            'card' => 'https://images.unsplash.com/photo-1534351590666-13e3e96b5017?w=800&q=85&fit=crop',
            'icon' => '🌷', 'type' => 'city',
        ],

        // ═══════════ ASIA / OCEANÍA ═══════════
        'bali' => [
            'hero' => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=1920&q=90&fit=crop',
            'card' => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=800&q=85&fit=crop',
            'icon' => '🛕', 'type' => 'tropical',
        ],
        'tokio' => [
            'hero' => 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=1920&q=90&fit=crop',
            'card' => 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=800&q=85&fit=crop',
            'icon' => '🗼', 'type' => 'city',
        ],
        'tailandia' => [
            'hero' => 'https://images.unsplash.com/photo-1528181304800-259b08848526?w=1920&q=90&fit=crop',
            'card' => 'https://images.unsplash.com/photo-1528181304800-259b08848526?w=800&q=85&fit=crop',
            'icon' => '🛕', 'type' => 'tropical',
        ],
        'dubai' => [
            'hero' => 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=1920&q=90&fit=crop',
            'card' => 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=800&q=85&fit=crop',
            'icon' => '🏙️', 'type' => 'city',
        ],

        // ═══════════ MÉXICO ═══════════
        'mexico' => [
            'hero' => 'https://images.unsplash.com/photo-1518638150340-f706e86654de?w=1920&q=90&fit=crop',
            'card' => 'https://images.unsplash.com/photo-1518638150340-f706e86654de?w=800&q=85&fit=crop',
            'icon' => '🇲🇽', 'type' => 'city',
        ],
        'riviera maya' => [
            'hero' => 'https://images.unsplash.com/photo-1552074284-5e88ef1aef18?w=1920&q=90&fit=crop',
            'card' => 'https://images.unsplash.com/photo-1552074284-5e88ef1aef18?w=800&q=85&fit=crop',
            'icon' => '🏖️', 'type' => 'beach',
        ],
        'playa del carmen' => [
            'hero' => 'https://images.unsplash.com/photo-1552074284-5e88ef1aef18?w=1920&q=90&fit=crop',
            'card' => 'https://images.unsplash.com/photo-1552074284-5e88ef1aef18?w=800&q=85&fit=crop',
            'icon' => '🏖️', 'type' => 'beach',
        ],
    ];

    /** Imagen por defecto (playa tropical genérica) */
    private static string $defaultHero = 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1920&q=90&fit=crop';
    private static string $defaultCard = 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&q=85&fit=crop';

    /**
     * Busca un destino en el catálogo (búsqueda parcial, case-insensitive)
     */
    private static function findDestination(string $destination): ?array {
        $dest = strtolower(trim($destination));
        // Remover acentos comunes para matching
        $dest = str_replace(
            ['á','é','í','ó','ú','ñ','ü'],
            ['a','e','i','o','u','n','u'],
            $dest
        );

        // Búsqueda exacta primero
        if (isset(self::$catalog[$dest])) {
            return self::$catalog[$dest];
        }

        // Búsqueda parcial (el destino contiene alguna keyword del catálogo)
        foreach (self::$catalog as $key => $data) {
            if (strpos($dest, $key) !== false || strpos($key, $dest) !== false) {
                return $data;
            }
        }

        return null;
    }

    /**
     * Obtener imagen hero (1920px) para un destino
     */
    public static function getHeroImage(string $destination): string {
        $found = self::findDestination($destination);
        if ($found) return $found['hero'];
        
        // Fallback: URL de búsqueda dinámica de Unsplash (siempre funciona)
        $query = urlencode(trim($destination) . ' travel landscape');
        return "https://images.unsplash.com/photos/random?query={$query}&w=1920&q=90&fit=crop&orientation=landscape";
    }

    /**
     * Obtener imagen para tarjeta (800px) 
     */
    public static function getCardImage(string $destination): string {
        $found = self::findDestination($destination);
        if ($found) return $found['card'];
        
        $query = urlencode(trim($destination) . ' travel');
        return "https://images.unsplash.com/photos/random?query={$query}&w=800&q=85&fit=crop";
    }

    /**
     * Obtener emoji/icono del destino
     */
    public static function getIcon(string $destination): string {
        $found = self::findDestination($destination);
        return $found['icon'] ?? '✈️';
    }

    /**
     * Obtener tipo de destino (beach, city, mountain, etc.)
     */
    public static function getType(string $destination): string {
        $found = self::findDestination($destination);
        return $found['type'] ?? 'travel';
    }

    /**
     * Obtener color de acento según el tipo de destino
     */
    public static function getAccentColor(string $destination): string {
        $type = self::getType($destination);
        return match($type) {
            'beach'      => '#4ABED9', // turquesa
            'mountain'   => '#2D5468', // petroleo-light
            'city'       => '#1B3A4B', // petroleo
            'jungle'     => '#059669', // emerald
            'tropical'   => '#F4A633', // gold
            'ruins'      => '#D4860F', // gold-dark
            'theme_park' => '#FF6B6B', // coral
            default      => '#4ABED9', // turquesa
        };
    }

    /**
     * Material icon según el tipo de destino
     */
    public static function getMaterialIcon(string $destination): string {
        $type = self::getType($destination);
        return match($type) {
            'beach'      => 'beach_access',
            'mountain'   => 'landscape',
            'city'       => 'location_city',
            'jungle'     => 'forest',
            'tropical'   => 'spa',
            'ruins'      => 'temple_hindu',
            'theme_park' => 'attractions',
            default      => 'flight_takeoff',
        };
    }
}
