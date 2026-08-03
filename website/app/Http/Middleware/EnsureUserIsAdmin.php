<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * শুধু অ্যাডমিন — সেটিংস, ব্যবহারকারী ব্যবস্থাপনা ও ব্যাকআপের জন্য।
 * ম্যানেজাররা অ্যাপয়েন্টমেন্ট ও কনটেন্ট সামলাতে পারেন, এগুলো নয়।
 */
class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->isAdmin(), 403, 'এই অংশে প্রবেশের অনুমতি নেই।');

        return $next($request);
    }
}
