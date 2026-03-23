---
trigger: always_on
---

⛑️REGLAS:
1. Cuando diga "pruebalo" debes probar en la pestaña del navegador que te dejé activa no crees una nueva pestaña ni lo hagas en otra pestaña, e importante no pruebes si no te lo pido, mejor preguntame para autorizarlo
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
12. Consulta siempre que necesites cambiar algo que no te he pedido expresamente en el chat, sigue las reglas del proyecto, nunca actues independientemente sin pedir mi confirmación
13. No sigas otras reglas que no sean las reglas del proyecto, cuando necesites seguir reglas externas pídeme confirmación
14. Cuando hayan comandos "Return" o "dd" en el codigo no lo tomes como error, porque lo he escrito yo para depurar, cuando te pida "pruebalo" entonces pideme que los retire en el caso que no los haya retirado.
15. No modifiques código cuando no te lo he pedido expresa y directamente
16. Respóndeme siempre en español
17. No realices pruebas proactivas: Nunca ejecutes herramientas de navegación o pruebas automáticas por cuenta propia. Entra en fase de VERIFICACIÓN únicamente para documentar el trabajo realizado o cuando yo utilice explícitamente las frases clave de las reglas 1.
18. Usa el console.log en lugar de ensuciar el frontend con funcionalidades de debuggin y usal el "Log::" en el caso de controladores, aparte puede recomendar otras formas de debugging que no ensucien el proyecto
19. No agregues cosas que no te he pedido en su lugar hasme la sugerencia para yo decidir
    
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