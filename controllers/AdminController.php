<?php

require_once __DIR__ . '/../services/UserService.php';
require_once __DIR__ . '/../config/dbConfig.php';

//Xử lý toàn bộ chức năng bên admin 
class AdminController
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function getAllUsers()
    {
        try {
            $users = $this->userService->getAllUsers();
            return $users;
        } catch (Exception $e) {
            $errorMessage = $$_SESSION['error'] = $e->getMessage();
            header('Location: /index.php?page=users&error=1');
        }
    }

    public function createUser()
    {
        try {
            $result = $this->userService->createUser($_POST['fullName'], $_POST['email'], $_POST['password'], $_POST['phone']);

            header('Location: ../../views/admin/index.php?page=users&add=1');
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ../../views/admin/index.php?page=users&error=1');
        }
    }

    public function updateUser($id)
    {
        try {
            $result = $this->userService->updateUser($id, $_POST['name'], $_POST['email'], $_POST['phone']);

            header('Location: ../../views/admin/index.php?page=users&aupdate=1');
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ../../views/admin/index.php?page=users&error=1');
        }
    }

    public function deleteUser($id)
    {
        try {
            $result = $this->userService->deleteUser($id);

            header('Location: ../../views/admin/index.php?page=users&delete=1');
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ../../views/admin/index.php?page=users&error=1');
        }
    }
}
