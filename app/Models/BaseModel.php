<?php

namespace App\Models;

use PDO;

class BaseModel
{
    protected PDO $pdo;

    // Constructor to initialize the PDO connection and set the timezone
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        date_default_timezone_set('Asia/Dhaka');
    }
}