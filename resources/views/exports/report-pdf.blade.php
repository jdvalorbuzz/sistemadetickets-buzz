<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #4F46E5; /* Color corporativo - Indigo */
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            font-size: 14px;
            margin-top: 0;
        }
        .summary {
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        .summary-box {
            width: 32%;
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 15px;
            box-sizing: border-box;
            margin-bottom: 10px;
            border: 1px solid #e5e7eb;
        }
        .summary-box h3 {
            margin-top: 0;
            margin-bottom: 5px;
            font-size: 16px;
            color: #4F46E5;
        }
        .summary-box p {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .chart {
            margin-bottom: 30px;
        }
        .chart h2 {
            color: #4F46E5;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: #f9fafb;
            font-weight: bold;
            color: #4F46E5;
        }
        table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Generado el {{ $generateDate }} por {{ auth()->user()->name }}</p>
    </div>
    
    <div class="summary">
        <div class="summary-box">
            <h3>Total de Tickets</h3>
            <p>{{ $totalTickets }}</p>
        </div>
        <div class="summary-box">
            <h3>Tickets Abiertos</h3>
            <p>{{ $openTickets }}</p>
        </div>
        <div class="summary-box">
            <h3>Tickets Cerrados</h3>
            <p>{{ $closedTickets }}</p>
        </div>
        <div class="summary-box">
            <h3>Tiempo Promedio de Resolución</h3>
            <p>{{ $avgResolutionTime }} horas</p>
        </div>
    </div>
    
    <div class="chart">
        <h2>Tickets por Estado</h2>
        <table>
            <thead>
                <tr>
                    <th>Estado</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ticketsByStatus as $status => $count)
                <tr>
                    <td>
                        @switch($status)
                            @case('open')
                                Abierto
                                @break
                            @case('in_progress')
                                En Progreso
                                @break
                            @case('closed')
                                Cerrado
                                @break
                            @case('archived')
                                Archivado
                                @break
                            @default
                                {{ $status }}
                        @endswitch
                    </td>
                    <td>{{ $count }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="chart">
        <h2>Tickets por Prioridad</h2>
        <table>
            <thead>
                <tr>
                    <th>Prioridad</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ticketsByPriority as $priority => $count)
                <tr>
                    <td>
                        @switch($priority)
                            @case('low')
                                Baja
                                @break
                            @case('medium')
                                Media
                                @break
                            @case('high')
                                Alta
                                @break
                            @case('urgent')
                                Urgente
                                @break
                            @default
                                {{ $priority }}
                        @endswitch
                    </td>
                    <td>{{ $count }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="chart">
        <h2>Tickets Cerrados por Usuario</h2>
        <table>
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Tickets Cerrados</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ticketsByUser as $user)
                <tr>
                    <td>{{ $user['name'] }}</td>
                    <td>{{ $user['total'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="chart">
        <h2>Tickets Creados por Mes (Últimos 6 meses)</h2>
        <table>
            <thead>
                <tr>
                    <th>Mes</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ticketsByMonth as $item)
                <tr>
                    <td>{{ $item['month'] }}</td>
                    <td>{{ $item['total'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <footer>
        <p>Este informe es confidencial y solo debe ser accedido por el personal autorizado.</p>
        <p>SoporteTickets - Sistema de Gestión de Tickets © {{ date('Y') }}</p>
    </footer>
</body>
</html>
