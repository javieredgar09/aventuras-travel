<?php
/**
 * api.php - Rutas API REST
 * Aventuras Travel
 */

// API de clientes (protegida)
Router::get('/api/clientes', [ClienteApiController::class, 'index'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::get('/api/clientes/{id}', [ClienteApiController::class, 'show'], [AuthMiddleware::class, AdminMiddleware::class]);

// API de contratos (protegida)
Router::get('/api/contratos', [ContratoApiController::class, 'index'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::get('/api/contratos/{id}', [ContratoApiController::class, 'show'], [AuthMiddleware::class, AdminMiddleware::class]);

// API de pagos (protegida)
Router::get('/api/pagos', [PagoApiController::class, 'index'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::get('/api/pagos/{id}', [PagoApiController::class, 'show'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::post('/api/pagos', [PagoApiController::class, 'store'], [AuthMiddleware::class, AdminMiddleware::class]);

// API de promociones (público)
Router::get('/api/promociones', [PromocionApiController::class, 'index']);
Router::get('/api/promociones/activas', [PromocionApiController::class, 'activas']);

// API SerpAPI proxy (imágenes, hoteles, lugares turísticos)
Router::get('/api/images', [ImageApiController::class, 'getImage']);
Router::get('/api/hotels', [ImageApiController::class, 'getHotels']);
Router::get('/api/places', [ImageApiController::class, 'getPlaces']);
Router::get('/api/vuelos/buscar', [SaleController::class, 'searchFlights']);

