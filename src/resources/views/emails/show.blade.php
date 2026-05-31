@extends('email-sandbox::layouts.app')
@section('content')
    @php
        $fromArray = is_array($email->from) ? $email->from : json_decode($email->from, true) ?? [];
        $toArray = is_array($email->to) ? $email->to : json_decode($email->to, true) ?? [];
        $ccArray = is_array($email->cc) ? $email->cc : json_decode($email->cc, true) ?? [];
        $bccArray = is_array($email->bcc) ? $email->bcc : json_decode($email->bcc, true) ?? [];

        $formatAddress = function ($arr) {
            return collect($arr)->map(function ($item) {
                $name = $item['name'] ?? '';
                $address = $item['address'] ?? '';
                $formatted = trim($name) ? "{$name} &lt;{$address}&gt;" : $address;
                return "<span class='inline-block px-2 py-0.5 bg-gray-100 rounded-md text-gray-800 border border-gray-200'>{$formatted}</span>";
            })->join(' ');
        };

        $fromStr = $formatAddress($fromArray);
        $toStr = $formatAddress($toArray);
        $ccStr = $formatAddress($ccArray);
        $bccStr = $formatAddress($bccArray);

        $subject = $email->subject ?: '(No Subject)';
        $attachments = is_array($email->attachments) ? $email->attachments : json_decode($email->attachments, true) ?? [];
    @endphp
    <div class="flex flex-col h-full bg-white relative"
        x-data="{ tab: '{{ $email->html_body ? 'html' : 'text' }}', device: 'desktop' }">
        <!-- Header Toolbar -->
        <header class="h-16 flex items-center justify-between px-6 border-b border-gray-100 bg-white shrink-0">
            <div class="flex items-center gap-2 sm:gap-4">
                <button @click="sidebarOpen = true"
                    class="lg:hidden p-2 -ml-3 text-gray-400 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                        </path>
                    </svg>
                </button>
                <div class="h-6 w-px bg-gray-200 lg:hidden"></div>
                <a href="{{ route('email-sandbox.index') }}"
                    class="p-2 -ml-2 text-gray-400 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
                    title="Back to Inbox">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>

                <div class="h-6 w-px bg-gray-200"></div>

                <form action="{{ route('email-sandbox.destroy', $email->id) }}" method="POST"
                    onsubmit="return confirm('Are you sure you want to delete this email?');" class="m-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="p-2 -ml-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors flex items-center gap-2"
                        title="Delete Email">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                            </path>
                        </svg>
                        <span class="text-sm font-medium hidden sm:inline">Delete</span>
                    </button>
                </form>
            </div>

            <div class="text-sm text-gray-500 font-medium">
                {{ $email->created_at->format('M j, Y, g:i A') }} <span class="text-gray-400 mx-1">&bull;</span>
                {{ $email->created_at->diffForHumans() }}
            </div>
        </header>

        <!-- Scrollable Wrapper for Envelope + Tabs + Content -->
        <div class="flex-1 overflow-y-auto w-full">
            <!-- Email Envelope Details -->
            <div class="px-6 sm:px-10 py-6 bg-white border-b border-gray-100 shrink-0">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 tracking-tight">{{ $subject }}</h2>

                <div class="space-y-3 text-[14px]">
                    @if($fromStr)
                        <div class="flex items-center">
                            <span
                                class="w-12 shrink-0 text-gray-400 font-medium text-right mr-4 text-xs uppercase tracking-wider">From</span>
                            <div class="flex flex-wrap gap-1">{!! $fromStr !!}</div>
                        </div>
                    @endif
                    @if($toStr)
                        <div class="flex items-center">
                            <span
                                class="w-12 shrink-0 text-gray-400 font-medium text-right mr-4 text-xs uppercase tracking-wider">To</span>
                            <div class="flex flex-wrap gap-1">{!! $toStr !!}</div>
                        </div>
                    @endif
                    @if($ccStr)
                        <div class="flex items-center">
                            <span
                                class="w-12 shrink-0 text-gray-400 font-medium text-right mr-4 text-xs uppercase tracking-wider">Cc</span>
                            <div class="flex flex-wrap gap-1">{!! $ccStr !!}</div>
                        </div>
                    @endif
                    @if($bccStr)
                        <div class="flex items-center">
                            <span
                                class="w-12 shrink-0 text-gray-400 font-medium text-right mr-4 text-xs uppercase tracking-wider">Bcc</span>
                            <div class="flex flex-wrap gap-1">{!! $bccStr !!}</div>
                        </div>
                    @endif
                </div>

                @if(count($attachments) > 0)
                    <div class="mt-6 pt-5 border-t border-gray-100">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13">
                                </path>
                            </svg>
                            Attachments ({{ count($attachments) }})
                        </h4>
                        <div class="flex flex-wrap gap-3">
                            @foreach($attachments as $file)
                                <a href="{{ route('email-sandbox.download', [$email->id, $file]) }}"
                                    class="inline-flex items-center px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 hover:bg-white hover:border-blue-400 transition-all text-sm group shadow-sm">
                                    <div class="bg-blue-100 text-blue-600 p-1.5 rounded mr-3">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <span
                                        class="text-gray-700 group-hover:text-blue-700 font-medium truncate max-w-xs">{{ preg_replace('/^[a-zA-Z0-9]+_/', '', $file) }}</span>
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-500 ml-2 opacity-0 group-hover:opacity-100 transition-opacity"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Tabs -->
            <div
                class="flex flex-col sm:flex-row sm:items-center justify-between px-6 sm:px-10 border-b border-gray-200 bg-gray-50/50 shrink-0">
                <nav class="-mb-px flex space-x-6 overflow-x-auto scrollbar-hide">
                    @if($email->html_body)
                        <button @click="tab = 'html'"
                            :class="{'border-blue-500 text-blue-600': tab === 'html', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'html'}"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors focus:outline-none">
                            HTML View
                        </button>
                        <button @click="tab = 'html_source'"
                            :class="{'border-blue-500 text-blue-600': tab === 'html_source', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'html_source'}"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors focus:outline-none hidden sm:inline-block">
                            HTML Source
                        </button>
                    @endif
                    @if($email->text_body)
                        <button @click="tab = 'text'"
                            :class="{'border-blue-500 text-blue-600': tab === 'text', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'text'}"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors focus:outline-none">
                            Text View
                        </button>
                    @endif
                    <button @click="tab = 'headers'"
                        :class="{'border-blue-500 text-blue-600': tab === 'headers', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'headers'}"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors focus:outline-none">
                        Headers
                    </button>
                    <button @click="tab = 'raw'"
                        :class="{'border-blue-500 text-blue-600': tab === 'raw', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'raw'}"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors focus:outline-none hidden sm:inline-block">
                        Raw
                    </button>
                </nav>

                <div x-show="tab === 'html'"
                    class="flex items-center gap-1.5 bg-gray-200/50 p-1 rounded-md mb-2 sm:mb-0 shrink-0" x-cloak>
                    <button @click="device='desktop'" :class="{'bg-white shadow text-blue-600': device=='desktop'}"
                        class="p-1 rounded text-gray-500 hover:text-gray-700 transition-colors" title="Desktop">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                            </path>
                        </svg>
                    </button>
                    <button @click="device='tablet'" :class="{'bg-white shadow text-blue-600': device=='tablet'}"
                        class="p-1 rounded text-gray-500 hover:text-gray-700 transition-colors" title="Tablet">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </button>
                    <button @click="device='mobile'" :class="{'bg-white shadow text-blue-600': device=='mobile'}"
                        class="p-1 rounded text-gray-500 hover:text-gray-700 transition-colors" title="Mobile">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Tab Content -->
            <div class="min-h-[calc(100vh-4rem-4rem)] sm:min-h-[calc(100vh-4rem-3.5rem)] relative bg-gray-50">
                @if($email->html_body)
                    <div x-show="tab === 'html'"
                        class="h-full w-full absolute inset-0 bg-gray-200/50 flex justify-center overflow-auto items-start">
                        <div :class="{'w-full h-full': device=='desktop', 'w-[768px] h-[1024px] mt-8 shadow-xl border border-gray-300 rounded-sm shrink-0': device=='tablet', 'w-[375px] h-[812px] mt-8 shadow-xl border border-gray-300 rounded-sm shrink-0': device=='mobile'}"
                            class="bg-white transition-all duration-300 ease-in-out relative origin-top mx-auto">
                            <iframe srcdoc="{{ $email->html_body }}" class="w-full h-full border-0 absolute inset-0"></iframe>
                        </div>
                    </div>

                    <div x-show="tab === 'html_source'"
                        class="h-full w-full absolute inset-0 overflow-auto p-6 sm:p-10 text-gray-800" x-cloak>
                        <pre
                            class="font-mono text-[13px] whitespace-pre-wrap bg-[#111827] border border-gray-800 rounded-lg p-6 sm:p-8 shadow-sm leading-relaxed text-gray-300">{{ $email->html_body }}</pre>
                    </div>
                @endif

                @if($email->text_body)
                    <div x-show="tab === 'text'" class="h-full w-full absolute inset-0 overflow-auto p-6 sm:p-10 text-gray-800"
                        x-cloak>
                        <pre
                            class="font-mono text-sm whitespace-pre-wrap bg-white border border-gray-200 rounded-lg p-6 sm:p-8 shadow-sm leading-relaxed text-gray-700">{{ $email->text_body }}</pre>
                    </div>
                @endif

                <div x-show="tab === 'headers'" class="h-full w-full absolute inset-0 overflow-auto p-6 sm:p-10" x-cloak>
                    <div
                        class="bg-[#111827] rounded-xl shadow-lg border border-gray-800 p-6 sm:p-8 text-sm overflow-hidden">
                        <div class="flex flex-col space-y-6 text-gray-300 font-mono text-[13px]">
                            @php
                                $headers = is_array($email->headers) ? $email->headers : json_decode($email->headers, true) ?? [];
                                ksort($headers); // Sort headers alphabetically like Mailpit does
                            @endphp
                            @foreach($headers as $key => $values)
                                <div class="flex flex-col">
                                    <span class="text-gray-400 font-bold mb-1.5 align-baseline">{{ $key }}</span>
                                    <span class="break-words leading-relaxed text-gray-100">
                                        @if(is_array($values))
                                            {{ implode(', ', $values) }}
                                        @else
                                            {{ $values }}
                                        @endif
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div x-show="tab === 'raw'" class="h-full w-full absolute inset-0 p-6 sm:p-10" x-cloak>
                    @php
                        $rawFile = config('email-sandbox.storage_path') . '/' . $email->id . '.eml';
                        $rawContent = file_exists($rawFile) ? file_get_contents($rawFile) : "Raw data not available.\nThis email might have been stored before the raw logging feature was introduced.";
                    @endphp
                    <pre
                        class="font-mono text-[13px] whitespace-pre-wrap bg-[#111827] border border-gray-800 rounded-lg p-6 sm:p-8 shadow-sm leading-relaxed text-gray-300">{{ $rawContent }}</pre>
                </div>
            </div>
        </div><!-- End Scrollable Wrapper -->
    </div>
@endsection