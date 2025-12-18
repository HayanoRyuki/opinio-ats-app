{{-- resources/views/components/onboarding-guide.blade.php --}}

<div class="mb-10">
    <h2 class="text-xl font-bold mb-6">はじめに行う3ステップ</h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- =====================
            STEP 1
        ===================== --}}
        @php
            $step1Done = $onboarding['step1'];
        @endphp
        <div class="rounded-2xl p-6 transition
            {{ $step1Done ? 'bg-gray-200 opacity-60' : 'bg-green-500 text-black' }}">

            <div class="text-lg font-semibold mb-2">Step1</div>
            <h3 class="text-xl font-bold mb-4">会社情報を登録</h3>

            <div class="mb-4">
                <img src="{{ asset('images/onboarding/company.png') }}"
                     alt="会社情報"
                     class="w-full h-44 object-contain">
            </div>

            <p class="text-sm mb-6 leading-relaxed">
                まずは会社名や所在地など、基本的な会社情報を登録します。
                採用活動の土台となる重要なステップです。
            </p>

            @if($step1Done)
                <button
                    class="w-full py-4 rounded-full bg-gray-400 text-gray-700 font-bold cursor-default"
                    disabled>
                    完了
                </button>
            @else
                <a href="{{ route('company.edit') }}"
                   class="block text-center w-full py-4 rounded-full bg-yellow-200 font-bold">
                    会社情報の登録
                </a>
            @endif
        </div>

        {{-- =====================
            STEP 2
        ===================== --}}
        @php
            $step2Done = $onboarding['step2'];
        @endphp
        <div class="rounded-2xl p-6 transition
            {{ $step2Done ? 'bg-gray-200 opacity-60' : 'bg-green-500 text-black' }}">

            <div class="text-lg font-semibold mb-2">Step2</div>
            <h3 class="text-xl font-bold mb-4">職種を登録</h3>

            <div class="mb-4">
                <img src="{{ asset('images/onboarding/job-role.png') }}"
                     alt="職種登録"
                     class="w-full h-44 object-contain">
            </div>

            <p class="text-sm mb-6 leading-relaxed">
                求人作成の前に、募集する職種を登録します。
                職種はあとから追加・編集できます。
            </p>

            @if($step2Done)
                <button
                    class="w-full py-4 rounded-full bg-gray-400 text-gray-700 font-bold cursor-default"
                    disabled>
                    完了
                </button>
            @else
                <a href="{{ route('ats.job_roles.index') }}"
                   class="block text-center w-full py-4 rounded-full bg-yellow-200 font-bold">
                    職種の登録
                </a>
            @endif
        </div>

        {{-- =====================
            STEP 3
        ===================== --}}
        @php
            $step3Done = $onboarding['step3'];
            $step3Enabled = $onboarding['step2'];
        @endphp
        <div class="rounded-2xl p-6 transition
            {{ $step3Done ? 'bg-gray-200 opacity-60' : ($step3Enabled ? 'bg-green-500 text-black' : 'bg-gray-300 opacity-40') }}">

            <div class="text-lg font-semibold mb-2">Step3</div>
            <h3 class="text-xl font-bold mb-4">求人を登録</h3>

            <div class="mb-4">
                <img src="{{ asset('images/onboarding/job.png') }}"
                     alt="求人登録"
                     class="w-full h-44 object-contain">
            </div>

            <p class="text-sm mb-6 leading-relaxed">
                職種をもとに、最初の求人を作成します。
                作成後は応募者の管理が可能になります。
            </p>

            @if($step3Done)
                <button
                    class="w-full py-4 rounded-full bg-gray-400 text-gray-700 font-bold cursor-default"
                    disabled>
                    完了
                </button>
            @elseif($step3Enabled)
                <a href="{{ route('jobs.create') }}"
                   class="block text-center w-full py-4 rounded-full bg-yellow-200 font-bold">
                    求人の登録
                </a>
            @else
                <button
                    class="w-full py-4 rounded-full bg-gray-400 text-gray-600 font-bold cursor-not-allowed"
                    disabled>
                    Step2 完了後に進めます
                </button>
            @endif
        </div>

    </div>
</div>
