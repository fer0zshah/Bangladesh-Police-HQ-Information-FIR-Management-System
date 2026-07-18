<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\CitizenComplaint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $complaints = null;
        $complaintSummary = null;

        if ($request->user()->role === 'citizen') {
            $query = CitizenComplaint::query()
                ->with(['station.parent', 'caseFir'])
                ->where('complainant_nid', $request->user()->nid_number)
                ->when($request->filled('search'), function ($query) use ($request) {
                    $term = '%'.$request->string('search')->toString().'%';
                    $query->where(function ($query) use ($term) {
                        $query->where('complaint_title', 'like', $term)
                            ->orWhere('description', 'like', $term)
                            ->orWhereHas('station', fn ($station) => $station->where('name', 'like', $term));
                    });
                })
                ->when(
                    in_array($request->string('status')->toString(), ['Pending', 'Under Review', 'Escalated', 'Dismissed'], true),
                    fn ($query) => $query->where('status', $request->string('status')->toString())
                )
                ->latest('submitted_date')
                ->latest('complaint_id');

            $complaints = $query->paginate(8, ['*'], 'complaints_page')->withQueryString();
            $complaints->getCollection()->each(function (CitizenComplaint $complaint): void {
                $complaint->reference = $this->reference($complaint);
                $complaint->station_name = $complaint->station?->name;
                $complaint->command_name = $complaint->station?->parent?->name;
                $complaint->case_id = $complaint->caseFir?->case_id;
                $complaint->case_title = $complaint->caseFir?->case_title;
                $complaint->case_status = $complaint->caseFir?->status;
            });

            $base = CitizenComplaint::where('complainant_nid', $request->user()->nid_number);
            $complaintSummary = [
                'total' => (clone $base)->count(),
                'open' => (clone $base)->whereIn('status', ['Pending', 'Under Review'])->count(),
                'escalated' => (clone $base)->where('status', 'Escalated')->count(),
                'dismissed' => (clone $base)->where('status', 'Dismissed')->count(),
            ];
        }

        return view('profile.edit', [
            'user' => $request->user(),
            'complaints' => $complaints,
            'complaintSummary' => $complaintSummary,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

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

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    private function reference(CitizenComplaint $complaint): string
    {
        return 'CMP-'.$complaint->submitted_date->format('Y').'-'.str_pad(
            (string) $complaint->complaint_id,
            6,
            '0',
            STR_PAD_LEFT
        );
    }
}
