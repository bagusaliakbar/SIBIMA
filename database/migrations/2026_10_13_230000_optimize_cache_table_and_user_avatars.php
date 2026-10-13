<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Alter cache table to MEDIUMTEXT to prevent truncation errors
        if (Schema::hasTable('cache')) {
            try {
                DB::statement('ALTER TABLE `cache` MODIFY `value` MEDIUMTEXT NOT NULL');
            } catch (\Throwable $e) {
                // Ignore if not supported by current DB driver
            }
        }

        // 2. Compress any existing oversized base64 avatars in users table
        try {
            $users = User::whereNotNull('avatar')->get();
            foreach ($users as $user) {
                if ($user->avatar && str_starts_with($user->avatar, 'data:image') && strlen($user->avatar) > 50000) {
                    $compressed = $this->compressBase64($user->avatar);
                    if ($compressed && strlen($compressed) < strlen($user->avatar)) {
                        DB::table('users')->where('id', $user->id)->update(['avatar' => $compressed]);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Ignore if error occurs
        }

        // 3. Clear existing dashboard common cache
        try {
            \Illuminate\Support\Facades\Cache::forget('sibima_dashboard_common_data');
            \Illuminate\Support\Facades\Cache::forget('dashboard_online_users_json');
        } catch (\Throwable $e) {
            // Ignore
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down migration needed
    }

    private function compressBase64(string $base64String, int $maxDim = 300): ?string
    {
        if (!preg_match('/^data:image\/(\w+);base64,(.+)$/', $base64String, $matches)) {
            return null;
        }

        $raw = base64_decode($matches[2]);
        if (!$raw || !extension_loaded('gd') || !function_exists('imagecreatefromstring')) {
            return null;
        }

        $img = @imagecreatefromstring($raw);
        if (!$img) {
            return null;
        }

        $w = imagesx($img);
        $h = imagesy($img);

        if ($w <= $maxDim && $h <= $maxDim) {
            imagedestroy($img);
            return null;
        }

        $ratio = min($maxDim / $w, $maxDim / $h);
        $newW = (int) ($w * $ratio);
        $newH = (int) ($h * $ratio);

        $thumb = imagecreatetruecolor($newW, $newH);
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
        imagecopyresampled($thumb, $img, 0, 0, 0, 0, $newW, $newH, $w, $h);

        ob_start();
        imagejpeg($thumb, null, 85);
        $compressed = ob_get_clean();

        imagedestroy($thumb);
        imagedestroy($img);

        return 'data:image/jpeg;base64,' . base64_encode($compressed);
    }
};
