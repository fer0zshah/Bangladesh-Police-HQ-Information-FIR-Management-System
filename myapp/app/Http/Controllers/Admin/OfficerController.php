<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Officer;
use App\Models\Station;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OfficerController extends Controller
{
    public function index(Request $request): View
    {
        return $this->renderPage($request);
    }

    public function create(Request $request): View
    {
        abort(403, 'Officer appointments are managed by the responsible Commissioner or District SP.');
        return $this->renderPage($request, formMode: 'create');
    }

    public function store(Request $request): RedirectResponse
    {
        abort(403, 'Officer appointments are managed by the responsible Commissioner or District SP.');
        $officer = Officer::create($this->validatedOfficer($request));

        return redirect()
            ->route('admin.officers.show', $officer)
            ->with('success', 'Officer added successfully. No login account was created.');
    }

    public function show(Request $request, Officer $officer): View
    {
        $officer->load(['station', 'user'])->loadCount(['cases', 'evidence']);

        return $this->renderPage($request, selectedOfficer: $officer);
    }

    public function edit(Request $request, Officer $officer): View
    {
        abort(403, 'Officer records are managed by the responsible Commissioner or District SP.');
        $officer->load(['station', 'user']);

        return $this->renderPage($request, formMode: 'edit', editingOfficer: $officer);
    }

    public function update(Request $request, Officer $officer): RedirectResponse
    {
        abort(403, 'Officer records are managed by the responsible Commissioner or District SP.');
        $data = $this->validatedOfficer($request, $officer);
        $this->guardOcAssignment($officer, $data);

        DB::transaction(function () use ($officer, $data) {
            $officer->update($data);

            if ($officer->user) {
                $officer->user->update(['name' => $officer->name]);
            }
        });

        return redirect()
            ->route('admin.officers.show', $officer)
            ->with('success', 'Officer details updated successfully.');
    }

    public function destroy(Officer $officer): RedirectResponse
    {
        abort(403, 'Officer records are managed by the responsible Commissioner or District SP.');
        if ($officer->is_oc) {
            return back()->with('error', 'Remove OC access before deleting this officer.');
        }

        if ($officer->cases()->exists() || $officer->evidence()->exists()) {
            return back()->with('error', 'This officer has case or evidence history and cannot be deleted. Mark the officer inactive instead.');
        }

        DB::transaction(function () use ($officer) {
            $officer->update(['user_id' => null]);
            $officer->delete();
        });

        return redirect()
            ->route('admin.officers.index')
            ->with('success', 'Officer deleted. Any former user account was preserved.');
    }

    public function toggleOc(Request $request, Officer $officer): RedirectResponse
    {
        abort(403, 'OC appointments are managed by the responsible Commissioner or District SP.');
        if ($officer->is_oc) {
            return $this->removeOcAccess($officer);
        }

        if (! $officer->station_id) {
            throw ValidationException::withMessages([
                'station_id' => 'Assign this officer to a station before granting OC access.',
            ]);
        }

        if (strtolower($officer->status) !== 'active') {
            throw ValidationException::withMessages([
                'status' => 'Only an active officer can be assigned as OC.',
            ]);
        }

        if (strtolower($officer->station->status) !== 'active') {
            throw ValidationException::withMessages([
                'station_id' => 'OC access cannot be assigned at an inactive station.',
            ]);
        }

        $account = $request->validate([
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($officer->user_id),
            ],
            'phone' => ['nullable', 'string', 'max:15'],
            'password' => [
                Rule::requiredIf($officer->user_id === null),
                'nullable',
                'confirmed',
                'min:8',
            ],
        ]);

        DB::transaction(function () use ($officer, $account) {
            $otherOcExists = Officer::query()
                ->where('station_id', $officer->station_id)
                ->where('is_oc', true)
                ->where('officer_id', '!=', $officer->getKey())
                ->lockForUpdate()
                ->exists();

            if ($otherOcExists) {
                throw ValidationException::withMessages([
                    'station_id' => 'This station already has an OC. Remove that assignment first.',
                ]);
            }

            $userData = [
                'name' => $officer->name,
                'email' => $account['email'],
                'phone' => $account['phone'] ?? null,
                'role' => 'station_oc',
                'station_id' => $officer->station_id,
                'officer_id' => $officer->officer_id,
            ];

            if (! empty($account['password'])) {
                $userData['password'] = $account['password'];
            }

            if ($officer->user) {
                $officer->user->update($userData);
                $user = $officer->user;
            } else {
                $user = User::create($userData);
            }

            $officer->update([
                'user_id' => $user->id,
                'is_oc' => true,
            ]);
        });

        return redirect()
            ->route('admin.officers.show', $officer)
            ->with('success', 'OC access assigned. Share the credentials through your approved secure channel.');
    }

    private function removeOcAccess(Officer $officer): RedirectResponse
    {
        DB::transaction(function () use ($officer) {
            $officer->update(['is_oc' => false]);

            if ($officer->user) {
                $officer->user->update([
                    'role' => 'citizen',
                    'station_id' => null,
                    'officer_id' => null,
                ]);
            }
        });

        return redirect()
            ->route('admin.officers.show', $officer)
            ->with('success', 'OC access removed. The linked user account was preserved with citizen access.');
    }

    private function renderPage(
        Request $request,
        ?string $formMode = null,
        ?Officer $editingOfficer = null,
        ?Officer $selectedOfficer = null,
    ): View {
        $officers = Officer::query()
            ->with(['station', 'user'])
            ->withCount('cases')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();

                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('badge_number', 'like', "%{$search}%")
                        ->orWhere('rank', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('station_id'), fn ($query) => $query->where('station_id', $request->integer('station_id')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->when($request->get('oc') === 'yes', fn ($query) => $query->where('is_oc', true))
            ->when($request->get('oc') === 'no', fn ($query) => $query->where('is_oc', false))
            ->latest('officer_id')
            ->paginate(15)
            ->withQueryString();

        $stations = Station::query()->orderBy('name')->get();
        $summary = [
            'total' => Officer::count(),
            'active' => Officer::whereRaw('LOWER(status) = ?', ['active'])->count(),
            'oc' => Officer::where('is_oc', true)->count(),
            'unassigned' => Officer::whereNull('station_id')->count(),
        ];
        $canManageOfficers = false;

        return view('admin.officers', compact(
            'officers',
            'stations',
            'summary',
            'formMode',
            'editingOfficer',
            'selectedOfficer',
            'canManageOfficers',
        ));
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedOfficer(Request $request, ?Officer $officer = null): array
    {
        return $request->validate([
            'station_id' => ['nullable', 'integer', 'exists:stations,station_id'],
            'name' => ['required', 'string', 'max:100'],
            'badge_number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('officers', 'badge_number')
                    ->ignore($officer?->getKey(), 'officer_id'),
            ],
            'rank' => ['required', 'string', 'max:50'],
            'status' => ['required', 'string', 'in:Active,Inactive'],
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function guardOcAssignment(Officer $officer, array $data): void
    {
        if (! $officer->is_oc) {
            return;
        }

        if (($data['status'] ?? null) !== 'Active') {
            throw ValidationException::withMessages([
                'status' => 'Remove OC access before marking this officer inactive.',
            ]);
        }

        if (empty($data['station_id'])) {
            throw ValidationException::withMessages([
                'station_id' => 'An OC must remain assigned to a station.',
            ]);
        }

        $station = Station::find($data['station_id']);

        if (! $station || strtolower($station->status) !== 'active') {
            throw ValidationException::withMessages([
                'station_id' => 'An OC must be assigned to an active station.',
            ]);
        }

        $otherOcExists = Officer::query()
            ->where('station_id', $data['station_id'])
            ->where('is_oc', true)
            ->where('officer_id', '!=', $officer->getKey())
            ->exists();

        if ($otherOcExists) {
            throw ValidationException::withMessages([
                'station_id' => 'The selected station already has an OC.',
            ]);
        }
    }
}
