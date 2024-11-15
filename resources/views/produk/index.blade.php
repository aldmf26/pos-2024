<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between">
            <h3>{{ $title }}</h3>
            <button type="button" data-bs-toggle="modal" data-bs-target="#tambah" class="btn btn-primary"><i
                    class="fa fa-plus"></i> Tambah</button>
        </div>

    </x-slot>


    <div class="section">
        <x-alert pesan="{{ session()->get('error') }}" />
        <table class="table table-hover" id="example">
            <thead class="bg-light">
                <tr>
                    <th>#</th>
                    <th>Foto</th>
                    <th>Nama</th>
                    <th>Deskripsi</th>
                    <th>Tags</th>
                    <th class="text-end">Harga Beli / Jual</th>
                    <th class="text-end">Stok</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($produk as $i => $d)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            <div class="d-flex justify-content-center" style="max-width: 130px; height: 100px;">
                                <img class="mx-auto mh-100"
                                    src="{{ strpos($d->foto, 'http') !== false ? $d->foto : asset('/uploads/' . $d->foto) }}">
                            </div>
                        </td>
                        <td>{{ $d->nama_produk }}</td>
                        <td>{{ $d->deskripsi }}</td>
                        <td class="text-primary">
                            {{$d->tags}}
                        </td>
                        <td align="right">{{number_format($d->hrg_beli,0)}} / {{ number_format($d->harga,0) }}</td>
                        <td align="right">{{$d->stok}}</td>
                        <td align="center">
                            <button class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></button>
                            <button class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <form action="{{ route('produk.create') }}" method="post" enctype="multipart/form-data">
            @csrf
            <x-modal idModal="tambah" size="modal-lg" title="Tambah Produk" btnSave="Y">
                <div class="row p-2" x-data="{
                    imageBy: 'upload',
                }">
                    <div class="col-12 col-md-6 col-lg-4 col-xl-3 mb-3 p-3">
                        <div class="position-relative" x-show="imageBy === 'upload'">
                            <img id="bookCoverPreview" src="{{ asset('uploads/default.jpg') }}" alt=""
                                height="300" class="img-fluid z-1">
                            <div class="position-absolute top-50 start-50 translate-middle z-0 d-none"
                                id="imagePreviewContainer">
                                <img id="imagePreview" src="" alt="" class="img-fluid">
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="image" class="form-label">Gambar</label>

                        <div class="d-flex align-items-center gap-2">
                            {{-- <div>
                                <label class="btn btn-outline-danger" for="danger-outlined">By Url</label>
                                <input @change="imageBy = 'upload'" type="radio" class="btn-check"
                                    name="options-outlined" id="success-outlined" autocomplete="off" checked="">
                            </div>
                            <div>
                                <label class="btn btn-outline-success" for="success-outlined">By Upload</label>
                                <input @change="imageBy = 'url'" type="radio" class="btn-check"
                                    name="options-outlined" id="danger-outlined" autocomplete="off">
                            </div> --}}
                            <div class="">
                                <input class="form-control @error('image') is-invalid @enderror"
                                    :type="imageBy === 'upload' ? 'file' : 'text'" id="image" name="image"
                                    onchange="previewImage(event)">
                                <div class="invalid-feedback">
                                    @error('image')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-1">
                            <label for="ind" class="form-label">Nama Produk</label>
                            <input placeholder="nama produk" type="text" class="form-control" name="nm_produk"
                                required>
                        </div>
                    </div>
                    <div class="col-6">
                        <div x-data="tagsInput()" class="mb-1">
                            <label for="ind" class="form-label">Tags</label>
                            <div class="input-group">
                                <input x-model="input" @keydown.enter.prevent="addTag" @keydown.comma.prevent="addTag"
                                    type="text" class="form-control" placeholder="pisahkan tags dengan koma (,)"
                                    autocomplete="off">
                                <span class="input-group-text">#</span>
                            </div>
                            <div class="tags-container mt-2">
                                <template x-for="(tag, index) in tags" :key="index">
                                    <span class="badge bg-primary text-white d-inline-block m-1 p-2"
                                        x-text="tag + ' x'" @click="removeTag(index)"
                                        style="cursor: pointer; display: inline-block;">
                                        <span class="ms-2">&times;</span>
                                    </span>
                                </template>
                                <input type="hidden" name="tags" :value="tags.join(',')" />
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-1">
                            <label for="ind" class="form-label">Deskripsi</label>
                            <input placeholder="deskripsi" type="text" class="form-control" name="deskripsi"
                                required>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label for="">Harga Beli</label>
                            <input type="text" name="hrg_beli" class="form-control">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label for="">Harga Jual</label>
                            <input type="text" name="hrg_jual" class="form-control">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label for="">Stok</label>
                            <input type="text" name="stok" class="form-control">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label for="">Satuan</label>
                            <select name="satuan" class="select2" id="">
                                <option value="">- Pilih Satuan -</option>
                                @foreach ($satuan as $r)
                                    <option value="{{ $r->id }}">{{ $r->satuan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label for="">Rak</label>
                            <select name="rak" class="select2" id="">
                                <option value="">- Pilih Rak -</option>
                                @foreach ($rak as $r)
                                    <option value="{{ $r->id }}">{{ $r->rak }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label for="">Pemilik</label>
                            <select name="pemilik" class="select2" id="">
                                <option value="">- Pilih Pemilik -</option>
                                @foreach ($pemilik as $p)
                                    <option value="{{ $p->id }}">{{ $p->pemilik }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </x-modal>
        </form>
    </div>
    @section('scripts')
        <script>
            function tagsInput() {
                return {
                    input: '',
                    tags: [],
                    addTag() {
                        if (this.input.trim() !== '') {
                            this.tags.push(this.input.trim());
                            this.input = ''; // Reset input field
                        }
                    },
                    removeTag(index) {
                        this.tags.splice(index, 1);
                    }
                }
            }
        </script>
        <script>
            function previewImage() {
                const fileInput = document.querySelector('#image');
                const imagePreview = document.querySelector('#bookCoverPreview');

                const reader = new FileReader();
                reader.readAsDataURL(fileInput.files[0]);

                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                };
            }
        </script>
    @endsection
</x-app-layout>
