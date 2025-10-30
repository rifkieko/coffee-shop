<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TableController extends Controller
{
    public function index(): View
    {
        return view('admin.tables.index', [
            'tables' => ShopTable::orderBy('name')->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('admin.tables.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'capacity' => ['required', 'integer', 'min:1', 'max:20'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        ShopTable::create([
            'name' => $validated['name'],
            'code' => strtoupper(Str::random(6)),
            'slug' => Str::slug($validated['name'].'-'.Str::random(3)),
            'qr_token' => Str::uuid()->toString(),
            'capacity' => $validated['capacity'],
            'notes' => $validated['notes'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.tables.index')
            ->with('status', 'Meja berhasil ditambahkan.');
    }

    public function edit(ShopTable $table): View
    {
        return view('admin.tables.edit', [
            'table' => $table,
        ]);
    }

    public function update(Request $request, ShopTable $table): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'capacity' => ['required', 'integer', 'min:1', 'max:20'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $table->update([
            'name' => $validated['name'],
            'capacity' => $validated['capacity'],
            'notes' => $validated['notes'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.tables.index')
            ->with('status', 'Data meja berhasil diperbarui.');
    }

    public function destroy(ShopTable $table): RedirectResponse
    {
        if ($table->orders()->exists()) {
            return redirect()->back()->withErrors('Meja tidak dapat dihapus karena memiliki riwayat pesanan.');
        }

        $table->delete();

        return redirect()->route('admin.tables.index')
            ->with('status', 'Meja berhasil dihapus.');
    }

    public function regenerateToken(ShopTable $table): RedirectResponse
    {
        $table->update([
            'qr_token' => Str::uuid()->toString(),
            'slug' => Str::slug($table->name.'-'.Str::random(3)),
        ]);

        return redirect()->route('admin.tables.edit', $table)
            ->with('status', 'Token QR berhasil diganti.');
    }
}

