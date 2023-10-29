@extends('template.main')

@section('content')
<div class="container px-6 mx-auto grid">
    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
      Riwayat Pemesanan
    </h2>
    <input type="hidden" id="id" value="{{ Auth::user()->profile->id }}">

    <div class="w-full overflow-hidden rounded-lg shadow-xs mt-5">
      <div class="w-full overflow-x-auto">
        <table class="w-full whitespace-no-wrap">
          <thead>
            <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800" >
              <th class="px-4 py-3">Produk</th>
              <th class="px-4 py-3">Total Harga</th>
              <th class="px-4 py-3">Aksi</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800" id="dashboard_rating_content">
            @forelse ($orders as $order)
              <tr class="text-gray-700 dark:text-gray-400 tr-order">
                <td class="px-4 py-3 text-sm">
                  {!! $order->item !!}
                </td>
                <td class="px-4 py-3 text-sm">
                  Rp {{ number_format($order->total_amount) }}
                </td>
                <td class="px-4 py-3">
                  <div class="flex items-center space-x-4 text-sm">
                    <button @click="openModal('rating', 'Beri Penilaian', 'modalRating', 'modalRatingBackground', 'modalConfirmBackground')" data-id="{{ $order->id }}" class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 {{ $order->rate == 1 ? 'bg-purple-200' : 'bg-purple-600' }} text-white rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray" aria-label="rating" id="rating-button" {{ $order->rate == 1 ? 'disabled' : '' }}>
                      Beri Penilaian
                    </button>
                  </div>
                </td>
              </tr>
            @empty
                <tr>
                  <td class="text-center font-semibold text-md px-4 py-2 w-full">
                    Belum Ada Pesanan Yang Telah Selesai Diproses
                  </td>
                </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
      Pesanan Dalam Proses
    </h2>

    <div class="w-full overflow-hidden rounded-lg shadow-xs mt-5">
      <div class="w-full overflow-x-auto">
        <table class="w-full whitespace-no-wrap" id="table_user">
          <thead>
            <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800" >
              <th class="px-4 py-3">Produk</th>
              <th class="px-4 py-3">Total Harga</th>
              <th class="px-4 py-3">Status</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800" id="dashboard_user_content">
              @forelse ($ordersIncomplete as $order)
                <tr class="text-gray-700 dark:text-gray-400 tr-item">
                  <td class="px-4 py-3 text-sm">
                    {!! $order->item !!}
                  </td>
                  <td class="px-4 py-3 text-sm">
                    Rp {{ number_format($order->total_amount) }}
                  </td>
                  <td class="px-4 py-3">
                    @if ($order->status == 0)
                      <div class="flex items-center space-x-4 text-sm">
                        <button @click="openModal('confirm', 'Bayar Sekarang', 'modalConfirm', 'modalConfirmBackground', 'modalRatingBackground')" data-id="{{ $order->id }}" data-val="1" class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 bg-yellow-400 text-white rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray" aria-label="payment confirm" id="confirm-button"> 
                          Bayar Sekarang
                        </button>
                      </div>
                    @else
                      <div class="flex items-center space-x-4 text-sm">
                        <button @click="openModal('confirm', 'Progress Pesanan', 'modalConfirm', 'modalConfirmBackground', 'modalRatingBackground')" data-id="{{ $order->id }}" data-val="1" class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 bg-blue-400 text-white rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray" aria-label="payment confirm" id="progress-button"> 
                          Lihat Progress Pesanan
                        </button>
                      </div>
                    @endif
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

  <div x-show="isModalOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-30 flex items-end bg-black  bg-opacity-50 sm:items-center sm:justify-center hidden" id="modalRatingBackground">
    <!-- Modal -->
    <div x-show="isModalOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 transform translate-y-1/2" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0  transform translate-y-1/2" @click.away="closeModal" @keydown.escape="closeModal" class="w-full px-6 py-4 overflow-hidden bg-white rounded-t-lg dark:bg-gray-800 sm:rounded-lg sm:m-4 sm:max-w-xl" role="dialog" id="modalRating" >
      <!-- Remove header if you don't want a close icon. Use modal body to place modal tile. -->
      <header class="flex justify-end">
        <button class="inline-flex items-center justify-center w-6 h-6 text-gray-400 transition-colors duration-150 rounded dark:hover:text-gray-200 hover: hover:text-gray-700" aria-label="close" @click="closeModal" > <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" role="img" aria-hidden="true" > <path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" fill-rule="evenodd" ></path>
          </svg>
        </button>
      </header>
      <!-- Modal body -->
      <div class="mt-4 mb-6">
        <!-- Modal title -->
        <p class="mb-6 text-lg font-semibold text-gray-700 dark:text-gray-300" id="modal-header-rating">
         
        </p>
        <!-- Modal description -->
        <form id="form">
          <input type="hidden" id="order_id" value="">
          <div class="text-center w-full text-5xl" id="rate-icon">
            🙂
          </div>
          <div class="flex flex-row gap-3 justify-center mt-5">
            <button type="button" id="star1" class="text-yellow-300 focus:outline-none" onclick="rate(1)">
              <svg class="w-8 h-8" viewBox="0 -0.5 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M16.0005 0L21.4392 9.27275L32.0005 11.5439L24.8005 19.5459L25.889 30.2222L16.0005 25.895L6.11194 30.2222L7.20049 19.5459L0.000488281 11.5439L10.5618 9.27275L16.0005 0Z" fill="currentColor"/>
              </svg>
            </button>
            <button type="button" id="star2" class="text-yellow-300 focus:outline-none" onclick="rate(2)">
              <svg class="w-8 h-8" viewBox="0 -0.5 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M16.0005 0L21.4392 9.27275L32.0005 11.5439L24.8005 19.5459L25.889 30.2222L16.0005 25.895L6.11194 30.2222L7.20049 19.5459L0.000488281 11.5439L10.5618 9.27275L16.0005 0Z" fill="currentColor"/>
              </svg>
            </button>
            <button type="button" id="star3" class="text-yellow-300 focus:outline-none" onclick="rate(3)">
              <svg class="w-8 h-8" viewBox="0 -0.5 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M16.0005 0L21.4392 9.27275L32.0005 11.5439L24.8005 19.5459L25.889 30.2222L16.0005 25.895L6.11194 30.2222L7.20049 19.5459L0.000488281 11.5439L10.5618 9.27275L16.0005 0Z" fill="currentColor"/>
              </svg>
            </button>
            <button type="button" id="star4" class="focus:outline-none" onclick="rate(4)">
              <svg class="w-8 h-8" viewBox="0 -0.5 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M16.0005 0L21.4392 9.27275L32.0005 11.5439L24.8005 19.5459L25.889 30.2222L16.0005 25.895L6.11194 30.2222L7.20049 19.5459L0.000488281 11.5439L10.5618 9.27275L16.0005 0Z" fill="currentColor"/>
              </svg>
            </button>
            <button type="button" id="star5" class="focus:outline-none" onclick="rate(5)">
              <svg class="w-8 h-8" viewBox="0 -0.5 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M16.0005 0L21.4392 9.27275L32.0005 11.5439L24.8005 19.5459L25.889 30.2222L16.0005 25.895L6.11194 30.2222L7.20049 19.5459L0.000488281 11.5439L10.5618 9.27275L16.0005 0Z" fill="currentColor"/>
              </svg>
            </button>
          </div>
          <label class="block mt-2 text-sm">
            <h2 class="my-6 text-md font-semibold text-gray-700 dark:text-gray-200" >
              Komentar anda
            </h2>
            <textarea class="block w-full mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-textarea focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray" rows="3" placeholder="Silahkan masukan catatan untuk setiap pesanan anda" id="comment"></textarea>
          </label>
        </form>
      </div>
      <footer class="flex flex-col items-center justify-end px-6 py-3 -mx-6 -mb-4 space-y-4 sm:space-y-0 sm:space-x-6 sm:flex-row bg-gray-50 dark:bg-gray-800">
        <button @click="closeModal" class="w-full px-5 py-3 text-sm font-medium leading-5 text-white text-gray-700 transition-colors duration-150 border border-gray-300 rounded-lg dark:text-gray-400 sm:px-4 sm:py-2 sm:w-auto active:bg-transparent hover:border-gray-500 focus:border-gray-500 active:text-gray-500 focus:outline-none focus:shadow-outline-gray">
          Batal
        </button>
        <button class="w-full px-5 py-3 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg sm:w-auto sm:px-4 sm:py-2 active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple" id="save-rating">
          Simpan
        </button>
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
      <div class="mt-4 mb-6" id="content-order">
        <p class="mb-6 text-lg font-semibold text-gray-700 dark:text-gray-300" id="modal-header-confirm"></p>

        <div id="progress-order">
          <form id="form" class="mt-6">
            <input type="hidden" id="confirm_id" value="">
            <input type="hidden" id="status" value="">
            <input type="hidden" id="process" value="confirm">
            <input type="hidden" id="order-item" value="">
            <input type="hidden" id="role" value="user">
            <div class="flex justify-center text-center">
              <div class="flex flex-col gap-2">
                <p>Pesanan anda dengan total harga :</p>
                <p class="text-4xl font-semibold text-center" id="price-text"></p>
              </div>
            </div>
            <div class="w-full mt-10 text-center">
              Silahkan tekan tombol di bawah ini untuk melakukan pembayaran
            </div>
          </form>
        </div>
      </div>
      <footer class="flex justify-center px-6 py-3 -mx-6 -mb-4 space-y-4 sm:space-y-0 sm:space-x-6 sm:flex-row bg-gray-50 dark:bg-gray-800" id="footer-order">
      </footer>
    </div>
  </div>
@endsection