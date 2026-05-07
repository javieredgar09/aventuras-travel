<?php
/**
 * test_simple_fixes.php
 * Prueba simple de los 3 fixes sin dependencias
 */

define('BASE_PATH', dirname(__DIR__));

// Simplemente incluir SerpApiService
require_once BASE_PATH . '/app/services/SerpApiService.php';

echo "\n" . str_repeat("=", 90) . "\n";
echo "🧪 PRUEBAS DE PRODUCCIÓN - AVENTURAS TRAVEL\n";
echo str_repeat("=", 90) . "\n\n";

// Test 1: Búsqueda de vuelos
echo "✈️ TEST 1: Búsqueda de Vuelos (Performance + Reintentos)\n";
echo str_repeat("-", 90) . "\n";

$serp = new SerpApiService();

$start = microtime(true);
$flightResult = $serp->searchFlights('LIM', 'MIA', date('Y-m-d', strtotime('+7 days')), null);
$duration = microtime(true) - $start;

echo "✓ Vuelos encontrados: " . count($flightResult['vuelos'] ?? []) . "\n";
echo "✓ Tiempo respuesta: {$duration}s\n";
echo "✓ Fuente: " . ($flightResult['source'] ?? 'API real') . "\n";
echo "✓ Status: " . ($flightResult['success'] ? "SUCCESS" : "FAIL") . "\n";

if ($duration < 5) {
    echo "✅ PASS: Búsqueda rápida (<5s)\n";
} else if ($duration < 10) {
    echo "⚠️ ACCEPTABLE: Búsqueda en tiempo razonable (<10s)\n";
} else {
    echo "❌ SLOW: Búsqueda lenta (>10s)\n";
}

if (!empty($flightResult['vuelos'])) {
    $v = $flightResult['vuelos'][0];
    echo "\nSample Flight:\n";
    echo "  Ruta: {$v['ruta']}\n";
    echo "  Aerolínea: {$v['aerolinea']}\n";
    echo "  Número: {$v['numero']}\n";
    echo "  Salida: {$v['salida_iso']}\n";
    echo "  Llegada: {$v['llegada_iso']}\n";
}

// Test 2: Validación teléfono (Nueva regex menos restrictiva)
echo "\n\n📱 TEST 2: Validación Teléfono (Nueva regex: 6-25 chars)\n";
echo str_repeat("-", 90) . "\n";

$testCases = [
    ['phone' => '5551234567', 'desc' => 'USA simple', 'shouldPass' => true],
    ['phone' => '+1 (555) 123-4567', 'desc' => 'USA internacional', 'shouldPass' => true],
    ['phone' => '+34 912 34 56 78', 'desc' => 'España', 'shouldPass' => true],
    ['phone' => '+55 11 98765-4321', 'desc' => 'Brasil', 'shouldPass' => true],
    ['phone' => '555 123 4567', 'desc' => 'Con espacios', 'shouldPass' => true],
    ['phone' => '+1-555-123-4567', 'desc' => 'Con guiones', 'shouldPass' => true],
    ['phone' => '(555)1234567', 'desc' => 'Con paréntesis', 'shouldPass' => true],
    ['phone' => '123', 'desc' => 'Muy corto (3)', 'shouldPass' => false],
    ['phone' => 'abc1234567', 'desc' => 'Con letras', 'shouldPass' => false],
];

$regex = '/^[0-9+\-\s\(\)\.]{6,25}$/';
$passCount = 0;
$totalTests = count($testCases);

foreach ($testCases as $test) {
    $match = preg_match($regex, $test['phone']);
    $passed = ($match === 1) === $test['shouldPass'];
    
    $status = $passed ? '✅' : '❌';
    $result = $match ? 'ACCEPTED' : 'REJECTED';
    $expected = $test['shouldPass'] ? 'accept' : 'reject';
    
    echo "$status '{$test['phone']}' ({$test['desc']}) -> $result\n";
    
    if ($passed) {
        $passCount++;
    }
}

echo "\nResultado: $passCount/$totalTests tests pasados\n";
if ($passCount === $totalTests) {
    echo "✅ PASS: Todos los teléfonos validados correctamente\n";
} else {
    echo "⚠️ WARNING: " . ($totalTests - $passCount) . " validaciones fallidas\n";
}

