<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MenuItemController extends Controller
{
    public function index(): View
    {
        return view('admin.menu-items.index', [
            'menuItems' => MenuItem::with('category')->latest()->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('admin.menu-items.create', [
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:4096'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('menu-items', 'public')
            : null;

        if ($imagePath) {
            $this->cropToFourThree(Storage::disk('public')->path($imagePath));
        }

        $menuItem = MenuItem::create([
            'name' => $validated['name'],
            'slug' => $this->generateSlug($validated['name']),
            'category_id' => $validated['category_id'] ?? null,
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'low_stock_threshold' => $validated['low_stock_threshold'] ?? 5,
            'image_path' => $imagePath,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $this->syncLowStockNotification($menuItem->fresh());

        return redirect()->route('admin.menu-items.index')
            ->with('status', 'Menu berhasil ditambahkan.');
    }

    public function edit(MenuItem $menuItem): View
    {
        return view('admin.menu-items.edit', [
            'menuItem' => $menuItem,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, MenuItem $menuItem): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:4096'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $imagePath = $menuItem->image_path;

        if ($request->hasFile('image')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            $imagePath = $request->file('image')->store('menu-items', 'public');
            $this->cropToFourThree(Storage::disk('public')->path($imagePath));
        }

        $menuItem->update([
            'name' => $validated['name'],
            'slug' => $this->generateSlug($validated['name'], $menuItem->id),
            'category_id' => $validated['category_id'] ?? null,
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'low_stock_threshold' => $validated['low_stock_threshold'] ?? $menuItem->low_stock_threshold,
            'image_path' => $imagePath,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $this->syncLowStockNotification($menuItem->fresh());

        return redirect()->route('admin.menu-items.index')
            ->with('status', 'Menu berhasil diperbarui.');
    }

    /**
     * Ensure uploaded image uses a consistent 4:3 aspect ratio (center-crop),
     * optionally downscale to a reasonable max size.
     */
    private function cropToFourThree(string $absolutePath): void
    {
        try {
            if (! function_exists('imagecreatetruecolor')) {
                return; // GD not available; skip silently
            }

            $info = @getimagesize($absolutePath);
            if (! $info) return;
            [$width, $height] = $info;
            $mime = $info['mime'] ?? '';

            switch ($mime) {
                case 'image/jpeg':
                case 'image/pjpeg':
                    $src = @imagecreatefromjpeg($absolutePath);
                    $ext = 'jpg';
                    break;
                case 'image/png':
                    $src = @imagecreatefrompng($absolutePath);
                    $ext = 'png';
                    break;
                case 'image/webp':
                    if (! function_exists('imagecreatefromwebp')) return;
                    $src = @imagecreatefromwebp($absolutePath);
                    $ext = 'webp';
                    break;
                default:
                    return; // unsupported
            }
            if (! $src) return;

                        // Center-crop to strict 4:3
            $targetRatio = 4 / 3;
            $currentRatio = $width / max(1, $height);
            if ($currentRatio > $targetRatio) {
                // too wide ? crop left/right
                $cropW = (int) round($height * $targetRatio);
                $cropH = $height;
                $srcX = (int) floor(($width - $cropW) / 2);
                $srcY = 0;
            } else {
                // too tall ? crop top/bottom
                $cropW = $width;
                $cropH = (int) round($width / $targetRatio);
                $srcX = 0;
                $srcY = (int) floor(($height - $cropH) / 2);
            }

            // Downscale output if very large
            $maxW = 1600; $maxH = (int) round($maxW / $targetRatio);
            $outW = min($cropW, $maxW);
            $outH = min($cropH, $maxH);

            $dst = imagecreatetruecolor($outW, $outH);
            if ($ext === 'png' || $ext === 'webp') {
                imagealphablending($dst, false);
                imagesavealpha($dst, true);
            }
            imagecopyresampled($dst, $src, 0, 0, $srcX, $srcY, $outW, $outH, $cropW, $cropH);// Overwrite file
            switch ($ext) {
                case 'jpg':
                    @imagejpeg($dst, $absolutePath, 85);
                    break;
                case 'png':
                    @imagepng($dst, $absolutePath, 6);
                    break;
                case 'webp':
                    if (function_exists('imagewebp')) @imagewebp($dst, $absolutePath, 85);
                    break;
            }

            imagedestroy($dst);
            imagedestroy($src);
        } catch (\Throwable $e) {
            // Fail silently; do not block uploads if processing fails
        }
    }

    public function destroy(MenuItem $menuItem): RedirectResponse
    {
        if ($menuItem->orderItems()->exists()) {
            return redirect()->back()->withErrors('Menu tidak dapat dihapus karena sudah pernah dipesan.');
        }

        if ($menuItem->image_path && Storage::disk('public')->exists($menuItem->image_path)) {
            Storage::disk('public')->delete($menuItem->image_path);
        }

        $menuItem->delete();

        return redirect()->route('admin.menu-items.index')
            ->with('status', 'Menu berhasil dihapus.');
    }

    public function updateStock(Request $request, MenuItem $menuItem): RedirectResponse
    {
        $validated = $request->validate([
            'stock' => ['required', 'integer', 'min:0'],
        ]);

        $menuItem->update(['stock' => $validated['stock']]);
        $this->syncLowStockNotification($menuItem->refresh());

        return redirect()->route('admin.menu-items.index')
            ->with('status', 'Stok menu berhasil diperbarui.');
    }

    private function generateSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $i = 1;

        while (
            MenuItem::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $baseSlug.'-'.$i;
            $i++;
        }

        return $slug;
    }

    private function syncLowStockNotification(MenuItem $menuItem): void
    {
        if ($menuItem->stock > $menuItem->low_stock_threshold && $menuItem->low_stock_notified_at) {
            $menuItem->forceFill([
                'low_stock_notified_at' => null,
            ])->save();

            return;
        }

        if ($menuItem->stock <= $menuItem->low_stock_threshold && ! $menuItem->low_stock_notified_at) {
            $menuItem->forceFill([
                'low_stock_notified_at' => now(),
            ])->save();

            $admins = User::where('role', UserRole::Admin)->get();

            if ($admins->isNotEmpty()) {
                Notification::send($admins, new LowStockNotification($menuItem, $menuItem->stock));
            }
        }
    }
}

