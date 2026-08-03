@php
    use App\Models\Notice;
    /* অ্যাডমিন প্যানেল থেকে নিয়ন্ত্রিত।
       একটিও সক্রিয় নোটিশ না থাকলে পুরো বারটিই থাকে না। */
    $notice = Notice::current()->first();
@endphp

@if($notice)
    @php
        $bg = match($notice->severity) {
            'urgent'  => 'bg-red-600',
            'warning' => 'bg-amber-500',
            default   => 'bg-sky2-500',
        };
    @endphp
    <div id="notice-bar" class="{{ $bg }} text-white" data-notice-id="{{ $notice->id }}">
        <div class="container-x flex items-center gap-3 py-2.5 text-sm">
            <span class="shrink-0 hidden sm:inline-flex"><x-icon name="clock" class="w-4 h-4"/></span>
            <p class="flex-1 leading-snug">
                <strong class="font-semibold">{{ $notice->title }}</strong>
                @if($notice->body)
                    <span class="opacity-90 hidden md:inline"> — {{ $notice->body }}</span>
                @endif
            </p>
            <button type="button" id="notice-close" class="shrink-0 rounded p-1 hover:bg-white/20"
                    aria-label="{{ __('common.close') }}">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2.5" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>
    </div>
@endif
