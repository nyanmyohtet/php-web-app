<?php

class AdminController {

    /**
     * Show admin dashboard page
     */
    public function index() {
        include __DIR__. '/../views/admin/dashboard.php';
    }
}