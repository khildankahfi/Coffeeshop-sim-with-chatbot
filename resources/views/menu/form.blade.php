@extends('layouts.admin')
@section('title', isset($menu) ? 'Edit Menu' : 'Tambah Menu')
@push('styles')
<style>
.form-card {
    background: #fff; border: 0.5px solid #e8d8c4;
    border-radius: 14px; padding: 32px; max-width: 600px;
}
.form-group { margin-bottom: 20px; }
.form-label {
    display: block; font-size: 13px; font-weight: 600;
    color: var(--brown-main); margin-bottom: 6px;
}
.form-label span { color: #e53e3e; margin-left: 2px; }
.form-control {
    width: 100%; border: 1px solid #e8d8c4; border-radius: 10px;
    padding: 10px 14px; font-size: 14px; outline: none;
    font-family: 'DM Sans', sans-serif; background: var(--cream-light);
    color: var(--text-dark); transition: border .2s;
}
.form-control:focus { border-color: var(--brown-main); }
.form-select {
    width: 100%; border: 1px solid #e8d8c4; border-radius: 10px;
    padding: 10px 14px; font-size: 14px; outline: none;
    font-family: 'DM Sans', sans-serif; background: var(--cream-light);
    color: var(--text-dark); cursor: pointer;
}
.form-select:focus { border-color: var(--brown-main); }
textarea.form-control { resize: vertical; min-height: 90px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.form-check { display: flex; align-items: center; gap: 10px; margin-top: 8px; }
.form-check input { width: 18px; height: 18px; cursor: pointer; accent-color: var(--brown-main); }
.form-check label { font-size: 13px; color: var(--text-dark); cursor: pointer; }
.form-actions { display: flex; gap: 10px; margin-top: 28px; }
.btn-submit {
    background: var(--brown-main); color: var(--cream);
    border: none; padding: 11px 28px; border-radius: 20px;
    font-size: 13px; font-weight: 600; cursor: pointer;
    font-family: 'DM Sans', sans-serif; transition: background .2s;
}
.btn-submit:hover { background: var(--accent); }
.btn-cancel {
    background: transparent; color: var(--text-muted);
    border: 1px solid #e8d8c4; padding: 11px 28px; border-radius: 20px;
    font-size: 13px; cursor: pointer; font-family: 'DM Sans', sans-serif;
    transition: all .2s;
}
.btn-cancel:hover { background: var(--cream-mid); color: var(--text-dark); }
.error-msg { color: #e53e3e; font-size: 12px; margin-top: 4px; }
.alert-error {
    background: #fee2e2; color: #991b1b; padding: 12px 16px;
    border-radius: 10px; font-size: 13px; margin-bottom: 20px;
}
</style>
@endpush

@section('content')
<div class="form-card">
    @if($errors->any())
    <div class="alert-error">
        ⚠️ Mohon perbaiki kesalahan berikut:
        <ul style="margin-top:6px; padding-left:16px;">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ isset($menu) ? route('admin.menu.update', $menu) : route('admin.menu.store') }}"
          method="POST">
        @csrf
        @if(isset($menu)) @method('PUT') @endif

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Nama Menu <span>*</span></label>
                <input type="text" name="name" class="form-control"
                       value="{{ old('name', $menu->name ?? '') }}"
                       placeholder="contoh: Latte" required>
                @error('name')<div class="error-msg">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Kategori <span>*</span></label>
                <select name="category_id" class="form-select" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}"
                        {{ old('category_id', $menu->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                    @endforeach
                </select>
                @error('category_id')<div class="error-msg">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Deskripsi</label>
            <textarea name="description" class="form-control"
                      placeholder="Deskripsi singkat menu...">{{ old('description', $menu->description ?? '') }}</textarea>
            @error('description')<div class="error-msg">{{ $message }}</div>@enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Harga (Rp) <span>*</span></label>
                <input type="number" name="price" class="form-control"
                       value="{{ old('price', $menu->price ?? '') }}"
                       placeholder="25000" min="0" required>
                @error('price')<div class="error-msg">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <div class="form-check">
                    <input type="checkbox" name="is_available" id="is_available" value="1"
                           {{ old('is_available', $menu->is_available ?? true) ? 'checked' : '' }}>
                    <label for="is_available">Tersedia / Aktif</label>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">
                {{ isset($menu) ? '💾 Simpan Perubahan' : '➕ Tambah Menu' }}
            </button>
            <a href="{{ route('admin.menu.create') }}">
                <button type="button" class="btn-cancel">Batal</button>
            </a>
        </div>
    </form>
</div>
@endsection