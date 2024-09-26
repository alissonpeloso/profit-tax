<div class="w-full flex gap-2">
    <div>
        <x-

        @error('brokerageNotePasswords.*')
        <p class="mt-2 text-sm text-red-600 dark:text-red-500">
            <span class="font-medium">{{ $message }}</span>
        </p>
        @enderror
    </div>

</div>
