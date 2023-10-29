let baseUrl = window.location.origin;
let long
let lat
let countItems = 0
let rating = 3
let items = []

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// Form untuk data yang sudah valid
function formData(data)
{
    let validData = new FormData()
    $.each(data, (index, value) => {
        validData.append(`${index}`, value)
    })
    return validData
}

// Fungsi untuk proses pengiriman data ke controller
function post(url, data, redirect)
{
    // Pembetukan form untuk data yang sudah valid
    let validData = formData(data)

    // Proses pengiriman data ke controller
    $.ajax({
        url : `${baseUrl}/${url}`,
        type: 'POST',
        data : validData,
        cache : false,
        contentType : false,
        processData : false,
        success: (response) => {
            if(url == 'auth'){
                window.location.href = `${baseUrl}${redirect}`
            }else{
              const jsonResponse = response.meta
              const status = jsonResponse.status
              const title = jsonResponse.title
              const message = jsonResponse.message
              const data = response.data
              if (data == null) {
                Swal.fire({
                    title : title,
                    text : message,
                    icon : status
                }).then((result) => {
                    if(result.value){
                        window.location.href = `${baseUrl}${redirect}`
                    }
                })
              }
              else {
                window.open(data.url); 
              }
            }
        },
        error: (response) => {
            const jsonResponse = response.responseJSON
            const data = jsonResponse.meta
            const status = data.status
            const title = data.title
            const message = data.message
            if(url == 'admin/user' || url == 'auth/register'){
                let errorMessage = jsonResponse.data
                if(errorMessage.name){
                  Swal.fire({
                    title : title,
                    text : errorMessage.name,
                    icon : status
                  }).then((result) => {
                      if(result.value){
                          $('#register-button').removeAttr('disabled')
                      }
                  })
                  return false
                }
                if(errorMessage.email){
                  Swal.fire({
                    title : title,
                    text : errorMessage.email,
                    icon : status
                  }).then((result) => {
                      if(result.value){
                          $('#register-button').removeAttr('disabled')
                      }
                  })
                  return false
                }
                if(errorMessage.role){
                  Swal.fire({
                    title : title,
                    text : errorMessage.role,
                    icon : status
                  }).then((result) => {
                      if(result.value){
                          $('#register-button').removeAttr('disabled')
                      }
                  })
                  return false
                }
                if(errorMessage.password){
                  Swal.fire({
                    title : title,
                    text : errorMessage.password,
                    icon : status
                  }).then((result) => {
                      if(result.value){
                          $('#register-button').removeAttr('disabled')
                      }
                  })
                  return false
                }
                if(errorMessage.verifyPassword){
                  Swal.fire({
                    title : title,
                    text : errorMessage.verifyPassword,
                    icon : status
                  }).then((result) => {
                      if(result.value){
                          $('#register-button').removeAttr('disabled')
                      }
                  })
                  return false
                }
            }else{
                Swal.fire({
                    title : title,
                    text : message,
                    icon : status
                })
            }
        }
    })
}

// Fungsi untuk proses delete data
function del(url)
{
    // Konfirmasi untuk hapus data
    Swal.fire({
        title: 'Apakah Anda Yakin?',
        text: "Kamu tidak dapat mengembalikan data yang telah dihapus!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3b82f6',
        cancelButtonColor: '#f53939',
        confirmButtonText: 'Hapus'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url : `${baseUrl}/${url}`,
                type: 'DELETE',
                success: (response) => {
                    const jsonResponse = response.meta
                    const status = jsonResponse.status
                    const title = jsonResponse.title
                    const message = jsonResponse.message
                    Swal.fire({
                        title : title,
                        text : message,
                        icon : status
                    }).then((result) => {
                        if(result.value){
                            location.reload() 
                        }
                    })
                },
                error: (response) => {
                    const jsonResponse = response.responseJSON
                    const data = jsonResponse.meta
                    const status = data.status
                    const title = data.title
                    const message = data.message
                    Swal.fire({
                        title : title,
                        text : message,
                        icon : status
                    })
                }
            })
        }
    })
}

// Fungsi untuk proses pengambilan data berdasarkan data spesifik
function get(url)
{
    var data
    $.ajax({
        url : `${baseUrl}/${url}`,
        type: 'GET',
        async : false,
        success: (response) => {
            data = response.data
        },
        error: (response) => {
            const jsonResponse = response.responseJSON
            const data = jsonResponse.meta
            const status = data.status
            const title = data.title
            const message = data.message

            if(url.includes('getshipping')){
              $('#ongkir').val('')
            }

            Swal.fire({
                title : title,
                text : message,
                icon : status
            })

        }
    })
    return data
}

function containsOnlyNumbers(str) {
  return /^\d+$/.test(str);
}

// Fungsi untuk pmendapatkan ongkos kirim
function getOngkir(){
    let data
    let distance
    let globalLocation
    let regency = $('#village').val()
    let address = $('#address').val()
    let district = $('#district').val()

    // Pengecekan jika alamat belum diisi
    if (regency == '' || address == '' || district == '') {
        Swal.fire(
          'Alamat Belum Terisi',
          'Silahkan lengkapi alamat anda terlebih dahulu',
          'info'
        )
     }else {
        let cekAddress = address.replaceAll(' ', '')

        if(containsOnlyNumbers(cekAddress) == true){
          Swal.fire(
              'Alamat Tidak Valid',
              'Alamat tidak valid jika hanya berisi angka',
              'warning'
          )

          $('#ongkir').val('')
          return false
        }else{
          $.getJSON(`https://api.mapbox.com/geocoding/v5/mapbox.places/${regency}.json?limit=1&access_token=pk.eyJ1IjoiZmFyaGFubWF1bGlkaWFuMTYiLCJhIjoiY2wyZTlsNDVnMTc2djNlbGhhbHRsbXQ3OSJ9.5uZyJXUB8qrbxqnmWoSkNg`, function (response) { 
            globalLocation = response.features
            if(globalLocation.length == 0){
              Swal.fire(
                'Kelurahan Tidak Ditemukan',
                'Silahkan periksa kembali data yang anda masukan!',
                'warning'
              )

              $('#ongkir').val('')

            }else{
              $.getJSON(`https://api.mapbox.com/geocoding/v5/mapbox.places/${district}.json?limit=1&access_token=pk.eyJ1IjoiZmFyaGFubWF1bGlkaWFuMTYiLCJhIjoiY2wyZTlsNDVnMTc2djNlbGhhbHRsbXQ3OSJ9.5uZyJXUB8qrbxqnmWoSkNg`, function (response) { 
                let found = response.features
                if(found.length == 0){
                  Swal.fire(
                    'Kecamatan Tidak Ditemukan',
                    'Silahkan periksa kembali data yang anda masukan!',
                    'warning'
                  )

                  $('#ongkir').val('')

                }else{
                    globalLocation.map( function(feature){ 
                    long = feature.center[0]
                    lat = feature.center[1]
        
                    $.getJSON(`https://api.mapbox.com/directions/v5/mapbox/driving/106.7922485%2C-6.4472989%3B${long}%2C${lat}?alternatives=true&geometries=geojson&language=en&overview=simplified&steps=true&access_token=pk.eyJ1IjoiZmFyaGFubWF1bGlkaWFuMTYiLCJhIjoiY2wyZTlsNDVnMTc2djNlbGhhbHRsbXQ3OSJ9.5uZyJXUB8qrbxqnmWoSkNg`, function (response) { 
                        data = (response.routes)
                        data.map(function (route) { 
                            distance = Math.round(route.distance)
                        })
        
                        res = get(`admin/cost/getshipping/cost/${distance}`)
                        $('#ongkir').val(res.cost.toLocaleString())
                    })
                  })
                }
              })
            }
          })
        }
     }

}