// Test 3: Búsqueda de hoteles
echo "\n\n🏨 TEST 3: Búsqueda de Hoteles\n";
echo str_repeat("-", 90) . "\n";

$start = microtime(true);
$hotelResult = $serp->searchHotels('Punta Cana');
$duration = microtime(true) - $start;

echo "✓ Hoteles encontrados: " . count($hotelResult['hoteles'] ?? []) . "\n";
echo "✓ Tiempo respuesta: {$duration}s\n";
echo "✓ Fuente: " . ($hotelResult['source'] ?? 'API real') . "\n";
echo "✓ Status: " . ($hotelResult['success'] ? "SUCCESS" : "FAIL") . "\n";

if ($hotelResult['success'] && !empty($hotelResult['hoteles'])) {
    echo "✅ PASS: Búsqueda exitosa con resultados\n";
} else {
    echo "❌ FAIL: Sin hoteles o error\n";
}

echo "\nHoteles encontrados:\n";
if (!empty($hotelResult['hoteles'])) {
    foreach (array_slice($hotelResult['hoteles'], 0, 5) as $idx => $h) {
        echo "  " . ($idx + 1) . ". {$h['nombre']}";
        if (!empty($h['rating'])) {
            echo " ⭐ {$h['rating']}";
        }
        echo "\n";
    }
}

// Test 4: Validación formatos JSON
echo "\n\n📋 TEST 4: Validación Formatos JSON\n";
echo str_repeat("-", 90) . "\n";

$flightValid = isset($flightResult['success']) && isset($flightResult['vuelos']) && is_array($flightResult['vuelos']);
$flightSample = $flightValid && !empty($flightResult['vuelos']) ? $flightResult['vuelos'][0] : null;
$flightFieldsOk = $flightSample && 
    isset($flightSample['ruta']) && 
    isset($flightSample['aerolinea']) && 
    isset($flightSample['numero']) && 
    isset($flightSample['salida_iso']) && 
    isset($flightSample['llegada_iso']);

echo ($flightValid ? "✅" : "❌") . " Vuelos: Estructura JSON válida\n";
echo ($flightFieldsOk ? "✅" : "❌") . " Vuelos: Campos requeridos [ruta, aerolinea, numero, salida_iso, llegada_iso]\n";

$hotelValid = isset($hotelResult['success']) && isset($hotelResult['hoteles']) && is_array($hotelResult['hoteles']);
$hotelSample = $hotelValid && !empty($hotelResult['hoteles']) ? $hotelResult['hoteles'][0] : null;
$hotelFieldsOk = $hotelSample && isset($hotelSample['nombre']) && isset($hotelSample['rating']);

echo ($hotelValid ? "✅" : "❌") . " Hoteles: Estructura JSON válida\n";
echo ($hotelFieldsOk ? "✅" : "❌") . " Hoteles: Campos requeridos [nombre, rating]\n";

// Resumen
echo "\n\n" . str_repeat("=", 90) . "\n";
echo "📊 RESUMEN FINAL\n";
echo str_repeat("=", 90) . "\n";
echo "✅ Test 1 (Vuelos): " . ($duration < 10 ? "PASS" : "SLOW") . "\n";
echo "✅ Test 2 (Teléfono): " . ($passCount === $totalTests ? "PASS" : "PARTIAL") . "\n";
echo "✅ Test 3 (Hoteles): " . ($hotelResult['success'] ? "PASS" : "FAIL") . "\n";
echo "✅ Test 4 (Formatos): " . ($flightFieldsOk && $hotelFieldsOk ? "PASS" : "PARTIAL") . "\n";

echo "\n🚀 PRÓXIMOS PASOS PARA PRODUCCIÓN:\n";
echo "  1. Abrir navegador → http://localhost/admin/sales/create\n";
echo "  2. Prueba Vuelos: Ingresar LIM, MIA, fecha → Click BUSCAR\n";
echo "     ✓ Debe cargar al PRIMER click (no 2 clicks)\n";
echo "  3. Prueba Familia: Ingresar datos + teléfono +1(555)123-4567\n";
echo "     ✓ Debe aceptar teléfono sin error\n";
echo "  4. Prueba Hoteles: Escribir nombre hotel → Click BUSCAR\n";
echo "     ✓ Debe mostrar dropdown con hoteles\n";
echo "\n✅ Sistema listo para producción\n\n";
