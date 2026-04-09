<?php

namespace App\Http\Requests;

use Closure;
use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'sometimes|string|max:255',
            'password' => 'sometimes|string|min:8|confirmed',
            'webhook_url' => [
                'sometimes',
                'url',
                function (string $attribute, mixed $value, Closure $fail) {
                    $host = parse_url($value, PHP_URL_HOST);

                    if (!$host) {
                        return;
                    }

                    // 1. Resolve the hostname to an IP address
                    // This catches users trying to use a real domain (like 'localtest.me') that points to 127.0.0.1
                    $ip = gethostbyname($host);

                    // 2. If it can't resolve, fail it (optional, but good for webhooks)
                    if ($ip === $host && !filter_var($host, FILTER_VALIDATE_IP)) {
                        $fail("The {$attribute} domain could not be resolved.");
                        return;
                    }

                    // 3. Block ALL private and reserved IP ranges instantly
                    // This blocks 127.*, 10.*, 192.168.*, 169.254.*, 0.0.0.0, etc.
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
                        $fail("The {$attribute} cannot point to a local, private, or reserved network.");
                    }

                    // 4. (Optional) Block your own server's public IP or hostname if you want
                    $serverIp = config('app.server_ip'); // If you set this in config
                    if ($ip === $serverIp || $host === parse_url(config('app.url'), PHP_URL_HOST)) {
                        $fail("The {$attribute} cannot point back to this server.");
                    }
                },
            ],
        ];
    }
}
