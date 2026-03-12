<?php
require_once __DIR__ . '/../services/ShowService.php';

class ShowController
{
    private ShowService $showService;

    public function __construct(ShowService $showService)
    {
        $this->showService = $showService;
    }

    public function getAllShows()
    {
        try {
            return $this->showService->getAllShows();
        } catch (Exception $e) {
            header('Location: index.php?page=shows&error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function getShowById($id)
    {
        try {
            return $this->showService->getShowById($id);
        } catch (Exception $e) {
            header('Location: index.php?page=shows&error=' . urlencode($e->getMessage()));
            exit;
        }
    }
}
?>