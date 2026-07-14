<?php

namespace App\Traits;

use App\Models\Officer;
use App\Models\Station;
use App\Models\User;
use Illuminate\Support\Collection;

trait ScopedToJurisdiction
{
    protected function jurisdictionStationIds(?User $user = null): Collection
    {
        $user ??= auth()->user();

        if (! $user) {
            return collect();
        }

        return match ($user->role) {
            'super_admin' => Station::query()->pluck('station_id')->map(fn ($id) => (int) $id),
            'metro_head', 'district_head' => $this->headScopeStationIds($user),
            'station_oc' => $this->stationOcScopeStationIds($user),
            default => collect(),
        };
    }

    protected function primaryJurisdictionStationId(?User $user = null): ?int
    {
        return $this->jurisdictionStationIds($user)->first();
    }

    protected function ensureStationInJurisdiction(int|string|null $stationId, ?User $user = null): void
    {
        abort_unless(
            $stationId !== null && $this->jurisdictionStationIds($user)->contains((int) $stationId),
            403,
            'You are not assigned to this police jurisdiction.'
        );
    }

    private function headScopeStationIds(User $user): Collection
    {
        if (! $user->station_id) {
            return collect();
        }

        return Station::query()
            ->where('station_id', $user->station_id)
            ->orWhere('parent_id', $user->station_id)
            ->pluck('station_id')
            ->map(fn ($id) => (int) $id);
    }

    private function stationOcScopeStationIds(User $user): Collection
    {
        if ($user->station_id) {
            return collect([(int) $user->station_id]);
        }

        $stationId = Officer::query()
            ->where('user_id', $user->id)
            ->where('is_oc', true)
            ->value('station_id');

        return $stationId ? collect([(int) $stationId]) : collect();
    }
}
