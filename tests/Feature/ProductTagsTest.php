<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTagsTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_page_renders_autocomplete_and_attached_tags(): void
    {
        $product = Product::factory()->create();
        $product->tags()->create(['title' => 'Fruit']);

        $response = $this->get(route('products/show', $product));

        $response->assertOk()
            ->assertSee('Fruit')
            ->assertSee('id="tag-search"', false)
            ->assertSee('name="tags[]"', false)
            ->assertSee('data-search-url="'.route('tag/search').'"', false);
    }

    public function test_search_returns_matching_tags_as_json(): void
    {
        Tag::create(['title' => 'Fruit']);
        Tag::create(['title' => 'Vegetable']);

        $this->getJson(route('tag/search', ['q' => 'rui']))
            ->assertOk()
            ->assertJsonCount(1, 'tags')
            ->assertJsonPath('tags.0.title', 'Fruit');
    }

    public function test_update_attaches_existing_tags_and_creates_new_ones(): void
    {
        $product = Product::factory()->create();
        $otherProduct = Product::factory()->create();
        $existing = Tag::create(['title' => 'Fruit']);
        $otherProduct->tags()->attach($existing);

        $response = $this->put(route('products/update', $product), [
            'tags' => ['Fruit', 'Fresh'],
        ]);

        $response->assertRedirect(route('products/show', $product));
        $this->assertEqualsCanonicalizing(['Fruit', 'Fresh'], $product->tags()->pluck('title')->all());
        $this->assertTrue(Tag::where('title', 'Fresh')->exists());
        $this->assertTrue($otherProduct->tags->contains($existing));
    }

    public function test_update_removes_tags_when_list_is_empty(): void
    {
        $product = Product::factory()->create();
        $product->tags()->create(['title' => 'Fruit']);

        $this->put(route('products/update', $product), ['tags' => []]);

        $this->assertCount(0, $product->tags);
    }
}