// Fungsi untuk mendapatkan total harga
function totalPrice()
{
    let totalPrice = 0

    for (let index = 1; index <= countItems; index++) {
        if ($(`#totalPriceItem${index}`).val() != undefined) {
            let text = $(`#totalPriceItem${index}`).val()
            let price = Number(text.replace(',',''))
            let tempPrice = totalPrice + price
            totalPrice = tempPrice
        }
    }
    
    return totalPrice
}

// Fungsi untuk checkout
function checkout()
{
    var items = []
    let item

    for (let index = 1; index <= countItems; index++) {
        if ($(`#totalPriceItem${index}`).val() != undefined) {
          if($(`#totalItem${index}`).val() !=  0 ){
            item = ` ${$(`#itemName${index}`).val()} ${$(`#totalItem${index}`).val()} buah`
            items.push(item)
          }
        }
    }

    return items
}

function onlyNumberKey(evt) { 
  // Only ASCII character in that range allowed
  var ASCIICode = (evt.which) ? evt.which : evt.keyCode
  if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
      return false;
  return true;
}

function onlyAlphaKey(evt) { 
  // Only ASCII character in that range allowed
  var ASCIICode = (evt.which) ? evt.which : evt.keyCode
  if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
      return true;
  return false;
}

function totalPricePerItem(id)
{
    var totalItem = $(`#totalItem${id}`).val()
    var price = $(`#priceItem${id}`).val()

    var totalPrice = totalItem * price

    $(`#totalPriceItem${id}`).val(totalPrice.toLocaleString())
}

function regexInput(inputId, maxInput, price) {
  const val = Number($(`#${inputId}`).val())

  if(val > maxInput){
    Swal.fire({
      title: 'Pesanan Melebihi Stok yang Tersedia',
      text: "Silahkan kurangi jumlah pesanan anda!",
      icon: 'warning',
      showCancelButton: false,
      confirmButtonColor: '#3b82f6',
      confirmButtonText: 'Tutup'
    }).then((result) => {
        if (result.isConfirmed) {
          let itemNumber = String(inputId.split('totalItem'))
          $(`#totalPriceItem${itemNumber[1]}`).val(price.toLocaleString())
          $(`#${inputId}`).val(1)
        }
    })
  }
}

// Fungsi untuk menghitung jumlah harga peritem jika membeli sebuah item lebih dari 1

function setValue(data)
{
    $.each(data, function (key, value) {
        if (key != 'photo') {
            $(`#${key}`).val(value)
        }
    })
}

// Fungsi untuk menghapus item yang akan dibeli
function removeItem(itemId)
{
    $(`#${itemId}`).remove()
}

// Fungsi untuk memberika penilaian
function rate(rate)
{
    rating = rate
    switch (rate) {
        case 1:
            $('#rate-icon').text('😭')
            break;
        case 2:
            $('#rate-icon').text('😥')
            break;
        case 3:
            $('#rate-icon').text('🙂')
            break;
        case 4:
            $('#rate-icon').text('😋')
            break;
        case 5:
            $('#rate-icon').text('😍')
            break;
        default:
            $('#rate-icon').text('🙂')
            break;
    }

    for (let index = 1; index <= 5; index++) {
        $(`#star${index}`).removeClass('text-yellow-300')
    }

    for (let index = 1; index <= rate; index++) { 
        $(`#star${index}`).addClass('text-yellow-300')
    }
}

function deleteItem (url)
{
    del(url)
}

// Proses registrasi
$('#register-button').on('click', () => {
    data = {
        name : $('#name').val(),
        email : $('#email').val(),
        password : $('#password').val(),
        verifyPassword : $('#verifyPassword').val()
    }
    post('auth/register', data, '/auth')
})

// Proses login
$('#login_button').on('click', () => {
    data = {
        email : $('#email').val(),
        password : $('#password').val()
    }
    post('auth', data, '/admin/dashboard')
})

// Proses edit product
$(document).delegate('#edit-product', 'click', function() {
    var id = $(this).data('id')
    $('#process-state').val('edit')
    var data = get(`admin/product/show/${id}`)

    setValue(data)
})

// Proses delete product
$(document).delegate('#delete-product', 'click', function() {
    var id = $(this).data('id')
    del(`admin/product/delete/${id}`)
})

// Proses menambahkan product
$('#save-product').on('click', () => {
    var process = $('#process-state').val()
    data = {
        name : $('#name').val(),
        stock : $('#stock').val(),
        price : $('#price').val(),
    }
    $('#photo').prop('files')[0] ? data.photo = $('#photo').prop('files')[0] : ''

    if(process == 'edit'){
        data._method = 'PUT'
        data.id = $('#id').val()
    }

    post('admin/product', data, '/admin/product')
})

// Proses menambahkan product yang akan dibeli
$(document).delegate('#add-item', 'click', function () {
    var id = $(this).data('id')
    var res = get(`admin/product/show/${id}`)

    // pengecekan apakah stock masih ada ?
    if (res.stock != 0) {

        if(items.includes(res.name) == true){
          Swal.fire(
            'Pesanan yang Sudah Dipilih Tidak Bisa Dipilih Kembali',
            'Silahkan pilih pesanan yang lain',
            'error'
          )
        }else{
          countItems = countItems + 1
          items.push(res.name)
          var order = `<div id="items${countItems}" class="flex flex-row items-center">
                      <div class="flex flex-col md:flex-row w-full p-4 bg-white rounded-lg shadow-xs justify-between mb-1 items-center">
                          <div class="flex flex-col md:flex-row  justify-between">
                              <div class="flex flex-col mb-2 md:mb-0">
                                  <label class="md:mb-2 text-xs font-medium text-gray-400 dark:text-gray-400">Nama Makanan</label>
                                  <input type="text" id="itemName${countItems}" value="${res.name}" disabled>
                              </div>
                              <div class="flex flex-col mb-2 md:mb-0">
                                  <label class="md:mb-2 text-xs font-medium text-gray-400 dark:text-gray-400">Harga Perporsi</label>
                                  <input type="text" id="priceItem${countItems}" value="${res.price}" disabled>
                              </div>
                              <div class="flex flex-col mb-2 md:mb-0">
                                  <label class="md:mb-2 text-xs font-medium text-gray-400 dark:text-gray-400">Jumlah Pesanan</label>
                                  <input class="focus:outline-none" id="totalItem${countItems}" type="number" min="1" max="${res.stock}" value="1" onchange="totalPricePerItem(${countItems})" onkeyup="regexInput('totalItem${countItems}' , ${res.stock}, ${res.price})" onkeypress="return onlyNumberKey(event)">
                              </div>
                          </div>
                          <div>
                              <div class="flex flex-col mb-2 md:mb-0">
                                  <label class="md:mb-2 text-xs font-medium text-gray-400 dark:text-gray-400">Total Harga</label>
                                  <input type="text" id="totalPriceItem${countItems}" value="${res.price.toLocaleString()}" onchange="totalPrice()" disabled>
                              </div>
                          </div>
                      </div>
                      <button class="ml-2 px-4 py-2 bg-white shadow-sm rounded-lg hover:bg-gray-200" onclick="removeItem('items${countItems}')">
                      ❌
                      </button>
                  </div>`
      
          $('#items-content').append(order)
          console.log(items)
        }
    }
})

