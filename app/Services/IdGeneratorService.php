<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class IdGeneratorService
{
    protected int $numberLength = 10; // Default length for the numeric part

    /**
     * Fetch prefixes directly from database (always fresh)
     */
    protected function fetchPrefixesFromDatabase(): array
    {
        try {
            $siteInfo = DB::table('site_infos')
                ->select('prefix')
                ->first();
            
            if ($siteInfo && isset($siteInfo->prefix)) {
                $prefixData = is_string($siteInfo->prefix) 
                    ? json_decode($siteInfo->prefix, true) 
                    : $siteInfo->prefix;
                
                if (is_array($prefixData) && !empty($prefixData)) {
                    Log::info('Fresh prefixes loaded from database', ['prefixes' => $prefixData]);
                    return $prefixData;
                }
            }
            
            Log::warning('No valid prefixes found in site_infos, using defaults');
            return $this->getDefaultPrefixes();
            
        } catch (\Exception $e) {
            Log::error('Database error while fetching prefixes', [
                'error' => $e->getMessage()
            ]);
            
            return $this->getDefaultPrefixes();
        }
    }

    /**
     * Get default prefixes as fallback
     */
    protected function getDefaultPrefixes(): array
    {
        return [
            'admin' => 'ADM',
            'user' => 'USR',
            'order' => 'ORD',
            'item' => 'ITM'
        ];
    }

    /**
     * Generate next ID for a given type (always uses fresh prefixes from database)
     *
     * @param string $type The type of ID (admin, user, order, item, etc.)
     * @param string|null $table The table name to check for existing IDs
     * @param string $column The column name that stores the ID (default: 'user_id')
     * @param int|null $numberLength Custom length for the numeric part
     * @return string
     */
    public function generateId(
        string $type, 
        ?string $table = null, 
        string $column = 'user_id', 
        ?int $numberLength = null
    ): string {
        // Always fetch fresh prefixes from database
        $prefixes = $this->fetchPrefixesFromDatabase();
        $prefix = $prefixes[$type] ?? strtoupper(substr($type, 0, 3));
        
        $length = $numberLength ?? $this->numberLength;
        
        if ($table) {
            $nextNumber = $this->getNextNumber($table, $column, $prefix);
        } else {
            $nextNumber = 1;
        }
        
        $generatedId = $prefix . str_pad($nextNumber, $length, '0', STR_PAD_LEFT);
        
        Log::info('Generated ID with fresh prefix', [
            'type' => $type,
            'prefix' => $prefix,
            'number' => $nextNumber,
            'generated_id' => $generatedId,
            'all_prefixes' => $prefixes
        ]);
        
        return $generatedId;
    }

    /**
     * Get the next sequential number for a given prefix
     *
     * @param string $table
     * @param string $column
     * @param string $prefix
     * @return int
     */
    protected function getNextNumber(string $table, string $column, string $prefix): int
    {
        try {
            $driver = DB::getDriverName(); // Detect the DB driver (mysql, pgsql, etc.)
            $prefixLength = strlen($prefix);

            if ($driver === 'pgsql') {
                // PostgreSQL-compatible substring and cast
                $latestId = DB::table($table)
                    ->where($column, 'LIKE', $prefix . '%')
                    ->orderByRaw("CAST(SUBSTRING(\"$column\" FROM " . ($prefixLength + 1) . ") AS INTEGER) DESC")
                    ->value($column);
            } else {
                // MySQL-compatible syntax
                $latestId = DB::table($table)
                    ->where($column, 'LIKE', $prefix . '%')
                    ->orderByRaw("CAST(SUBSTRING($column, " . ($prefixLength + 1) . ") AS UNSIGNED) DESC")
                    ->value($column);
            }

            if ($latestId) {
                $numericPart = substr($latestId, $prefixLength);
                $nextNumber = (int)$numericPart + 1;

                Log::info('Next number calculated', [
                    'table' => $table,
                    'column' => $column,
                    'prefix' => $prefix,
                    'latest_id' => $latestId,
                    'next_number' => $nextNumber
                ]);
                return $nextNumber;
            }
        } catch (\Exception $e) {
            Log::error('Error getting next number for ID generation', [
                'table' => $table,
                'column' => $column,
                'prefix' => $prefix,
                'error' => $e->getMessage()
            ]);
        }

        return 1;
    }


    /**
     * Get current prefix for a given type (always fresh from database)
     *
     * @param string $type
     * @return string
     */
    public function getPrefix(string $type): string
    {
        $prefixes = $this->fetchPrefixesFromDatabase();
        return $prefixes[$type] ?? strtoupper(substr($type, 0, 3));
    }

    /**
     * Get all available prefixes (always fresh from database)
     *
     * @return array
     */
    public function getAllPrefixes(): array
    {
        return $this->fetchPrefixesFromDatabase();
    }

    /**
     * Set custom number length
     *
     * @param int $length
     * @return self
     */
    public function setNumberLength(int $length): self
    {
        $this->numberLength = $length;
        return $this;
    }

    /**
     * Update a single prefix in database
     *
     * @param string $type
     * @param string $prefix
     * @return bool
     */
    public function updatePrefix(string $type, string $prefix): bool
    {
        try {
            // Get current prefixes from database
            $currentPrefixes = $this->fetchPrefixesFromDatabase();
            $currentPrefixes[$type] = $prefix;
            
            // Update in database
            $updatedPrefixes = json_encode($currentPrefixes);
            
            $updated = DB::table('site_infos')->updateOrInsert(
                ['id' => 1], // Assuming single row configuration
                [
                    'prefix' => $updatedPrefixes,
                    'updated_at' => now()
                ]
            );
            
            if ($updated) {
                Log::info('Prefix updated successfully', [
                    'type' => $type,
                    'prefix' => $prefix,
                    'all_prefixes' => $currentPrefixes
                ]);
                
                return true;
            }
            
            return false;
            
        } catch (\Exception $e) {
            Log::error('Error updating prefix', [
                'type' => $type,
                'prefix' => $prefix,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Update multiple prefixes at once
     *
     * @param array $prefixes
     * @return bool
     */
    public function updatePrefixes(array $prefixes): bool
    {
        try {
            $updatedPrefixes = json_encode($prefixes);
            
            $updated = DB::table('site_infos')->updateOrInsert(
                ['id' => 1],
                [
                    'prefix' => $updatedPrefixes,
                    'updated_at' => now()
                ]
            );
            
            if ($updated) {
                Log::info('Multiple prefixes updated successfully', [
                    'prefixes' => $prefixes
                ]);
                
                return true;
            }
            
            return false;
            
        } catch (\Exception $e) {
            Log::error('Error updating multiple prefixes', [
                'prefixes' => $prefixes,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Check if a prefix exists for a given type
     *
     * @param string $type
     * @return bool
     */
    public function hasPrefix(string $type): bool
    {
        $prefixes = $this->fetchPrefixesFromDatabase();
        return isset($prefixes[$type]);
    }

    /**
     * Preview next ID without generating it
     *
     * @param string $type
     * @param string|null $table
     * @param string $column
     * @param int|null $numberLength
     * @return string
     */
    public function previewNextId(
        string $type, 
        ?string $table = null, 
        string $column = 'user_id', 
        ?int $numberLength = null
    ): string {
        // Get fresh prefixes
        $prefixes = $this->fetchPrefixesFromDatabase();
        $prefix = $prefixes[$type] ?? strtoupper(substr($type, 0, 3));
        
        $length = $numberLength ?? $this->numberLength;
        
        if ($table) {
            $nextNumber = $this->getNextNumber($table, $column, $prefix);
        } else {
            $nextNumber = 1;
        }
        
        return $prefix . str_pad($nextNumber, $length, '0', STR_PAD_LEFT);
    }
}
