<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;

class AddTimeStampToResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $response = json_decode($response->content(), true);
        if (!isset($response['timestamp'])) {
            $response['timestamp'] = Carbon::now()->timezone('Asia/Kolkata')->format("Y-m-d H:i:s");
        }
        return response()->json($response, 200);
    }
}
