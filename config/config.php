<?php

// Load database configuration
$dbConfig = require 'database.config.php';

// Add other configurations here as needed
$config = [
    'site_title' => 'PHP Web App',
];

// Merge database configs into main config if needed
$config['database'] = $dbConfig;

return $config;
