
    
@extends('layouts.dashboard')

@section('content')

    <div class="container my-4">
        <p class="mb-5 px-0">* Data ini di update setiap 30 menit sekali. Terakhir update data pada <span class="font-weight-bold" id="lastupdate">xxx</span></p>

        {{-- SURAT PESAN BULAN INI --}}
        <div>
            <div class="row mt-4 px-0">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header text-white">
                            <div class="w-100"><h6>SURAT PESAN BULAN INI</h6></div>
                        </div>
                        <div class="card-body py-1">
                            <div class="row">
                                <div class="col col-sm-4" id="tablebulanini">

                                </div>
                                <div class="col col-sm-8">
                                    <canvas id="graphpesanbulanini"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SURAT PESAN BULAN INI PERGUDANG --}}
        <div>
            <div class="row mt-4 px-0">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header text-white">
                            <div class="w-100"><h6>SURAT PESAN BULAN INI PER GUDANG</h6></div>
                        </div>
                        <div class="card-body py-1">
                            <div id="divbulaninigudang">
                                <ul class="nav nav-pills">
                                    @foreach ($bulaninigudang as $d)
                                        <li class="nav-item">
                                            {{-- <a class="nav-link btn btn-sm btn-light p-1 m-1 {{ $loop->iteration == 1 ? 'active' : '' }}" href="#">{{ $d->kdgudang }}</a> --}}
                                            <button class="nav-link btn btn-sm btn-light p-0 px-1 m-1 bulaninigudangtombol" id="bulanini{{ $loop->iteration }}" href="#" onclick="showbulaninigudang('{{ $loop->iteration }}')">{{ $d->kdgudang }}</button>
                                        </li>
                                    @endforeach
                                </ul>
                                <hr class="mt-1">
                                <span id="bulanininamagudang" class="font-weight-bold">SEMUA GUDANG</span>
                                <div class="row">
                                    <div class="col col-sm-4" id="tablebulaninigudang">
    
                                    </div>
                                    <div class="col col-sm-8">
                                        <canvas id="graphpesanbulaninigudang"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SURAT PESAN SETAHUN KEBELAKANG --}}
        <div>
            <div class="row mt-4 px-0">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header text-white">
                            <div class="w-100"><h6>SURAT PESAN SATU TAHUN KEBELAKANG</h6></div>
                        </div>
                        <div class="card-body py-1">
                            <div class="row">
                                <div class="col col-sm-4" id="tablesetahun">

                                </div>
                                <div class="col col-sm-8">
                                    <canvas id="graphpesansetahun"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
   

        {{-- OVERDUE --}}
        <div class="mt-5">
            {{-- <h4 class="m-2">
                <i class="far fa-dot-circle mr-2"></i>Pesanan yang sudah melewati batas estimasi kirim
            </h4> --}}

            {{-- <div class="row mt-4 px-4">
                <div class="col-sm-6">
                    <div class="card card-warning">
                        <div class="card-header text-white d-flex justify-content-between">
                            <div class="w-100"><h6>PENDING</h6></div>
                            <div class="w-100 text-right"><h6>Total : {{ $data_totoverdue->totpending }}</h6></div>
                        </div>
                        <div class="card-body py-1">
                            <table class="table table-sm">
                                <thead>
                                <tr>
                                    <th scope="col">Estimasi kirim</th>
                                    <th scope="col" class="text-right">Jumlah</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data_pendingoverdue as $d)
                                        <tr>
                                            <td>
                                                <a href="#" onclick="showdetail('{{ $d->statuskirim }}', '-{{ $d->selisihestkirim }}', '{{ $d->status }}')" class="text-dark">
                                                    @if ($d->selisihestkirim == 0)
                                                        Hari ini                                                                                        
                                                    @else
                                                        {{ $d->selisihestkirim }} Hari yang lalu
                                                    @endif    
                                                </a>
                                            </td>
                                            <td class="text-right">{{ $d->total }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>     
                        </div>
                    </div>
                </div>
                
                <div class="col-sm-6">                
                    <div class="card card-warning">
                        <div class="card-header text-white d-flex justify-content-between">
                            <div class="w-100"><h6>PENDING PROSES</h6></div>
                            <div class="w-100 text-right"><h6>Total : {{ $data_totoverdue->totpendingproses }}</h6></div>
                        </div>
                        <div class="card-body py-1">
                            <table class="table table-sm">
                                <thead>
                                <tr>
                                    <th scope="col">Estimasi kirim</th>
                                    <th scope="col" class="text-right">Jumlah</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data_pendingprosesoverdue as $d)
                                        <tr>
                                            <td>
                                                <a href="#" onclick="showdetail('{{ $d->statuskirim }}', '-{{ $d->selisihestkirim }}', '{{ $d->status }}')" class="text-dark">
                                                    @if ($d->selisihestkirim == 0)
                                                        Hari ini                                                                                        
                                                    @else
                                                        {{ $d->selisihestkirim }} Hari yg lalu
                                                    @endif    
                                                </a>
                                            </td>
                                            <td class="text-right">{{ $d->total }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>     
                        </div>
                    </div>
                </div>


            </div> --}}
        </div>
    </div>


    <!-- Modal -->
    <div id="modaldetail" class="modaldetail modal fade" role="dialog" >
        <div class="modal-dialog  modal-lg">
    
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header" style="background-color: #2c506e;">
                    <h5 class="card-title judulbiru" id="juduldetail">Data Detail</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                
                <div class="modal-body" style="padding: 0rem;">
                    <div class="card-body" id="bodydetail">
                        <div id="divcont" class="small table-responsive">

                        </div>
                    </div>
                </div>
            </div>
    
        </div>
    </div>

