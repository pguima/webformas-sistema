<?php

namespace App\Http\Middleware;

use App\Models\ApiToken;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenMiddleware
{
    public function handle(Request $request, Closure $next, ?string $ability = null): Response
    {
        $raw = $request->header('Authorization', '');
        $token = null;

        if (is_string($raw) && str_starts_with($raw, 'Bearer ')) {
            $token = trim(substr($raw, 7));
        }

        if (! $token) {
            $token = $request->header('X-API-Token');
        }

        if (! is_string($token) || $token === '') {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $hash = hash('sha256', $token);

        $apiToken = ApiToken::query()->where('token_hash', $hash)->first();
        if (! $apiToken) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($ability) {
            $abilities = $apiToken->abilities ?? [];
            if (! is_array($abilities) || ! in_array($ability, $abilities, true)) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
        }

        $apiToken->forceFill(['last_used_at' => Carbon::now()])->save();

        $request->attributes->set('apiToken', $apiToken);

        return $next($request);
    }
}
