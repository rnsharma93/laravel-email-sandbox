@extends('email-sandbox::layouts.app')

@section('content')
    <div class="flex flex-col h-full bg-white relative">
        <!-- Header -->
        <header
            class="h-16 flex justify-between items-center px-6 sm:px-8 border-b border-gray-100 shadow-sm bg-white/80 backdrop-blur shrink-0 z-20">
            <div class="flex items-center gap-2 sm:gap-3">
                <button @click="sidebarOpen = true"
                    class="lg:hidden p-2 -ml-3 mr-1 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                        </path>
                    </svg>
                </button>
                <h1 class="text-xl font-bold text-gray-800 flex items-center gap-3">
                    Inbox
                    <span
                        class="text-xs font-semibold bg-blue-100 text-blue-700 px-2.5 py-0.5 rounded-full">{{ $emails instanceof \Illuminate\Pagination\LengthAwarePaginator ? $emails->total() : count($emails) }}</span>
                </h1>
            </div>

            <button x-data @click="document.getElementById('filter-bar').classList.toggle('hidden')"
                class="text-sm font-medium text-gray-500 hover:text-gray-900 border border-gray-200 px-3 py-1.5 rounded-md flex items-center gap-2 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                    </path>
                </svg>
                Filters
            </button>
        </header>

        <!-- Filters Bar -->
        <div id="filter-bar"
            class="px-6 sm:px-8 py-4 bg-gray-50/80 border-b border-gray-100 shrink-0 {{ request()->anyFilled(['search', 'from', 'to', 'date', 'date_from', 'date_to']) ? '' : 'hidden' }}">
            <form action="{{ route('email-sandbox.index') }}" method="GET"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 items-end">
                <div class="lg:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Search
                        Keywords</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Subject or body..."
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-[14px] focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none text-gray-700 transition-shadow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">From</label>
                    <input type="text" name="from" value="{{ request('from') }}" placeholder="Sender email..."
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-[14px] focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none text-gray-700 transition-shadow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">To</label>
                    <input type="text" name="to" value="{{ request('to') }}" placeholder="Recipient email..."
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-[14px] focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none text-gray-700 transition-shadow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-[14px] focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none text-gray-700 transition-shadow">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-[14px] focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none text-gray-700 transition-shadow">
                </div>
                <div class="flex gap-2 lg:col-span-6 mt-1">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-md text-sm transition-colors shadow-sm">Apply
                        Filters</button>
                    @if(request()->anyFilled(['search', 'from', 'to', 'date', 'date_from', 'date_to']))
                        <a href="{{ route('email-sandbox.index') }}"
                            class="bg-white hover:bg-gray-50 border border-gray-300 text-gray-700 font-medium px-4 py-2 rounded-md text-sm transition-colors shadow-sm">Clear</a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Email List -->
        <div class="flex-1 overflow-y-auto bg-gray-50/30">
            @if(count($emails) > 0)
                <div class="divide-y divide-gray-100">
                    @foreach($emails as $email)
                        @php
                            $fromArray = is_array($email->from) ? $email->from : json_decode($email->from, true) ?? [];
                            $fromFirst = collect($fromArray)->first() ?? [];
                            $fromName = $fromFirst['name'] ?? $fromFirst['address'] ?? 'Unknown Sender';
                            $fromEmailInfo = $fromFirst['address'] ?? '';

                            $initialBadge = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $fromName) ?: 'E', 0, 1));
                            $subject = $email->subject ?: '(No Subject)';

                            $html = $email->html_body ?? '';
                            $html = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $html);
                            $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);

                            $cleanHtml = trim(preg_replace('/\s+/', ' ', strip_tags($html)));
                            $snippetText = $cleanHtml ?: trim(preg_replace('/\s+/', ' ', strip_tags($email->text_body ?? '')));

                            $snippet = \Illuminate\Support\Str::limit($snippetText, 120);

                            $attachments = is_array($email->attachments) ? $email->attachments : json_decode($email->attachments, true) ?? [];
                            $hasAttachments = count($attachments) > 0;
                        @endphp
                        <a href="{{ route('email-sandbox.show', $email->id) }}"
                            class="block p-4 sm:px-8 hover:bg-blue-50/50 transition-colors duration-150 group bg-white border-l-4 border-transparent hover:border-blue-400">
                            <div class="flex items-start gap-4 sm:gap-6">
                                <!-- Avatar -->
                                <div
                                    class="flex-shrink-0 h-10 w-10 mt-1 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-sm opacity-90 group-hover:opacity-100 transition-opacity">
                                    {{ $initialBadge }}
                                </div>

                                <!-- Details -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-0.5">
                                        <p class="text-[15px] font-semibold text-gray-900 truncate">
                                            {{ $fromName }}
                                            @if($fromName !== $fromEmailInfo && $fromEmailInfo)
                                                <span
                                                    class="hidden sm:inline-block text-gray-400 font-normal ml-1 text-sm">&lt;{{ $fromEmailInfo }}&gt;</span>
                                            @endif
                                        </p>
                                        <div class="flex items-center gap-3 shrink-0 ml-4">
                                            @if($hasAttachments)
                                                <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-500 transition-colors"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13">
                                                    </path>
                                                </svg>
                                            @endif
                                            <p class="text-xs text-gray-500 whitespace-nowrap font-medium group-hover:text-blue-600 transition-colors"
                                                title="{{ $email->created_at }}">
                                                {{ $email->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                    <!-- Subject & Snippet -->
                                    <div class="flex flex-col sm:flex-row sm:items-baseline sm:gap-2 truncate">
                                        <p class="text-sm font-medium text-gray-800 truncate">
                                            {{ $subject }}
                                        </p>
                                        <p class="text-sm text-gray-500 truncate hidden sm:block">
                                            <span class="text-gray-300 mx-1">&mdash;</span> {{ $snippet }}
                                        </p>
                                    </div>
                                    <p class="text-sm text-gray-500 truncate sm:hidden mt-0.5">
                                        {{ $snippet }}
                                    </p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                @if($emails instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="px-8 py-5 bg-gray-50/80 border-t border-gray-100 z-10 sticky bottom-0">
                        {{ $emails->links('pagination::tailwind') }}
                    </div>
                @endif
            @else
                <div class="flex flex-col items-center justify-center h-full text-center p-8">
                    <div class="w-20 h-20 mb-6 text-gray-200">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Inbox is empty</h3>
                    <p class="text-gray-500 max-w-sm">No emails have been captured yet. Test your application's email sending
                        features to see emails appear here.</p>
                </div>
            @endif
        </div>
    </div>
@endsection