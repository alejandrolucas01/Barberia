<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Consolidado de Rentabilidad</title>
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
        .total-box {
            background: #fdf8ed;
            border: 1px solid #e2d3be;
            padding: 15px;
            text-align: center;
            margin-bottom: 30px;
            border-radius: 4px;
        }
        .total-box .label {
            font-size: 11px;
            color: #8b5e3c;
            text-transform: uppercase;
            display: block;
        }
        .total-box .val {
            font-size: 24px;
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
            padding: 10px;
            font-size: 11px;
            text-transform: uppercase;
            border-bottom: 2px solid #ccc;
        }
        table.data td {
            padding: 12px 10px;
            border-bottom: 1px solid #eee;
            font-size: 12px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
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
        <h2>Consolidado Global de Rentabilidad</h2>
        <p>Fecha de Emisión: <strong>{{ now()->format('d/m/Y') }}</strong></p>
        <p>Alcance: <strong>Todas las Sucursales</strong></p>
    </div>
    <div class="clear"></div>

    <table style="width: 100%; margin-bottom: 25px;">
        <tr>
            <td style="width: 25%; padding: 5px;">
                <div class="total-box" style="margin-bottom: 0; padding: 10px;">
                    <span class="label">Total Global</span>
                    <span class="val" style="font-size: 18px;">${{ number_format($totalVentasGeneral, 2) }}</span>
                </div>
            </td>
            <td style="width: 25%; padding: 5px;">
                <div class="total-box" style="margin-bottom: 0; padding: 10px; background: #ecfdf5; border-color: #a7f3d0;">
                    <span class="label" style="color: #047857;">En Efectivo</span>
                    <span class="val" style="font-size: 18px; color: #047857;">${{ number_format($totalEfectivoGeneral, 2) }}</span>
                </div>
            </td>
            <td style="width: 25%; padding: 5px;">
                <div class="total-box" style="margin-bottom: 0; padding: 10px; background: #fffbeb; border-color: #fde68a;">
                    <span class="label" style="color: #b45309;">Con Tarjeta</span>
                    <span class="val" style="font-size: 18px; color: #b45309;">${{ number_format($totalTarjetaGeneral, 2) }}</span>
                </div>
            </td>
            <td style="width: 25%; padding: 5px;">
                <div class="total-box" style="margin-bottom: 0; padding: 10px; background: #fffbfb; border-color: #fcd5d5;">
                    <span class="label" style="color: #e11d48;">Cancelados</span>
                    <span class="val" style="font-size: 18px; color: #e11d48;">{{ $totalCanceladasGeneral }}</span>
                </div>
            </td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th>Sucursal</th>
                <th class="text-center">Completados</th>
                <th class="text-right">Efectivo</th>
                <th class="text-right">Tarjeta</th>
                <th class="text-right">Total Generado</th>
                <th class="text-center">Aporte</th>
            </tr>
        </thead>
        <tbody>
            @forelse($todasSucursales as $suc)
            @php
                $datos = $ventasPorSucursal[$suc->id] ?? ['total' => 0, 'efectivo' => 0, 'tarjeta' => 0, 'count' => 0, 'canceladas' => 0];
                $porcentaje = $totalVentasGeneral > 0 ? ($datos['total'] / $totalVentasGeneral) * 100 : 0;
            @endphp
            <tr>
                <td><strong>{{ $suc->nombre }}</strong></td>
                <td class="text-center">{{ $datos['count'] }} <span style="color:#e11d48; font-size:10px;">(-{{ $datos['canceladas'] ?? 0 }})</span></td>
                <td class="text-right" style="color: #047857;">${{ number_format($datos['efectivo'] ?? 0, 2) }}</td>
                <td class="text-right" style="color: #b45309;">${{ number_format($datos['tarjeta'] ?? 0, 2) }}</td>
                <td class="text-right"><strong>${{ number_format($datos['total'], 2) }}</strong></td>
                <td class="text-center" style="color: #8b5e3c; font-weight: bold;">
                    {{ number_format($porcentaje, 1) }}%
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center" style="padding: 20px; color: #999;">No hay sucursales registradas.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <span>Reporte generado por el panel de administración de <strong>La Vieja Guardia</strong>.</span>
        <span class="signature">Firma de Auditoría: ___________________________</span>
    </div>

</body>
</html>
