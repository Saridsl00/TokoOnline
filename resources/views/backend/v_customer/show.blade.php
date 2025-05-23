@extends('backend.v_layouts.app')
@section('content')

<!-- contentAwal --> 

<div class="container-fluid"> 
    <div class="row"> 
        <div class="col-12"> 
            <div class="card"> 
                <form action="{{ route('backend.customer.show', $show->user->id) }}" method="post" enctype="multipart/form-data"> 
                    @method('put') 
                    @csrf 

                    <div class="card-body"> 
                        <h4 class="card-title"> {{$judul}} </h4> 
                        <div class="row"> 

                            <div class="col-md-4"> 
                                <div class="form-group"> 
                                    <label>Foto</label> 
                                    {{-- view image --}} 
                                    @if ($show->user->foto) 
                                        <img src="{{ asset('storage/img-customer/' . $show->user->foto) }}" class="foto-preview" width="100%"> 
                                    @else 
                                        <img src="{{ asset('storage/img-customer/imgdefault.jpg') }}" class="foto-preview" width="100%"> 
                                    @endif 
                                    <p></p> 
                                    {{-- file foto --}} 
                                    <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror" onchange="previewFoto()"> 
                                    @error('foto') 
                                        <div class="invalid-feedback alert-danger">{{ $message }}</div> 
                                    @enderror 
                                </div> 
                            </div> 

                            <div class="col-md-8"> 
                                <div class="form-group"> 
                                    <label>Nama Customer</label> 
                                    <input type="text" name="nama" value="{{ old('nama',$show->user->nama) }}" class="form-control @error('nama') is-invalid @enderror" placeholder="Masukkan Nama Customer" readonly> 
                                    @error('nama') 
                                        <span class="invalid-feedback alert-danger" role="alert">{{ $message }}</span> 
                                    @enderror 
                                </div> 

                                <div class="form-group"> 
                                    <label>Email</label> 
                                    <input type="email" name="email" value="{{ old('email',$show->user->email) }}" class="form-control @error('email') is-invalid @enderror" placeholder="Masukkan Email" readonly> 
                                    @error('email') 
                                        <span class="invalid-feedback alert-danger" role="alert">{{ $message }}</span> 
                                    @enderror 
                                </div> 

                                <div class="form-group"> 
                                    <label>No HP</label> 
                                    <input type="text" name="hp" value="{{ old('hp',$show->user->hp) }}" onkeypress="return hanyaAngka(event)" class="form-control @error('hp') is-invalid @enderror" placeholder="Masukkan Nomor HP" readonly> 
                                    @error('hp') 
                                        <span class="invalid-feedback alert-danger" role="alert">{{ $message }}</span> 
                                    @enderror 
                                </div> 

                                <div class="form-group"> 
                                    <label>Alamat</label> 
                                    <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3" placeholder="Masukkan Alamat" readonly>{{ old('alamat',$show->alamat) }}</textarea> 
                                    @error('alamat') 
                                        <span class="invalid-feedback alert-danger" role="alert">{{ $message }}</span> 
                                    @enderror 
                                </div> 

                                <div class="form-group"> 
                                    <label>Kode Pos</label> 
                                    <input type="text" name="pos" value="{{ old('pos',$show->pos) }}" onkeypress="return hanyaAngka(event)" class="form-control @error('pos') is-invalid @enderror" placeholder="Masukkan Kode Pos" readonly> 
                                    @error('pos') 
                                        <span class="invalid-feedback alert-danger" role="alert">{{ $message }}</span> 
                                    @enderror 
                                </div> 
                            </div> 

                        </div> 
                    </div> 
                    <div class="border-top"> 
                        <div class="card-body"> 
                            <a href="{{ route('backend.customer.index') }}"> 
                                <button type="button" class="btn btn-secondary">Kembali</button> 
                            </a> 
                        </div> 
                    </div> 
                </form> 
            </div> 
        </div> 
    </div> 
</div> 

<!-- contentAkhir --> 
@endsection