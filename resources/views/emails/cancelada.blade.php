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
            max-width: 600px;
            margin: 0 auto;
            background-color: #1E293B;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            border: 1px solid #E5D5B5;
        }

        .header {
            background-color: #111827;
            padding: 30px;
            text-align: center;
            border-bottom: 2px solid #ef4444;
        }

        .header h1 {
            color: #ef4444;
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

        .details-box {
            background-color: #2D3748;
            border-left: 4px solid #ef4444;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }

        .details-box table {
            width: 100%;
            border-collapse: collapse;
        }

        .details-box td {
            padding: 8px 0;
            font-size: 15px;
        }

        .details-box td.label {
            color: #cbd5e1;
            font-weight: 500;
            width: 35%;
        }

        .details-box td.value {
            color: #F8F9FA;
            font-weight: 600;
            text-decoration: line-through;
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
            <h1>🧔🏻‍♂️ CITA CANCELADA</h1>
        </div>
        <div class="content">
            <p>Hola, <strong>{{ $cita->cliente->nombre }}</strong>,</p>
            <p>Te informamos que tu cita programada ha sido **cancelada**. A continuación se detallan los datos de la
                reserva que fue cancelada:</p>

            <div class="details-box">
                <table>
                    <tr>
                        <td class="label">Servicio:</td>
                        <td class="value">{{ $cita->servicio->nombre }}</td>
                    </tr>
                    <tr>
                        <td class="label">Barbero:</td>
                        <td class="value">{{ $cita->barbero->nombre }}</td>
                    </tr>
                    <tr>
                        <td class="label">Fecha original:</td>
                        <td class="value">
                            {{ \Carbon\Carbon::parse($cita->fecha)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                        </td>
                    </tr>
                    <tr>
                        <td class="label">Hora:</td>
                        <td class="value">{{ $cita->hora_inicio }}</td>
                    </tr>
                </table>
            </div>

            <p style="text-align: center;">
                Puedes agendar una nueva cita contactándonos directamente al
                <strong>{{ $cita->barberia->telefono }}</strong>.
            </p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} La Vieja Guardia. Todos los derechos reservados.</p>
        </div>
    </div>
</body>

</html>
