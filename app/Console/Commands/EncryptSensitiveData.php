<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class EncryptSensitiveData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:encrypt-sensitive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Encrypt plain-text phone numbers in the database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting encryption of phone numbers...');
        
        $users = DB::table('users')->whereNotNull('phone_number')->get();
        $count = 0;
        
        foreach ($users as $user) {
            $phone = $user->phone_number;
            
            // Check if it's already encrypted. 
            // Laravel's Crypt::encryptString() usually starts with eyJp (base64 encoded JSON)
            // But to be safe, we can try decrypting it. If it fails, it's plain text.
            try {
                Crypt::decryptString($phone);
                // If it decrypts successfully, it's already encrypted
                continue;
            } catch (\Exception $e) {
                // It failed to decrypt, meaning it is plain text (or corrupted, but mostly plain text)
                $encryptedPhone = Crypt::encryptString($phone);
                
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['phone_number' => $encryptedPhone]);
                    
                $count++;
            }
        }
        
        $this->info("Successfully encrypted {$count} phone numbers.");
        
        $this->warn("IMPORTANT: Please ensure you add 'phone_number' => 'encrypted' to the \$casts array in the User model now.");
    }
}
