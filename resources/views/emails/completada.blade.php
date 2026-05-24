<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Outfit', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #FDF8ED;
            margin: 0;
            padding: 20px;
            color: #1E293B;
        }
        .container {
            max-width: 500px;
            margin: 0 auto;
            background-color: #1E293B;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            border: 1px solid #E5D5B5;
        }
        .header {
            background-color: #111827;
            padding: 30px;
            text-align: center;
            border-bottom: 2px solid #C5A059;
        }
        .header h1 {
            color: #C5A059;
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .content {
            padding: 30px;
            color: #F8F9FA;
        }
        .content p {
            line-height: 1.6;
            font-size: 16px;
        }
        .ticket {
            background-color: #FFFFFF;
            color: #1E293B;
            border-radius: 8px;
            padding: 25px;
            margin: 25px 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            font-family: 'Courier New', Courier, monospace;
            border-top: 5px dashed #C5A059;
            border-bottom: 5px dashed #C5A059;
        }
        .ticket-title {
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 15px;
            letter-spacing: 1px;
            color: #111827;
        }
        .ticket-divider {
            border-top: 1px dashed #1E293B;
            margin: 15px 0;
        }
        .ticket-row {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            font-size: 14px;
        }
        .ticket-row.total {
            font-weight: bold;
            font-size: 16px;
            color: #111827;
            margin-top: 15px;
        }
        .footer {
            background-color: #111827;
            padding: 20px;
            text-align: center;
            font-size: 13px;
            color: #94a3b8;
            border-top: 1px solid #2D3748;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🧔🏻‍♂️ GRACIAS POR TU VISITA</h1>
        </div>
        <div class="content">
            <p>Hola, <strong>{{ $cita->cliente->nombre }}</strong>,</p>
            <p>Queremos agradecerte por haber visitado hoy **La Vieja Guardia**. Esperamos que tu experiencia haya sido excelente.</p>
            
            <p>A continuación te presentamos el ticket desglosado de tu compra:</p>

            <div class="ticket">
                <div class="ticket-title">TICKET DE COMPRA</div>
                <div style="text-align: center; font-size: 12px; margin-bottom: 10px;">
                    LA VIEJA GUARDIA BARBERÍA<br>
                    Fecha: {{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }} {{ $cita->hora_inicio }}
                </div>
                <div class="ticket-divider"></div>
                
                <div class="ticket-row">
                    <span>Cliente:</span>
                    <span>{{ $cita->cliente->nombre }}</span>
                </div>
                <div class="ticket-row">
                    <span>Barbero:</span>
                    <span>{{ $cita->barbero->nombre }}</span>
                </div>
                <div class="ticket-row">
                    <span>Atención:</span>
                    <span>{{ $cita->tipo_atencion == 'con_cita' ? 'Con Cita' : 'Sin Cita' }}</span>
                </div>
                
                <div class="ticket-divider"></div>
                
                <div class="ticket-row">
                    <span><strong>Concepto:</strong></span>
                    <span><strong>Precio</strong></span>
                </div>
                <div class="ticket-row">
                    <span>{{ $cita->servicio->nombre }}</span>
                    <span>${{ number_format($cita->servicio->precio, 2) }}</span>
                </div>

                <div class="ticket-divider"></div>
                
                <div class="ticket-row">
                    <span>Método de Pago:</span>
                    <span style="text-transform: capitalize;">{{ $cita->metodo_pago ?? 'Efectivo' }}</span>
                </div>
                
                <div class="ticket-row total">
                    <span>TOTAL PAGADO:</span>
                    <span>${{ number_format($cita->servicio->precio, 2) }}</span>
                </div>
            </div>

            <p style="text-align: center;">
                ¡Fue un placer atenderte! Esperamos verte de nuevo muy pronto.
            </p>
        </div>
        <div class="footer">
            <p>Este correo electrónico fue generado automáticamente tras finalizar tu servicio.</p>
            <p>&copy; {{ date('Y') }} La Vieja Guardia. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
