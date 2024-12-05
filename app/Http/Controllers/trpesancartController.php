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
use PDF;


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
        $title = 'REVIEW SURAT PESAN';
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
        // $pesanbayar = DB::table('vwtrpesantmpbayar')->where('userid', $cur_user->id)->get();
        $pesanbayar = $this->gettablebodyhtmlbayar();
        $mstbayar = DB::table('posframe_stdefaultbayar')->get();

        $cartcount = $this->cartcount();  
        $total = $this->gettotaltransaksi();

        return view('trpesancart',compact('title', 'mstbarang', 'mstgudang', 'mstongkir', 'mstleasing', 'mstbayar', 'cartcount', 'pesanhead', 'pesanbayar', 'total'));
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
                    'ls_asuransi' => $request->ls_asuransi ? $request->ls_asuransi : 0,
                    'keterangan' => $request->keterangan,
                    'nonotamanual' => $request->nonotamanual
                );
        
                DB::table('trpesantmph')->updateOrInsert(['userid' => $cur_user->id], 
                                                    $form_data);   
                $total = $this->gettotaltransaksi();
                

                return response()->json(['success' => 'oke', 'total' => $total]);
                break;

            
            case 'tambahbayar':
                $pk = array(
                    'userid' => $cur_user->id,
                    'kode' => $request->kodebayar,
                    'nobukti' => $request->nobuktibayar
                );

                $val = array(
                    'jumlah' => $request->jumlahbayar
                );
        
                DB::table('trpesantmpbayar')->updateOrInsert($pk, $val);  
                $bodybayar = $this->gettablebodyhtmlbayar();
                $total = $this->gettotaltransaksi();
  
                return response()->json(['success' => 'oke', 'bodybayar' => $bodybayar, 'total' => $total]);
                break;

        
            case 'delbayar':                
                DB::table('trpesantmpbayar')->where('userid', $cur_user->id)->where('kode', $request->kode)->where('nobukti', $request->nobukti)->delete();
                $bodybayar = $this->gettablebodyhtmlbayar();
                $total = $this->gettotaltransaksi();
                return response()->json(['success' => 'oke', 'bodybayar' => $bodybayar, 'total' => $total]);
                break;

        
            case 'saveme':
                $tmp = DB::table('trpesantmpd')->where('userid', $cur_user->id)->count();
                if ($tmp == 0) {return response()->json(['error' => 'Belum ada barang yang dipilih']);}

                $tmp = DB::table('trpesantmph')->where('userid', $cur_user->id)->count();
                if ($tmp == 0) {
                    return response()->json(['error' => 'Info customer belum diisi']);}
                else{
                    $tmp = DB::table('trpesantmph')->selectraw("kdgudang, csnama, csalamat, csnohp, cskota, nonotamanual")->where('userid', $cur_user->id)->first();
                    $err = '';
                    if ($tmp->csnama == '') {return response()->json(['error' => 'Nama Customer masih kosong']);}
                    if ($tmp->csalamat == '') {return response()->json(['error' => 'Alamat customer masih kosong']);}
                    if ($tmp->cskota == '') {return response()->json(['error' => 'Kota Customer masih kosong']);}
                    if ($tmp->csnohp == '') {return response()->json(['error' => 'No HP Customer masih kosong']);}
                    if ($tmp->kdgudang == '') {return response()->json(['error' => 'Outlet belum di pilih']);}
                    if ($tmp->nonotamanual == '') {return response()->json(['error' => 'No Nota Manual masih kosong']);}
                }

                $tmp = DB::table('trpesantmpbayar')->where('userid', $cur_user->id)->sum('jumlah');
                if ($tmp == 0) {return response()->json(['error' => 'DP belum diisi sama sekali']);}
 

                $data = DB::select(DB::raw("CALL spsavetrpesan($cur_user->id)"));
                $status = $data[0]->status;
                $info = $data[0]->info;
                if ($status == '1') {
                    $this->savetopdf($info);

                    $d = DB::table('trpesanh')->where('faktur', $info)->first();
                    $goto = url('/cartsp/' . $d->alias);
                    $gotonewsp = url('/newsp');
                    return response()->json(['success' => 'oke hmmm', 'data' => $data, 'goto' => $goto, 'gotonewsp' => $gotonewsp]);    
                }
                else{
                    return response()->json(['error' => $info, 'data' => $data]);    
                }
                break;

        
            case 'cancelcart':
                $data = DB::select(DB::raw("CALL spdeltrpesan($cur_user->id)"));
                $status = $data[0]->status;
                $info = $data[0]->info;
                if ($status == '1') {
                    $gotonewsp = url('/newsp');
                    return response()->json(['success' => 'oke hmmm', 'data' => $data, 'gotonewsp' => $gotonewsp]);    
                }
                else{
                    return response()->json(['error' => $info, 'data' => $data]);    
                }
                break;

            
            default:
                # code...
                break;
        }        

    }


    public function show($alias)
    {
        $d = DB::table('trpesanh')->where('alias', $alias)->first();
        if ($d) {
            $data = $this->getfaktur($d->faktur);
            // $this->savetopdf($faktur);
            return view('trpesanprint',$data);
        }
        else{
            abort(403, 'Unauthorized action.');
        }
    }


    function savetopdf($faktur){
        // $pdf = PDF::loadView('trpesanprint',compact('faktur'))->save(public_path() . '/faktur/trpesan/' . $faktur . ".pdf");

        // Browsershot::url('https://google.com')
        //     ->setOption('landscape', true)
        //     ->windowSize(3840, 2160)
        //     ->waitUntilNetworkIdle()
        //     ->save(public_path() . '/faktur/trpesan/' . $faktur . ".pdf");

        $data = $this->getfaktur($faktur);
        $pdf = PDF::loadView('trpesanprint', $data);
        $pdf->save(public_path() . '/faktur/trpesan/' . $faktur . ".pdf");
        // PDF::loadView('trpesanprint', $data)->save(public_path() . '/faktur/trpesan/' . $faktur . ".pdf");
    }


    function getfaktur($faktur)
    {
        $title = 'CETAK SURAT PESAN';
        $que = "select a.*, ifnull(b.qty, 0) as qty, ifnull(b.harga, 0) as harga, ifnull(b.disc, 0) as disc, ifnull(b.jumlah, 0) as jumlah, ifnull(b.keterangan, '') as keterangan,
                    REPLACE(a.kode, '.', '') as kodex
                FROM posframe_mstbarang a
                inner join trpesand b on a.kode = b.kode and b.qty > 0 and b.faktur = '$faktur'
                order by a.nama";
        $mstbarang = DB::select(DB::raw($que));

        $mstongkir = mstongkir::orderby('kota')->get();
        $mstgudang = mstgudang::where('kode','<>', '')->where('nama','<>', '-')->orderby('nama')->get();
        $mstleasing = posframe_mstleasing::orderby('nama')->get();
        $pesanhead = DB::table('vwtrpesanh')->where('faktur', $faktur)->first();
        $pesanbayar = $this->gettablebodyhtmlbayarbyfaktur($faktur);
        $mstbayar = DB::table('posframe_stdefaultbayar')->get();

        $cartcount = 99;  
        $total = $this->gettotaltransaksibyfaktur($faktur);

        return compact('title', 'mstbarang', 'mstgudang', 'mstongkir', 'mstleasing', 'mstbayar', 'cartcount', 'pesanhead', 'pesanbayar', 'total');
    }
    

    function gettotaltransaksi(){
        $cur_user = \Auth::user();
        $head = DB::table('trpesantmph')->where('userid', $cur_user->id)->first();
        $ongkir = $head ? $head->ongkir : 0;
        $dp = $head ? $head->dp : 0;
        $ls_cicilan1 = $head ? $head->ls_cicilan1 : 0;
        $ls_admin = $head ? $head->ls_admin : 0;
        $ls_asuransi = $head ? $head->ls_asuransi : 0;
        $kdleasing = $head ? $head->kdleasing : null;

        $totalbarang = DB::table('trpesantmpd')->where('userid', $cur_user->id)->sum('jumlah');
        $totaldp = DB::table('trpesantmpbayar')->where('userid', $cur_user->id)->sum('jumlah');

        if ($kdleasing) {
            $total = $dp + $ls_cicilan1 + $ls_admin + $ls_asuransi + $ongkir;
        }
        else{
            $total = $totalbarang + $ongkir;
        }
        $kurangbayar = $total - $totaldp;
        return ['totalbarang' => number_format($totalbarang), 'ongkir' => number_format($ongkir), 
                'total' => number_format($total), 'kdleasing' => $kdleasing, 'dp' => number_format($dp), 
                'ls_cicilan1' => number_format($ls_cicilan1), 'ls_admin' => number_format($ls_admin), 'ls_asuransi' => number_format($ls_asuransi),
                'totaldp' => number_format($totaldp), 'kurangbayar' => number_format($kurangbayar), 'kurangbayarx' => $kurangbayar] ;
    }
    

    function gettotaltransaksibyfaktur($faktur){
        $head = DB::table('vwtrpesanh')->where('faktur', $faktur)->first();
        $ongkir = $head ? $head->ongkir : 0;
        $dp = $head ? $head->dp : 0;
        $ls_cicilan1 = $head ? $head->ls_cicilan1 : 0;
        $ls_admin = $head ? $head->ls_admin : 0;
        $ls_asuransi = $head ? $head->ls_asuransi : 0;
        $kdleasing = $head ? $head->kdleasing : null;
        $namaleasing = $head ? $head->namaleasing : null;

        $totalbarang = DB::table('trpesand')->where('faktur', $faktur)->sum('jumlah');
        $totaldp = DB::table('trpesanbayar')->where('faktur', $faktur)->sum('jumlah');

        if ($kdleasing) {
            $total = $dp + $ls_cicilan1 + $ls_admin + $ls_asuransi + $ongkir;
        }
        else{
            $total = $totalbarang + $ongkir;
        }
        $kurangbayar = $total - $totaldp;
        return ['totalbarang' => number_format($totalbarang), 'ongkir' => number_format($ongkir), 
                'total' => number_format($total), 'kdleasing' => $kdleasing, 'namaleasing' => $namaleasing, 'dp' => number_format($dp), 
                'ls_cicilan1' => number_format($ls_cicilan1), 'ls_admin' => number_format($ls_admin), 'ls_asuransi' => number_format($ls_asuransi),
                'totaldp' => number_format($totaldp), 'kurangbayar' => number_format($kurangbayar)] ;
    }


    function gettablebodyhtmlbayar(){
        $cur_user = \Auth::user();
        $pesanbayar = DB::table('vwtrpesantmpbayar')->where('userid', $cur_user->id)->get();

        $ht = '';
        foreach ($pesanbayar as $d){
            $ht .= <<<EOD
                        <tr class="small">
                            <td>$d->nama</td>
                            <td>$d->nobukti</td>
                            <td class="text-end">$d->jumlahx</td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-danger p-0 px-1" onclick="delbayar('$d->kode', '$d->nobukti')"><i class="fas fa-trash-alt"></i></button>
                            </td>
                        </tr>
                    EOD;                            
        }
        return $ht;
    }


    function gettablebodyhtmlbayarbyfaktur($faktur){
        $pesanbayar = DB::table('vwtrpesanbayar')->where('faktur', $faktur)->get();

        $ht = '';
        $jml = 0;
        foreach ($pesanbayar as $d){
            $jml = $jml + $d->jumlah;
            $ht .= <<<EOD
                        <tr class="small border-bottom">
                            <td class="text-start py-1">$d->nama</td>
                            <td class="text-start py-1">$d->nobukti</td>
                            <td class="text-end py-1">$d->jumlahx</td>
                        </tr>
                    EOD;                            
        }
        $j = number_format($jml);
        $ht .= <<<EOD
                    <tr class="small fw-bold">
                        <td class="border-bottom-0" >TOTAL DP</td>
                        <td class="border-bottom-0" ></td>
                        <td class="text-end border-bottom-0">$j</td>
                    </tr>
                EOD;             
                
        return $ht;
    }


}
