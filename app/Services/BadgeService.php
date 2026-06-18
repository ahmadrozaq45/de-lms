<?php

namespace App\Services;

class BadgeService
{
    public function __construct()
    {
        //
    }

    /**
     * Dummy method untuk mencegah eror saat submit tugas.
     * Menggunakan method biasa (bukan static) dan menerima parameter user.
     */
    public function checkAfterSubmission($user = null)
    {
        return true; 
    }
}