<?php

namespace App\Services;

class MusicBrainzService
{
    /**
     * Generate the User-Agent string for MusicBrainz API.
     *
     * @return string
     */
    public function getUserAgent(): string
    {
        $appName = config('app.name', 'MyLaravelApp');
        $version = '1.0';
        $contact = 'africteam@gmail.com'; 

        return "{$appName}/{$version} ({$contact})";
    }
}
