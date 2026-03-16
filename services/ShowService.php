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
        //Format thoi gian
        $show_date = date('dd-mm-yyyy', strtotime($show_date));
        $start_time = date('HH:mm', strtotime($start_time));
        $end_time = date('HH:mm', strtotime($end_time));

        if ($end_time < $start_time) {
            return throw new Exception("End time must be greater than start time");
        }

        if ($this->showModel->getShowsByTimeRangeofHalls($hall_id, $show_date, $start_time, $end_time)) {
            return throw new Exception("This time range is not available for this hall");
        }

        return $this->showModel->createShow($movie_id, $hall_id, $show_date, $start_time, $end_time, $base_price);
    }

    public function updateShow($id, $movie_id, $hall_id, $show_date, $start_time, $end_time, $base_price)
    {
        //Format thoi gian
        $show_date = date('dd-mm-yyyy', strtotime($show_date));
        $start_time = date('HH:mm', strtotime($start_time));
        $end_time = date('HH:mm', strtotime($end_time));

        if ($end_time < $start_time) {
            return throw new Exception("End time must be greater than start time");
        }

        if ($this->showModel->getShowsByTimeRangeofHalls($hall_id, $show_date, $start_time, $end_time, $id)) {
            return throw new Exception("This time range is not available for this hall");
        }
        
        return $this->showModel->updateShow($id, $movie_id, $hall_id, $show_date, $start_time, $end_time, $base_price);
    }

    public function deleteShow($id)
    {
        return $this->showModel->deleteShow($id);
    }
}
?>