<?php
/**
 * SearchController.php - Buscador de destinos
 */
class SearchController extends Controller {

    public function index(): void {
        $data = [
            'title' => 'Explorar Destinos - Aventuras Travel',
            'query' => '',
        ];
        $this->render('search/index', $data);
    }

    public function results(): void {
        $query = trim($_GET['q'] ?? '');
        if (empty($query)) {
            $this->redirect('/search');
            return;
        }
        $data = [
            'title' => htmlspecialchars($query) . ' - Aventuras Travel',
            'query' => $query,
        ];
        $this->render('search/results', $data);
    }
}
