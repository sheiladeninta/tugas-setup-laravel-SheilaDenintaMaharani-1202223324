<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Shei's Mart - Sistem Kasir</title>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100 min-h-screen" x-data="cashierApp()">
    <header class="bg-red-600 shadow-lg">
        <div class="container mx-auto py-4 px-6 flex justify-between items-center">
            <div class="flex items-center">
                <svg class="h-8 w-8 text-white mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <h1 class="text-2xl font-bold text-white">Shei's Mart</h1>
            </div>
            <div class="flex items-center">
                <div class="text-white mr-4">
                    <div class="text-sm">Sistem Kasir</div>
                    <div class="text-xs opacity-75">{{ date('d F Y') }}</div>
                </div>
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="text-white flex items-center focus:outline-none">
                        <span class="mr-2">{{ Auth::user()->name }}</span>
                        <svg class="h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <main class="container mx-auto py-6 px-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Transaksi Baru</h2>
                    
                    <div class="mb-6">
                        <label for="customer" class="block text-sm font-medium text-gray-700 mb-1">Nama Pembeli</label>
                        <input type="text" id="customer" x-model="customerName" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="Masukkan nama pembeli">
                    </div>
                    
                    <div class="flex flex-col md:flex-row md:items-end md:space-x-4 w-full">
                        <div class="flex flex-col w-full md:w-1/2">
                            <label for="product" class="block text-sm font-medium text-gray-700 mb-1">Pilih Produk</label>
                            <select id="product" x-model="productId"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                                <option value="0">Pilih Produk</option>
                                <template x-for="product in products" :key="product.id">
                                    <option :value="product.id"
                                        x-text="product.name + ' - ' + formatRupiah(product.price)"></option>
                                </template>
                            </select>
                        </div>
                        <div class="flex flex-col w-full md:w-1/5">
                            <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                            <input type="number" id="quantity" x-model="quantity" min="1"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                        <div class="flex flex-col w-full md:w-1/4">
                            <button @click="addToCart"
                                class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out">
                                Tambah ke Keranjang
                            </button>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1 italic">
                        *Jika daftar produk tidak ada, jalankan <code class="bg-gray-100 px-1 py-0.5 rounded">php artisan db:seed --class=ProductSeeder</code>
                    </p>
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-800 mb-2">Keranjang Belanja</h3>
                        <div class="border rounded-md overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template x-if="cart.length === 0">
                                        <tr>
                                            <td colspan="5" class="px-4 py-4 text-center text-sm text-gray-500">
                                                Keranjang belanja kosong
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-for="(item, index) in cart" :key="index">
                                        <tr>
                                            <td class="px-4 py-3 text-sm text-gray-900" x-text="item.name"></td>
                                            <td class="px-4 py-3 text-sm text-gray-900 text-right" x-text="formatRupiah(item.price)"></td>
                                            <td class="px-4 py-3 text-sm text-gray-900 text-right" x-text="item.quantity"></td>
                                            <td class="px-4 py-3 text-sm text-gray-900 text-right" x-text="formatRupiah(item.price * item.quantity)"></td>
                                            <td class="px-4 py-3 text-right">
                                                <button @click="removeFromCart(index)" class="text-red-600 hover:text-red-800">
                                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Ringkasan Pembayaran</h2>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="text-gray-800 font-medium" x-text="formatRupiah(subtotal)"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">PPN (11%)</span>
                            <span class="text-gray-800 font-medium" x-text="formatRupiah(tax)"></span>
                        </div>
                        <div class="border-t pt-3 mt-3">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-semibold text-gray-800">Total</span>
                                <span class="text-lg font-bold text-red-600" x-text="formatRupiah(total)"></span>
                            </div>
                        </div>
                    </div>
                    
                    <button @click="finishTransaction" class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-4 rounded-md transition duration-150 ease-in-out flex items-center justify-center">
                        <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Selesaikan Transaksi
                    </button>
                </div>
            </div>
        </div>
        
        <div class="mt-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Riwayat Transaksi</h2>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pembeli</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Item</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-if="transactions.length === 0">
                                <tr>
                                    <td colspan="5" class="px-4 py-4 text-center text-sm text-gray-500">
                                        Belum ada transaksi
                                    </td>
                                </tr>
                            </template>
                            <template x-for="transaction in transactions" :key="transaction.id">
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900" x-text="'TRX-' + transaction.id.toString().padStart(4, '0')"></td>
                                    <td class="px-4 py-3 text-sm text-gray-900" x-text="transaction.customer_name"></td>
                                    <td class="px-4 py-3 text-sm text-gray-900 text-right" x-text="transaction.total_items"></td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 text-right" x-text="formatRupiah(transaction.total_amount)"></td>
                                    <td class="px-4 py-3 text-sm text-gray-900 text-right" x-text="formatDate(transaction.created_at)"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    
    <footer class="bg-red-600 py-4 mt-8">
        <div class="container mx-auto px-6">
            <div class="text-center text-white">
                <p class="text-sm">© {{ date('Y') }} Shei's Mart. Semua hak dilindungi.</p>
            </div>
        </div>
    </footer>

    <script>
        function cashierApp() {
            return {
                products: @json($products),
                transactions: @json($transactions),
                cart: [],
                productId: 0,
                quantity: 1,
                customerName: '',
                loading: false,
                
                init() {
                    // Set up CSRF token for AJAX requests
                    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                },
                
                get totalItems() {
                    return this.cart.reduce((total, item) => total + parseInt(item.quantity), 0);
                },
                
                get subtotal() {
                    return this.cart.reduce((total, item) => total + (item.price * item.quantity), 0);
                },
                
                get tax() {
                    return this.subtotal * 0.11;
                },
                
                get total() {
                    return this.subtotal + this.tax;
                },
                
                addToCart() {
                    if (this.productId === 0) return;
                    
                    const product = this.products.find(p => p.id == this.productId);
                    const existingItem = this.cart.find(item => item.id == product.id);
                    
                    if (existingItem) {
                        existingItem.quantity = parseInt(existingItem.quantity) + parseInt(this.quantity);
                    } else {
                        this.cart.push({
                            id: product.id,
                            name: product.name,
                            price: parseFloat(product.price),
                            quantity: parseInt(this.quantity)
                        });
                    }
                    
                    this.productId = 0;
                    this.quantity = 1;
                },
                
                removeFromCart(index) {
                    this.cart.splice(index, 1);
                },
                
                async finishTransaction() {
                    if (this.cart.length === 0 || !this.customerName) {
                        alert('Harap isi nama pembeli dan tambahkan minimal 1 produk ke keranjang!');
                        return;
                    }
                    
                    this.loading = true;
                    
                    try {
                        const response = await fetch('/transactions', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                customer_name: this.customerName,
                                items: this.cart.map(item => ({
                                    product_id: item.id,
                                    quantity: item.quantity
                                })),
                                subtotal: this.subtotal,
                                tax: this.tax,
                                total_amount: this.total
                            })
                        });
                        
                        const result = await response.json();
                        
                        if (result.status === 'success') {
                            // Add to transactions list
                            this.transactions.unshift(result.transaction);
                            
                            // Clear cart
                            this.cart = [];
                            this.customerName = '';
                            
                            alert('Transaksi berhasil disimpan!');
                        } else {
                            alert('Terjadi kesalahan: ' + result.message);
                        }
                    } catch (error) {
                        alert('Terjadi kesalahan saat menyimpan transaksi.');
                        console.error(error);
                    } finally {
                        this.loading = false;
                    }
                },
                
                formatRupiah(amount) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(amount);
                },
                
                formatDate(dateString) {
                    const date = new Date(dateString);
                    return date.toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                }
            };
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</body>
</html>