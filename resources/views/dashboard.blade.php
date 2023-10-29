@extends('template.main')

@section('content')
<div class="container px-6 mx-auto grid">
    <!-- Charts -->
    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200" >
      Pesanan Selesai
    </h2>
    <div class="w-full">
      <div class="min-w-0 p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800" >
        <h4 class="mb-4 font-semibold text-gray-800 dark:text-gray-300">
          Daftar Pesanan
        </h4>

        <div class="w-full overflow-hidden rounded-lg shadow-xs mt-5">
          <div class="w-full overflow-x-auto">
            <table class="w-full whitespace-no-wrap">
              <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800" >
                  <th class="px-4 py-3">Nama Pelanggan</th>
                  <th class="px-4 py-3">Produk</th>
                  <th class="px-4 py-3">Detail Pesanan</th>
                  <th class="px-4 py-3">Status</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800" >
                @forelse ($completeOrders as $order)
                  <tr class="text-gray-700 dark:text-gray-400">
                    <td class="px-4 py-3 text-sm">
                      {{ $order->profile->name }}
                    </td>
                    <td class="px-4 py-3 text-sm">
                      {!! $order->item !!}
                    </td>
                    <td class="px-4 py-3 text-sm">
                      <button @click="openModal('detail', 'Detail Pesanan', 'modalDetail', 'modalDetailBackground', 'modalConfirmBackground')" data-id="{{ $order->id }}"  class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 bg-blue-400 text-white rounded-lg" id="detail-button">
                        Lihat Detail
                      </button>
                    </td>
                    <td class="px-4 py-3">
                      <div class="flex items-center space-x-4 text-sm">
                        <div class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 bg-green-600 text-white rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"> 
                          Pesanan Selesai
                        </div>
                      </div>
                    </td>
                  </tr>
                @empty
                    <tr>
                      <td class="text-center font-semibold text-md px-4 py-2 w-full">
                        Belum Ada Pesanan Selesai
                      </td>
                    </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="px-4 py-4 text-xs font-semiboldtext-gray-500 border-t" >
            {{ $completeOrders->links() }}
          </div>
        </div>
      </div>
    </div>

    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200" >
      Pesanan Dalam Proses
    </h2>
    <div class="w-full">
      <div class="min-w-0 p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800" >
        <h4 class="mb-4 font-semibold text-gray-800 dark:text-gray-300">
          Daftar Pesanan
        </h4>

        <div class="w-full overflow-hidden rounded-lg shadow-xs mt-5">
          <div class="w-full overflow-x-auto">
            <table class="w-full whitespace-no-wrap">
              <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800" >
                  <th class="px-4 py-3">Nama Pelanggan</th> 
                  <th class="px-4 py-3">Produk</th> 
                  <th class="px-4 py-3">Detail Pesanan</th>
                  <th class="px-4 py-3">Status</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800" id="tbody-incomplete-order">
                @forelse ($orders as $order)
                  <tr class="text-gray-700 dark:text-gray-400" id="tr-incomplete-order">
                    <td class="px-4 py-3 text-sm">
                      {{ $order->profile->name }}
                    </td>
                    <td class="px-4 py-3 text-sm">
                      {!! $order->item !!}
                    </td>
                    <td class="px-4 py-3 text-sm">
                      <button @click="openModal('detail', 'Detail Pesanan', 'modalDetail', 'modalDetailBackground', 'modalConfirmBackground')" data-id="{{ $order->id }}"  class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 bg-blue-400 text-white rounded-lg" id="detail-button">
                        Lihat Detail
                      </button>
                    </td>
                    <td class="px-4 py-3">
                      <div class="flex items-center space-x-4 text-sm">
                        @switch($order->status)
                            @case(-1)
                              <div class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 bg-red-600 text-white rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"> 
                                Pesanan Dibatalkan
                              </div>
                                @break
                            @case(0)
                              <div class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 bg-yellow-400 text-white rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray" aria-label="payment confirm" id="confirm-button"> 
                                Menunggu Pembayaran
                              </div>
                                @break
                            @case(1)
                              <button @click="openModal('confirm', 'Menuju Proses Selanjutnya', 'modalConfirm', 'modalConfirmBackground', 'modalDetailBackground')" data-id="{{ $order->id }}" data-val="2" class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 bg-yellow-400 text-white rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray" aria-label="payment confirm" id="confirm-button"> 
                                Konfirmasi Pembayaran Pembeli
                              </button>
                                @break
                            @case(2)
                              <button @click="openModal('confirm', 'Menuju Proses Selanjutnya', 'modalConfirm', 'modalConfirmBackground', 'modalDetailBackground')" data-id="{{ $order->id }}" data-val="3" class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 bg-blue-600 text-white rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray" aria-label="payment confirm" id="confirm-button"> 
                                Makanan Sedang Disiapkan
                              </button>
                                @break
                            @case(3)
                              <button @click="openModal('confirm', 'Menuju Proses Selanjutnya', 'modalConfirm', 'modalConfirmBackground', 'modalDetailBackground')" data-id="{{ $order->id }}" data-val="4" class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 bg-blue-600 text-white rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray" aria-label="payment confirm" id="confirm-button"> 
                                Menunggu Pengiriman
                              </button>
                                @break
                            @case(4)
                              <button @click="openModal('confirm', 'Menuju Proses Selanjutnya', 'modalConfirm', 'modalConfirmBackground', 'modalDetailBackground')" data-id="{{ $order->id }}" data-val="5" class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 bg-blue-600 text-white rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray" aria-label="payment confirm" id="confirm-button"> 
                                Makanan Sedang Dalam Perjalanan
                              </button>
                                @break
                            @case(5)
                              <div class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 bg-green-600 text-white rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"> 
                                Pesanan Selesai
                              </div>
                              @break
                            @default
                            <div class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 bg-yellow-400 text-white rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray" aria-label="payment confirm" id="confirm-button"> 
                              Menunggu Pembayaran
                            </div> 
                        @endswitch
                      </div>
                    </td>
                  </tr>
                @empty
                    <tr>
                      <td class="text-center font-semibold text-md px-4 py-2 w-full">
                        Belum Ada Pesanan
                      </td>
                    </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div x-show="isModalOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-30 flex items-end bg-black  bg-opacity-50 sm:items-center sm:justify-center hidden" id="modalDetailBackground">
    <!-- Modal -->
    <div x-show="isModalOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 transform translate-y-1/2" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0  transform translate-y-1/2" @click.away="closeModal" @keydown.escape="closeModal" class="w-full px-6 py-4 overflow-hidden bg-white rounded-t-lg dark:bg-gray-800 sm:rounded-lg sm:m-4 sm:max-w-xl" role="dialog" id="modalDetail" >
      <!-- Remove header if you don't want a close icon. Use modal body to place modal tile. -->
      <header class="flex justify-end">
        <button class="inline-flex items-center justify-center w-6 h-6 text-gray-400 transition-colors duration-150 rounded dark:hover:text-gray-200 hover: hover:text-gray-700" aria-label="close" @click="closeModal" > <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" role="img" aria-hidden="true" > <path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" fill-rule="evenodd" ></path>
          </svg>
        </button>
      </header>
      <!-- Modal body -->
      <div class="mt-4 mb-6">
        <!-- Modal title -->
        <p class="mb-6 text-lg font-semibold text-gray-700 dark:text-gray-300" id="modal-header-detail">
         
        </p>
        <!-- Modal description -->
        <p class="text-md font-semibold">Produk yang dipesan : </p> 
        <p id="product-content"></p>
        <p class="text-md font-semibold mt-6">Catatan Pesanan :</p>
        <p id="product-message"></p>
      </div>
      <footer class="flex flex-col items-center justify-end px-6 py-3 -mx-6 -mb-4 space-y-4 sm:space-y-0 sm:space-x-6 sm:flex-row bg-gray-50 dark:bg-gray-800">
      </footer>
    </div>
  </div>

  <div x-show="isModalOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-30 flex items-end bg-black  bg-opacity-50 sm:items-center sm:justify-center" id="modalConfirmBackground">
    <!-- Modal -->
    <div x-show="isModalOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 transform translate-y-1/2" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0  transform translate-y-1/2" @click.away="closeModal" @keydown.escape="closeModal" class="w-full px-6 py-4 overflow-hidden bg-white rounded-t-lg dark:bg-gray-800 sm:rounded-lg sm:m-4 sm:max-w-xl" role="dialog" id="modalConfirm" >
      <!-- Remove header if you don't want a close icon. Use modal body to place modal tile. -->
      <header class="flex justify-end">
        <button class="inline-flex items-center justify-center w-6 h-6 text-gray-400 transition-colors duration-150 rounded dark:hover:text-gray-200 hover: hover:text-gray-700" aria-label="close" @click="closeModal" > <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" role="img" aria-hidden="true" > <path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" fill-rule="evenodd" ></path>
          </svg>
        </button>
      </header>
      <!-- Modal body -->
      <div class="mt-4 mb-6">
        <!-- Modal title -->
        <p class="mb-6 text-xl font-semibold text-gray-700 dark:text-gray-300" id="modal-header-confirm">
         
        </p>
        <!-- Modal description -->
        <form id="form" class="mt-6">
          <input type="hidden" id="confirm_id" value="">
          <input type="hidden" id="status" value="">
          <input type="hidden" id="process" value="process_state">
          <input type="hidden" id="role" value="admin">
          <p class="text-md font-semibold">Pesanan telah selesai diproses</p>
          <p>Silahkan klik tombol di bawah ini untuk meneruskan ke proses selanjutnya</p>
        </form>
      </div>
      <footer class="flex justify-center px-6 py-3 -mx-6 -mb-4 space-y-4 sm:space-y-0 sm:space-x-6 sm:flex-row bg-gray-50 dark:bg-gray-800" id="footer-order">
      </footer>
    </div>
  </div>
@endsection