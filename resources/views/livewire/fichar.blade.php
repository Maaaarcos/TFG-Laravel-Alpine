<div x-data="{ 
        horaInicio: @entangle('hora_inicio'), 
        horaFin: @entangle('hora_fin'), 
        tiempo_total_jornada: @entangle('tiempo_total_jornada'),
        horasTotales: @entangle('horas_totales')
        
    }">
    <div>
        <h1>Fichar</h1>
        <p>Hora de inicio: <span x-text="horaInicio"></span></p>
        <p>Hora de fin: <span x-text="horaFin"></span></p>
        <p>Tiempo total de jornada: <span x-text="tiempo_total_jornada"></span></p>
        <p>Horas totales mes: <span x-text="horasTotales"></span></p>
    </div>
    <div>
        <button @click="$wire.fichar()">Fichar</button>
        <button @click="$wire.desfichar()">Desfichar</button>
    </div>
</div>

