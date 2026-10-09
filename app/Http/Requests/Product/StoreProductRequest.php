<?php

namespace App\Http\Requests\Product;

use App\Enums\DeliveryType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Product::class);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['required', 'string'],
            'delivery_type' => ['required', Rule::enum(DeliveryType::class)],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'is_draft' => ['nullable', 'boolean'],
            'variants' => ['required', 'array', 'min:1'],
            'variants.*.name' => ['required', 'string', 'max:255'],
            'variants.*.price' => ['required', 'numeric', 'min:1000', 'max:10000000'],
            'variants.*.original_price' => ['nullable', 'numeric', 'min:1000', 'max:10000000'],
            'variants.*.duration_days' => ['required', 'integer', 'min:1'],
            'variants.*.stock' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama produk wajib diisi.',
            'category_id.required' => 'Kategori produk wajib dipilih.',
            'category_id.exists' => 'Kategori tidak valid.',
            'description.required' => 'Deskripsi produk wajib diisi.',
            'delivery_type.required' => 'Tipe pengiriman wajib dipilih.',
            'thumbnail.image' => 'File thumbnail harus berupa gambar.',
            'thumbnail.mimes' => 'Format gambar harus jpeg, png, jpg, atau webp.',
            'thumbnail.max' => 'Ukuran gambar maksimal 2 MB.',
            'variants.required' => 'Produk minimal harus memiliki satu varian.',
            'variants.*.name.required' => 'Nama varian wajib diisi.',
            'variants.*.price.required' => 'Harga varian wajib diisi.',
            'variants.*.price.min' => 'Harga varian minimal Rp 1.000.',
            'variants.*.price.max' => 'Harga varian maksimal Rp 10.000.000.',
            'variants.*.duration_days.required' => 'Durasi hari wajib diisi.',
        ];
    }
}
