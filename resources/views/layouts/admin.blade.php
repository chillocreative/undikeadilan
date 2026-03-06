@extends('layouts.app')

@section('content')
<div class="mb-6">
    <nav class="flex items-center gap-2 text-sm text-sky-300/60">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-white">Dashboard</a>
        @hasSection('breadcrumb')
            <span>/</span>
            @yield('breadcrumb')
        @endif
    </nav>
</div>

@if(session('success'))
    <div class="mb-4 rounded-xl bg-emerald-500/20 border border-emerald-400/30 px-4 py-3 text-emerald-200 text-sm">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-4 rounded-xl bg-rose-500/20 border border-rose-400/30 px-4 py-3 text-rose-200 text-sm">
        {{ session('error') }}
    </div>
@endif

@yield('admin-content')
@endsection
