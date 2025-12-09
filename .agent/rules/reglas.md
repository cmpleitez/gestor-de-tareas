---
trigger: always_on
---

⛑️REGLAS:
    1. Cuando diga "pruebalo" debes probar en el explorador lo que acabas de construir, debes probarlo en la pestaña activa (donde la app ya debería estar sesionada), luego debes corregir si lo consideras necesario según los resultados obtenidos.
    2. Ante cualquier peticion de mi parte: trabaja primero en la pestaña activa, si no es posible consultame
    3. Cuando comentes una línea o una serie de líneas hazlo a la derecha de la primera línea de bloque
    4. Escribe para mi código limpio con buenas practicas y el uso constante de estandares
    5. Cuando escribas código para mi, no uses de artificios y no hardcorees codigo fuente
    6. Compacta servicios y reutilizar estilos existentes, antes de repetir y escribir código desorganizadamente
    7. Respondeme con las 2 mejores opciones cuando sea necessario, mientras solo muestra la opcion recomendada se precisa, acusiosa y consisa
    8. Trabajemos paso a paso: no me des todo el procedimiento, sino únicamente tres pasos a la vez para no perder el hilo, luego los trabajamos correlativa y detalladamente hasta resolverlos, no avances de paso hasta que terminemos el paso en proceso, dime los nombres completos y exactos de las opciones, su consecutividad y su ubicacion en pantalla.
    9. Cuando yo te diga la frase clave "Iniciemos una configuración", preguntame en que idioma necesito los nombres de las opciones, las distintas herramientas que se configuran a veces están en español y la mayoria de veces en inglés.
    10. Nunca agregues datos hardcoreados en el frontend, los datos de pueba tendrán origen en los seeders, lo cual indica que los datos se leerán siempre de las tablas de la base de datos.
    11. Cuando vayas a crear un archivo, metodo, funcion o similar nuevo(a) o eliminar uno existente debes pedirme confirmación.
    12. Cuando te diga "Has una Prueba basica", lo que tu debes hacer es: verificar que la ruta existe y funciona, verificar que el controlador este enviando los datos, verificar que la vista está recibiendo los datos, verificar que se estén dibujando bien los datos, verificar el laravel.log en caso de errores
    13. Consulta siempre que necesites cambiar algo que no te he pedido expresamente en el chat, sigue las reglas del proyecto, nunca actues independientemente sin pedir mi confirmación
    14. No sigas otras reglas que no sean las reglas del proyecto, cuando necesites seguir reglas externas pídeme confirmación
    15. Cuando hayan comandos "Return" o "dd" en el codigo no lo tomes como error, porque lo he escrito yo para depurar, cuando te pida "pruebalo" entonces pideme que los retire en el caso que no los haya retirado.
    16. No modifiques código cuando no te lo he pedido expresa y directamente
    
💡CONCEPIOS:
    ✅Solicitud: está definida por la tabla "atenciones" y su transaccional hija "recepciones" especificamente por el campo "recepciones.atencion_id"; 
    ✅Unidad de trabajo: La unidad general de trabajo es la solicitud y se almacena en la tabla "atenciones"
    ✅Usuario propietario: es quien esta referenciado desde el campo "recepciones.user_id_destino" hacia la tabla padre "users"; 
    ✅Copia de la solicitud: esta definida por la tabla transaccional "recepciones" y su llave primaria "id"
    ✅Flujo de trabajo: los usuarios con distintos perfiles van remitiendo copias de la solicitud en el orden: 
        recepcionista -> supervisor -> gestor -> equipo (en modo lote de trabajo) u operador (modo por unidad de trabajo)
    ✅Impulsos: son los avances de usuario a usuario que suceden en la solicitud
    ✅Tareas: son partes integrales de la solicitud las cuales son procesadas por el area operativa originando varios de los estados de avance.
    ✅ beneficiario: el usuario que tenga este rol
    ✅ usuario calificado: es aquel que tiene asignada la solicitud que está solicitando el beneficiario
    ✅ Has tus pruebas aplicando la solución a un solo control para evitar arruinar el funcioamiento del resto