// Proses checkout
$('#order-button').on('click', function () {
    var tempOngkir = $('#ongkir').val()
    var ongkir = tempOngkir == undefined ? '' : Number(tempOngkir.replace(',',''))
    var totalPriceItems  = totalPrice()
    var totalAmount = $('#role-id').val() == 0 ? totalPriceItems + ongkir : totalPriceItems
    var total = $('#role-id').val() == 0 ? totalAmount.toLocaleString("en-US") : totalPriceItems.toLocaleString("en-US")

    // Pengecekan siapa yang memesan ?
    if ($('#role-id').val() == 0) {
        // Pengecekan apakan sudah ada item yang dipesan
        if (totalPriceItems < 10000) {
            Swal.fire(
                'Anda Belum Memesan',
                'Silahkan memesan makanan terlebih dahulu!',
                'info'
            )
        } else {
            // Pengecekan apakah ongkos kirim sudah terisi
            if ($('#ongkir').val() == '') {
                Swal.fire(
                    'Ongkos Kirim Kosong',
                    'Silahkan mengisikan alamat dan klik tombol Cek Ongkir',
                    'info'
                )
            } else {
                // Konfirmasi pemesanan

                var string = $('#phone_number').val()
                var phone = string.replace(/(\d{3})(\d{4})(\d{4})/, '$1 $2 $3')

                if(phone[0] != '0' || phone[1] != '8'){
                  Swal.fire(
                    'Nomor Telepon Tidak Valid',
                    'Silahkan isi kembali nomor telepon yang valid!',
                    'info'
                  )
                  var string = $('#phone_number').val('')
                  return false

                }else{
                  if(phone[9] == undefined || phone[15] != undefined){
                    Swal.fire(
                    'Nomor Telepon Tidak Valid',
                    'Silahkan isi kembali nomor telepon yang valid!',
                    'info'
                    )
                    var string = $('#phone_number').val('')
                    return false
                  }else{
                    Swal.fire({
                    title: 'Yakin Ingin Memesan ?',
                    text: `Total pesanan anda ditambah dengan ongkir adalah RP ${total}!`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3b82f6',
                    cancelButtonColor: '#f53939',
                    confirmButtonText: 'Pesan'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            var data = {
                                role_id : $('#role-id').val(),
                                profile_id: $('#profile_id').val(),
                                item: checkout(),
                                address: $('#address').val(),
                                village: $('#village').val(),
                                district: $('#district').val(),
                                full_address: `${$('#address').val()}, ${$('#village').val()}, ${$('#district').val()}`,
                                total_amount: totalAmount,
                                message: $('#message').val(),
                                status: 0,
                                phone_number: $('#phone_number').val(),
                            }
        
                            post('admin/order', data, '/admin/dashboard')
                        }
                    })
                  }
                }
            }
        }
    } else {
        if (totalPriceItems == 0) {
            Swal.fire(
                'Anda Belum Memesan',
                'Silahkan memesan makanan terlebih dahulu!',
                'info'
            )
        } else {
            Swal.fire({
                title: 'Yakin Ingin Memesan ?',
                text: `Total pesanan anda ditambah dengan ongkir adalah RP ${total}!`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3b82f6',
                cancelButtonColor: '#f53939',
                confirmButtonText: 'Pesan'
            }).then((result) => {
                if (result.isConfirmed) {
                    var data = {
                        role_id : $('#role-id').val(),
                        profile_id: $('#profile_id').val(),
                        item: checkout(),
                        total_amount: totalAmount,
                        message: $('#message').val(),
                        status: 1,
                    }
    
                    post('admin/order', data, '/admin/dashboard')
                }
            })
        }
    }


})

// Proses penilaian
$(document).delegate('#rating-button', 'click', function () {
    var id = $(this).data('id')
    $('#order_id').val(id)
})

// Proses pengiriman penilaian
$('#save-rating').on('click', function () {
    var data = {
        order_id: $('#order_id').val(),
        rating: rating,
        comment: $('#comment').val(),
    }

    post('admin/rating', data, '/admin/dashboard')
})

$(document).delegate('#save-confirm', 'click', function () {
    var data = {
        order_id : $('#confirm_id').val(),
        status: $('#status').val(),
        process: $('#process').val()
    }

    $('#status').val() == 1 ? data.items = $('#order-item').val() : ''

    post('admin/update/status', data, '/admin/dashboard')
})

// Proses pembayaran melalui midtrans
$(document).delegate('#midtrans-pay', 'click', function () {
    var data = {
      order_id: $('#confirm_id').val(),
      amount : $('#total-amount').val(),
      items : $('#order-item').val()
  }

    post('admin/midtrans-pay', data, '/admin/dashboard')
})

// Proses pengambilan detail pesanan
$(document).delegate('#detail-button', 'click', function () {
    let id = $(this).data('id')
    let data = get(`admin/order/show/${id}`)

    $('#product-content').text(data.item)
    $('#product-message').text(data.message)
})

// Proses untuk memunculkan konfirmasi pembayaran pembeli
$(document).delegate('#confirm-button', 'click', function () {
    $('#progress-order').remove()
    $('#save-confirm').remove()
    $('#midtrans-pay').remove()
  
  var role = $('#role').val()
  var text

  role == 'admin' ? text = "Proses Selanjutnya" : text ="Bayar Sekarang"
  role == 'admin' ? buttonId = "save-confirm" : buttonId ="midtrans-pay"
    
    var confirm = `
        <div id="progress-order">
          <form id="form" class="mt-6">
            <input type="hidden" id="confirm_id" value="">
            <input type="hidden" id="status" value="">
            <input type="hidden" id="process" value="confirm">
            <input type="hidden" id="order-item" value="">
            <input type="hidden" id="total-amount" value="">
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
    `
    var button = `
    <button class="w-full px-5 py-3 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg sm:w-auto sm:px-4 sm:py-2 active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple" id="${buttonId}">
        ${text}
    </button>
    `
    $('#content-order').append(confirm)
    $('#footer-order').append(button)

    let id = $(this).data('id')
    let status = $(this).data('val')

    if (status == 1) {
        let data = get(`admin/order/show/${id}`)
        let totalAmount = data.total_amount

        $('#price-text').text(`Rp ${totalAmount.toLocaleString("en-US")}`)
        $('#order-item').val(data.item)
        $('#confirm_id').val(data.id)
        $('#status').val(status)
        $('#total-amount').val(totalAmount)
    } else {
        $('#confirm_id').val(id)
        $('#status').val(status)
    }
})

// Proses edit ongkos kirim
$(document).delegate('#edit-cost', 'click', function () {
    let id = $(this).data('id')
    $('#process-state').val('edit')

    let data = get(`admin/cost/show/${id}`)
    setValue(data)
})

// Proses simpan ongkos kirim
$('#save-cost').on('click', () => {
    var process = $('#process-state').val()
    data = {
        distance : $('#distance').val(),
        cost : $('#cost').val(),
    }

    if(process == 'edit'){
        data._method = 'PUT'
        data.id = $('#id').val()
    }

    post('admin/cost', data, '/admin/cost')
})

// Proses export laporan
$('#report-button').on('click', () => { 
    var minDate = $('#minDate').val()
    var maxDate = $('#maxDate').val()

    get(`admin/report/export/${minDate}/${maxDate}`)
})

// Proses edit user
$(document).delegate('#edit-user', 'click', function () {
    let id = $(this).data('id')
    $('#process-state').val('edit')

    let data = get(`admin/user/show/${id}`)

    setValue(data)
    $('#name').val(data.profile.name)
})

// Proses simpan user
$('#save-user').on('click', () => {
    var process = $('#process-state').val()
    data = {
        name : $('#name').val(),
        email: $('#email').val(),
        role: $('#role').val(),
    }

    if(process == 'edit'){
        data._method = 'PUT'
        data.id = $('#id').val()
    }

    post('admin/user', data, '/admin/user')
})

// Proses hapus user
$(document).delegate('#delete-user', 'click', function() {
    var id = $(this).data('id')
    del(`admin/user/delete/${id}`)
})

