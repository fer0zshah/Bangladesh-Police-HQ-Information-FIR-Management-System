<x-app-layout>
    <link href="https://api.mapbox.com/mapbox-gl-js/v3.25.0/mapbox-gl.css" rel="stylesheet">
    <style>
        .complaint-station-grid {
            display: grid;
            gap: 1.5rem;
            align-items: stretch;
        }

        .station-map-panel {
            position: relative;
            min-height: 420px;
            overflow: hidden;
        }

        .station-map-canvas,
        .station-map-shade,
        .station-map-intro {
            position: absolute;
            inset: 0;
        }

        .station-map-intro {
            z-index: 20;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .station-map-intro.is-hidden {
            display: none;
        }

        @media (min-width: 1024px) {
            .complaint-station-grid {
                grid-template-columns: minmax(0, .9fr) minmax(420px, 1.1fr);
            }
        }
    </style>

    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Submit a Complaint</h2>
            <p class="mt-1 text-sm text-gray-500">Send a preliminary complaint directly to the responsible police thana.</p>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-50 py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6">
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <header class="border-b border-slate-200 bg-sky-50 px-6 py-5">
                    <p class="text-xs font-bold uppercase tracking-[.2em] text-sky-700">Citizen complaint form</p>
                    <h3 class="mt-2 text-xl font-bold text-slate-900">Tell the station what happened</h3>
                    <p class="mt-2 text-sm text-slate-500">Your registered name and NID will be attached automatically.</p>
                </header>

                <form method="POST" action="{{ route('citizen.complaints.store') }}" class="space-y-6 p-6">
                    @csrf
                    <div class="grid gap-4 rounded-xl border border-slate-200 bg-slate-50 p-4 sm:grid-cols-2">
                        <div><p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Complainant</p><p class="mt-1 font-semibold text-slate-800">{{ auth()->user()->name }}</p></div>
                        <div><p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Registered NID</p><p class="mt-1 font-semibold text-slate-800">{{ auth()->user()->nid_number }}</p></div>
                    </div>

                    <div>
                        <label for="complaint_title" class="mb-2 block text-sm font-semibold text-slate-700">Complaint title</label>
                        <input id="complaint_title" name="complaint_title" value="{{ old('complaint_title') }}" minlength="5" maxlength="150" required placeholder="Example: Mobile phone stolen at New Market" class="h-12 w-full rounded-xl border-slate-300 px-4 text-sm">
                        @error('complaint_title')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="complaint-station-grid">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                        <h4 class="text-sm font-bold text-slate-800">Select the responsible police thana</h4>
                        <p class="mt-1 text-xs text-slate-500">Choose division, district and then the police station.</p>
                        <div class="mt-5 grid gap-4">
                            <div>
                                <label for="division" class="mb-2 block text-sm font-semibold text-slate-700">1. Division</label>
                                <select id="division" name="division" required class="h-12 w-full rounded-xl border-slate-300 bg-white text-sm">
                                    <option value="">Select division</option>
                                    @foreach($divisions as $division)<option value="{{ $division }}" @selected(old('division') === $division)>{{ $division }}</option>@endforeach
                                </select>
                                @error('division')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="district" class="mb-2 block text-sm font-semibold text-slate-700">2. District</label>
                                <select id="district" name="district" required disabled class="h-12 w-full rounded-xl border-slate-300 bg-white text-sm disabled:bg-slate-100"><option value="">Select division first</option></select>
                                @error('district')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="station_id" class="mb-2 block text-sm font-semibold text-slate-700">3. Police thana</label>
                                <select id="station_id" name="station_id" required disabled class="h-12 w-full rounded-xl border-slate-300 bg-white text-sm disabled:bg-slate-100"><option value="">Select district first</option></select>
                                @error('station_id')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <p id="station-message" class="mt-3 hidden text-xs font-semibold"></p>
                        </div>

                        <section class="station-map-panel rounded-2xl border border-slate-200 bg-slate-100" aria-labelledby="station-map-title">
                            <div id="station-map" class="station-map-canvas transition duration-500" style="filter: blur(5px); transform: scale(1.025);"></div>
                            <div id="map-shade" class="station-map-shade pointer-events-none z-10 bg-white/55 backdrop-blur-[1px] transition duration-500"></div>

                            <div id="map-intro" class="station-map-intro">
                                <div class="max-w-sm rounded-2xl border border-white/80 bg-white/95 p-6 text-center shadow-xl">
                                    <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-sky-50 text-sky-700">
                                        <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 21s6-5.1 6-11a6 6 0 1 0-12 0c0 5.9 6 11 6 11Z"/><circle cx="12" cy="10" r="2.2"/></svg>
                                    </span>
                                    <h4 id="station-map-title" class="mt-4 text-lg font-bold text-slate-900">Find the nearest police thana</h4>
                                    <p class="mt-2 text-sm leading-6 text-slate-500">Allow location access to compare your position with mapped stations in the Dhaka and Khulna demonstration.</p>
                                    <button id="find-nearest-station" type="button" class="mt-5 inline-flex h-11 items-center justify-center rounded-xl border border-sky-300 bg-white px-5 text-sm font-bold text-sky-700 shadow-sm transition hover:border-sky-500 hover:bg-sky-50">
                                        Find Nearest Station
                                    </button>
                                    <p id="map-error" class="mt-3 hidden text-xs font-semibold text-red-600"></p>
                                </div>
                            </div>

                            <div id="nearest-result" class="absolute bottom-4 left-4 right-4 z-20 hidden rounded-xl border border-sky-200 bg-white/95 px-4 py-3 shadow-lg backdrop-blur">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-[11px] font-bold uppercase tracking-[.16em] text-sky-700">Nearest mapped thana</p>
                                        <p id="nearest-name" class="mt-1 text-sm font-bold text-slate-900"></p>
                                        <p id="nearest-distance" class="mt-1 text-xs text-slate-500"></p>
                                    </div>
                                    <button id="recheck-location" type="button" class="shrink-0 rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50">Recheck</button>
                                </div>
                            </div>
                        </section>
                    </div>

                    <div>
                        <label for="description" class="mb-2 block text-sm font-semibold text-slate-700">Incident description</label>
                        <textarea id="description" name="description" rows="7" minlength="15" maxlength="255" required class="w-full rounded-xl border-slate-300 text-sm leading-6" placeholder="Describe what happened, when and where it happened.">{{ old('description') }}</textarea>
                        @error('description')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex justify-end gap-3 border-t border-slate-200 pt-5">
                        <a href="{{ route('profile.edit') }}" class="inline-flex h-11 items-center rounded-xl border border-slate-300 px-5 text-sm font-semibold text-slate-600">Cancel</a>
                        <button class="h-11 rounded-xl border border-sky-200 bg-sky-50 px-6 text-sm font-bold text-sky-700 hover:bg-sky-100">Submit complaint</button>
                    </div>
                </form>
            </section>
        </div>
    </div>

    <script src="https://api.mapbox.com/mapbox-gl-js/v3.25.0/mapbox-gl.js"></script>
    <script>
        const division=document.getElementById('division'),district=document.getElementById('district'),station=document.getElementById('station_id'),message=document.getElementById('station-message');
        const districtUrl=@json(route('citizen.station-options.districts')),thanaUrl=@json(route('citizen.station-options.thanas'));
        const mapStationsUrl=@json(route('citizen.station-options.map')),mapboxToken=@json($mapboxToken);
        const oldDistrict=@json(old('district')),oldStation=@json((string)old('station_id'));
        const options=(select,label,rows,key,text)=>{select.innerHTML='';select.append(new Option(label,''));rows.forEach(row=>select.append(new Option(text(row),row[key])))};
        async function loadThanas(selected=''){station.disabled=true;options(station,'Loading stations...',[],'station_id',r=>r.name);if(!division.value||!district.value)return;try{const response=await fetch(`${thanaUrl}?${new URLSearchParams({division:division.value,district:district.value})}`,{headers:{Accept:'application/json'}});if(!response.ok)throw new Error();const rows=await response.json();options(station,rows.length?'Select police thana':'No station found',rows,'station_id',r=>r.command_name?`${r.name} (${r.command_name})`:r.name);station.disabled=!rows.length;if(selected)station.value=selected;message.classList.add('hidden')}catch(e){message.textContent='Station options could not be loaded.';message.className='mt-3 text-xs font-semibold text-red-600'}}
        async function loadDistricts(selected='',selectedStation=''){district.disabled=true;station.disabled=true;options(district,'Loading districts...',[],'district',r=>r.district);options(station,'Select district first',[],'station_id',r=>r.name);if(!division.value)return;try{const response=await fetch(`${districtUrl}?${new URLSearchParams({division:division.value})}`,{headers:{Accept:'application/json'}});if(!response.ok)throw new Error();const rows=await response.json();options(district,rows.length?'Select district':'No district found',rows,'district',r=>r.district);district.disabled=!rows.length;if(selected){district.value=selected;if(district.value)await loadThanas(selectedStation)}}catch(e){message.textContent='District options could not be loaded.';message.className='mt-3 text-xs font-semibold text-red-600'}}
        division.addEventListener('change',()=>loadDistricts());district.addEventListener('change',()=>loadThanas());if(division.value)loadDistricts(oldDistrict,oldStation);

        const mapElement=document.getElementById('station-map'),mapIntro=document.getElementById('map-intro'),mapShade=document.getElementById('map-shade');
        const mapError=document.getElementById('map-error'),nearestResult=document.getElementById('nearest-result');
        const findButton=document.getElementById('find-nearest-station'),recheckButton=document.getElementById('recheck-location');
        let stationMap,mapRows=[],mapMarkers=[];

        function showMapError(text){
            mapError.textContent=text;
            mapError.classList.remove('hidden');
            findButton.disabled=false;
            findButton.textContent='Try Again';
        }

        function distanceKm(origin,place){
            const radians=value=>value*Math.PI/180;
            const earthRadius=6371;
            const latitudeDelta=radians(place.latitude-origin.latitude);
            const longitudeDelta=radians(place.longitude-origin.longitude);
            const value=Math.sin(latitudeDelta/2)**2+
                Math.cos(radians(origin.latitude))*Math.cos(radians(place.latitude))*Math.sin(longitudeDelta/2)**2;
            return earthRadius*2*Math.atan2(Math.sqrt(value),Math.sqrt(1-value));
        }

        function initializeMap(){
            if(stationMap)return stationMap;
            mapboxgl.accessToken=mapboxToken;
            stationMap=new mapboxgl.Map({
                container:'station-map',
                style:'mapbox://styles/mapbox/standard',
                center:[90.10,23.45],
                zoom:6.25,
                attributionControl:true
            });
            stationMap.addControl(new mapboxgl.NavigationControl({showCompass:false}),'top-right');
            return stationMap;
        }

        async function chooseMappedStation(mappedStation){
            division.value=mappedStation.division;
            await loadDistricts(mappedStation.district,String(mappedStation.station_id));
            message.textContent=`Selected from map: ${mappedStation.name}`;
            message.className='mt-3 text-xs font-semibold text-sky-700';
            document.getElementById('nearest-name').textContent=mappedStation.name;
            document.getElementById('nearest-distance').textContent=
                `${mappedStation.distance.toFixed(2)} km away · ${mappedStation.district}, ${mappedStation.division}`;
            nearestResult.classList.remove('hidden');
        }

        function stationMarkerElement(isNearest=false){
            const marker=document.createElement('button');
            marker.type='button';
            marker.setAttribute('aria-label',isNearest?'Nearest police thana':'Police thana');
            marker.style.cssText=`width:${isNearest?30:24}px;height:${isNearest?30:24}px;border-radius:999px;border:3px solid white;background:${isNearest?'#f59e0b':'#0284c7'};box-shadow:0 4px 14px rgba(15,23,42,.35);cursor:pointer`;
            return marker;
        }

        function renderStations(origin){
            mapMarkers.forEach(marker=>marker.remove());
            mapMarkers=[];
            const nearestStations=mapRows
                .map(row=>({...row,distance:distanceKm(origin,row)}))
                .sort((left,right)=>left.distance-right.distance)
                .slice(0,5);
            if(!nearestStations.length)throw new Error('No mapped stations are available yet.');

            const bounds=new mapboxgl.LngLatBounds([origin.longitude,origin.latitude],[origin.longitude,origin.latitude]);
            const citizenMarker=new mapboxgl.Marker({color:'#0f172a'})
                .setLngLat([origin.longitude,origin.latitude])
                .setPopup(new mapboxgl.Popup({offset:18}).setText('Your current location'))
                .addTo(stationMap);
            mapMarkers.push(citizenMarker);

            nearestStations.forEach((row,index)=>{
                const popup=document.createElement('div');
                const popupName=document.createElement('strong');
                popupName.textContent=row.name;
                const popupDistance=document.createElement('p');
                popupDistance.textContent=`${row.distance.toFixed(2)} km away`;
                popupDistance.style.margin='4px 0 0';
                const popupAddress=document.createElement('small');
                popupAddress.textContent=row.address??'Address not listed';
                popup.append(popupName,popupDistance,popupAddress);
                const select=document.createElement('button');
                select.type='button';
                select.textContent='Select this thana';
                select.style.cssText='display:block;margin-top:8px;border:1px solid #7dd3fc;border-radius:7px;background:white;padding:5px 8px;color:#0369a1;font-weight:700;cursor:pointer';
                select.addEventListener('click',()=>chooseMappedStation(row));
                popup.append(select);
                const marker=new mapboxgl.Marker({element:stationMarkerElement(index===0)})
                    .setLngLat([row.longitude,row.latitude])
                    .setPopup(new mapboxgl.Popup({offset:20}).setDOMContent(popup))
                    .addTo(stationMap);
                mapMarkers.push(marker);
                bounds.extend([row.longitude,row.latitude]);
            });

            stationMap.fitBounds(bounds,{padding:65,maxZoom:13,duration:1100});
            chooseMappedStation(nearestStations[0]);
        }

        async function findNearestStation(){
            mapError.classList.add('hidden');
            if(!mapboxToken){
                showMapError('Mapbox token is missing. Add MAPBOX_PUBLIC_TOKEN to PHQ .env.');
                return;
            }
            if(!window.mapboxgl){
                showMapError('The map library could not be loaded. Check the internet connection.');
                return;
            }
            if(!navigator.geolocation){
                showMapError('This browser does not support location access.');
                return;
            }

            findButton.disabled=true;
            findButton.textContent='Finding your location...';
            try{
                if(!mapRows.length){
                    const response=await fetch(mapStationsUrl,{headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'}});
                    if(!response.ok)throw new Error('Mapped stations could not be loaded.');
                    mapRows=(await response.json()).stations??[];
                }
                if(!mapRows.length)throw new Error('No mapped stations are available. Run the station coordinate seeder.');

                const map=initializeMap();
                const position=await new Promise((resolve,reject)=>
                    navigator.geolocation.getCurrentPosition(resolve,reject,{enableHighAccuracy:true,timeout:12000,maximumAge:60000})
                );
                const origin={latitude:position.coords.latitude,longitude:position.coords.longitude};
                map.once('load',()=>renderStations(origin));
                if(map.loaded())renderStations(origin);
                mapElement.style.filter='none';
                mapElement.style.transform='none';
                mapShade.style.opacity='0';
                mapShade.style.visibility='hidden';
                mapIntro.classList.add('is-hidden');
                findButton.disabled=false;
                findButton.textContent='Find Nearest Station';
            }catch(error){
                const denied=error?.code===1;
                showMapError(denied?'Location permission was denied. Allow location access and try again.':(error.message||'Your location could not be detected.'));
            }
        }

        findButton.addEventListener('click',findNearestStation);
        recheckButton.addEventListener('click',()=>{
            nearestResult.classList.add('hidden');
            findButton.disabled=false;
            findButton.textContent='Find Nearest Station';
            mapIntro.classList.remove('is-hidden');
            findNearestStation();
        });
        if(mapboxToken&&window.mapboxgl)initializeMap();
    </script>
</x-app-layout>
