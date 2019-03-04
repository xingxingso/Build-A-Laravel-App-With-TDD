@extends('layouts.app')

@section('content')
    <header class="flex items-center mb-3 pb-4">
        <div class="flex justify-between items-center w-full">
            <h3 class="text-grey text-sm font-normal">My Projects</h3>
            <a href="/projects/create" class="button">New Project</a>
        </div>
    </header>

    <main class="lg:flex lg:flex-wrap -mx-3">
        @forelse ($projects as $project)
            <div class="lg:w-1/3 px-3 pb-6">
                @include ('projects.card')
            </div>
        @empty
            <div>No Project yet.</div>
        @endforelse
    </main>
@endsection
