@extends('template.main')

@section('content')
    <div class="container px-6 mx-auto grid">
      <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200" >
        Dashboard Penjualan
      </h2>
      <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-3">
        <!-- Card -->
        <div class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800" >
          <div class="p-3 mr-4 text-orange-500 bg-orange-100 rounded-full dark:text-orange-100 dark:bg-orange-500" >
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" ></path>
            </svg>
          </div>
          <div>
            <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400" >
              Total pesanan tahun ini
            </p>
            <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">
              {{ $totalOrder }}
            </p>
          </div>
        </div>
        <!-- Card -->
        <div class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800" >
          <div class="p-3 mr-4 text-green-500 bg-green-100 rounded-full dark:text-green-100 dark:bg-green-500"  >
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" ></path>
            </svg>
          </div>
          <div>
            <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400" >
              Total pemasukan tahun ini
            </p>
            <p class="text-lg font-semibold text-gray-700 dark:text-gray-200" >
              Rp {{ number_format($total_amount[0]['total_amount']) }}
            </p>
          </div>
        </div>
         <!-- Card -->
        <div class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800" >
          <div class="p-3 mr-4 text-green-500 bg-green-100 rounded-full dark:text-green-100 dark:bg-green-500"  >
            <svg class="w-5 h-5" viewBox="0 -0.5 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M16.0005 0L21.4392 9.27275L32.0005 11.5439L24.8005 19.5459L25.889 30.2222L16.0005 25.895L6.11194 30.2222L7.20049 19.5459L0.000488281 11.5439L10.5618 9.27275L16.0005 0Z" fill="currentColor"/>
            </svg>
          </div>
          <div>
            <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400" >
              Penilaian pelanggan
              {{ $avg_rating }}
            </p>
            <div class="flex flex-row gap-3 justify-center">
              <div class="{{ $avg_rating >= 1 ? 'text-yellow-300' : ''}}">
                <svg class="w-5 h-5" viewBox="0 -0.5 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M16.0005 0L21.4392 9.27275L32.0005 11.5439L24.8005 19.5459L25.889 30.2222L16.0005 25.895L6.11194 30.2222L7.20049 19.5459L0.000488281 11.5439L10.5618 9.27275L16.0005 0Z" fill="currentColor"/>
                </svg>
              </div>
              <div class="{{ $avg_rating >= 2 ? 'text-yellow-300' : ''}}">
                <svg class="w-5 h-5" viewBox="0 -0.5 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M16.0005 0L21.4392 9.27275L32.0005 11.5439L24.8005 19.5459L25.889 30.2222L16.0005 25.895L6.11194 30.2222L7.20049 19.5459L0.000488281 11.5439L10.5618 9.27275L16.0005 0Z" fill="currentColor"/>
                </svg>
              </div>
              <div class="{{ $avg_rating >= 3 ? 'text-yellow-300' : ''}}">
                <svg class="w-5 h-5" viewBox="0 -0.5 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M16.0005 0L21.4392 9.27275L32.0005 11.5439L24.8005 19.5459L25.889 30.2222L16.0005 25.895L6.11194 30.2222L7.20049 19.5459L0.000488281 11.5439L10.5618 9.27275L16.0005 0Z" fill="currentColor"/>
                </svg>
              </div>
              <div class="{{ $avg_rating >= 4 ? 'text-yellow-300' : ''}}">
                <svg class="w-5 h-5" viewBox="0 -0.5 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M16.0005 0L21.4392 9.27275L32.0005 11.5439L24.8005 19.5459L25.889 30.2222L16.0005 25.895L6.11194 30.2222L7.20049 19.5459L0.000488281 11.5439L10.5618 9.27275L16.0005 0Z" fill="currentColor"/>
                </svg>
              </div>
              <div class="{{ $avg_rating >= 5 ? 'text-yellow-300' : ''}}">
                <svg class="w-5 h-5" viewBox="0 -0.5 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M16.0005 0L21.4392 9.27275L32.0005 11.5439L24.8005 19.5459L25.889 30.2222L16.0005 25.895L6.11194 30.2222L7.20049 19.5459L0.000488281 11.5439L10.5618 9.27275L16.0005 0Z" fill="currentColor"/>
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>
      {{-- <h2 class="my-6 text-xl font-semibold text-gray-700 dark:text-gray-200" >
        Laporan Penjualan
      </h2>
      <div class="mt-5 mb-5 w-full lg:w-1/2">
        <form action="{{ route('report') }}" method="post" class="flex flex-row gap-2">
          @csrf
          <input class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input" placeholder="mulai dari..." name="minDate" type="date" required/>
          <input class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input" placeholder="mulai dari..." name="maxDate" type="date" required/>
          <button class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple w-40" type="sumbi
          ">
            Buat laporan
          </button>
        </form>
      </div> --}}
      {{-- <div class="w-full overflow-hidden rounded-lg shadow-xs mt-5">
        <div class="w-full overflow-x-auto">
          <table class="w-full whitespace-no-wrap">
            <thead>
              <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800" >
                <th class="px-4 py-3">Produk</th>
                <th class="px-4 py-3">Total Terjual</th>
                <th class="px-4 py-3">Total Harga</th>
                <th class="px-4 py-3">Tanggal Penjualan</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800" >
              @forelse ($reports as $report)
                <tr class="text-gray-700 dark:text-gray-400">
                  <td class="px-4 py-3 text-sm">
                    {{ $report->item }}
                  </td>
                  <td class="px-4 py-3 text-sm">
                    {{ $report->stock_out }}
                  </td>
                  <td class="px-4 py-3 text-sm">
                    Rp {{ number_format($report->total_amount) }}
                  </td>
                  <td class="px-4 py-3 text-sm">
                    {{ $report->created_at  }}
                  </td>
                </tr>
              @empty
                  <tr>
                    <td class="text-center font-semibold text-md px-4 py-2 w-full">
                      Laporan Tidak Tersedia
                    </td>
                  </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="px-4 py-4 text-xs font-semiboldtext-gray-500 border-t" >
          {{ $reports->links() }}
        </div>
      </div> --}}
      <h2 class="my-6 text-xl font-semibold text-gray-700 dark:text-gray-200" >
        Penilaian Pelanggan
      </h2>
      <div class="w-1/2 flex flex-col gap-3 max-h-lg overflow-y-scroll">
        @forelse ($ratings as $rating)
          <div class="bg-gray-100 px-4 py-2 w-full rounded-lg shadow-md">
            <div class="flex flex-col gap-1">
              <p class="text-sm font-semibold">{{ $rating->order->profile->name }}</p>
              <div class="flex flex-row gap-1 justify-start">
                <div class="{{ $rating->rating >= 1 ? 'text-yellow-300' : ''}}">
                  <svg class="w-3 h-3" viewBox="0 -0.5 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16.0005 0L21.4392 9.27275L32.0005 11.5439L24.8005 19.5459L25.889 30.2222L16.0005 25.895L6.11194 30.2222L7.20049 19.5459L0.000488281 11.5439L10.5618 9.27275L16.0005 0Z" fill="currentColor"/>
                  </svg>
                </div>
                <div class="{{ $rating->rating >= 2 ? 'text-yellow-300' : ''}}">
                  <svg class="w-3 h-3" viewBox="0 -0.5 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16.0005 0L21.4392 9.27275L32.0005 11.5439L24.8005 19.5459L25.889 30.2222L16.0005 25.895L6.11194 30.2222L7.20049 19.5459L0.000488281 11.5439L10.5618 9.27275L16.0005 0Z" fill="currentColor"/>
                  </svg>
                </div>
                <div class="{{ $rating->rating >= 3 ? 'text-yellow-300' : ''}}">
                  <svg class="w-3 h-3" viewBox="0 -0.5 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16.0005 0L21.4392 9.27275L32.0005 11.5439L24.8005 19.5459L25.889 30.2222L16.0005 25.895L6.11194 30.2222L7.20049 19.5459L0.000488281 11.5439L10.5618 9.27275L16.0005 0Z" fill="currentColor"/>
                  </svg>
                </div>
                <div class="{{ $rating->rating >= 4 ? 'text-yellow-300' : ''}}">
                  <svg class="w-3 h-3" viewBox="0 -0.5 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16.0005 0L21.4392 9.27275L32.0005 11.5439L24.8005 19.5459L25.889 30.2222L16.0005 25.895L6.11194 30.2222L7.20049 19.5459L0.000488281 11.5439L10.5618 9.27275L16.0005 0Z" fill="currentColor"/>
                  </svg>
                </div>
                <div class="{{ $rating->rating >= 5 ? 'text-yellow-300' : ''}}">
                  <svg class="w-3 h-3" viewBox="0 -0.5 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16.0005 0L21.4392 9.27275L32.0005 11.5439L24.8005 19.5459L25.889 30.2222L16.0005 25.895L6.11194 30.2222L7.20049 19.5459L0.000488281 11.5439L10.5618 9.27275L16.0005 0Z" fill="currentColor"/>
                  </svg>
                </div>
              </div>
              <span class="text-xs">
                @php
                  setlocale(LC_ALL, 'IND');
                  echo strftime('%d %B %Y', strtotime($rating->created_at));
                @endphp 
            </div>
            <p class="text-sm mt-3">{{ $rating->comment }}</p>
          </div>
        @empty
        <tr>
          <td class="text-center font-semibold text-md px-4 py-2 w-full">
            Belum ada penilaian
          </td>
        </tr>
        @endforelse
      </div>
    </div>
@endsection