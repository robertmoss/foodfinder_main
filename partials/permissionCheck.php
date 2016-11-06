<?php
    // must be an admin for current tenant to access this page
    if ($userID==0 || ($user && !$user->hasRole('admin',$tenantID))) {
        Log::debug('Non admin user (id=' . $userID . ', session_id=' . session_id() . ') attempted to access an admin-only page', 10);
        header('Location: ../403.php');
        die();
    }