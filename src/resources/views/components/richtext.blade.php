@php
    $name ??= 'content';
    $value ??= '';
@endphp

<div x-data="richEditor('{{ $name }}', '{{ addslashes($value) }}')" x-init="init()">
    <div class="border rounded-md overflow-hidden dark:border-gray-700">
        {{-- Toolbar --}}
        <div class="bg-gray-50 dark:bg-gray-800 border-b dark:border-gray-700 p-2 flex gap-1 flex-wrap">
            <button type="button" @click="format('bold')"
                class="px-2 py-1 hover:bg-gray-200 dark:hover:bg-gray-700 rounded text-sm">
                <strong>B</strong>
            </button>
            <button type="button" @click="format('italic')"
                class="px-2 py-1 hover:bg-gray-200 dark:hover:bg-gray-700 rounded text-sm">
                <em>I</em>
            </button>
            <button type="button" @click="format('underline')"
                class="px-2 py-1 hover:bg-gray-200 dark:hover:bg-gray-700 rounded text-sm">
                <u>U</u>
            </button>
            <div class="border-l mx-1"></div>
            <button type="button" @click="format('insertUnorderedList')"
                class="px-2 py-1 hover:bg-gray-200 dark:hover:bg-gray-700 rounded text-sm">
                • List
            </button>
            <button type="button" @click="format('insertOrderedList')"
                class="px-2 py-1 hover:bg-gray-200 dark:hover:bg-gray-700 rounded text-sm">
                1. List
            </button>
        </div>
        {{-- Editor --}}
        <div x-ref="editor" contenteditable="true"
            class="min-h-[200px] p-3 focus:outline-none prose dark:prose-invert max-w-none" @input="updateValue()">
        </div>
    </div>
    <input type="hidden" name="{{ $name }}" x-model="content">
</div>
