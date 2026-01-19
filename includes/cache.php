<?php
/**
 * Simple Image Caching System for Dyslexia App
 */

class ImageCache {
    private static $instance = null;
    private $cacheDir;
    private $cacheEnabled;
    private $cacheTtl;

    private function __construct() {
        $this->cacheDir = __DIR__ . '/../cache/images/';
        $this->cacheEnabled = getenv('IMAGE_CACHE_ENABLED') ?: true;
        $this->cacheTtl = getenv('IMAGE_CACHE_TTL') ?: 86400; // 24 hours default

        // Create cache directory if it doesn't exist
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get cached image URL or fetch and cache new one
     */
    public function getImage($url, $key = null) {
        if (!$this->cacheEnabled) {
            return $url;
        }

        // Generate cache key from URL if not provided
        if ($key === null) {
            $key = md5($url);
        }

        $cacheFile = $this->cacheDir . $key . '.cache';

        // Check if cached version exists and is still valid
        if ($this->isValidCache($cacheFile)) {
            $cachedData = json_decode(file_get_contents($cacheFile), true);
            if ($cachedData && isset($cachedData['url'])) {
                log_debug("Image cache hit for key: $key");
                return $cachedData['url'];
            }
        }

        // Cache miss - return original URL and cache in background if possible
        $this->cacheImageAsync($url, $cacheFile, $key);

        return $url;
    }

    /**
     * Check if cache file is valid
     */
    private function isValidCache($cacheFile) {
        if (!file_exists($cacheFile)) {
            return false;
        }

        $fileTime = filemtime($cacheFile);
        $currentTime = time();

        return ($currentTime - $fileTime) < $this->cacheTtl;
    }

    /**
     * Cache image asynchronously (in background)
     */
    private function cacheImageAsync($url, $cacheFile, $key) {
        // For now, we'll cache synchronously but in a real implementation
        // you might want to use a queue system or background processes

        try {
            // Attempt to fetch and validate the image URL
            $headers = @get_headers($url, 1);

            if ($headers && strpos($headers[0], '200') !== false) {
                $cacheData = [
                    'url' => $url,
                    'cached_at' => time(),
                    'expires_at' => time() + $this->cacheTtl,
                    'original_url' => $url
                ];

                file_put_contents($cacheFile, json_encode($cacheData));
                log_info("Image cached successfully", ['key' => $key, 'url' => $url]);
            } else {
                log_warning("Failed to validate image URL for caching", ['url' => $url]);
            }
        } catch (Exception $e) {
            log_error("Error caching image", [
                'error' => $e->getMessage(),
                'url' => $url,
                'key' => $key
            ]);
        }
    }

    /**
     * Clear expired cache files
     */
    public function cleanExpiredCache() {
        if (!is_dir($this->cacheDir)) {
            return;
        }

        $files = glob($this->cacheDir . '*.cache');
        $currentTime = time();
        $cleaned = 0;

        foreach ($files as $file) {
            $fileTime = filemtime($file);
            if (($currentTime - $fileTime) > $this->cacheTtl) {
                unlink($file);
                $cleaned++;
            }
        }

        if ($cleaned > 0) {
            log_info("Cleaned $cleaned expired cache files");
        }

        return $cleaned;
    }

    /**
     * Get cache statistics
     */
    public function getStats() {
        if (!is_dir($this->cacheDir)) {
            return ['enabled' => false];
        }

        $files = glob($this->cacheDir . '*.cache');
        $totalSize = 0;
        $validFiles = 0;
        $expiredFiles = 0;

        foreach ($files as $file) {
            $totalSize += filesize($file);

            if ($this->isValidCache($file)) {
                $validFiles++;
            } else {
                $expiredFiles++;
            }
        }

        return [
            'enabled' => $this->cacheEnabled,
            'cache_dir' => $this->cacheDir,
            'total_files' => count($files),
            'valid_files' => $validFiles,
            'expired_files' => $expiredFiles,
            'total_size_bytes' => $totalSize,
            'total_size_mb' => round($totalSize / 1048576, 2),
            'ttl_seconds' => $this->cacheTtl
        ];
    }
}

// Global cache function for easy use
function get_cached_image($url, $key = null) {
    return ImageCache::getInstance()->getImage($url, $key);
}
?>
