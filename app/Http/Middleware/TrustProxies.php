<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrustProxies
{
    /**
     * The trusted proxies for this application.
     *
     * @var array|string|null
     */
    protected $proxies;

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers;

    /**
     * Create a new middleware instance.
     */
    public function __construct()
    {
        // Trust specific proxies only (configurable via env)
        // SECURITY: Never default to '*' in production - must be explicit IPs
        $proxyConfig = env('TRUSTED_PROXIES', '127.0.0.1');
        
        // Support comma-separated list or single IP/CIDR
        $this->proxies = str_contains($proxyConfig, ',') 
            ? explode(',', $proxyConfig) 
            : $proxyConfig;
        
        // Use X-Forwarded-For header for client IP detection
        $this->headers = Request::HEADER_X_FORWARDED_FOR |
                         Request::HEADER_X_FORWARDED_HOST |
                         Request::HEADER_X_FORWARDED_PORT |
                         Request::HEADER_X_FORWARDED_PROTO |
                         Request::HEADER_X_FORWARDED_AWS_ELB;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Configure trusted proxies for this request
        $request->setTrustedProxies(
            is_array($this->proxies) ? $this->proxies : [$this->proxies],
            $this->headers
        );
        
        return $next($request);
    }
}
