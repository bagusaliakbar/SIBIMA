<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\MentoringSession;
use App\Models\Thesis;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $thesis = null;
        $p1Sessions = collect();
        $p2Sessions = collect();
        $academicStage = null;
        $dosenStats = null;

        if ($user->role === 'mahasiswa') {
            $thesis = Thesis::where('student_id', $user->id)
                ->with(['pembimbing1', 'pembimbing2', 'graduation'])
                ->first();

            if ($thesis) {
                $completedSessions = MentoringSession::where('thesis_id', $thesis->id)
                    ->where('status', 'completed')
                    ->where('is_absent', false)
                    ->orderBy('scheduled_at', 'asc')
                    ->orderBy('id', 'asc')
                    ->get(['id', 'dosen_id', 'scheduled_at', 'topic']);

                if ($thesis->pembimbing1_id) {
                    $p1Sessions = $completedSessions->where('dosen_id', $thesis->pembimbing1_id)->values();
                }
                if ($thesis->pembimbing2_id) {
                    $p2Sessions = $completedSessions->where('dosen_id', $thesis->pembimbing2_id)->values();
                }

                $academicStage = $thesis->getCurrentAcademicStage();
            }
        } elseif (in_array($user->role, ['dosen', 'kaprodi', 'admin'])) {
            $activeP1 = Thesis::where('pembimbing1_id', $user->id)->notGraduated()->count();
            $activeP2 = Thesis::where('pembimbing2_id', $user->id)->notGraduated()->count();
            $completedSessions = MentoringSession::where('dosen_id', $user->id)
                ->where('status', 'completed')
                ->where('is_absent', false)
                ->count();

            $dosenStats = [
                'active_p1' => $activeP1,
                'active_p2' => $activeP2,
                'active_total' => $activeP1 + $activeP2,
                'completed_sessions' => $completedSessions,
            ];
        }

        return view('profile.edit', [
            'user' => $user,
            'thesis' => $thesis,
            'p1Sessions' => $p1Sessions,
            'p2Sessions' => $p2Sessions,
            'academicStage' => $academicStage,
            'dosenStats' => $dosenStats,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $this->userService->updateUser($user, $request->validated(), $request->file('avatar'));

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        Auth::logout();
        
        $this->userService->deleteUser($user);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Update the user's signature.
     */
    public function updateSignature(Request $request): RedirectResponse
    {
        $request->validate([
            'signature' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:1024'],
        ]);

        if ($request->hasFile('signature')) {
            $this->userService->updateSignature($request->user(), $request->file('signature'));
        }

        return Redirect::route('profile.edit')->with('status', 'signature-updated');
    }
}