// Proses pelacakan pengiriman
$(document).delegate('#progress-button', 'click', function () {
    var id = $(this).data('id')

    $('#progress-order').remove()
    $('#save-confirm').remove()

    let data = get(`admin/order/show/${id}`)

    var check = [];

    for (var i = 0; i < 6; i++) { 
        if (data.status >= i) {
            check[i] = 
            `
            <span>
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4.91988 12.257C4.2856 12.257 3.65131 12.5199 3.19988 13.0342C2.79417 13.4913 2.59417 14.0799 2.63417 14.6913C2.67417 15.3027 2.94846 15.857 3.4056 16.2627L7.51417 19.8684C7.93131 20.2342 8.46846 20.4399 9.02274 20.4399C9.0856 20.4399 9.14846 20.4399 9.21131 20.4342C9.82846 20.3827 10.4056 20.0799 10.7942 19.5999L20.857 7.27986C21.657 6.30272 21.5085 4.85701 20.5313 4.05701C20.057 3.67415 19.4627 3.49129 18.857 3.55415C18.2513 3.61701 17.7027 3.90844 17.3142 4.38272L8.74846 14.8627L6.42274 12.8227C5.99417 12.4456 5.45131 12.257 4.91988 12.257Z" fill="url(#paint0_linear)"/>
                <path d="M9.02279 20.0284C8.56565 20.0284 8.12565 19.8627 7.78279 19.5598L3.67422 15.9541C2.89708 15.2684 2.81708 14.0798 3.50279 13.3027C4.18851 12.5255 5.37708 12.4455 6.15422 13.1313L8.79994 15.4513L17.6285 4.63983C18.2856 3.83412 19.4685 3.71983 20.2742 4.37126C21.0799 5.0284 21.1942 6.21126 20.5428 7.01697L10.4742 19.337C10.1542 19.7313 9.67993 19.977 9.17708 20.0227C9.12565 20.0227 9.07422 20.0284 9.02279 20.0284Z" fill="url(#paint1_linear)"/>
                <path opacity="0.75" d="M9.02279 20.0284C8.56565 20.0284 8.12565 19.8627 7.78279 19.5598L3.67422 15.9541C2.89708 15.2684 2.81708 14.0798 3.50279 13.3027C4.18851 12.5255 5.37708 12.4455 6.15422 13.1313L8.79994 15.4513L17.6285 4.63983C18.2856 3.83412 19.4685 3.71983 20.2742 4.37126C21.0799 5.0284 21.1942 6.21126 20.5428 7.01697L10.4742 19.337C10.1542 19.7313 9.67993 19.977 9.17708 20.0227C9.12565 20.0227 9.07422 20.0284 9.02279 20.0284Z" fill="url(#paint2_radial)"/>
                <path opacity="0.5" d="M9.02279 20.0284C8.56565 20.0284 8.12565 19.8627 7.78279 19.5598L3.67422 15.9541C2.89708 15.2684 2.81708 14.0798 3.50279 13.3027C4.18851 12.5255 5.37708 12.4455 6.15422 13.1313L8.79994 15.4513L17.6285 4.63983C18.2856 3.83412 19.4685 3.71983 20.2742 4.37126C21.0799 5.0284 21.1942 6.21126 20.5428 7.01697L10.4742 19.337C10.1542 19.7313 9.67993 19.977 9.17708 20.0227C9.12565 20.0227 9.07422 20.0284 9.02279 20.0284Z" fill="url(#paint3_radial)"/>
                <defs>
                <linearGradient id="paint0_linear" x1="15.825" y1="-13.9667" x2="9.82533" y2="23.9171" gradientUnits="userSpaceOnUse">
                <stop stop-color="#00CC00"/>
                <stop offset="0.1878" stop-color="#06C102"/>
                <stop offset="0.5185" stop-color="#17A306"/>
                <stop offset="0.9507" stop-color="#33740C"/>
                <stop offset="1" stop-color="#366E0D"/>
                </linearGradient>
                <linearGradient id="paint1_linear" x1="15.2501" y1="0.625426" x2="7.43443" y2="23.6215" gradientUnits="userSpaceOnUse">
                <stop offset="0.2544" stop-color="#90D856"/>
                <stop offset="0.736" stop-color="#00CC00"/>
                <stop offset="0.7716" stop-color="#0BCD07"/>
                <stop offset="0.8342" stop-color="#29CF18"/>
                <stop offset="0.9166" stop-color="#59D335"/>
                <stop offset="1" stop-color="#90D856"/>
                </linearGradient>
                <radialGradient id="paint2_radial" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(15.452 8.95803) rotate(116.129) scale(8.35776 4.28316)">
                <stop stop-color="#FBE07A" stop-opacity="0.75"/>
                <stop offset="0.0803394" stop-color="#FBE387" stop-opacity="0.6897"/>
                <stop offset="0.5173" stop-color="#FDF2C7" stop-opacity="0.362"/>
                <stop offset="0.8357" stop-color="#FFFBF0" stop-opacity="0.1233"/>
                <stop offset="1" stop-color="white" stop-opacity="0"/>
                </radialGradient>
                <radialGradient id="paint3_radial" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(11.6442 17.0245) rotate(155.316) scale(9.80163 4.14906)">
                <stop stop-color="#440063" stop-opacity="0.25"/>
                <stop offset="1" stop-color="#420061" stop-opacity="0"/>
                </radialGradient>
                </defs>
                </svg>
            </span>`    
        } else {
            check[i] = ''
        }
    }

    var progress = `
    <div id="progress-order">
          <div class="flex flex-col gap-2">
              <div class="flex gap-2 items-center">
                <svg class="w-8 h-8" viewBox="0 0 1024 1024" class="icon"  version="1.1" xmlns="http://www.w3.org/2000/svg"><path d="M829.314844 672.733867H190.145422c-56.979911 0-103.173689 46.409956-103.173689 103.662933v60.359111h845.5168v-60.359111c0-57.252978-46.205156-103.662933-103.173689-103.662933z" fill="#FFCA6C" /><path d="M386.321067 672.733867H190.145422c-56.979911 0-103.173689 46.409956-103.173689 103.662933v60.359111h196.175645v-60.359111c0-57.252978 46.193778-103.662933 103.173689-103.662933z" fill="#E5B05C" /><path d="M855.4496 672.733867c0-191.852089-154.783289-347.386311-345.725156-347.386311-190.930489 0-345.713778 155.522844-345.713777 347.386311" fill="#FFCA6C" /><path d="M566.977422 330.126222a346.612622 346.612622 0 0 0-57.252978-4.767289c-190.930489 0-345.713778 155.522844-345.713777 347.386311h114.494577c0-172.270933 124.780089-315.209956 288.472178-342.619022z" fill="#E5B05C" /><path d="M447.294578 211.273956a62.714311 62.429867 90 1 0 124.859733 0 62.714311 62.429867 90 1 0-124.859733 0Z" fill="#FFCA6C" /><path d="M496.344178 211.285333A62.725689 62.725689 0 0 1 534.243556 153.6 61.872356 61.872356 0 0 0 509.724444 148.571022c-34.474667 0-62.418489 28.080356-62.418488 62.725689 0 34.633956 27.9552 62.714311 62.418488 62.714311 8.704 0 16.987022-1.797689 24.519112-5.028978a62.7712 62.7712 0 0 1-37.899378-57.696711z" fill="#E5B05C" /><path d="M783.792356 588.561067c-5.814044 0-11.241244-3.618133-13.323378-9.432178-32.028444-89.827556-109.454222-158.139733-202.046578-178.301156-7.645867-1.661156-12.4928-9.238756-10.831644-16.907377s9.193244-12.549689 16.827733-10.888534a304.856178 304.856178 0 0 1 136.965689 69.7344 308.349156 308.349156 0 0 1 85.731555 126.7712 14.244978 14.244978 0 0 1-13.323377 19.023645zM868.898133 745.483378a14.108444 14.108444 0 0 1-11.286755-5.643378c-2.787556-3.709156-9.5232-6.269156-11.650845-6.781156a14.222222 14.222222 0 0 1 6.610489-27.659377c1.820444 0.443733 18.1248 4.664889 27.613867 17.282844a14.244978 14.244978 0 0 1-11.286756 22.801067z" fill="#FFFFFF" /><path d="M869.512533 665.645511c-3.6864-191.3856-156.080356-346.794667-345.645511-354.2016v-24.564622a76.936533 76.936533 0 0 0 62.418489-75.593956 77.368889 77.368889 0 0 0-9.511822-37.182577 14.119822 14.119822 0 0 0-19.228445-5.563734 14.256356 14.256356 0 0 0-5.5296 19.319467c3.925333 7.122489 5.984711 15.212089 5.984712 23.426844 0 26.749156-21.651911 48.503467-48.275912 48.503467-26.624 0-48.275911-21.754311-48.275911-48.503467s21.651911-48.503467 48.264534-48.503466c6.587733 0 12.970667 1.308444 18.966755 3.879822a14.119822 14.119822 0 0 0 18.568534-7.486578 14.222222 14.222222 0 0 0-7.441067-18.659555 75.776 75.776 0 0 0-30.094222-6.166756c-42.211556 0-76.561067 34.5088-76.561067 76.936533a76.970667 76.970667 0 0 0 62.418489 75.605334v24.553244a354.804622 354.804622 0 0 0-135.213511 32.221867 14.2336 14.2336 0 0 0-6.985956 18.830222 14.108444 14.108444 0 0 0 18.7392 7.031467A327.554844 327.554844 0 0 1 509.724444 339.569778c178.2784 0 324.130133 142.119822 331.275378 319.533511a116.963556 116.963556 0 0 0-11.684978-0.591645H190.145422c-3.948089 0-7.839289 0.2048-11.684978 0.591645a332.629333 332.629333 0 0 1 150.232178-265.534578 14.244978 14.244978 0 0 0 4.107378-19.672178 14.108444 14.108444 0 0 0-19.581156-4.130133c-100.317867 65.809067-160.950044 176.014222-163.248355 295.879111A118.044444 118.044444 0 0 0 72.817778 776.3968v60.359111c0 7.839289 6.337422 14.210844 14.153955 14.210845h845.5168c7.805156 0 14.142578-6.371556 14.142578-14.210845v-60.359111c0-50.824533-32.176356-94.230756-77.118578-110.751289zM101.114311 822.533689v-46.136889c0-49.334044 39.936-89.452089 89.031111-89.452089h639.158045c44.282311 0 81.1008 32.642844 87.904711 75.241245H805.888c-7.816533 0-14.142578 6.371556-14.142578 14.210844 0 7.850667 6.337422 14.222222 14.142578 14.222222h112.457956v31.914667H101.114311z" fill="" /><path d="M756.8384 762.185956H328.635733c-7.816533 0-14.153956 6.371556-14.153955 14.210844 0 7.850667 6.337422 14.222222 14.153955 14.222222h428.191289c7.816533 0 14.142578-6.371556 14.142578-14.222222a14.165333 14.165333 0 0 0-14.1312-14.210844zM283.147378 762.185956H162.633956c-7.816533 0-14.153956 6.371556-14.153956 14.210844 0 7.850667 6.337422 14.222222 14.153956 14.222222h120.502044c7.816533 0 14.153956-6.371556 14.153956-14.222222 0-7.839289-6.326044-14.210844-14.142578-14.210844z" fill=""/></svg>
                <div class="w-auto flex items-center gap-2">
                  <h6 class="mb-0 font-semibold leading-normal text-sm text-slate-700">Pesanan Diterima</h6>
                  ${check[0]}
                </div>
              </div>
              <div class="ml-3 w-1 h-6 bg-gray-500"></div>
              <div class="flex gap-2 items-center">
                <svg class="w-8 h-8" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" 
                  viewBox="0 0 508 508" xml:space="preserve">
                <path style="fill:#F7BB83;" d="M496,240.5c0,8.8-7.2,16-16,16H144c-8.8,0-16-7.2-16-16V32.7c0-8.8,7.2-16,16-16h336
                  c8.8,0,16,7.2,16,16V240.5z"/>
                <g>
                  <path style="fill:#6E4123;" d="M480,268.5H144c-15.4,0-28-12.6-28-28V32.7c0-15.4,12.6-28,28-28h336c15.4,0,28,12.6,28,28v207.8
                    C508,255.9,495.4,268.5,480,268.5z M144,28.7c-2.2,0-4,1.8-4,4v207.8c0,2.2,1.8,4,4,4h336c2.2,0,4-1.8,4-4V32.7c0-2.2-1.8-4-4-4
                    H144z"/>
                  <rect x="128" y="52.7" style="fill:#6E4123;" width="372" height="48"/>
                </g>
                <rect x="260" y="120.7" style="fill:#F6E89A;" width="204" height="40"/>
                <path style="fill:#6E4123;" d="M472.5,169.2h-221v-57h221V169.2z M268.5,152.2h187v-23h-187V152.2z"/>
                <polygon style="fill:#B2EDA6;" points="416,188.7 416,388.2 12,388.7 12,188.7 "/>
                <path style="fill:#3C663E;" d="M12,391.7c-0.8,0-1.6-0.3-2.1-0.9c-0.6-0.6-0.9-1.3-0.9-2.1v-200c0-1.7,1.3-3,3-3h404
                  c1.7,0,3,1.3,3,3v199.5c0,1.7-1.3,3-3,3L12,391.7L12,391.7z M15,191.7v194l398-0.5V191.7H15z"/>
                <ellipse style="opacity:0.5;fill:#B8CBCD;enable-background:new    ;" cx="266.7" cy="456" rx="190.7" ry="47.3"/>
                <polygon style="fill:#B2EDA6;" points="464,240.7 464,440.2 64,440.7 64,240.7 "/>
                <path style="fill:#3C663E;" d="M64,449.2c-2.3,0-4.4-0.9-6-2.5s-2.5-3.8-2.5-6v-200c0-4.7,3.8-8.5,8.5-8.5h400
                  c4.7,0,8.5,3.8,8.5,8.5v199.5c0,4.7-3.8,8.5-8.5,8.5L64,449.2L64,449.2z M72.5,249.2v183l383-0.5V249.2H72.5z"/>
                <circle style="fill:#84C671;" cx="250.2" cy="338.4" r="57.7"/>
                <path style="fill:#3C663E;" d="M60,448.7c-3.2,0-6.2-1.3-8.5-3.5c-2.3-2.3-3.5-5.3-3.5-8.5v-36H12c-6.6,0-12-5.4-12-12v-200
                  c0-6.6,5.4-12,12-12h404c6.6,0,12,5.4,12,12v36h36c6.6,0,12,5.4,12,12v199.5c0,6.6-5.4,12-12,12L60,448.7L60,448.7z M24,376.7h36
                  c6.6,0,12,5.4,12,12v36l380-0.5V248.7h-36c-6.6,0-12-5.4-12-12v-36H24V376.7z"/>
                </svg>
                <div class="w-auto flex items-center gap-2">
                  <h6 class="mb-0 font-semibold leading-normal text-sm text-slate-700">Konfirmasi Pembayaran</h6>
                  ${check[1]}
                </div>
              </div>
              <div class="ml-3 w-1 h-6 bg-gray-500"></div>
              <div class="flex gap-2 items-center">
                <svg class="w-8 h-8" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" 
                  viewBox="0 0 506.4 506.4" xml:space="preserve">
                <circle style="fill:#54B265;" cx="253.2" cy="253.2" r="249.2"/>
                <path style="fill:#F4EFEF;" d="M372.8,200.4l-11.2-11.2c-4.4-4.4-12-4.4-16.4,0L232,302.4l-69.6-69.6c-4.4-4.4-12-4.4-16.4,0
                  L134.4,244c-4.4,4.4-4.4,12,0,16.4l89.2,89.2c4.4,4.4,12,4.4,16.4,0l0,0l0,0l10.4-10.4l0.8-0.8l121.6-121.6
                  C377.2,212.4,377.2,205.2,372.8,200.4z"/>
                <path d="M253.2,506.4C113.6,506.4,0,392.8,0,253.2S113.6,0,253.2,0s253.2,113.6,253.2,253.2S392.8,506.4,253.2,506.4z M253.2,8
                  C118,8,8,118,8,253.2s110,245.2,245.2,245.2s245.2-110,245.2-245.2S388.4,8,253.2,8z"/>
                <path d="M231.6,357.2c-4,0-8-1.6-11.2-4.4l-89.2-89.2c-6-6-6-16,0-22l11.6-11.6c6-6,16.4-6,22,0l66.8,66.8L342,186.4
                  c2.8-2.8,6.8-4.4,11.2-4.4c4,0,8,1.6,11.2,4.4l11.2,11.2l0,0c6,6,6,16,0,22L242.8,352.4C239.6,355.6,235.6,357.2,231.6,357.2z
                  M154,233.6c-2,0-4,0.8-5.6,2.4l-11.6,11.6c-2.8,2.8-2.8,8,0,10.8l89.2,89.2c2.8,2.8,8,2.8,10.8,0l132.8-132.8c2.8-2.8,2.8-8,0-10.8
                  l-11.2-11.2c-2.8-2.8-8-2.8-10.8,0L234.4,306c-1.6,1.6-4,1.6-5.6,0l-69.6-69.6C158,234.4,156,233.6,154,233.6z"/>
                </svg>
                <div class="w-auto flex items-center gap-2">
                  <h6 class="mb-0 font-semibold leading-normal text-sm text-slate-700">Pembayaran Terkonfirmasi</h6>
                  ${check[2]}
                </div>
              </div>
              <div class="ml-3 w-1 h-6 bg-gray-500"></div>
              <div class="flex gap-2 items-center">
                <svg class="w-8 h-8" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" 
                  viewBox="0 0 512 512" xml:space="preserve">
                <path style="fill:#5B7A7F;" d="M512,306.087v11.13c0,9.22-7.475,16.696-16.696,16.696H16.696C7.475,333.913,0,326.438,0,317.217
                  v-11.13c0-9.22,7.475-16.696,16.696-16.696h478.609C504.525,289.391,512,296.866,512,306.087z M201.045,152.33l110.881-9.7
                  l-2.911-33.264c-1.072-12.247-11.868-21.307-24.116-20.236l-66.529,5.82c-12.248,1.072-21.307,11.868-20.236,24.116L201.045,152.33z
                  "/>
                <path style="fill:#769CA5;" d="M442.77,280.476l-11.795,117.949c-4.552,45.519-42.855,80.184-88.602,80.184H169.627
                  c-45.746,0-84.049-34.665-88.602-80.184L69.23,280.476C67.92,267.371,78.21,256,91.381,256h329.238
                  C433.79,256,444.08,267.371,442.77,280.476z"/>
                <path style="fill:#90B5BF;" d="M442.77,280.476l-11.795,117.949c-4.552,45.519-42.855,80.184-88.602,80.184h-70.238
                  c-45.746,0-84.049-34.665-88.602-80.184l-11.795-117.949c-1.31-13.105,8.98-24.476,22.151-24.476h226.729
                  C433.79,256,444.08,267.371,442.77,280.476z M457.525,146.651c-1.339-15.31-14.836-26.635-30.146-25.295L83.65,151.428
                  c-15.31,1.34-26.634,14.836-25.295,30.146l0,0c1.339,15.309,14.836,26.634,30.145,25.295l343.731-30.072
                  C447.54,175.457,458.864,161.96,457.525,146.651z"/>
                <path style="fill:#769CA5;" d="M457.525,146.651c1.339,15.31-9.985,28.806-25.295,30.146L88.5,206.869
                  c-15.31,1.339-28.806-9.985-30.146-25.295l0,0c-0.536-6.136,0.965-11.979,3.938-16.867c5.328,8.755,15.305,14.226,26.208,13.272
                  l343.73-30.072c9.174-0.802,16.912-5.973,21.357-13.279C455.742,138.169,457.14,142.242,457.525,146.651z"/>
                <path style="fill:#FFA233;" d="M367.304,244.87c6.147,0,11.13,4.983,11.13,11.13v89.043c0,12.564-10.408,22.697-23.072,22.246
                  c-12.063-0.429-21.449-10.71-21.449-22.78v-13.599c-3.861,2.236-8.44,3.37-13.325,2.895c-11.484-1.118-20.066-11.122-20.066-22.66
                  V256c0-6.147,4.983-11.13,11.13-11.13h22.261h11.13H367.304z"/>
                <path style="fill:#FFC248;" d="M356.174,311.652L356.174,311.652c-12.295,0-22.261-9.966-22.261-22.261V244.87h33.391
                  c6.147,0,11.13,4.983,11.13,11.13v33.391C378.435,301.686,368.469,311.652,356.174,311.652z"/>
                <path style="fill:#5B7A7F;" d="M464.696,503.652c0,4.61-3.738,8.348-8.348,8.348H44.522c-4.61,0-8.348-3.738-8.348-8.348
                  s3.738-8.348,8.348-8.348h411.826C460.958,495.304,464.696,499.042,464.696,503.652z"/>
                <path style="fill:#FFC248;" d="M364.522,8.348v77.913c0,4.61-3.736,8.348-8.348,8.348c-4.611,0-8.348-3.738-8.348-8.348V8.348
                  c0-4.61,3.736-8.348,8.348-8.348C360.785,0,364.522,3.738,364.522,8.348z M389.565,0c-4.611,0-8.348,3.738-8.348,8.348v77.913
                  c0,4.61,3.736,8.348,8.348,8.348c4.611,0,8.348-3.738,8.348-8.348V8.348C397.913,3.738,394.177,0,389.565,0z"/>
                </svg>
                <div class="w-auto flex items-center gap-2">
                  <h6 class="mb-0 font-semibold leading-normal text-sm text-slate-700">Pesanan Sedang Disiapkan</h6>
                  ${check[3]}
                </div>
              </div>
              <div class="ml-3 w-1 h-6 bg-gray-500"></div>
              <div class="flex gap-2 items-center">
                <svg class="w-8 h-8" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" 
                  viewBox="0 0 512 512" xml:space="preserve">
                <path style="fill:#FDDD85;" d="M145.067,123.733H25.6c-4.713,0-8.533,3.821-8.533,8.533V217.6c0,4.713,3.821,8.533,8.533,8.533
                  l59.733,8.533l59.733-8.533c4.713,0,8.533-3.821,8.533-8.533v-85.333C153.6,127.554,149.779,123.733,145.067,123.733z"/>
                <path style="fill:#FDD042;" d="M34.133,217.6v-85.333c0-4.713,3.821-8.533,8.533-8.533H25.6c-4.713,0-8.533,3.821-8.533,8.533V217.6
                  c0,4.713,3.821,8.533,8.533,8.533h17.067C37.954,226.133,34.133,222.313,34.133,217.6z"/>
                <path style="fill:#F2C127;" d="M162.133,89.6H8.533C3.821,89.6,0,93.421,0,98.133v34.133c0,4.713,3.821,8.533,8.533,8.533h153.6
                  c4.713,0,8.533-3.821,8.533-8.533V98.133C170.667,93.421,166.846,89.6,162.133,89.6z"/>
                <path style="fill:#E1A527;" d="M17.067,132.267V98.133c0-4.713,3.821-8.533,8.533-8.533H8.533C3.821,89.6,0,93.421,0,98.133v34.133
                  c0,4.713,3.821,8.533,8.533,8.533H25.6C20.887,140.8,17.067,136.979,17.067,132.267z"/>
                <path style="fill:#4C4E55;" d="M162.132,294.402c-3.573,0-6.902-2.261-8.095-5.837L138.916,243.2H8.533
                  C3.821,243.2,0,239.379,0,234.667c0-4.713,3.821-8.533,8.533-8.533h136.533c3.673,0,6.934,2.351,8.096,5.835l17.067,51.2
                  c1.489,4.47-0.926,9.303-5.397,10.794C163.936,294.26,163.026,294.402,162.132,294.402z"/>
                <path style="fill:#F07B52;" d="M146.102,45.899C141.267,41.063,134.838,38.4,128,38.4c-6.837,0-13.267,2.663-18.101,7.498
                  c-0.001,0,0,0-0.001,0c-4.029,4.028-10.967,11.677-16.068,17.371c-0.371-4.369-4.03-7.802-8.496-7.802
                  c-4.466,0-8.125,3.433-8.496,7.803c-5.101-5.694-12.039-13.343-16.068-17.371c-0.001,0,0,0-0.001,0
                  C55.934,41.063,49.504,38.4,42.667,38.4c-6.838,0-13.267,2.663-18.101,7.498C19.729,50.733,17.067,57.162,17.067,64
                  s2.662,13.267,7.498,18.101c2.898,2.898,7.603,5.468,20.898,5.468c3.252,0,7.019-0.155,11.398-0.494
                  c8.71-0.677,16.771-1.867,19.939-2.364v149.955c0,4.713,3.821,8.533,8.533,8.533s8.533-3.821,8.533-8.533V84.711
                  c3.168,0.498,11.229,1.686,19.939,2.364c4.38,0.34,8.146,0.494,11.398,0.494c13.293,0,18-2.571,20.897-5.467
                  C150.938,77.267,153.6,70.838,153.6,64C153.6,57.162,150.938,50.733,146.102,45.899z M36.246,69.622
                  c-4.749-5.405-0.757-14.156,6.42-14.156c2.279,0,4.422,0.887,6.033,2.499h0.001c2.697,2.697,6.948,7.308,10.975,11.753
                  C48.692,70.77,39.307,70.811,36.246,69.622z M134.42,69.622c-3.055,1.185-12.441,1.143-23.427,0.094
                  c4.026-4.444,8.276-9.053,10.973-11.749h0.001c3.195-3.196,8.502-3.306,11.822-0.236C137.137,60.827,137.435,66.19,134.42,69.622z"
                  />
                <circle style="fill:#606268;" cx="170.667" cy="396.8" r="76.8"/>
                <circle style="fill:#4C4E55;" cx="170.667" cy="396.8" r="42.667"/>
                <circle style="fill:#D7D8D9;" cx="170.667" cy="396.8" r="25.6"/>
                <circle style="fill:#606268;" cx="401.067" cy="396.8" r="76.8"/>
                <circle style="fill:#4C4E55;" cx="401.067" cy="396.8" r="42.667"/>
                <circle style="fill:#D7D8D9;" cx="401.067" cy="396.8" r="25.6"/>
                <path style="fill:#606268;" d="M366.933,149.333h-42.667c-4.713,0-8.533-3.821-8.533-8.533c0-4.713,3.821-8.533,8.533-8.533h42.667
                  c4.713,0,8.533,3.821,8.533,8.533C375.467,145.513,371.646,149.333,366.933,149.333z"/>
                <circle style="fill:#FDD042;" cx="426.667" cy="166.4" r="34.133"/>
                <path style="fill:#AFF0E8;" d="M426.667,132.267h-34.133c-4.713,0-8.533,3.821-8.533,8.533V192c0,4.713,3.821,8.533,8.533,8.533
                  h34.133c4.713,0,8.533-3.821,8.533-8.533v-51.2C435.2,136.087,431.379,132.267,426.667,132.267z"/>
                <path style="fill:#606268;" d="M264.533,226.133h-51.2c-23.526,0-42.667,19.14-42.667,42.667l0,0c0,4.713,3.821,8.533,8.533,8.533
                  l59.733,8.533l42.667-8.533c4.713,0,8.533-3.821,8.533-8.533v-17.067C290.133,237.618,278.649,226.133,264.533,226.133z"/>
                <path style="fill:#74DBC9;" d="M434.973,283.911l-34.133-145.067c-0.907-3.855-4.346-6.578-8.306-6.578h-25.6
                  c-4.713,0-8.533,3.821-8.533,8.533v204.8h-68.267v-59.733c0-4.713-3.821-8.533-8.533-8.533H145.067c-42.348,0-76.8,34.452-76.8,76.8
                  v34.133c0,4.713,3.821,8.533,8.533,8.533h349.867c4.713,0,8.533-3.821,8.533-8.533v-102.4
                  C435.2,285.209,435.124,284.553,434.973,283.911z"/>
                <g>
                  <path style="fill:#6AC8B7;" d="M375.467,140.8c0-4.713,3.821-8.533,8.533-8.533h-17.067c-4.713,0-8.533,3.821-8.533,8.533v204.8
                    h17.067V140.8z"/>
                  <path style="fill:#6AC8B7;" d="M85.333,388.267v-34.133c0-42.348,34.452-76.8,76.8-76.8h-17.067c-42.348,0-76.8,34.452-76.8,76.8
                    v34.133c0,4.713,3.821,8.533,8.533,8.533h17.067C89.154,396.8,85.333,392.979,85.333,388.267z"/>
                </g>
                <path style="fill:#4C4E55;" d="M187.733,268.8c0-23.526,19.14-42.667,42.667-42.667h-17.067c-23.526,0-42.667,19.14-42.667,42.667
                  c0,4.713,3.821,8.533,8.533,8.533h17.067C191.554,277.333,187.733,273.513,187.733,268.8z"/>
                <g>
                  <path style="fill:#2DB59F;" d="M221.867,320H179.2c-4.713,0-8.533-3.821-8.533-8.533c0-4.713,3.821-8.533,8.533-8.533h42.667
                    c4.713,0,8.533,3.821,8.533,8.533C230.4,316.179,226.579,320,221.867,320z"/>
                  <path style="fill:#2DB59F;" d="M221.867,345.6H179.2c-4.713,0-8.533-3.821-8.533-8.533s3.821-8.533,8.533-8.533h42.667
                    c4.713,0,8.533,3.821,8.533,8.533S226.579,345.6,221.867,345.6z"/>
                  <path style="fill:#2DB59F;" d="M221.867,371.2H179.2c-4.713,0-8.533-3.821-8.533-8.533s3.821-8.533,8.533-8.533h42.667
                    c4.713,0,8.533,3.821,8.533,8.533S226.579,371.2,221.867,371.2z"/>
                </g>
                <path style="fill:#74DBC9;" d="M401.067,277.333c-61.169,0-110.933,49.764-110.933,110.933c0,4.713,3.821,8.533,8.533,8.533h204.8
                  c4.713,0,8.533-3.821,8.533-8.533C512,327.098,462.236,277.333,401.067,277.333z"/>
                <path style="fill:#6AC8B7;" d="M307.2,388.267c0-58.298,45.203-106.237,102.4-110.608c-2.817-0.215-5.663-0.325-8.533-0.325
                  c-61.169,0-110.933,49.764-110.933,110.933c0,4.713,3.821,8.533,8.533,8.533h17.067C311.021,396.8,307.2,392.979,307.2,388.267z"/>
                <g>
                  <circle style="fill:#AFF0E8;" cx="443.733" cy="320" r="17.067"/>
                  <circle style="fill:#AFF0E8;" cx="469.333" cy="345.6" r="8.533"/>
                </g>
                </svg>
                <div class="w-auto flex items-center gap-2">
                  <h6 class="mb-0 font-semibold leading-normal text-sm text-slate-700">Psanan Dalam Perjalanan</h6>
                  ${check[4]}
                </div>
              </div>
              <div class="ml-3 w-1 h-6 bg-gray-500"></div>
              <div class="flex gap-2 items-center">
                <svg class="w-8 h-8" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" 
                  viewBox="0 0 506.4 506.4" xml:space="preserve">
                <circle style="fill:#54B265;" cx="253.2" cy="253.2" r="249.2"/>
                <path style="fill:#F4EFEF;" d="M372.8,200.4l-11.2-11.2c-4.4-4.4-12-4.4-16.4,0L232,302.4l-69.6-69.6c-4.4-4.4-12-4.4-16.4,0
                  L134.4,244c-4.4,4.4-4.4,12,0,16.4l89.2,89.2c4.4,4.4,12,4.4,16.4,0l0,0l0,0l10.4-10.4l0.8-0.8l121.6-121.6
                  C377.2,212.4,377.2,205.2,372.8,200.4z"/>
                <path d="M253.2,506.4C113.6,506.4,0,392.8,0,253.2S113.6,0,253.2,0s253.2,113.6,253.2,253.2S392.8,506.4,253.2,506.4z M253.2,8
                  C118,8,8,118,8,253.2s110,245.2,245.2,245.2s245.2-110,245.2-245.2S388.4,8,253.2,8z"/>
                <path d="M231.6,357.2c-4,0-8-1.6-11.2-4.4l-89.2-89.2c-6-6-6-16,0-22l11.6-11.6c6-6,16.4-6,22,0l66.8,66.8L342,186.4
                  c2.8-2.8,6.8-4.4,11.2-4.4c4,0,8,1.6,11.2,4.4l11.2,11.2l0,0c6,6,6,16,0,22L242.8,352.4C239.6,355.6,235.6,357.2,231.6,357.2z
                  M154,233.6c-2,0-4,0.8-5.6,2.4l-11.6,11.6c-2.8,2.8-2.8,8,0,10.8l89.2,89.2c2.8,2.8,8,2.8,10.8,0l132.8-132.8c2.8-2.8,2.8-8,0-10.8
                  l-11.2-11.2c-2.8-2.8-8-2.8-10.8,0L234.4,306c-1.6,1.6-4,1.6-5.6,0l-69.6-69.6C158,234.4,156,233.6,154,233.6z"/>
                </svg>
                <div class="w-auto flex items-center gap-2">
                  <h6 class="mb-0 font-semibold leading-normal text-sm text-slate-700">Pesanan Telah Diterima</h6>
                  ${check[5]}
                </div>
              </div>
          </div>
        </div>
    `
    $('#content-order').append(progress)
})

