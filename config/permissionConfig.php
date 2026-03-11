<?php

function hasPermission($permission)
{
    // Admin bypass
    // if (isset($_SESSION['user']) && $_SESSION['user']['role_id'] == 1) {
    //     return true;
    // }

    if (!isset($_SESSION['permissions'])) {
        return false;
    }

    return in_array($permission, $_SESSION['permissions']);
}

function hasModule($module)
{
    // Admin bypass
    if (isset($_SESSION['user']) && $_SESSION['user']['role_id'] == 1) {
        return true;
    }

    if (!isset($_SESSION['permissions'])) {
        return false;
    }

    foreach ($_SESSION['permissions'] as $permission) {

        if (strpos($permission, $module . "_") === 0) {
            return true;
        }
    }

    return false;
}
