<?php
/**
 * HomeController.php - Página de inicio y promociones
 */
class HomeController extends Controller {

    public function index(): void {
        $promocionModel = new Promocion();
        $promociones = $promocionModel->getActivas();

        $data = [
            'title'       => 'Aventuras Travel - The Art of Discovery',
            'promociones' => $promociones,
            'flash'       => $this->getFlash(),
        ];
        $this->render('home/index', $data);
    }

    public function promotions(): void {
        $promocionModel = new Promocion();
        $promociones = $promocionModel->all('fecha_fin DESC');

        $data = [
            'title'       => 'Promociones - Aventuras Travel',
            'promociones' => $promociones,
        ];
        $this->render('home/promotions', $data);
    }

    public function asesoria(): void {
        $data = [
            'title' => 'Asesoría Personalizada - Aventuras Travel Pucallpa',
            'flash' => $this->getFlash(),
        ];
        $this->render('home/asesoria', $data);
    }

    /**
     * Ejecutar seed fresco de la BD (solo desarrollo)
     */
    public function seedFresh(): void {
        $seederPath = BASE_PATH . '/scripts/seed_fresh.php';
        if (!file_exists($seederPath)) {
            echo "Seeder no encontrado.";
            return;
        }
        header('Content-Type: text/plain; charset=utf-8');
        require $seederPath;
    }
}
