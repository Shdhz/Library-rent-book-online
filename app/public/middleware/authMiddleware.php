<?php
session_start();

class AuthMiddleware {
    public static function checkLoggedIn() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: ../auth/login.php");
            exit();
        }
    }

    public static function checkRole($role) {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
            header("Location: ../auth/login.php");
            exit();
        }
    }

    public static function checkRoleMultiple($roles) {
        if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $roles)) {
            header("Location: ../auth/login.php");
            exit();
        }
    }

    public static function checkNotLoggedIn() {
        if (isset($_SESSION['user_id'])) {
            header("Location: ../index.php");
            exit();
        }
    }
}
?>
