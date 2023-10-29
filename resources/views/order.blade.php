@extends('template.main')

@section('content')
    <div class="container px-6 mx-auto grid mb-10">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200" >
        Halaman Pesanan
        </h2>
        {{-- <button @click="openModal('Tambah Produk')" class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple w-40">
            Tambah Produk
        </button> --}}
        <div class="w-full grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-4 mt-5 max-h-lg overflow-y-scroll">
          @forelse ($products as $product)
            <div class="w-32 h-40">
              <div class="overflow-hidden rounded-lg shadow-lg mb-2 w-full h-24 group" style="background-image: url({{ asset($product->photo) }}); background-size: cover; background-repeat: no-repeat;">
                <button data-id="{{ $product->id }}" id="add-item" class="w-full h-24 opacity-0 transition-opacity duration-300 flex justify-center items-center bg-white text-3xl group-hover:opacity-80">
                  <span class="opacity-100">➕</span>
                </button>
              </div>
              <p class="text-gray-700 font-bold text-sm truncate cursor-pointer hover:text-secondary">{{ $product->name }}</p>
              <p class="text-gray-700 text-[10px]">Harga : {{ number_format($product->price) }}</p>
              @if ($product->stock < 1)
              <span class="px-1 py-1 text-[8px] leading-tight text-red-700 bg-red-100 rounded-md">
                  Stok Habis
                </span>
              @elseif($product->stock >= 1 && $product->stock <= 10)
                <span class="px-1 py-1 text-[8px] leading-tight text-orange-700 bg-orange-100 rounded-md">
                  Stok Sisa Sedikit
                </span>
              @else
              <span class="px-1 py-1 text-[8px] leading-tight text-green-700 bg-green-100 rounded-md">
                  Tersedia
                </span>
              @endif
            </div>
          @empty
              
          @endforelse
        </div>

        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200" >
          Pesanan Anda
        </h2>
        <div id="items-content" class="max-h-md overflow-y-scroll">
          
        </div>

        @if (Auth::user()->role == 0)
          <h2 class="my-6 text-md font-semibold text-gray-700 dark:text-gray-200" >
            Alamat Pengiriman
          </h2>

          <form class="flex flex-row items-center">
            <div class="flex flex-col md:flex-row w-full p-4 bg-white rounded-lg shadow-xs justify-between mb-1 items-center">
                <div class="flex flex-col md:flex-row  justify-between">
                    <div class="flex flex-col mb-2 md:mb-0">
                        <label class="md:mb-2 text-xs font-medium text-gray-400 dark:text-gray-400">Alamat</label>
                        <input class="focus:outline-none shadow-md rounded-md pl-3 pr-3 placeholder:text-sm" placeholder="alamat..." type="text" id="address" value="{{ Auth::user()->profile->address }}" required>
                    </div>
                    <div class="flex flex-col mb-2 md:mb-0 ml-1">
                        <label class="md:mb-2 text-xs font-medium text-gray-400 dark:text-gray-400">Kelurahan</label>
                        <input class="focus:outline-none shadow-md rounded-md pl-3 pr-3 placeholder:text-sm" placeholder="kelurahan..." type="text" id="village" value="{{ Auth::user()->profile->village }}" onkeypress="return onlyAlphaKey(event)" required>
                    </div>
                    <div class="flex flex-col mb-2 md:mb-0 ml-1">
                        <label class="md:mb-2 text-xs font-medium text-gray-400 dark:text-gray-400">Kecamatan</label>
                        <input class="focus:outline-none shadow-md rounded-md pl-3 pr-3 placeholder:text-sm" placeholder="kecamatan..." id="district" type="text" value="{{ Auth::user()->profile->district }}" required onkeypress="return onlyAlphaKey(event)">
                    </div>
                </div>
                <div>
                    <div class="flex flex-col mb-2 md:mb-0 ml-1">
                        <label class="md:mb-2 text-xs font-medium text-gray-400 dark:text-gray-400">Ongkir</label>
                        <input class="focus:outline-none shadow-md rounded-md pl-3 pr-3 placeholder:text-sm" type="text" id="ongkir" disabled required>
                    </div>
                </div>
            </div> 
            <button type="button" class="ml-2 px-4 py-2 text-white font-semibold bg-white shadow-sm rounded-lg bg-purple-600 hover:bg-purple-900" onclick="getOngkir()">
              Cek Ongkir
            </button>
          </form>
          <label class="block mt-2 text-sm">
            <h2 class="my-6 text-md font-semibold text-gray-700 dark:text-gray-200" >
              Nomor Telepon
            </h2>
            <input class="focus:outline-none shadow-md rounded-md pl-3 pr-3 placeholder:text-sm" type="text" id="phone_number" placeholder="08..." value="{{ Auth::user()->profile->phone_number }}" onkeypress="return onlyNumberKey(event)">
          </label>
        @endif
        <input type="hidden" id="role-id" value="{{ Auth::user()->role }}">
        <label class="block mt-2 text-sm">
          <h2 class="my-6 text-md font-semibold text-gray-700 dark:text-gray-200" >
            Catatan Pesanan ( Opsional )
          </h2>
          <input type="hidden" id="profile_id" value="{{ Auth::user()->profile->id }}">
          <textarea class="block w-full mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-textarea focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray" rows="3" placeholder="Silahkan masukan catatan untuk setiap pesanan anda" id="message"></textarea>
        </label>
        <button class="px-4 py-2 text-white font-semibold bg-white shadow-sm rounded-lg bg-purple-600 hover:bg-purple-900 mt-3" id="order-button">
          Konfirmasi Pemesanan
        </button>
      </div>
@endsection