// Proses live update untuk setiap pesanan di halaman admin
setInterval(() => {
  var id = $('#id').val()

  var item = $('.tr-item')

  for (var i = 0; i < item.length; i++) {
    $('.tr-item').remove()
  }

  var data = get(`admin/order/profile/${id}`)
  
  if (data.length <= 0) {
    var tr = `<tr class="tr-item">
                <td class="text-center font-semibold text-md px-4 py-2 w-full">
                  Belum Ada Pesanan
                </td>
              </tr>`
  } else {
    $.each(data, function (index, value) { 
      if(value.status == 0) {
        var button = `<div class="flex items-center space-x-4 text-sm">
          <button @click="openModal('confirm', 'Bayar Sekarang', 'modalConfirm', 'modalConfirmBackground', 'modalRatingBackground')" data-id="${value.id}" data-val="1" class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 bg-yellow-400 text-white rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray" aria-label="payment confirm" id="confirm-button"> 
            Bayar Sekarang
          </button>
        </div>`
      } else {
        var button = `<div class="flex items-center space-x-4 text-sm">
          <button @click="openModal('confirm', 'Progress Pesanan', 'modalConfirm', 'modalConfirmBackground', 'modalRatingBackground')" data-id="${value.id}" data-val="1" class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 bg-blue-400 text-white rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray" aria-label="payment confirm" id="progress-button"> 
            Lihat Progress Pesanan
          </button>
        </div>`
      }
  
      var response = `
          <tr class="text-gray-700 dark:text-gray-400 tr-item">
            <td class="px-4 py-3 text-sm">
              ${value.item}
            </td>
            <td class="px-4 py-3 text-sm">
              Rp ${value.total_amount.toLocaleString("en-US")}
            </td>
            <td class="px-4 py-3">
              ${button}
            </td>
          </tr>
      `
      $('#dashboard_user_content').append(response)
    })
  }
}, 30000)

