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
    <h4>Quantity: {{ $product->quantity }}</h4>
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
    <a href="{{ route('products/edit', $product) }}">Edit</a>
    <form action="{{ route('products/destroy', $product) }}" method="post">
        @csrf
        @method('DELETE')
        <input type="submit" value="Delete">
    </form>
</x-layout>