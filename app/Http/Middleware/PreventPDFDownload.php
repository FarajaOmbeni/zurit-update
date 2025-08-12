<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventPDFDownload
{
    public function handle(Request $request, Closure $next)
{
    $response = $next($request);
    
    if ($request->is('course-materials/*')) {
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Content-Security-Policy', "frame-ancestors 'self'");
        $response->headers->set('Referrer-Policy', 'no-referrer');
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
    }
    
    return $response;
}
}