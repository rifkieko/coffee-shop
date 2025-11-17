<button {{ $attributes->merge([
    'type' => 'submit',
    'class' => 'inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest text-white shadow-sm transition duration-150 ease-in-out hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-2 focus:ring-offset-white active:bg-emerald-700'
]) }}>
    {{ $slot }}
</button>
