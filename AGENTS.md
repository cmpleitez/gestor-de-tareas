// Ver reglas completas: .agent/rules/reglas.md
    
💡CONCEPIOS:
    ✅Solicitud: está definida por la tabla "atenciones" y se dispersa atravez de sus tablas hijas: recepciones, actividades, ordenes de compra y detalles, visualmente aparecen en el kanban como tarjetas dinámicas que van cambiando entre tableros
    ✅Los tableros representan los tres estados de la solicitud: Recibida, En progreso y Resuelta
    ✅Las trazas o tracking son representadas por los nombres de las distintas tareas registradas: Solicitud, Revisión, Verificación física, Descarga del Stock y  
    Entrega del producto
    ✅Usuario propietario: es quien esta referenciado desde el campo "recepciones.user_id_destino" hacia la tabla padre "users"; 
    ✅Copia de la solicitud: esta definida por la tabla "recepcion" y su llave primaria
    ✅Flujo de trabajo: los usuarios con distintos perfiles van remitiendo copias de la solicitud en el orden: 
      cliente -> receptor -> operador
    ✅Impulsos: son los avances que realizan las solicitudes moviendose entre los tableros del kanban
    ✅Tareas: son partes integrales de la solicitud las cuales son procesadas por las personas participantes