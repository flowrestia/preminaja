<?php

namespace App\Http\Requests\Product;

use App\Enums\DeliveryType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        $product = $this->route('product');
        return $this->user()->can('update', $product);
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'category_id' => ['sometimes', 'required', 'exists:categories,id'],
            'description' => ['sometimes', 'required', 'string'],
            'delivery_type' => ['sometimes', 'required', Rule::enum(DeliveryType::class)],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'save_as_draft' => ['nullable', 'boolean'],
            'submit_for_review' => ['nullable', 'boolean'],
            'variants' => ['sometimes', 'required', 'array', 'min:1'],
            'variants.*.id' => ['nullable', 'integer'],
            'variants.*.name' => ['required', 'string', 'max:255'],
            'variants.*.price' => ['required', 'numeric', 'min:1000', 'max:10000000'],
            'variants.*.original_price' => ['nullable', 'numeric', 'min:1000', 'max:10000000'],
            'variants.*.duration_days' => ['required', 'integer', 'min:1'],
            'variants.*.stock' => ['nullable', 'integer', 'min:0'],
            'variants.*.is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama produk wajib diisi.',
            'category_id.required' => 'Kategori produk wajib dipilih.',
            'category_id.exists' => 'Kategori tidak valid.',
            'description.required' => 'Deskripsi produk wajib diisi.',
            'thumbnail.image' => 'File thumbnail harus berupa gambar.',
            'thumbnail.mimes' => 'Format gambar harus jpeg, png, jpg, atau webp.',
            'thumbnail.max' => 'Ukuran gambar maksimal 2 MB.',
            'variants.*.price.min' => 'Harga varian minimal Rp 1.000.',
            'variants.*.price.max' => 'Harga varian maksimal Rp 10.000.000.',
        ];
    }
}
