<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Sucursal - {{ $sucursal->nombre }}</title>
    <style>
        body {
            font-family: sans-serif;
            color: #333;
            line-height: 1.4;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }
        .header {
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #111;
        }
        .header p {
            margin: 2px 0 0 0;
            font-size: 11px;
            color: #8B5E3C;
            text-transform: uppercase;
        }
        .title-area {
            float: right;
            text-align: right;
            margin-top: -45px;
        }
        .title-area h2 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .title-area p {
            margin: 2px 0;
            font-size: 11px;
            color: #666;
        }
        .clear {
            clear: both;
        }
        .metrics {
            width: 100%;
            margin-bottom: 25px;
        }
        .metrics td {
            width: 50%;
            text-align: center;
            background: #fdf8ed;
            padding: 12px;
            border: 1px solid #e2d3be;
        }
        .metrics .label {
            font-size: 10px;
            color: #8b5e3c;
            text-transform: uppercase;
            display: block;
        }
        .metrics .val {
            font-size: 20px;
            font-weight: bold;
            color: #111;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table.data th {
            background-color: #f1f1f1;
            color: #444;
            text-align: left;
            padding: 8px;
            font-size: 10px;
            text-transform: uppercase;
            border-bottom: 1px solid #ccc;
        }
        table.data td {
            padding: 8px;
            border-bottom: 1px solid #eee;
            font-size: 11px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .status-completada {
            color: #10b981;
            font-weight: bold;
        }
        .status-cancelada {
            color: #e11d48;
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            font-size: 10px;
            color: #888;
        }
        .signature {
            float: right;
            margin-top: -15px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>LA VIEJA GUARDIA</h1>
        <p>Barbería Premium</p>
    </div>
    <div class="title-area">
        <h2>Reporte Detallado de Sucursal</h2>
        <p>Sucursal: <strong>{{ $sucursal->nombre }}</strong></p>
        <p>Fecha de Emisión: <strong>{{ now()->format('d/m/Y') }}</strong></p>
    </div>
    <div class="clear"></div>

    <table class="metrics" style="margin-bottom: 25px;">
        <tr>
            <td style="width: 25%;">
                <span class="label">Ingresos Totales</span>
                <span class="val">${{ number_format($totalVentas, 2) }}</span>
            </td>
            <td style="width: 25%; background: #ecfdf5; border-color: #a7f3d0;">
                <span class="label" style="color: #047857;">En Efectivo</span>
                <span class="val" style="color: #047857;">${{ number_format($totalEfectivo, 2) }}</span>
            </td>
            <td style="width: 25%; background: #fffbeb; border-color: #fde68a;">
                <span class="label" style="color: #b45309;">Con Tarjeta</span>
                <span class="val" style="color: #b45309;">${{ number_format($totalTarjeta, 2) }}</span>
            </td>
            <td style="width: 25%;">
                <span class="label">Atenciones Realizadas</span>
                <span class="val" style="color: #10b981;">{{ $ventas->where('estado', 'completada')->count() }}</span>
            </td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Servicio</th>
                <th>Barbero</th>
                <th class="text-right">Monto</th>
                <th class="text-center">Método</th>
                <th class="text-center">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ventas as $venta)
            @php
                $esCompletada = $venta->estado == 'completada';
                $precio = $esCompletada ? ($venta->servicio?->precio ?? 0) : 0;
            @endphp
            <tr>
                <td>{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</td>
                <td>{{ $venta->cliente?->nombre ?? '-' }}</td>
                <td>{{ $venta->servicio?->nombre ?? '-' }}</td>
                <td><strong>{{ $venta->barbero?->nombre ?? '-' }}</strong></td>
                <td class="text-right"><strong>${{ number_format($precio, 2) }}</strong></td>
                <td class="text-center" style="color: {{ $venta->metodo_pago == 'tarjeta' ? '#b45309' : '#047857' }};">
                    {{ $esCompletada ? ucfirst($venta->metodo_pago ?? 'Efectivo') : '-' }}
                </td>
                <td class="text-center">
                    <span class="status-{{ $venta->estado }}">{{ strtoupper($venta->estado) }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center" style="padding: 20px; color: #999;">No hay atenciones finalizadas registradas en esta sucursal.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <span>Reporte de la sucursal <strong>{{ $sucursal->nombre }}</strong>.</span>
        <span class="signature">Firma del Gerente: ___________________________</span>
    </div>

</body>
</html>
