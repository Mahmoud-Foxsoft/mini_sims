<?php
namespace App\Http\DTOs;

class TotalObject
{
    public function __construct(
        public string $key,
        public string $value,
        public ?string $route = null,
        public string $icon = '',
    ) {}
}