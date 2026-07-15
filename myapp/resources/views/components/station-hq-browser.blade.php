@props(['title', 'subtitle', 'type', 'stations'])

<article class="browser" data-browser="{{ $type }}">
    <div class="browser-head">
        <div>
            <h2>{{ $title }}</h2>
            <p>{{ $subtitle }}</p>
        </div>
        <span class="count">{{ $stations->count() }} HQs</span>
    </div>

    <div class="viewport">
        @forelse($stations as $station)
            <div class="hq-card" data-card>
                <p class="eyebrow">{{ $station->head_rank ?? ($station->type === 'metropolitanHQ' ? 'Commissioner' : 'SP') }}</p>
                <h3>{{ $station->name }}</h3>
                <p class="meta">
                    {{ $station->district ?: ($station->type === 'metropolitanHQ' ? 'Metropolitan area' : 'District area') }}
                    @if($station->division)
                        &bull; {{ $station->division }}
                    @endif
                </p>
                <p class="meta">{{ $station->address ?: 'HQ address will be updated soon.' }}</p>

                <div class="stats">
                    <div class="stat">
                        <strong>{{ $station->thanas_count }}</strong>
                        <span>Thanas</span>
                    </div>
                    <div class="stat">
                        <strong>{{ $station->contact_number ? 'Yes' : 'No' }}</strong>
                        <span>Contact</span>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="slide-controls">
                        <button type="button" data-prev aria-label="Previous {{ $title }}">‹</button>
                        <span class="position" data-current>1/{{ $stations->count() }}</span>
                        <button type="button" data-next aria-label="Next {{ $title }}">›</button>
                    </div>
                    <a class="profile-link" href="{{ route('stations.show', $station) }}">View Thanas</a>
                </div>
            </div>
        @empty
            <section class="empty">
                <h3>No HQ found</h3>
                <p>Try another search keyword.</p>
            </section>
        @endforelse
    </div>
</article>
