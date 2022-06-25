
@extends('layouts.bs5')


@section('filecss')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')

    <style>
        .select2-container {
            box-sizing: border-box;
            display: inline-block;
            margin: 0;
            position: relative;
            vertical-align: middle;
            width: 100% !important;
        }

        .loader {
            border: 4px solid #6c757d;
            border-radius: 50%;
            border-top: 8px solid #3498db;
            width: 24px;
            height: 24px;
            -webkit-animation: spin 2s linear infinite;
            animation: spin 2s linear infinite;
        }

        /* Safari */
        @-webkit-keyframes spin {
            0% { -webkit-transform: rotate(0deg); }
            100% { -webkit-transform: rotate(360deg); }
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>


    <div class="container my-4">
        <div id="loader" class="bg-white rounded border p-2" style="position: fixed; top: 90px; right: 12px; z-index: 99999; display: none;">
            <div class="loader"></div>
            <span>Saving ...</span>
        </div>

        <nav class="navbar navbar-expand-sm fixed-top navbar-light bg-light border">
            <div class="container">
                <div class="d-flex my-1">
                    <img src="{{ url('images/logokecil.png') }}" style="max-width: 50px; max-height: 50px;" class="me-2"/>
                    <h4 class="align-self-center">REVIEW SURAT PESAN</h4>
                </div>
                <form class="d-flex my-1">
                    <div style="position: relative">
                        <a class="btn btn-outline-secondary mx-2" href="{{ url('newsp') }}"><i class="fas fa-arrow-circle-left me-2"></i><small>Kembali ke Surat Pesan</small></a>
                    </div>
                    <a class="btn btn-outline-secondary" href="{{ url('/') }}"><i class="fas fa-home"></i></a>
                </form>
            </div>
        </nav>


        <div >
            <div class="row">
                <div class="col-md-6 my-3 text-white">
                    <h4>alskdjalskdjaslkdjalsdjalsdalksdjalksdjalsjdalksdj</h4>
                </div>
                <div class="col-md-6 my-2 text-white">
                    <h4>alskdjalskdjaslkdjalsdjalsdalksdjalksdjalsjdalksdj</h4>
                </div>
            </div>
        </div>


        <div class="card my-4">
            <h5 class="card-header bg-secondary text-white"><i class="far fa-list-alt me-2"></i>DAFTAR BARANG</h5>
            <div class="card-body">
                <div id="kosong" class="text-center {{ $cartcount == 0 ? '' : 'd-none' }}">
                    <h6 class="my-2">Belum ada barang yang diorder</h6>
                    <a class="btn btn-outline-secondary m-2" href="{{ url('newsp') }}"><i class="fas fa-arrow-circle-left me-2"></i><small>Order barang sekarang</small></a>
                </div>

                <table id="data-table" class="table {{ $cartcount == 0 ? 'd-none' : '' }}">
                    <thead>
                      <tr>
                        <th scope="col" style="border-bottom-color: #dee2e6;">Img</th>
                        <th scope="col" style="border-bottom-color: #dee2e6;">Nama</th>
                        <th scope="col" style="border-bottom-color: #dee2e6;">Keterangan</th>
                        <th scope="col" class="text-end" style="border-bottom-color: #dee2e6;">Qty</th>
                        <th scope="col" class="text-end" style="border-bottom-color: #dee2e6;">Jumlah</th>
                        <th scope="col" class="text-end" style="border-bottom-color: #dee2e6;">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                        @foreach ($mstbarang as $d)
                            <tr id="row{{ $d->kodex }}">
                                <td @if ($loop->last) class="border-bottom-0" @endif>
                                    @if ($d->img)
                                        <img src="data:image/png;base64, {{ base64_encode($d->img) }}" class="border rounded" style="max-width: 50px; max-height: 37px;"/>
                                    @else
                                        <div class="border rounded" style="width: 50px; height: 37px; background-image: url('{{ url('images/noimage2.webp') }}'); background-size: cover; background-repeat: no-repeat; background-position: center;">
                                        </div>
                                        {{-- <img src="{{ url('images/noimage2.webp') }}" style="max-width: 105px; max-height: 75px;"/> --}}
                                    @endif
                                </td>
                                <td class="@if ($loop->last) border-bottom-0 @endif">{{ $d->nama }}</td>
                                <td id="keterangan{{ $d->kodex }}" class="@if ($loop->last) border-bottom-0 @endif">{{ $d->keterangan }}</td>
                                <td id="qty{{ $d->kodex }}" class="text-end @if ($loop->last) border-bottom-0 @endif">{{ number_format($d->qty) }}</td>
                                <td id="jumlah{{ $d->kodex }}" class="text-end @if ($loop->last) border-bottom-0 @endif">{{ number_format($d->jumlah) }}</td>
                                <td class="text-end @if ($loop->last) border-bottom-0 @endif">
                                    <button id="btn-cart{{ $d->kodex }}" type="button" data-kode="{{ $d->kode }}" data-nama="{{ $d->nama }}" data-qty="{{ round($d->qty) }}" data-harga="{{ round($d->harga) }}" data-keterangan="{{ $d->keterangan }}" class="btn btn-sm btn-outline-warning btn-cart my-1" data-bs-toggle="tooltip" title="Edit data"><i class="far fa-edit"></i></button>
                                    {{-- <button id="btn-delete{{ $d->kodex }}" type="button" data-kode="{{ $d->kode }}" class="btn btn-sm btn-outline-danger btn-delete my-1" data-bs-toggle="tooltip" title="Delete data"><i class="far fa-trash-alt"></i></button> --}}
                                </td>
                            </tr>                        
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        

        <div class="card my-4">
            <h5 class="card-header bg-secondary text-white"><i class="far fa-user me-2"></i>DATA CUSTOMER</h5>
            <div class="card-body">
                <div class="mb-2 row">
                    <label class="col-sm-2 col-form-label">Nama</label>
                    <div class="col-sm-10">
                        <input type="text" class="sv form-control form-control-sm" id="csnama" value="{{ $pesanhead->csnama ?? '' }}">
                    </div>
                </div>
                <div class="mb-2 row">
                    <label class="col-sm-2 col-form-label">Alamat</label>
                    <div class="col-sm-10">
                        <textarea id="csalamat" name="alamat" class="sv form-control" cols="30" rows="2">{{ $pesanhead->csalamat ?? '' }}</textarea>
                    </div>
                </div>
                <div class="mb-2 row">
                    <label class="col-sm-2 col-form-label">Kota</label>
                    <div class="col-sm-5">
                        <select id="cskota" class="sv sel2 form-control form-control-sm">
                            <option value=""></option>      
                            @foreach ($mstongkir as $d)
                                <option value="{{ $d->id }}" data-biayax="{{ number_format($d->biaya) }}" data-biaya="{{ round($d->biaya) }}"
                                    {{ $pesanhead->cskota == $d->id ? 'selected' : '' }}>
                                    {{ $d->kota }}
                                </option>                                
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mb-2 row">
                    <label class="col-sm-2 col-form-label">Ongkir</label>
                    <div class="col-sm-5">
                        <input id="ongkir" type="number" class="sv form-control form-control-sm" value="{{ round($pesanhead->ongkir) ?? '' }}">
                    </div>
                </div>
                <div class="mb-2 row">
                    <label class="col-sm-2 col-form-label">No HP</label>
                    <div class="col-sm-5">
                        <input id="csnohp" type="tel" class="sv form-control form-control-sm" value="{{ $pesanhead->csnohp ?? '' }}">
                    </div>
                </div>
                <hr>
                <div class="mb-2 row">
                    <label class="col-sm-2 col-form-label">Outlet</label>
                    <div class="col-sm-5">
                        <select id="kdgudang" class="sv form-control form-control-sm sel2">
                            <option value=""></option>      
                            @foreach ($mstgudang as $d)
                                <option value="{{ $d->kode }}" {{ $pesanhead->kdgudang == $d->kode ? 'selected' : '' }}>{{ $d->nama . '  (' . $d->kode . ')' }}</option>                                
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mb-2 row">
                    <label class="col-sm-2 col-form-label">Keterangan</label>
                    <div class="col-sm-10">
                        <textarea id="keterangan" class="sv form-control" cols="30" rows="2">{{ $pesanhead->keterangan ?? '' }}</textarea>
                    </div>
                </div>

            </div>
        </div>
        

        <div class="card my-4">
            <h5 class="card-header bg-secondary text-white"><i class="fas fa-money-check-alt me-2"></i>PEMBAYARAN</h5>
            <div class="card-body">
                <div class="mb-2 row">
                    <label class="col-sm-2 col-form-label">DP</label>
                    <div class="col-sm-5">
                        <input id="dp" type="number" class="sv form-control form-control-sm" value="{{ round($pesanhead->dp) ?? '' }}">
                    </div>
                </div>
                <div class="mb-2 row">
                    <label class="col-sm-2 col-form-label">Leasing</label>
                    <div class="col-sm-5">
                        <select id="kdleasing" class="sv sel2 form-control form-control-sm sel2">
                            <option value=""></option>      
                            @foreach ($mstleasing as $d)
                                <option value="{{ $d->kode }}" {{ $pesanhead->kdleasing == $d->kode ? 'selected' : '' }}>{{ $d->nama }}</option>                                
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-5">
                        <small>Kosongi jika tidak menggunakan leasing</small>
                    </div>
                </div>
            </div>
        </div>


    </div>


    <div class="modal fade" id="exampleModalCenter" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog h-auto" style="max-width: 500px !important">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="modaltitle">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-modal" action="" method="post" enctype="multipart/form-data" >
                    @csrf
                    <div class="modal-body">
                        <div class="container">

                            <div class="mb-3 row">
                                <label class="col-lg-3 col-form-label">Qty</label>
                                <div class="col-lg-5">
                                    <input id="qqty" type="number" class="form-control" name="qty" min="0" required>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label class="col-lg-3 col-form-label">Harga</label>
                                <div class="col-lg-5">
                                    <input id="qharga" type="number" class="form-control" name="harga" min="0" required>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label class="col-lg-3 col-form-label">Keterangan</label>
                                <div class="col-lg-9">
                                    <textarea id="qketerangan" name="keterangan" cols="30" rows="4" class="form-control"></textarea>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <input id="modalkode" name="kode" type="hidden">
                        <button id="btn-hapus" type="submit" form="form-modal" class="btn btn-danger" value="hapusitem"><i class="fas fa-trash-alt me-2"></i>Hapus</button>
                        <button id="btn-simpan" type="submit" form="form-modal" class="btn btn-primary" value="saveqty"><i class="far fa-save me-2"></i>Simpan</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-2"></i>Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection


@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.sel2').select2(
                {
                // placeholder: "Pilih",
                // allowClear: true
                }
            );

            // $('#selectgudang').select2({
            //     placeholder: "Pilih Gudang",
            //     allowClear: true
            // });

            $('#cskota').on('select2:select', function (e) {
                var data = e.params.data;
                var biayax = $("#cskota").select2().find(":selected").data("biaya");
                $('#ongkir').val(biayax);
            });

            $('.sel2').on('select2:select', function (e) {
                savehead();
            });

            
            $(".sv").focusout(function(){
                savehead();
            });
        });


        function savehead(){
            var kdgudang = $('#kdgudang').val();
            var csnama = $('#csnama').val()
            var csalamat = $('#csalamat').val()
            var csnohp = $('#csnohp').val()
            var cskota = $('#cskota').val()
            var ongkir = $('#ongkir').val()
            var dp = $('#dp').val()
            var kdleasing = $('#kdleasing').val()
            var keterangan = $('#keterangan').val()

            $('#loader').show();

            // loading2(1, '#col' + kode, 'Saving ...');
            var pdata = {mode:'savehead', 
                        kdgudang: kdgudang,
                        csnama: csnama,
                        csalamat: csalamat,
                        csnohp: csnohp,
                        cskota: cskota,
                        ongkir: ongkir,
                        dp: dp,
                        kdleasing: kdleasing,
                        keterangan: keterangan,
                        _token: _token};
            $.ajax({
                    url: '{{ route("cartsp.store") }}',
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
                            // console.log(data);
                        }
                        // loading2(0, '#col' + kode);
                        $('#loader').hide();
                    }
            });  
        };


        $('.btn-cart').click(function(e){
            e.preventDefault();
            $('#form-modal')[0].reset();

            var kode = $(this).attr('data-kode');
            var nama = $(this).attr('data-nama');
            var qty = $(this).attr('data-qty');
            var harga = $(this).attr('data-harga');
            var keterangan = $(this).attr('data-keterangan');

            if (qty == 0) {qty = '';harga = '';}
            // if (harga == 0) {harga = '';}

            $('#modaltitle').text(nama);
            $('#modalkode').val(kode);
            $('#qqty').val(qty);
            $('#qharga').val(harga);
            $('#qketerangan').val(keterangan);
            $('#exampleModalCenter').modal('show');
        });


        $('#form-modal').on('submit', function(event){
            event.preventDefault();

            var mod = $(document.activeElement).val();

            if (mod != 'saveqty') {
                if (confirm('Yakin akan menghapus item ini ? ') != true) {
                    return;
                }
            }

            var q = $('#qqty').val();
            if (q < 1) {
                alert('Qty tidak boleh kurang atau sama dengan 0');
                return;
            }

            var fd =  new FormData(this);
            fd.append("mode", mod);

            var prosmsg = 'Menyimpan';
            if (mod != 'saveqty') {
                prosmsg = 'Menghapus'
            }
            loading2(1, '.modal-content', prosmsg + ' data ...');

            $.ajax({
                url:"{{ route('newsp.store') }}",
                method:"POST",
                data: fd,
                contentType: false,
                cache:false,
                processData: false,
                dataType:"json",
                success:function(data)
                {
                    console.log(data);
                    if(data.error){
                        alert('ERROR!!!  ' + data.error);
                    }
                    else{
                        if (mod == 'saveqty') {
                            $q = parseFloat(data.qty).toLocaleString(window.document.documentElement.lang);
                            $j = parseFloat(data.jumlah).toLocaleString(window.document.documentElement.lang);
                            $('#qty' + data.kodex).text($q);
                            $('#jumlah' + data.kodex).text($j);
                            if (data.keterangan) {$('#keterangan' + data.kodex).text(data.keterangan);}else{$('#keterangan' + data.kodex).text('');}

                            $('#btn-cart' + data.kodex).attr('data-qty', data.qty); 
                            $('#btn-cart' + data.kodex).attr('data-harga', data.harga); 
                            $('#btn-cart' + data.kodex).attr('data-keterangan', data.keterangan);                             
                        }
                        else{
                            $('#row' + data.kodex).remove();
                        }

                        $('#exampleModalCenter').modal('hide');
                    }
                    loading2(0, '.modal-content');
                }
            })
        });

    </script>
@endsection