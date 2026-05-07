<?php
require_once __DIR__ . '/core/Database.php';

$db = Database::getInstance();

$promotions = [
    [
        'titulo' => 'Cyber Days Punta Cana - Catalonia',
        'descripcion' => 'Boleto aéreo, Traslados, Todo incluido, Hotel, T. Asistencia.',
        'destino' => 'Punta Cana',
        'descuento' => 'Desde US$ 1025', // 935 + 90
        'imagen' => 'cyber_punta_cana.jpg',
        'fecha_fin' => '2026-04-26',
        'activa' => 1
    ],
    [
        'titulo' => 'Cyber Days Panamá Ciudad y Playa',
        'descripcion' => 'Ticket, Traslados, Según hotel, Hotel, T. Asistencia.',
        'destino' => 'Panamá',
        'descuento' => 'Desde US$ 939', // 849 + 90
        'imagen' => 'cyber_panama_ciudad.jpg',
        'fecha_fin' => '2026-04-26',
        'activa' => 1
    ],
    [
        'titulo' => 'Cyber Days Panamá',
        'descripcion' => 'Boleto aéreo, Traslados, Todo incluido, Hotel, T. Asistencia.',
        'destino' => 'Panamá',
        'descuento' => 'Desde US$ 1225', // 1135 + 90
        'imagen' => 'cyber_panama.jpg',
        'fecha_fin' => '2026-04-26',
        'activa' => 1
    ],
    [
        'titulo' => 'Cyber Days Curacao y Panamá desde Chiclayo',
        'descripcion' => 'Ticket, Traslados, Según hotel, Hotel, T. Asistencia, Tours.',
        'destino' => 'Curacao y Panamá',
        'descuento' => 'Desde US$ 1385', // 1295 + 90
        'imagen' => 'cyber_curacao_panama.jpg',
        'fecha_fin' => '2026-04-26',
        'activa' => 1
    ],
    [
        'titulo' => 'Cyber Days Rio de Janeiro y Panamá',
        'descripcion' => 'Ticket, Traslados, Desayunos, Hotel, T. Asistencia, Tours.',
        'destino' => 'Rio de Janeiro y Panamá',
        'descuento' => 'Desde US$ 1489', // 1399 + 90
        'imagen' => 'cyber_rio_panama.jpg',
        'fecha_fin' => '2026-04-26',
        'activa' => 1
    ]
];

foreach ($promotions as $promo) {
    try {
        $db->insert('promociones', $promo);
        echo "Inserted: {$promo['titulo']}\n";
    } catch (Exception $e) {
        echo "Error inserting {$promo['titulo']}: " . $e->getMessage() . "\n";
    }
}
echo "Done.";
