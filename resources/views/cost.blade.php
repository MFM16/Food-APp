@extends('template.main')

@section('content')
    <div class="container px-6 mx-auto grid">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Daftar Ongkos Kirim
        </h2>
        <button @click="openModal(null, 'Tambah Ongkos Kirim', 'modal', null, null)" class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple w-40">
          Tambah Ongkir
        </button>
          <div class="w-full overflow-hidden rounded-lg shadow-xs mt-5">
            <div class="w-full overflow-x-auto">
              <table class="w-full whitespace-no-wrap">
                <thead>
                  <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800" >
                    <th class="px-4 py-3">Jarak ( meter )</th>
                    <th class="px-4 py-3">Ongkos Kirim</th>
                    <th class="px-4 py-3">Aksi</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800" >
                  @forelse ($costs as $cost)
                    <tr class="text-gray-700 dark:text-gray-400">
                      <td class="px-4 py-3 text-sm">
                        {{  $cost->distance  }}
                      </td>
                      <td class="px-4 py-3 text-sm">
                        Rp {{ number_format($cost->cost) }}
                      </td>
                      <td class="px-4 py-3">
                        <div class="flex items-center space-x-4 text-sm">
                          <button @click="openModal(null, 'Edit Ongkos Kirim', 'modal', null, null)" data-id="{{ $cost->id }}" class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 bg-green-400 text-white rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray" id="edit-cost">
                            Edit
                          </button>
                          <button class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 bg-red-600 text-white rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray" onclick="deleteItem('admin/cost/delete/{{ $cost->id }}')">
                            Hapus
                          </button>
                        </div>
                      </td>
                    </tr>
                  @empty
                      <tr>
                        <td class="text-center font-semibold text-md px-4 py-2 w-full">
                          Belum Ada Daftar Ongkos Kirim
                        </td>
                      </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
            <div class="px-4 py-4 text-xs font-semiboldtext-gray-500 border-t" >
              {{ $costs->links() }}
            </div>
          </div>
    </div>
    <div x-show="isModalOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-30 flex items-end bg-black  bg-opacity-50 sm:items-center sm:justify-center" >
      <!-- Modal -->
      <div x-show="isModalOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 transform translate-y-1/2" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0  transform translate-y-1/2" @click.away="closeModal" @keydown.escape="closeModal" class="w-full px-6 py-4 overflow-hidden bg-white rounded-t-lg dark:bg-gray-800 sm:rounded-lg sm:m-4 sm:max-w-xl" role="dialog" id="modal" >
        <!-- Remove header if you don't want a close icon. Use modal body to place modal tile. -->
        <header class="flex justify-end">
          <button class="inline-flex items-center justify-center w-6 h-6 text-gray-400 transition-colors duration-150 rounded dark:hover:text-gray-200 hover: hover:text-gray-700" aria-label="close" @click="closeModal" > <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" role="img" aria-hidden="true" > <path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" fill-rule="evenodd" ></path>
            </svg>
          </button>
        </header>
        <!-- Modal body -->
        <div class="mt-4 mb-6">
          <!-- Modal title -->
          <p class="mb-6 text-lg font-semibold text-gray-700 dark:text-gray-300" id="modal-header">
          </p>
          <!-- Modal description -->
          <form id="form">
            <input type="hidden" value="add" id="process-state">
            <input type="hidden" id="id" value="">
            <label class="block text-sm mb-3">
              <span class="text-gray-700 dark:text-gray-400">Jatak ( meter )</span>
              <input class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input" placeholder="jarak..." id="distance" type="number"/>
            </label>
            <label class="block text-sm mb-3">
              <span class="text-gray-700 dark:text-gray-400">Biaya</span>
              <input class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input" placeholder="biaya..." id="cost" type="number"/>
            </label>
          </form>
        </div>
        <footer class="flex flex-col items-center justify-end px-6 py-3 -mx-6 -mb-4 space-y-4 sm:space-y-0 sm:space-x-6 sm:flex-row bg-gray-50 dark:bg-gray-800">
          <button @click="closeModal" class="w-full px-5 py-3 text-sm font-medium leading-5 text-white text-gray-700 transition-colors duration-150 border border-gray-300 rounded-lg dark:text-gray-400 sm:px-4 sm:py-2 sm:w-auto active:bg-transparent hover:border-gray-500 focus:border-gray-500 active:text-gray-500 focus:outline-none focus:shadow-outline-gray">
            Batal
          </button>
          <button class="w-full px-5 py-3 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg sm:w-auto sm:px-4 sm:py-2 active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple" id="save-cost">
            Simpan
          </button>
        </footer>
      </div>
    </div>
@endsection