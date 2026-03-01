<?php
require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../config/dbConfig.php';

class AuthController
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Xử lý đăng ký
     */
    public function register()
    {
        try {
            $fullName = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirmPassword'] ?? '';

            $this->authService->register($fullName, $email, $password, $confirmPassword, $phone);

            // Đăng ký thành công -> chuyển về trang login
            header('Location: ../views/auth/login.php?success=registered');
            exit;
        } catch (Exception $e) {
            $errorCode = $e->getMessage();
            header('Location: ../views/auth/register.php?error=' . urlencode($errorCode));
            exit;
        }
    }

    /**
     * Xử lý đăng nhập
     */
    public function login()
    {
        try {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            // AuthService sẽ xác thực + tạo JWT cookie
            $user = $this->authService->login($email, $password);

            // Redirect theo role
            if ($user['role_id'] == 1) {
                header('Location: ../views/admin/index.php');
            } else {
                header('Location: ../views/user/index.php');
            }
            exit;
        } catch (Exception $e) {
            $errorCode = $e->getMessage();
            header('Location: ../views/auth/login.php?error=' . urlencode($errorCode));
            exit;
        }
    }

    /**
     * Xử lý đăng xuất
     */
    public function logout()
    {
        $this->authService->logout();
        header('Location: ../views/auth/login.php');
        exit;
    }
}
