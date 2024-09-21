<div>
    <div class="bg-gray-50 dark:bg-gray-700 px-6 py-3 flex justify-between">
        <div>
            <label for="pageSize" class="text-sm text-gray-900 dark:text-gray-200">{{ __('Page Size') }}</label>
            <select wire:model="pageSize" id="pageSize" class="block w-24 mt-1 form-select shadow-sm sm:text-sm rounded-md dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="15">15</option>
                <option value="20">20</option>
            </select>
        </div>


       <div>
           <label for="search" class="text-sm text-gray-900 dark:text-gray-200">{{ __('Search') }}</label>
           <input wire:model="search" id="search" type="text" class="block w-48 mt-1 form-input shadow-sm sm:text-sm rounded-md dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200">
       </div>
    </div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('Symbol') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('Quantity') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('Price') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('Action') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($stockTrades as $stockTrade)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-gray-200">
                                        {{ $stockTrade->symbol }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-gray-200">
                                        {{ $stockTrade->quantity }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-gray-200">
                                        {{ $stockTrade->price }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('stock-trades.edit', $stockTrade) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600">
                                        {{ __('Edit') }}
                                    </a>
                                    <form action="{{ route('stock-trades.destroy', $stockTrade) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-600">
                                            {{ __('Delete') }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="bg-gray-50 dark:bg-gray-700 px-6 py-3">
                    {{ $stockTrades->links() }}
                </div>

            </div>
        </div>
    </div>
</div>
