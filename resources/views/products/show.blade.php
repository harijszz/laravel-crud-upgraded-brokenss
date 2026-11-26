
<x-layout>
    <x-slot:title>
        Show a product
    </x-slot>

    @if ($errors->any())
        <div class="error-message">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h1>{{ $product->name }}</h1>

    <h4>
        Quantity:
        <span id="quantity">{{ $product->quantity }}</span>
    </h4>

    <button type="button" id="increase">+</button>
    <button type="button" id="decrease">-</button>

    <p>{{ $product->description }}</p>

    <a href="{{ route('products/edit', $product) }}">Edit</a>

    <form action="{{ route('products/destroy', $product) }}" method="post">
        @csrf
        @method('DELETE')
        <input type="submit" value="Delete">
    </form>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
document.addEventListener('DOMContentLoaded', function () {

    document.getElementById('increase').addEventListener('click', function () {
        updateQuantity('increase');
    });

    document.getElementById('decrease').addEventListener('click', function () {
        updateQuantity('decrease');
    });

    function updateQuantity(action) {

        console.log('Button clicked:', action);

        fetch("{{ route('products.updateQuantity', $product) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                action: action
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('HTTP error: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log(data);
            document.getElementById('quantity').textContent = data.quantity;
        })
        .catch(error => {
            console.error(error);
        });
    }

});
</script>

</x-layout>
