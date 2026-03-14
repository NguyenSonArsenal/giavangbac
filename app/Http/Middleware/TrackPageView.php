<?php

namespace App\Http\Middleware;

use App\Models\PageView;
use Closure;

class TrackPageView
{
    /**
     * Bot patterns — skip detailed tracking for known crawlers
     */
    private $botPatterns = [
        'bot', 'crawl', 'spider', 'slurp', 'mediapartners',
        'facebookexternalhit', 'linkedinbot', 'twitterbot',
        'whatsapp', 'telegram', 'curl', 'wget', 'python',
    ];

    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Only track GET requests that return HTML (not API, not assets)
        if ($request->method() !== 'GET') {
            return $response;
        }

        // Skip asset/api/admin routes
        $path = $request->path();
        if ($this->shouldSkip($path)) {
            return $response;
        }

        // Detect bot
        $ua = strtolower($request->userAgent() ?: '');
        $isBot = false;
        foreach ($this->botPatterns as $pattern) {
            if (strpos($ua, $pattern) !== false) {
                $isBot = true;
                break;
            }
        }

        // Insert asynchronously-ish (fire and forget)
        try {
            PageView::create([
                'url'        => '/' . ltrim($path, '/'),
                'ip'         => $request->ip(),
                'user_agent' => substr($request->userAgent() ?: '', 0, 500),
                'session_id' => session()->getId(),
                'referer'    => substr($request->header('referer', ''), 0, 500),
                'is_bot'     => $isBot,
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Silently fail — tracking should never break the site
        }

        return $response;
    }

    private function shouldSkip($path)
    {
        $skipPrefixes = ['api/', 'admin/', '_debugbar/', 'dk-log', 'dk-server', 'favicon', 'robots'];
        foreach ($skipPrefixes as $prefix) {
            if (strpos($path, $prefix) === 0) {
                return true;
            }
        }
        // Skip file extensions (css, js, images)
        if (preg_match('/\.(css|js|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf|map)$/i', $path)) {
            return true;
        }
        return false;
    }
}
