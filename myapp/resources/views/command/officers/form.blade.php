<x-command-layout pageTitle="{{ $editing ? 'Edit Officer' : 'Add Officer' }}">
    <div class="mx-auto max-w-4xl">
        <section class="overflow-hidden rounded-xl border border-hq-700 bg-hq-800">
            <div class="border-b border-hq-700 bg-hq-900/30 px-6 py-5"><h2 class="text-lg font-bold text-white">{{ $editing ? 'Update personnel record' : 'Register personnel' }}</h2><p class="mt-1 text-xs text-gray-500">Assignments are restricted to active thanas under {{ $headquarters->name }}.</p></div>
            @if($errors->any())<div class="m-6 rounded-lg border border-rose-500/20 bg-rose-500/10 p-4 text-sm text-rose-300"><ul class="list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            <form method="POST" action="{{ $editing ? route('command.officers.update',$officer) : route('command.officers.store') }}">
                @csrf @if($editing) @method('PUT') @endif
                <div class="grid gap-5 p-6 sm:grid-cols-2">
                    <div class="sm:col-span-2"><label class="mb-2 block text-xs font-semibold text-gray-300">Full name</label><input name="name" required maxlength="100" value="{{ old('name',$officer->name) }}" class="h-11 w-full rounded-lg border border-hq-700 bg-hq-900 px-4 text-sm text-white outline-none focus:border-indigo-500/60"></div>
                    <div><label class="mb-2 block text-xs font-semibold text-gray-300">Badge number</label><input name="badge_number" required maxlength="20" value="{{ old('badge_number',$officer->badge_number) }}" class="h-11 w-full rounded-lg border border-hq-700 bg-hq-900 px-4 text-sm text-white outline-none"></div>
                    <div><label class="mb-2 block text-xs font-semibold text-gray-300">Rank</label><input name="rank" required maxlength="50" value="{{ old('rank',$officer->rank) }}" placeholder="Inspector, SI, Nayek, Constable..." class="h-11 w-full rounded-lg border border-hq-700 bg-hq-900 px-4 text-sm text-white outline-none"></div>
                    <div><label class="mb-2 block text-xs font-semibold text-gray-300">Thana</label><select name="station_id" required class="h-11 w-full rounded-lg border border-hq-700 bg-hq-900 px-4 text-sm text-white"><option value="">Select a thana</option>@foreach($stations as $station)<option value="{{ $station->station_id }}" @selected((string)old('station_id',$officer->station_id)===(string)$station->station_id)>{{ $station->name }}</option>@endforeach</select></div>
                    <div><label class="mb-2 block text-xs font-semibold text-gray-300">Service status</label><select name="status" required class="h-11 w-full rounded-lg border border-hq-700 bg-hq-900 px-4 text-sm text-white"><option value="Active" @selected(old('status',$officer->status)==='Active')>Active</option><option value="Inactive" @selected(old('status',$officer->status)==='Inactive')>Inactive</option></select></div>
                </div>
                <div class="flex justify-end gap-3 border-t border-hq-700 bg-hq-900/30 px-6 py-4"><a href="{{ route('command.officers.index') }}" class="rounded-lg px-5 py-2.5 text-sm font-semibold text-gray-400 hover:text-white">Cancel</a><button class="rounded-lg border border-gold-500/40 px-5 py-2.5 text-sm font-bold text-gold-400 hover:bg-gold-500/10">{{ $editing ? 'Save changes' : 'Add officer' }}</button></div>
            </form>
        </section>
    </div>
</x-command-layout>
