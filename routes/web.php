<?php
/**
 * web.php - Rutas web
 * Aventuras Travel
 */

// ==================== ARCHIVOS (servir desde /storage) ====================
Router::get('/storage/{type}/{filename}', [FileController::class, 'serve']);

// ==================== PÁGINAS PÚBLICAS ====================
Router::get('/', [HomeController::class, 'index']);
Router::get('/search', [SearchController::class, 'index']);
Router::get('/search/results', [SearchController::class, 'results']);
Router::get('/promotions', [HomeController::class, 'promotions']);
Router::get('/asesoria', [HomeController::class, 'asesoria']);

// ==================== AUTENTICACIÓN ====================
Router::get('/login', [AuthController::class, 'showLogin']);
Router::post('/login', [AuthController::class, 'login']);
Router::get('/admin/login', [AuthController::class, 'showAdminLogin']);
Router::post('/admin/login', [AuthController::class, 'adminLogin']);
Router::get('/logout', [AuthController::class, 'logout']);

// ==================== PANEL CLIENTE ====================
Router::get('/client/dashboard', [ClientController::class, 'dashboard'], [AuthMiddleware::class]);
Router::get('/client/services', [ClientController::class, 'services'], [AuthMiddleware::class]);
Router::get('/client/contract/{id}', [ClientController::class, 'contract'], [AuthMiddleware::class]);
Router::get('/client/payments', [ClientController::class, 'payments'], [AuthMiddleware::class]);
Router::post('/client/payments/register', [ClientController::class, 'registerPayment'], [AuthMiddleware::class]);
Router::get('/client/payments/{id}/receipt', [ClientController::class, 'downloadReceipt'], [AuthMiddleware::class]);
Router::get('/client/soporte', [ClientController::class, 'soporte'], [AuthMiddleware::class]);

// ==================== PANEL REPRESENTANTE (GRUPAL) ====================
Router::get('/leader/dashboard', [ClientController::class, 'leaderDashboard'], [AuthMiddleware::class]);
Router::get('/leader/contracts', [ClientController::class, 'leaderContracts'], [AuthMiddleware::class]);
Router::get('/leader/payments', [ClientController::class, 'leaderPayments'], [AuthMiddleware::class]);
Router::post('/leader/payments/register', [ClientController::class, 'leaderRegisterPayment'], [AuthMiddleware::class]);
Router::get('/leader/payments/{id}/receipt', [ClientController::class, 'downloadReceipt'], [AuthMiddleware::class]);
Router::post('/leader/group/{id}/itinerary/save', [SaleController::class, 'saveItinerary'], [AuthMiddleware::class]);

