{{-- Field Component: Renders individual form fields based on type --}}

@php
    $fieldData = $field->toArray();
    $fieldName = $field->getName();
    $fieldType = $field->getType();
    $fieldValue = old($fieldName, $field->getValue());
    $hasError = isset($errors) && isset($errors[$fieldName]);
    $errorMessage = $hasError ? $errors[$fieldName][0] ?? '' : '';

    // Conditional rendering
    if (!$field->isVisible($formData ?? [])) {
        return;
    }

    // Column span for grid
    $colSpan = $fieldData['columnSpan'] ?? 12;
    $colClass = match ($colSpan) {
        1 => 'col-span-1',
        2 => 'col-span-2',
        3 => 'col-span-3',
        4 => 'col-span-4',
        5 => 'col-span-5',
        6 => 'col-span-6',
        7 => 'col-span-7',
        8 => 'col-span-8',
        9 => 'col-span-9',
        10 => 'col-span-10',
        11 => 'col-span-11',
        default => 'col-span-12',
    };

    // Field dependency (Alpine.js)
    $alpineShow = '';
    if ($fieldData['dependsOn']) {
        $depField = $fieldData['dependsOn'];
        $depValues = $fieldData['dependsOnValues'];
        if (!empty($depValues)) {
            $valuesJson = json_encode($depValues);
            $alpineShow = "x-show=\"{$valuesJson}.includes(\$refs.{$depField}?.value)\"";
        }
    }
@endphp

<div class="{{ $colClass }}" {!! $alpineShow !!}>
    {{-- Label --}}
    @if ($fieldType !== 'hidden' && $fieldType !== 'checkbox')
        <label for="{{ $fieldName }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            {{ $field->getLabel() }}
            @if ($field->isRequired())
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    {{-- Field based on type --}}
    @switch($fieldType)
        @case('text')
        @case('email')

        @case('url')
        @case('password')

        @case('number')
            <input type="{{ $fieldType }}" name="{{ $fieldName }}" id="{{ $fieldName }}" x-ref="{{ $fieldName }}"
                value="{{ $fieldValue }}" {!! $field->getAttributesString() !!}
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white sm:text-sm @error($fieldName) border-red-500 @enderror">
        @break

        @case('textarea')
            <textarea name="{{ $fieldName }}" id="{{ $fieldName }}" x-ref="{{ $fieldName }}" rows="4"
                {!! $field->getAttributesString() !!}
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white sm:text-sm @error($fieldName) border-red-500 @enderror">{{ $fieldValue }}</textarea>
        @break

        @case('rich_editor')
            <div x-data="richEditor('{{ $fieldName }}', '{{ addslashes($fieldValue ?? '') }}')" x-init="init()">
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
                <input type="hidden" name="{{ $fieldName }}" x-model="content">
            </div>
        @break

        @case('select')
            <select name="{{ $fieldName }}" id="{{ $fieldName }}" x-ref="{{ $fieldName }}" {!! $field->getAttributesString() !!}
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white sm:text-sm @error($fieldName) border-red-500 @enderror">
                @if ($fieldData['placeholder'] ?? false)
                    <option value="">{{ $fieldData['placeholder'] }}</option>
                @endif
                @foreach ($field->getOptions() as $value => $label)
                    <option value="{{ $value }}" @selected($fieldValue == $value)>{{ $label }}</option>
                @endforeach
            </select>
        @break

        @case('checkbox')
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input type="checkbox" name="{{ $fieldName }}" id="{{ $fieldName }}" x-ref="{{ $fieldName }}"
                        value="1" @checked($fieldValue) {!! $field->getAttributesString() !!}
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:bg-gray-800 dark:border-gray-700">
                </div>
                <div class="ml-3 text-sm">
                    <label for="{{ $fieldName }}" class="font-medium text-gray-700 dark:text-gray-300">
                        {{ $field->getLabel() }}
                        @if ($field->isRequired())
                            <span class="text-red-500">*</span>
                        @endif
                    </label>
                    @if ($fieldData['helperText'])
                        <p class="text-gray-500 dark:text-gray-400">{{ $fieldData['helperText'] }}</p>
                    @endif
                </div>
            </div>
        @break

        @case('radio')
            <div class="space-y-2">
                @foreach ($field->getOptions() as $value => $label)
                    <div class="flex items-center">
                        <input type="radio" name="{{ $fieldName }}" id="{{ $fieldName }}_{{ $value }}"
                            value="{{ $value }}" @checked($fieldValue == $value) {!! $field->getAttributesString() !!}
                            class="h-4 w-4 border-gray-300 text-primary-600 focus:ring-primary-500 dark:bg-gray-800 dark:border-gray-700">
                        <label for="{{ $fieldName }}_{{ $value }}"
                            class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ $label }}
                        </label>
                    </div>
                @endforeach
            </div>
        @break

        @case('date')
        @case('datetime')

        @case('time')
            <input type="{{ $fieldType === 'datetime' ? 'datetime-local' : $fieldType }}" name="{{ $fieldName }}"
                id="{{ $fieldName }}" x-ref="{{ $fieldName }}" value="{{ $fieldValue }}" {!! $field->getAttributesString() !!}
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white sm:text-sm @error($fieldName) border-red-500 @enderror">
        @break

        @case('file')
        @case('image')
            <div x-data="fileUpload('{{ $fieldName }}')" class="space-y-2">
                {{-- Current image preview --}}
                @if ($fieldValue && $fieldType === 'image')
                    <div class="mb-2">
                        <img src="{{ $fieldValue }}" alt="Current" class="h-32 w-auto rounded border">
                    </div>
                @endif

                <input type="file" name="{{ $fieldName }}" id="{{ $fieldName }}" x-ref="fileInput"
                    @change="handleFileSelect($event)" {!! $field->getAttributesString() !!}
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 dark:file:bg-gray-800 dark:file:text-gray-300">

                {{-- Preview for new upload --}}
                <div x-show="preview" class="mt-2">
                    <img :src="preview" alt="Preview" class="h-32 w-auto rounded border">
                </div>
            </div>
        @break

        @case('color')
            <input type="color" name="{{ $fieldName }}" id="{{ $fieldName }}" x-ref="{{ $fieldName }}"
                value="{{ $fieldValue }}" {!! $field->getAttributesString() !!}
                class="block h-10 w-20 rounded-md border-gray-300 cursor-pointer">
        @break

        @case('hidden')
            <input type="hidden" name="{{ $fieldName }}" value="{{ $fieldValue }}">
        @break

        @default
            {{-- Custom field type - allow override --}}
            <input type="text" name="{{ $fieldName }}" id="{{ $fieldName }}" x-ref="{{ $fieldName }}"
                value="{{ $fieldValue }}" {!! $field->getAttributesString() !!}
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white sm:text-sm @error($fieldName) border-red-500 @enderror">
    @endswitch

    {{-- Helper Text --}}
    @if ($fieldData['helperText'] && $fieldType !== 'checkbox')
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $fieldData['helperText'] }}</p>
    @endif

    {{-- Error Message --}}
    @if ($hasError)
        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $errorMessage }}</p>
    @endif
</div>