// Proses live update untuk setiap pesanan di halaman user
setInterval(() => {
  var id = $('#id').val()

  var item = $('.tr-order')

  for (var i = 0; i < item.length; i++) {
    $('.tr-order').remove()
  }

  var data = get(`admin/order/rating/profile/${id}`)
  
  if (data.length <= 0) {
    var tr = `<tr class="tr-order">
                <td class="text-center font-semibold text-md px-4 py-2 w-full">
                  Belum Ada Pesanan
                </td>
              </tr>`
  } else {
    $.each(data, function (index, value) {
  
      var response = `
      <tr class="text-gray-700 dark:text-gray-400 tr-order">
        <td class="px-4 py-3 text-sm">
          ${value.item}
        </td>
        <td class="px-4 py-3 text-sm">
          Rp ${value.total_amount.toLocaleString("en-US")}
        </td>
        <td class="px-4 py-3">
          <div class="flex items-center space-x-4 text-sm">
            <button @click="openModal('rating', 'Beri Penilaian', 'modalRating', 'modalRatingBackground', 'modalConfirmBackground')" data-id="${value.id}" class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 ${value.rate == 1 ? 'bg-purple-200' : 'bg-purple-600'} text-white rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray" aria-label="rating" id="rating-button" ${ value.rate == 1 ? 'disabled' : '' }>
              Beri Penilaian
            </button>
          </div>
        </td>
      </tr>
      `
      $('#dashboard_rating_content').append(response)
    })
  }
},30000)

$('#add-stock').on('click', () => {
  let productId = $('#product_id').val()
  let stock = $('#stock_value').val()

  if(productId == '') {
    Swal.fire(
      'Pilih Produk',
      'Silahkan pilih produk yang akan ditambahkan stoknya!',
      'warning'
    )
  }

  if(stock == '') {
    Swal.fire(
      'Masukan Jumlah Stok',
      'Silahkan masukan jumlah stok yang ingin ditambahkan!',
      'warning'
    )
  }

  if(stock > 50) {
    Swal.fire(
      'Melebihi Jumlah Maksimal Stok',
      'Stok maksimal dari setiap produk ada 50 buah',
      'warning'
    )
  }else{
    data = {
      productId : productId,
      stock: stock,
    }
  
    post('admin/stock', data, '/admin/stock')
  }

})