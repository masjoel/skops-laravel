@php
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
    } else {
        $ip = Request::ip();
    }
    $clientIP = $ip;
    $doing = request()->path() ?: Request::route()->getName();
    $log = [
        'user_id' => 0,
        'reseller_id' => Session::get('reff') ?? null,
        'nama' => Auth::user()->username ?? 'guest',
        'level' => Auth::user()->role ?? '',
        'do' => $doing,
        'datetime' => date('Y-m-d H:i:s'),
        'ipaddr' => $clientIP,
    ];
    DB::table('userlog')->insert($log);
@endphp
