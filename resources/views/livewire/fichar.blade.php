<div class="p-4 bg-white rounded-md">
    <p class="mb-2"><span class="font-semibold">Hora de inicio:</span> {{ $hora_inicio }}</p>
    <p class="mb-2"><span class="font-semibold">Hora de fin:</span> {{ $hora_fin }}</p>
    <p class="mb-2"><span class="font-semibold">Tiempo total de jornada:</span> {{ $tiempo_total_jornada }}</p>
    <p class="mb-2"><span class="font-semibold">Horas totales:</span> {{ $horas_totales }}</p>
    <div class="mt-4 space-y-4"> <!-- Clase space-y-4 para espacio entre elementos hijos -->
        @if ($fichado)
            <button wire:click="desfichar" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-red-500 w-full">
                Desfichar
            </button>
        @else
            <button wire:click="fichar" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 w-full">
                Fichar
            </button>
        @endif
    </div>
</div>
