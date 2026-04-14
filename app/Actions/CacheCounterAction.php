<?php
namespace App\Actions;

use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;

class CacheCounterAction
{
    /**
     * Check if a cache key exists and increment or decrement its value.
     *
     * @param string $key The cache key to look for
     * @param int $amount The amount to add or subtract
     * @param string $operation 'increment' or 'decrement'
     * @return int|false Returns the new value, or false if the key does not exist
     */
    public static function execute(string $key, int $amount = 1, string $operation = 'increment'): int|false
    {
        // 1. Validate the operation
        if (! in_array($operation, ['increment', 'decrement'])) {
            throw new InvalidArgumentException("Operation must be either 'increment' or 'decrement'.");
        }

        // 2. Check if the key exists
        if (! Cache::has($key)) {
            return false;
        }

        // 3. Perform the requested operation
        return $operation === 'increment' 
            ? Cache::increment($key, $amount)
            : Cache::decrement($key, $amount);
    }
}