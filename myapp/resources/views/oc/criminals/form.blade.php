<x-oc-layout :pageTitle="$criminal ? 'Edit Criminal Profile' : 'Add Criminal'">
<div class="mx-auto max-w-3xl">@if($errors->any())<div class="mb-5 rounded-xl border border-rose-500/20 bg-rose-500/10 p-4 text-sm text-rose-300"><ul class="list-inside list-disc">@foreach($errors->all() as $error)<li>{{$error}}</li>@endforeach</ul></div>@endif
<section class="rounded-xl border border-hq-700 bg-hq-800 shadow-xl"><header class="border-b border-hq-700 px-6 py-4"><h2 class="font-semibold text-white">{{$criminal?'Update registry profile':'Register a new criminal'}}</h2></header>
<form method="POST" action="{{$criminal?route('oc.criminals.update',$criminal):route('oc.criminals.store')}}" class="grid gap-5 p-6 sm:grid-cols-2">@csrf @if($criminal)@method('PUT')@endif
<label class="text-xs font-semibold text-gray-400 sm:col-span-2">Full name<input name="name" required maxlength="100" value="{{old('name',$criminal?->name)}}" class="mt-2 h-11 w-full rounded-lg border border-hq-600 bg-hq-900 px-3 text-sm text-white"></label>
<label class="text-xs font-semibold text-gray-400">Alias<input name="alias" maxlength="100" value="{{old('alias',$criminal?->alias)}}" class="mt-2 h-11 w-full rounded-lg border border-hq-600 bg-hq-900 px-3 text-sm text-white"></label>
<label class="text-xs font-semibold text-gray-400">NID number<input name="nid_number" maxlength="20" value="{{old('nid_number',$criminal?->nid_number)}}" class="mt-2 h-11 w-full rounded-lg border border-hq-600 bg-hq-900 px-3 text-sm text-white"></label>
<label class="text-xs font-semibold text-gray-400 sm:col-span-2">Date of birth<input type="date" name="date_of_birth" value="{{old('date_of_birth',$criminal?->date_of_birth)}}" class="mt-2 h-11 w-full rounded-lg border border-hq-600 bg-hq-900 px-3 text-sm text-white"></label>
<div class="flex justify-end gap-3 border-t border-hq-700 pt-5 sm:col-span-2"><a href="{{$criminal?route('oc.criminals.show',$criminal):route('oc.criminals.index')}}" class="px-5 py-2.5 text-sm text-gray-400">Cancel</a><button class="rounded-lg bg-gold-500 px-5 py-2.5 text-sm font-bold text-hq-900">Save profile</button></div>
</form></section></div>
</x-oc-layout>
