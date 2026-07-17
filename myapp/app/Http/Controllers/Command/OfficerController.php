<?php

namespace App\Http\Controllers\Command;

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
        $headquarters = $this->headquarters();
        $stationIds = $this->stationIds($headquarters);

        $officers = Officer::query()
            ->with(['station', 'user'])
            ->withCount(['cases', 'evidence'])
            ->whereIn('station_id', $stationIds)
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = $request->string('search')->toString();
                $query->where(function ($query) use ($term) {
                    $query->where('name', 'like', "%{$term}%")
                        ->orWhere('badge_number', 'like', "%{$term}%")
                        ->orWhere('rank', 'like', "%{$term}%");
                });
            })
            ->when($request->filled('station_id'), fn ($query) => $query->where('station_id', $request->integer('station_id')))
            ->when($request->filled('rank'), fn ($query) => $query->where('rank', $request->string('rank')->toString()))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->orderByDesc('is_oc')
            ->orderBy('rank')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $stations = Station::query()->whereIn('station_id', $stationIds)->orderBy('name')->get();
        $summaryQuery = Officer::query()->whereIn('station_id', $stationIds);
        $summary = [
            'total' => (clone $summaryQuery)->count(),
            'active' => (clone $summaryQuery)->whereRaw('LOWER(status) = ?', ['active'])->count(),
            'oc' => (clone $summaryQuery)->where('is_oc', true)->count(),
            'constables' => (clone $summaryQuery)->whereRaw('LOWER(rank) LIKE ?', ['%constable%'])->count(),
        ];
        $ranks = Officer::query()
            ->whereIn('station_id', $stationIds)
            ->whereNotNull('rank')
            ->distinct()
            ->orderBy('rank')
            ->pluck('rank');

        return view('command.officers.index', compact(
            'headquarters',
            'officers',
            'stations',
            'summary',
            'ranks',
        ));
    }

    public function create(): View
    {
        return view('command.officers.form', [
            'headquarters' => $this->headquarters(),
            'officer' => new Officer(['status' => 'Active']),
            'stations' => $this->stations(),
            'editing' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $officer = Officer::query()->create($this->validatedOfficer($request));

        return redirect()->route('command.officers.show', $officer)
            ->with('success', 'Officer added to your command.');
    }

    public function show(Officer $officer): View
    {
        $this->ensureOfficerInCommand($officer);
        $officer->load(['station', 'user'])->loadCount(['cases', 'evidence']);

        return view('command.officers.show', [
            'headquarters' => $this->headquarters(),
            'officer' => $officer,
        ]);
    }

    public function edit(Officer $officer): View
    {
        $this->ensureOfficerInCommand($officer);

        return view('command.officers.form', [
            'headquarters' => $this->headquarters(),
            'officer' => $officer,
            'stations' => $this->stations(),
            'editing' => true,
        ]);
    }

    public function update(Request $request, Officer $officer): RedirectResponse
    {
        $this->ensureOfficerInCommand($officer);
        $data = $this->validatedOfficer($request, $officer);
        $this->guardOcAssignment($officer, $data);

        DB::transaction(function () use ($officer, $data) {
            $officer->update($data);
            $officer->user?->update([
                'name' => $officer->name,
                'station_id' => $officer->station_id,
            ]);
        });

        return redirect()->route('command.officers.show', $officer)
            ->with('success', 'Officer details updated.');
    }

    public function toggleOc(Request $request, Officer $officer): RedirectResponse
    {
        $this->ensureOfficerInCommand($officer);

        if ($officer->is_oc) {
            DB::transaction(function () use ($officer) {
                $officer->update(['is_oc' => false]);
                $officer->user?->update([
                    'role' => 'citizen',
                    'station_id' => null,
                    'officer_id' => null,
                ]);
            });

            return redirect()->route('command.officers.show', $officer)
                ->with('success', 'OC access removed; the user account was preserved.');
        }

        if (! $officer->station_id || strtolower($officer->status) !== 'active') {
            throw ValidationException::withMessages([
                'officer' => 'Only an active officer assigned to one of your thanas can become OC.',
            ]);
        }

        $account = $request->validate([
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($officer->user_id)],
            'phone' => ['nullable', 'string', 'max:15'],
            'password' => [Rule::requiredIf($officer->user_id === null), 'nullable', 'confirmed', 'min:8'],
        ]);

        DB::transaction(function () use ($officer, $account) {
            $otherOc = Officer::query()
                ->where('station_id', $officer->station_id)
                ->where('is_oc', true)
                ->where('officer_id', '!=', $officer->getKey())
                ->lockForUpdate()
                ->exists();

            if ($otherOc) {
                throw ValidationException::withMessages([
                    'officer' => 'This thana already has an OC. Remove that assignment first.',
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
                $user = User::query()->create($userData);
            }

            $officer->update(['user_id' => $user->id, 'is_oc' => true]);
        });

        return redirect()->route('command.officers.show', $officer)
            ->with('success', 'OC access assigned successfully.');
    }

    private function validatedOfficer(Request $request, ?Officer $officer = null): array
    {
        $stationIds = $this->stationIds($this->headquarters())->all();

        return $request->validate([
            'station_id' => ['required', 'integer', Rule::in($stationIds)],
            'name' => ['required', 'string', 'max:100'],
            'badge_number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('officers', 'badge_number')->ignore($officer?->getKey(), 'officer_id'),
            ],
            'rank' => ['required', 'string', 'max:50'],
            'status' => ['required', 'string', 'in:Active,Inactive'],
        ]);
    }

    private function guardOcAssignment(Officer $officer, array $data): void
    {
        if (! $officer->is_oc) {
            return;
        }

        if ($data['status'] !== 'Active' || (int) $data['station_id'] !== (int) $officer->station_id) {
            throw ValidationException::withMessages([
                'officer' => 'Remove OC access before changing this officer’s station or active status.',
            ]);
        }
    }

    private function ensureOfficerInCommand(Officer $officer): void
    {
        abort_unless(
            $this->stationIds($this->headquarters())->contains((int) $officer->station_id),
            403,
            'This officer is outside your command.'
        );
    }

    private function headquarters(): Station
    {
        return Station::query()->findOrFail(auth()->user()->station_id);
    }

    private function stationIds(Station $headquarters)
    {
        return Station::query()
            ->where('parent_id', $headquarters->station_id)
            ->where('type', 'thana')
            ->pluck('station_id')
            ->map(fn ($id) => (int) $id);
    }

    private function stations()
    {
        return Station::query()
            ->whereIn('station_id', $this->stationIds($this->headquarters()))
            ->whereRaw('LOWER(status) = ?', ['active'])
            ->orderBy('name')
            ->get();
    }
}
