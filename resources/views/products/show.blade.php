
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

    <fieldset>
        <legend>Choose your tags</legend>
        <div>
            <input type="checkbox" name="tag" value="food" />
            <label for="food">Food</label>
        </div>
        <div>
            <input type="checkbox" name="tag" value="tech" />
            <label for="tech">Tech</label>
        </div>
        <div>
            <input type="checkbox" name="tag" value="clothes" />
            <label for="clothes">Clothing</label>
        </div>
    </fieldset>

    <form action="{{ route('products/update', $product) }}" method="post" class="tag-form" data-search-url="{{ route('tag/search') }}">
        @csrf
        @method('PUT')

        <label for="tag-search">Tags</label>
        <input type="text" id="tag-search" autocomplete="off" placeholder="Search or type a tag">

        <ul id="tag-suggestions" class="tag-suggestions" hidden></ul>

        <ul id="selected-tags" class="tag-list">
            @foreach (old('tags', $product->tags->pluck('title')->all()) as $tagTitle)
                <li class="tag-item">
                    <span>{{ $tagTitle }}</span>
                    <input type="hidden" name="tags[]" value="{{ $tagTitle }}">
                    <button type="button" class="tag-remove" aria-label="Remove tag">&times;</button>
                </li>
            @endforeach
        </ul>

        <input type="submit" value="Update">
    </form>

    <a href="{{ route('products/edit', $product) }}">Edit</a>

    <form action="{{ route('products/destroy', $product) }}" method="post">
        @csrf
        @method('DELETE')
        <input type="submit" value="Delete">
    </form>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const quantity = document.getElementById('quantity');
            const url = "{{ route('products.updateQuantity', $product) }}";
            const token = document.querySelector('meta[name="csrf-token"]').content;

            async function updateQuantity(action) {
                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ action: action })
                    });

                    if (!response.ok) {
                        throw new Error('HTTP error: ' + response.status);
                    }

                    const data = await response.json();
                    quantity.textContent = data.quantity;
                } catch (error) {
                    console.error(error);
                }
            }

            document.getElementById('increase').addEventListener('click', function () {
                updateQuantity('increase');
            });

            document.getElementById('decrease').addEventListener('click', function () {
                updateQuantity('decrease');
            });
        });
    </script>
</x-layout>
