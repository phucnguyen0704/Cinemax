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
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        //Format thoi gian
        $show_date  = date('Y-m-d', strtotime($show_date));
        $start_time = date('H:i', strtotime($start_time));
        $end_time   = date('H:i', strtotime($end_time));


        $now = new DateTime();
        $showDateTime = new DateTime($show_date . ' ' . $start_time);

        if ($showDateTime <= $now) {
            throw new Exception("Không thể tạo suất chiếu trong quá khứ.");
        }

        if ($end_time < $start_time) {
            throw new Exception("End time must be greater than start time");
        }

        if (empty($movie_id) || empty($base_price) || empty($hall_id) || empty($show_date) || empty($start_time) || empty($end_time)) {
            throw new InvalidArgumentException("All fields are required to create a show.");
        }

        return $this->showModel->createShow($movie_id, $hall_id, $show_date, $start_time, $end_time, $base_price);
    }

    public function updateShow($id, $movie_id, $hall_id, $show_date, $start_time, $end_time, $base_price, $status)
    {

        date_default_timezone_set('Asia/Ho_Chi_Minh');
        //Format thoi gian
        $show_date  = date('Y-m-d', strtotime($show_date));
        $start_time = date('H:i', strtotime($start_time));
        $end_time   = date('H:i', strtotime($end_time));


        // $now = new DateTime();
        // $showDateTime = new DateTime($show_date . ' ' . $start_time);

        // if ($showDateTime <= $now) {
        //     throw new Exception("Không thể cập nhật suất chiếu đã chiếu.");
        // }

        if ($end_time < $start_time) {
            throw new Exception("End time must be greater than start time");
        }

        if (empty($movie_id) || empty($base_price) || empty($hall_id) || empty($show_date) || empty($start_time) || empty($end_time)) {
            throw new InvalidArgumentException("All fields are required to create a show.");
        }

        return $this->showModel->updateShow($id, $movie_id, $hall_id, $show_date, $start_time, $end_time, $base_price, $status);
    }

    public function deleteShow($id)
    {
        return $this->showModel->deleteShow($id);
    }

    //Ham lay ten rap va ten phong
    public function getAllHalls_CinemasByShow()
    {
        return $this->showModel->getAllHalls_CinemasByShow();
    }
}
