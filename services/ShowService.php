<?php

require_once __DIR__ . '/../models/Show.php';

class ShowService
{
    private $showModel;

    public function __construct($showModel)
    {
        $this->showModel = $showModel;
    }

    public function getAllShows()
    {
        return $this->showModel->getAllShows();
    }

    public function getShowById($id)
    {
        return $this->showModel->getShowById($id);
    }

    public function getShowsByMovieId($movie_id)
    {
        return $this->showModel->getShowsByMovieId($movie_id);
    }

    public function createShow($movie_id, $hall_id, $show_date, $start_time, $end_time, $base_price)
    {
        return $this->showModel->createShow($movie_id, $hall_id, $show_date, $start_time, $end_time, $base_price);
    }

    public function updateShow($id, $movie_id, $hall_id, $show_date, $start_time, $end_time, $base_price)
    {
        return $this->showModel->updateShow($id, $movie_id, $hall_id, $show_date, $start_time, $end_time, $base_price);
    }

    public function deleteShow($id)
    {
        return $this->showModel->deleteShow($id);
    }
}
?>