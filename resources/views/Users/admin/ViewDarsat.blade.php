@extends('Users.admin.cover')

@section('content')

<div class="p-6">

    <h2 class="mb-6 text-3xl font-bold">
        Darsat Library
    </h2>

    <!-- Teachers Tabs -->
    <div class="flex flex-wrap gap-3 mb-8">

        @foreach($users as $index=>$user)

            <!-- <button
                class="teacher-tab px-5 py-3 rounded-xl bg-primary text-white font-semibold"
                data-tab="teacher{{ $user->id }}"
            >
                {{ $user->firstname }} {{ $user->lastname }}
            </button> -->
            <button
                class="teacher-tab px-5 py-3 rounded-xl bg-primary text-white font-semibold"
                data-tab="teacher{{ $user->id }}"
            >
                {{ $user->firstname }} {{ $user->lastname }}

                <span class="ml-2 px-2 py-1 text-xs bg-black text-primary rounded-full">
                    {{ $user->darsat_count }}
                </span>
            </button>

        @endforeach

    </div>

    <!-- Lessons -->
    @foreach($users as $index=>$user)

        <div
            id="teacher{{ $user->id }}"
            class="teacher-content {{ $index!=0 ? 'hidden' : '' }}"
        >

            @forelse($darsat[$user->id] ?? [] as $lesson)

                <div class="p-5 mb-5 bg-white rounded-2xl shadow dark:bg-darker">

                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between ">

                        <div> 

                            <h3 class="text-xl font-bold dark:text-gray-400">
                                {{ $lesson->title }}
                            </h3>

                            <p class="text-gray-500">
                                {{ $lesson->type }}
                            </p>

                        </div>

                        <div class="flex items-center gap-3">

                            <audio
                                id="audio{{ $lesson->id }}"
                                src="{{ asset('uploads/audio/'.$lesson->audio) }}"
                            ></audio>

                            <button
                                class="play-btn px-5 py-2 text-white bg-green-600 rounded-xl"
                                data-audio="audio{{ $lesson->id }}"
                            >
                                ▶ Play
                            </button>

                        </div>

                    </div>

                </div>

            @empty

                <div class="p-8 text-center bg-white rounded-xl shadow">

                    <h4 class="text-lg font-semibold">
                        No lessons uploaded.
                    </h4>

                </div>

            @endforelse

        </div>

    @endforeach

</div>

<script>

// Teacher Tabs
const tabs=document.querySelectorAll('.teacher-tab');
const contents=document.querySelectorAll('.teacher-content');

tabs.forEach(tab=>{

    tab.addEventListener('click',()=>{

        contents.forEach(c=>c.classList.add('hidden'));

        document
            .getElementById(tab.dataset.tab)
            .classList.remove('hidden');

    });

});

// Play Button
document.querySelectorAll('.play-btn').forEach(btn=>{

    btn.addEventListener('click',()=>{

        let audio=document.getElementById(btn.dataset.audio);

        if(audio.paused){

            document.querySelectorAll('audio').forEach(a=>{

                if(a!=audio){

                    a.pause();
                    a.currentTime=0;

                }

            });

            audio.play();

            btn.innerHTML="⏸ Pause";

        }else{

            audio.pause();

            btn.innerHTML="▶ Play";

        }

        audio.onended=function(){

            btn.innerHTML="▶ Play";

        };

    });

});

</script>

@endsection