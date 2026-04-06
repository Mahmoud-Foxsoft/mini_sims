<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;

class RecaptchaService
{
    protected $projectId;
    protected $secretKey;
    protected $siteKey;
    
    public function __construct()
    {
        $this->projectId = env('RECAPTCHA_PROJECT_ID'); 
        $this->siteKey = env('RECAPTCHA_SITE_KEY');      
        $this->secretKey = env('RECAPTCHA_SECRET_KEY'); 
    }
    
    public function checkToken(string $token, string $expectedAction = 'contact_us', float $minScore = 0.5): bool
    {
        $url = "https://recaptchaenterprise.googleapis.com/v1/projects/{$this->projectId}/assessments?key={$this->secretKey}";
        
        $response = Http::withHeaders(['Content-Type' => 'application/json'])
        ->post($url , [
            'event' => [
                'token' => $token,
                'siteKey' => $this->siteKey,
                'expectedAction' => $expectedAction
            ],
        ]); 
        $data = $response->json();
        return ($data['tokenProperties']['valid'] ?? false) ;
    }
    
}
