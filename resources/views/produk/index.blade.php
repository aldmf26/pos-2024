<x-app-layout :title="$title">
    <x-slot name="header">
        <div class="d-flex justify-content-between">
            <h3>{{ $title }}</h3>
            <button type="button" data-bs-toggle="modal" data-bs-target="#tambah" class="btn btn-primary"><i
                    class="fa fa-plus"></i> Tambah</button>
        </div>

    </x-slot>


    <div class="section">
        <style>
            .pagination {
        font-size: 0.875rem; /* Sesuaikan ukuran font */
    }
        </style>

        <x-alert pesan="{{ session()->get('error') }}" />
        @livewire('produk-table')
        

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
                                    <span class="badge bg-primary text-white d-inline-block m-1 p-2" x-text="tag + ' x'"
                                        @click="removeTag(index)" style="cursor: pointer; display: inline-block;">
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
                            <div id="satuan"></div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label for="">Rak</label>
                            <div id="rak"></div>

                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label for="">Pemilik</label>
                            <div id="pemilik"></div>
                        </div>
                    </div>
                </div>
            </x-modal>
        </form>

        <form id="tbhSatuanSubmit">
            <x-modal idModal="tbhSatuan" title="Tambah Satuan" btnSave="Y">
                <input type="text" id="satuan_val" class="form-control">
            </x-modal>
        </form>

        <form id="tbhRakSubmit">
            <x-modal idModal="tbhRak" title="Tambah Rak" btnSave="Y">
                <input type="text" id="rak_val" class="form-control">
            </x-modal>
        </form>

        <form id="tbhPemilikSubmit">
            <x-modal idModal="tbhPemilik" title="Tambah Pemilik" btnSave="Y">
                <input type="text" id="pemilik_val" class="form-control">
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

        <script>
            // Fungsi generik untuk load select dan tambah item baru
            function setupDynamicSelect(options) {
                const {
                    selectId,
                    modalId,
                    submitFormId,
                    inputId,
                    loadRoute,
                    createRoute,
                    loadCallback
                } = options;

                // Load select
                function loadSelect() {
                    $.ajax({
                        type: "GET",
                        url: loadRoute,
                        beforeSend: function() {
                            $(`#${options.containerId}`).html('loading...');
                        },
                        success: function(r) {
                            $(`#${options.containerId}`).html(r);
                            $('.select2').select2({
                                dropdownParent: $('#tambah .modal-content')
                            });

                            if (loadCallback) loadCallback(r);
                        }
                    });
                }

                // Panggil load select saat inisialisasi
                loadSelect();

                // Event handler untuk select
                $(document).on('change', `#${selectId}`, function() {
                    const getVal = $(this).val();
                    if (getVal === 'tambah') {
                        $(`#${modalId}`).modal('show');

                        $(`#${submitFormId}`).off('submit').on('submit', function(e) {
                            e.preventDefault();
                            const inputVal = $(`#${inputId}`).val();

                            $.ajax({
                                type: "GET",
                                url: createRoute,
                                data: {
                                    [options.paramName]: inputVal
                                },
                                success: function(r) {
                                    $(`#${modalId}`).modal('hide');
                                    loadSelect();
                                }
                            });
                        });
                    }
                });
            }

            // Inisialisasi untuk Satuan
            setupDynamicSelect({
                selectId: 'selectSatuan',
                modalId: 'tbhSatuan',
                submitFormId: 'tbhSatuanSubmit',
                inputId: 'satuan_val',
                containerId: 'satuan',
                loadRoute: "{{ route('produk.satuan') }}",
                createRoute: "{{ route('produk.create_satuan') }}",
                paramName: 'satuan'
            });

            // Inisialisasi untuk Rak
            setupDynamicSelect({
                selectId: 'selectRak',
                modalId: 'tbhRak',
                submitFormId: 'tbhRakSubmit',
                inputId: 'rak_val',
                containerId: 'rak',
                loadRoute: "{{ route('produk.rak') }}",
                createRoute: "{{ route('produk.create_rak') }}",
                paramName: 'rak'
            });

            // Inisialisasi untuk Pemilik
            setupDynamicSelect({
                selectId: 'selectPemilik',
                modalId: 'tbhPemilik',
                submitFormId: 'tbhPemilikSubmit',
                inputId: 'pemilik_val',
                containerId: 'pemilik',
                loadRoute: "{{ route('produk.pemilik') }}",
                createRoute: "{{ route('produk.create_pemilik') }}",
                paramName: 'pemilik'
            });
        </script>
    @endsection
</x-app-layout>
