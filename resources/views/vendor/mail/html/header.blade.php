<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            {{-- GANTI BAGIAN INI --}}
            @if(trim($slot) === 'Laravel')
            <img src="{{ asset('assets/img/unjani.png') }}" class="logo" alt="Unjani Logo"
                style="max-height: 75px; width: auto;">
            @else
            {{ $slot }}
            @endif
        </a>
    </td>
</tr>