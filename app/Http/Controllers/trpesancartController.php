<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\model\trpesantmpd;
use App\model\mstongkir;
use App\model\mstgudang;
use App\model\posframe_mstleasing;

use DB;
use Carbon\Carbon;

class trpesancartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // $this->middleware('cekmenuroles');
    }


    function cartcount(){
        $cur_user = \Auth::user();
        $tmp = trpesantmpd::where('userid', $cur_user->id)->where('qty', '>' ,  0)->count();
        return $tmp;
    }


    public function index()
    {
        $cur_user = \Auth::user();
        // $mstbarang = posframe_mstbarang::orderby('nama')->get();

        $que = "select a.*, ifnull(b.qty, 0) as qty, ifnull(b.harga, 0) as harga, ifnull(b.disc, 0) as disc, ifnull(b.jumlah, 0) as jumlah, ifnull(b.keterangan, '') as keterangan,
                    REPLACE(a.kode, '.', '') as kodex
                FROM posframe_mstbarang a
                inner join trpesantmpd b on a.kode = b.kode and b.qty > 0 and b.userid = $cur_user->id 
                order by a.nama";
        $mstbarang = DB::select(DB::raw($que));

        $mstongkir = mstongkir::orderby('kota')->get();
        $mstgudang = mstgudang::where('kode','<>', '')->where('nama','<>', '-')->orderby('nama')->get();
        $mstleasing = posframe_mstleasing::orderby('nama')->get();
        $pesanhead = DB::table('trpesantmph')->where('userid', $cur_user->id)->first();

        $cartcount = $this->cartcount();  

        return view('trpesancart',compact('mstbarang', 'mstgudang', 'mstongkir', 'mstleasing', 'cartcount', 'pesanhead'));
    }

    
    public function store(Request $request)
    {        
        $cur_user = \Auth::user();

        switch ($request->mode) {
            case 'savehead':
                $ongkir = $request->ongkir ? $request->ongkir : 0;
                $dp = $request->dp ? $request->dp : 0;
                $form_data = array(
                    'kdgudang' => $request->kdgudang,
                    'csnama' => $request->csnama,
                    'csalamat' => $request->csalamat,
                    'csnohp' => $request->csnohp,
                    'cskota' => $request->cskota,
                    'ongkir' => $ongkir,
                    'dp' => $dp,
                    'kdleasing' => $request->kdleasing,
                    'ls_cicilan1' => $request->ls_cicilan1 ? $request->ls_cicilan1 : 0,
                    'ls_admin' => $request->ls_admin ? $request->ls_admin : 0,
                    'keterangan' => $request->keterangan
                );
        
                DB::table('trpesantmph')->updateOrInsert(['userid' => $cur_user->id], 
                                                    $form_data);   
                

                return response()->json(['success' => 'oke']);
                break;
            
            default:
                # code...
                break;
        }        

    }



}
