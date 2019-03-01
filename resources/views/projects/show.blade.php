@extends('layouts.app')

@section('content')
    <header class="flex items-center mb-6">
        <div class="flex justify-between items-end w-full">
            <p class="text-grey text-sm font-normal">
                <a href="/projects" class="text-grey text-sm font-normal no-underline">My Projects</a> / {{ $project->title }}
            </p>
            <a href="/projects/create" class="button">New Project</a>
        </div>
    </header>

    <main>
        <div class="lg:flex -mx-3">
            <div class="lg:w-3/4 px-3 mb-6">
                <div class="mb-8">
                    <h2 class="text-grey text-lg font-normal mb-3">Tasks</h2>

                    {{-- Tasks --}}
                    <div class="card mb-3"> Lorem ipsum.</div>
                    <div class="card mb-3"> Lorem ipsum.</div>
                    <div class="card mb-3"> Lorem ipsum.</div>
                    <div class="card"> Lorem ipsum.</div>
                </div>

                <div>
                    <h2 class="text-grey text-lg font-normal mb-3">General Notes</h2>

                    {{-- General Notes --}}
                    <textarea class="card w-full" style="height: 200px"></textarea>
                </div>                
            </div>

            <div class="lg:w-1/4 px-3">
                @include ('projects.card')
            </div>
        </div>
    </main>
@endsection
