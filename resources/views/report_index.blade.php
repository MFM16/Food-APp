@extends('template.main')

@section('content') 
    <h2 class="my-6 text-xl font-semibold text-gray-700 dark:text-gray-200" >
    Laporan Penjualan
    </h2>
    <div class="mt-5 mb-5 w-full lg:w-1/2">
    <form action="{{ route('report.generate') }}" method="post" class="flex flex-row gap-2">
        @csrf
        <input class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input" placeholder="mulai dari..." name="minDate" type="date" value="{{ isset($minDate) ? $minDate : '' }}" required/>
        <input class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input" placeholder="mulai dari..." name="maxDate" type="date" value="{{ isset($maxDate) ? $maxDate : '' }}" required/>
        <button class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple w-40" type="sumbi
        ">
        Buat laporan
        </button>
    </form>
    </div>
    @if (isset($maxDate))
    <div class="w-full overflow-x-auto mt-5" id="report-content">
        <div class="w-full mx-auto flex justify-center">
            <div class="flex flex-col gap-1 items-center text-lg font-bold">
                <h3 class="uppercase">Laporan Pencatatan</h3>
                <h3 class="uppercase">Warung Jawa Timur</h3>
                <h3>{{ date('j M Y', strtotime($minDate)) }} sampai {{ date('j M Y', strtotime($maxDate)) }}</h3>
            </div>
        </div>
        <div class="w-full overflow-hidden mt-5">
        <table @style('border:black 2px solid') class="w-full mt-4">
            <thead>
                <th @style('border:black 2px solid') class="px-1 py-3">No.</th>
                <th @style('border:black 2px solid') class="px-1 py-3">Produk</th>
                <th @style('border:black 2px solid') class="px-1 py-3">Jumlah Stok Tersedia</th>
                <th @style('border:black 2px solid') class="px-1 py-3">Jumlah Stok Masuk</th>
                <th @style('border:black 2px solid') class="px-1 py-3">Jumlah Stok Terjual</th>
                <th @style('border:black 2px solid') class="px-1 py-3">Harga Produk</th>
                <th @style('border:black 2px solid') class="px-1 py-3">Total Penjualan</th>
                </tr>
            </thead>
            <tbody @style('border:black 2px solid') class="bg-white text-center divide-y dark:divide-gray-700 dark:bg-gray-800" >
                @foreach ($products as $item)

                    @php
                        $stock_out_val = '-';
                        $total_price = '-';
                        $stock_in_update = '-';

                        for ($iterate=0; $iterate < count($stock_out) ; $iterate++) { 
                            switch ($stock_out[$iterate]['item']) {
                                case $item->name:
                                    $stock_out_val = $stock_out[$iterate]['stock_out'];
                                    $total_price = $stock_out[$iterate]['total_amount'];
                                    break;
                            }
                        }

                        for ($iteration=0; $iteration < count($stock_in) ; $iteration++) { 
                            switch ($stock_in[$iteration]['product_name']) {
                                case $item->name:
                                    $stock_in_update = $stock_in[$iteration]['stock_in'];
                                    break;
                            }
                        }
                    @endphp

                    <tr @style('border:black 2px solid')>
                        <td @style('border: 1px solid') class="py-3">{{ $loop->iteration }}</td>
                        <td @style('border:black 2px solid') class="py-3">{{ $item->name }}</td>
                        <td @style('border:black 2px solid') class="py-3">{{ $item->stock }}</td>
                        <td @style('border:black 2px solid') class="py-3">{{ $stock_in_update == '-' ? '-' : number_format($stock_in_update) }}</td>
                        <td @style('border:black 2px solid') class="py-3">{{ $stock_out_val == '-' ? '-' : number_format($stock_out_val) }}</td>
                        <td @style('border:black 2px solid') class="py-3">{{ number_format($item->price) }}</td>
                        <td @style('border:black 2px solid') class="py-3">{{ $total_price == '-' ? '-' : number_format($total_price) }}</td>
                    </tr>
                @endforeach
                <tr @style('border:black 2px solid')>
                    <td @style('border:black 2px solid') class="py-3 font-semibold" colspan="6">Total Pemasukan</td>
                    <td @style('border:black 2px solid') class="py-3 font-semibold">{{ number_format($total_amount[0]['total_amount']) }}</td>
                </tr>
            </tbody>
        </table>
        <div class="flex justify-end">
            <div class="flex flex-col mr-3 items-center">
                <p class="mt-3">Depok, {{ date('j M Y') }}</p>
                <p class="mt-20">Herni Susilowati</p>
            </div>
        </div>
    </div>
</div>
<div class="flex flex-row gap-2 mb-3">
    <form action="{{ route('report') }}" method="post" class="flex flex-row gap-2 mt-3">
        @csrf
        <input class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input" placeholder="mulai dari..." name="minDate" type="hidden" value="{{ $minDate }}"/>
        <input class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input" placeholder="mulai dari..." name="maxDate" type="hidden" value="{{ $maxDate }}" required/>
        <button class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple w-40" type="sumbi
        ">
        Download Excell
        </button>
        <button onclick="printDiv('report-content')" type="button" class="px-4 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple w-40">
            Cetak
        </button>
    </div>
    </form>
@endif

    <script>
        function printDiv(divName) {
            var css = '@page { size: landscape; }',
            head = document.head || document.getElementsByTagName('head')[0],
            style = document.createElement('style');

            style.type = 'text/css';
            style.media = 'print';

            if (style.styleSheet){
            style.styleSheet.cssText = css;
            } else {
            style.appendChild(document.createTextNode(css));
            }

            head.appendChild(style);

            var printContents = document.getElementById(divName).innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload();
        }
    </script>
@endsection

