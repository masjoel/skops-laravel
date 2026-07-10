<?php

namespace App\Providers;

use App\Models\PeriodeAkademik;
use App\Models\TahunAjaran;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('partials.periode-switcher', function ($view) {
            $periodeAktif = PeriodeAkademik::with('tahunAjaran')->where('is_aktif', true)->first();
            $semuaTahunAjaran = TahunAjaran::with('periodeAkademik')->orderByDesc('nama')->get();
            
            $tahunAjaranList = $semuaTahunAjaran;
            if ($periodeAktif && $semuaTahunAjaran->isNotEmpty()) {
                $activeIndex = $semuaTahunAjaran->search(function ($item) use ($periodeAktif) {
                    return $item->id === $periodeAktif->tahun_ajaran_id;
                });
                
                if ($activeIndex !== false) {
                    $startIndex = max(0, $activeIndex - 2);
                    $tahunAjaranList = $semuaTahunAjaran->slice($startIndex, 5);
                } else {
                    $tahunAjaranList = $semuaTahunAjaran->take(5);
                }
            } else {
                $tahunAjaranList = $semuaTahunAjaran->take(5);
            }

            $view->with([
                'periodeAktif' => $periodeAktif,
                'tahunAjaranList' => $tahunAjaranList,
            ]);
        });
        Paginator::useBootstrapFive();
    }
}
