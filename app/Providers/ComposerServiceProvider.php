<?php

namespace App\Providers;

use Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

use App\model\strole;
use App\mstgudang;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {   
        // view()->share('current_user', $user = \Auth::user());

        View::composer(['layouts.sidebar'], 'App\Http\ViewComposers\globalreportComposer');

        View::composer('*', 
            function ($view) {
                $view->with('composer_cur_user', Auth::user() );
            });


        View::composer('users', 
            function ($view) {
                $view->with('composer_strole', strole::select(['id', 'name as text'])->orderBy('name')->get());
            });


        View::composer(['master\mstcompany'], 
            function ($view) {
                $view->with('composer_kota', DB::select(DB::raw("SELECT id, name2 FROM vwmstkota order by `name`")));
            });


        View::composer(['rptpesanrekap'], 
            function ($view) {
                $view->with('composer_tahunbulan', db::table('vwtahunbulan')->get());
            });


        View::composer(['rptpersediaan'], 
            function ($view) {
                $view->with('composer_mstgudang', mstgudang::where('kode','<>',"''")->orderBy('nama')->get());
            });
        
            
            
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