// ==================== PANEL ADMIN ====================
Router::get('/admin', [AdminController::class, 'dashboard'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::get('/admin/dashboard', [AdminController::class, 'dashboard'], [AuthMiddleware::class, AdminMiddleware::class]);

// Ventas
Router::get('/admin/sales', [SaleController::class, 'index'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::get('/admin/sales/create', [SaleController::class, 'create'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::post('/admin/sales/store', [SaleController::class, 'store'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::get('/admin/sales/search-flight', [SaleController::class, 'ajaxSearchFlight'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::get('/admin/sales/search-airport', [SaleController::class, 'ajaxSearchAirport'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::get('/admin/sales/search-hotel', [SaleController::class, 'ajaxSearchHotel'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::get('/admin/sales/search-destination', [SaleController::class, 'ajaxSearchDestination'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::post('/admin/sales/payment', [SaleController::class, 'registerPayment'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::post('/admin/sales/payment/approve/{id}', [SaleController::class, 'approvePayment'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::post('/admin/sales/voucher', [SaleController::class, 'uploadVoucher'], [AuthMiddleware::class, AdminMiddleware::class]);

// Rutas dinámicas de Ventas (con {id}) deben ir al final
Router::get('/admin/sales/{id}', [SaleController::class, 'show'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::get('/admin/sales/{id}/edit', [SaleController::class, 'edit'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::post('/admin/sales/{id}/update', [SaleController::class, 'update'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::post('/admin/sales/delete/{id}', [SaleController::class, 'delete'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::post('/admin/sales/{id}/passenger', [SaleController::class, 'addPassenger'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::get('/admin/sales/{id}/contract/create', [ContractController::class, 'create'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::post('/admin/sales/{id}/contract', [ContractController::class, 'store'], [AuthMiddleware::class, AdminMiddleware::class]);

Router::get('/admin/contracts/{id}/print', [ContractController::class, 'print'], [AuthMiddleware::class, AdminMiddleware::class]);

// Pagos
Router::get('/admin/payments', [PaymentController::class, 'index'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::post('/admin/payments/register', [PaymentController::class, 'registerAdmin'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::get('/admin/payments/{id}/receipt', [PaymentController::class, 'downloadReceipt'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::post('/admin/payments/{id}/regenerate', [PaymentController::class, 'regenerate'], [AuthMiddleware::class, AdminMiddleware::class]);

// Reportes
Router::get('/admin/reports', [ReportController::class, 'index'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::get('/admin/reports/export', [ReportController::class, 'exportCsv'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::post('/admin/payments/approve/{id}', [PaymentController::class, 'approve'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::post('/admin/payments/reject/{id}', [PaymentController::class, 'reject'], [AuthMiddleware::class, AdminMiddleware::class]);

// Promociones
Router::get('/admin/promotions', [PromotionController::class, 'index'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::get('/admin/promotions/create', [PromotionController::class, 'create'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::post('/admin/promotions/store', [PromotionController::class, 'store'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::get('/admin/promotions/edit/{id}', [PromotionController::class, 'edit'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::post('/admin/promotions/update/{id}', [PromotionController::class, 'update'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::post('/admin/promotions/delete/{id}', [PromotionController::class, 'delete'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::post('/admin/promotions/toggle/{id}', [PromotionController::class, 'toggle'], [AuthMiddleware::class, AdminMiddleware::class]);

// Contratos
Router::get('/admin/contracts', [ContractController::class, 'index'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::get('/admin/contracts/{id}', [ContractController::class, 'show'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::get('/admin/contracts/{id}/edit', [ContractController::class, 'edit'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::post('/admin/contracts/{id}/update', [ContractController::class, 'update'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::post('/admin/contracts/{id}/upload-contract', [FileController::class, 'uploadContract'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::post('/admin/contracts/{id}/upload-voucher', [FileController::class, 'uploadVoucher'], [AuthMiddleware::class, AdminMiddleware::class]);

// Pasajeros
Router::get('/admin/passengers', [PassengerController::class, 'index'], [AuthMiddleware::class, AdminMiddleware::class]);

// Usuarios (CRUD Admin)
Router::get('/admin/users', [UserController::class, 'index'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::get('/admin/users/create', [UserController::class, 'create'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::post('/admin/users/store', [UserController::class, 'store'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::get('/admin/users/{id}/edit', [UserController::class, 'edit'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::post('/admin/users/{id}/update', [UserController::class, 'update'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::post('/admin/users/{id}/toggle', [UserController::class, 'toggle'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::post('/admin/users/{id}/delete', [UserController::class, 'delete'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::post('/admin/users/{id}/reset-password', [UserController::class, 'resetPassword'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::post('/admin/users/{id}/send-whatsapp', [UserController::class, 'sendWhatsApp'], [AuthMiddleware::class, AdminMiddleware::class]);
Router::get('/admin/users/{id}/credentials', [UserController::class, 'credentials'], [AuthMiddleware::class, AdminMiddleware::class]);

// ==================== SEED (solo desarrollo - protegido por admin) ====================
Router::get('/seed-fresh', [HomeController::class, 'seedFresh'], [AuthMiddleware::class, AdminMiddleware::class]);
