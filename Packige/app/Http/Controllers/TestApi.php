<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestApi extends Controller
{
    public function getData() {
        // fetch data from api http://api/json or https://api/json
        $data = json_decode(file_get_contents('https://chabloz.eu/files/horaires/students.json'), true);
        return $data;
    }
}
