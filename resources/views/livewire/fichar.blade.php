<div>
    <p>Hora de inicio: {{ $hora_inicio }}</p>
    <p>Hora de fin: {{ $hora_fin }}</p>
    <p>Tiempo total de jornada: {{ $tiempo_total_jornada }}</p>
    <p>Horas totales: {{ $horas_totales }}</p>
    <button wire:click="fichar" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Fichar</button>
    <button wire:click="desfichar" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Desfichar</button>
</div>

