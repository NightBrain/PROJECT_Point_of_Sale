<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ใบเสร็จ {{ $sale->sale_code }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="p-6 bg-gray-100">

<div class="max-w-sm mx-auto bg-white shadow-md p-4 text-sm">
    <h1 class="text-center font-bold text-lg mb-2">ใบเสร็จรับเงิน</h1>
    <p>เลขที่: {{ $sale->sale_code }}</p>
    <p>วันที่: {{ $sale->created_at->format('d/m/Y H:i') }}</p>
    <p>การชำระเงิน: {{ $sale->payment_method }}</p>
    <hr class="my-2">

    <table class="w-full text-left text-xs">
        <thead>
            <tr class="border-b">
                <th>สินค้า</th>
                <th class="text-center">จำนวน</th>
                <th class="text-right">ราคา</th>
            </tr>
        </thead>
        <tbody>
        @foreach($sale->items as $item)
            <tr>
                <td>{{ $item->product->product_name ?? '-' }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format($item->subtotal,2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <hr class="my-2">
    <div class="text-right font-bold">รวม: {{ number_format($sale->total,2) }} ฿</div>
</div>

<script>
    window.print();
</script>
</body>
</html>
