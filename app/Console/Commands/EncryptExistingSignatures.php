<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class EncryptExistingSignatures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'signatures:encrypt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Encrypt all existing plain-text signatures and move them to local disk.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::whereNotNull('signature')->get();
        $count = 0;

        foreach ($users as $user) {
            $oldPath = $user->signature;

            // Check if it's already encrypted (ends with .enc) or if it exists on local disk
            if (Str::endsWith($oldPath, '.enc')) {
                $this->info("User {$user->name} signature is already encrypted.");
                continue;
            }

            // Check if file exists in public disk
            if (Storage::disk('public')->exists($oldPath)) {
                $content = Storage::disk('public')->get($oldPath);
                
                $encryptedContent = Crypt::encrypt($content);
                $newFilename = Str::random(40) . '.enc';
                $newPath = 'signatures/' . $newFilename;
                
                // Store in local disk
                Storage::disk('local')->put($newPath, $encryptedContent);
                
                // Update DB
                $user->signature = $newPath;
                $user->save();
                
                // Delete old file
                Storage::disk('public')->delete($oldPath);
                
                $this->info("Encrypted signature for user: {$user->name}");
                $count++;
            } else {
                $this->warn("Signature file not found for user: {$user->name} ($oldPath)");
            }
        }

        $this->info("Successfully encrypted {$count} signatures.");
    }
}
