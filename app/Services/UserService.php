<?php

namespace App\Services;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;

class UserService
{
    /**
     * Create a new user.
     */
    public function createUser(array $data, $avatarFile = null)
    {
        $avatarPath = null;
        if ($avatarFile) {
            $avatarPath = $avatarFile->store('avatars', 'public');
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'identifier' => $data['identifier'],
            'entry_year' => $data['entry_year'] ?? null,
            'phone_number' => $data['phone_number'] ?? null,
            'research_interests' => $data['research_interests'] ?? null,
            'avatar' => $avatarPath,
            'is_active' => true,
        ]);

        ActivityLog::log('Tambah Pengguna', "Admin menambahkan pengguna baru: {$user->name} ({$user->role}).", 'User');

        return $user;
    }

    /**
     * Update an existing user.
     */
    public function updateUser(User $user, array $data, $avatarFile = null)
    {
        $updateData = [
            'name' => $data['name'] ?? $user->name,
            'email' => $data['email'] ?? $user->email,
            'role' => $data['role'] ?? $user->role,
            'identifier' => $data['identifier'] ?? $user->identifier,
            'entry_year' => $data['entry_year'] ?? $user->entry_year,
            'phone_number' => $data['phone_number'] ?? $user->phone_number,
            'research_interests' => $data['research_interests'] ?? $user->research_interests,
        ];

        if ($avatarFile) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $updateData['avatar'] = $avatarFile->store('avatars', 'public');
        }

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);

        ActivityLog::log('Edit Pengguna', "Data pengguna diperbarui: {$user->name}.", 'User');

        return $user;
    }

    /**
     * Delete a user.
     */
    public function deleteUser(User $user)
    {
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }
        $user->delete();
        ActivityLog::log('Hapus Pengguna', "Pengguna dihapus: {$user->name}.", 'User');
    }

    /**
     * Toggle user active status.
     */
    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        ActivityLog::log('Toggle Status User', "Status akun pengguna {$user->name} {$status}.", 'User');
        return $status;
    }
    /**
     * Update user signature.
     */
    public function updateSignature(User $user, $signatureFile)
    {
        if ($user->signature) {
            Storage::disk('local')->delete($user->signature);
        }

        $encryptedContent = Crypt::encrypt(file_get_contents($signatureFile->getRealPath()));
        $filename = \Illuminate\Support\Str::random(40) . '.enc';
        $path = 'signatures/' . $filename;
        
        Storage::disk('local')->put($path, $encryptedContent);
        $user->signature = $path;
        
        if (!$user->signature_token) {
            $user->signature_token = \Illuminate\Support\Str::uuid();
        }
        
        $user->save();
        ActivityLog::log('Update Tanda Tangan', 'User memperbarui tanda tangan digital mereka.', 'Profil');
    }
}
