<?php

namespace App\Http\Controllers;

use App\Services\SatusehatService;
use Illuminate\Http\Request;

class SatusehatController extends Controller
{
    public function testToken()
    {
        $ss = new SatuSehatService();
        return $ss->getToken(); // lihat token yang dihasilkan
        //return "Controller jalan";
    }

    public function testGetPatient()
    {
        $ss = new SatuSehatService();

        $nik = "3201123456780001"; // NIK dummy atau sandbox
        $response = $ss->request(
            'get',
            "fhir-r4/v1/Patient?identifier=NIK|$nik"
        );

        return $response;
    }
    
}