@endsection



@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>


<script>

    $(document).ready(function(){
        getbulanini();
    });


    function showbulaninigudang(elem){
        loading2(1, '#divbulaninigudang', 'Opening ...');

        var pkdgudang = $('#bulanini' + elem).text();
        var pdata = {mode:'showbulaninigudang',
                    kdgudang: pkdgudang,
                    _token: _token};

        $.ajax({
                url: '{{ route("dashb.store") }}',
                type:"POST",
                data:pdata,
                async: true,
                dataFilter: function(response){
                        return response;
                    },
                success:function(data){
                    console.log(data);
                    if(data.error){
                        alert('ERROR!!!  ' + data.error);
                    }
                    else{
                        $('.bulaninigudangtombol').removeClass('active');
                        creategraph('graphpesanbulaninigudang', data.x, data.y);
                        $('#tablebulaninigudang').html(data.data);
                        $('#bulanini' + elem).addClass('active');
                        $('#bulanininamagudang').text(data.namagudang);
                    }
                    loading2(0, '#divbulaninigudang');
                }

        });           
    }


    function getbulanini(){
        loading2(1, 'body', 'Opening ...');

        var ket = $('#tketerangan').val();
        var pdata = {mode:'getdasboard',
                    _token: _token};

        $.ajax({
                url: '{{ route("dashb.store") }}',
                type:"POST",
                data:pdata,
                async: true,
                dataFilter: function(response){
                        return response;
                    },
                success:function(data){
                    console.log(data);
                    if(data.error){
                        alert('ERROR!!!  ' + data.error);
                    }
                    else{
                        $('#lastupdate').text(data.lastupdate);

                        creategraph('graphpesanbulanini', data.bulaninix, data.bulaniniy);
                        $('#tablebulanini').html(data.bulanini);

                        creategraph('graphpesansetahun', data.setahunx, data.setahuny);
                        $('#tablesetahun').html(data.setahun);

                        showbulaninigudang(1);
                    }
                    loading2(0, 'body');
                }

        });   
    }


    function creategraph(pidelement, xValues, yValues){
        new Chart(pidelement, {
            type: "line",
            data: {
                labels: xValues,
                datasets: [{
                fill: false,
                lineTension: 0,
                backgroundColor: "rgba(0,0,255,1.0)",
                borderColor: "rgba(0,0,255,0.1)",
                data: yValues
                }]
            },
            options: {
                    legend: {display: false},
                    scales: {
                        yAxes: [
                            {
                                ticks: {
                                    callback: function(label, index, labels) {
                                        return parseInt(label/1000000).toLocaleString() +'M';
                                    }
                                },
                                scaleLabel: {
                                    display: true,
                                    labelString: '1M = 1,000,000'
                                }
                            }
                        ]
                    }
                }
            });
}


</script>



@endsection
