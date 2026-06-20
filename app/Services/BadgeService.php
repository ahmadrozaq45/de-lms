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
     */
    public function checkAfterSubmission($user = null)
    {
        return true; 
    }

    /**
     * Dummy method untuk mencegah eror saat submit kuis.
     */
    public function checkAfterQuiz($user = null, $score = 0, $isPassed = false)
    {
        return true;
    }
}