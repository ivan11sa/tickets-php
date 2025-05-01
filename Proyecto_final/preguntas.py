import random

def cargar_preguntas():
    return [
        {
            "pregunta": "Si solo poseemos una clave pública eso quiere decir que?",
            "opciones": [
                "Podemos replicar la clave privada a partir de ella",
                "Nos ha sido dada por el emisor de la clave privada",
                "Podemos firmar un documento y enviarlo solo al emisor de la clave privada",
                "Todas son correctas"
            ],
            "respuesta": "Nos ha sido dada por el emisor de la clave privada"
        },
        {
            "pregunta": "Si tenemos la clave privada podemos?",
            "opciones": [
                "Encriptar un documento",
                "Firmar un documento",
                "Publicarla y convertirla en clave pública",
                "Todo es correcto"
            ],
            "respuesta": "Firmar un documento"
        },
        {
            "pregunta": "La orden PASS es usada por?",
            "opciones": [
                "POP para indicar la contraseña del usuario en la fase de autorización",
                "TCP para indicar la contraseña del usuario en la fase de autenticación",
                "IP para indicar que el paquete del mensaje ha llegado a la dirección de destino",
                "Nada de lo anterior"
            ],
            "respuesta": "POP para indicar la contraseña del usuario en la fase de autorización"
        },
        {
            "pregunta": "IMAP se define como?",
            "opciones": [
                "Protocolo amplio de mensajes de internet",
                "Protocolo de acceso a mensajes de internet",
                "Protocolo aleatorio para mensajes de internet",
                "Nada de lo anterior"
            ],
            "respuesta": "Protocolo de acceso a mensajes de internet"
        },
        {
            "pregunta": "El puerto 25 es utilizado por?",
            "opciones": [
                "POP3",
                "IMAP",
                "IMAPS",
                "SMTP"
            ],
            "respuesta": "SMTP"
        },
        {
            "pregunta": "fetchmail se caracteriza por?",
            "opciones": [
                "Ser un MTA para Windows",
                "Ser un servidor solo de descarga de correo que trabaja en modo texto para Linux",
                "Ser un cliente solo de descarga de correo que trabaja en modo texto para Linux",
                "MUA para Windows"
            ],
            "respuesta": "Ser un cliente solo de descarga de correo que trabaja en modo texto para Linux"
        },
        {
            "pregunta": "Qué tipos de servidores nos podemos encontrar en el servicio de correo electrónico?",
            "opciones": [
                "Servidor de transferencia",
                "Servidor de descarga",
                "Servidor MUA",
                "La a) y la b) son correctas"
            ],
            "respuesta": "La a) y la b) son correctas"
        },
        {
            "pregunta": "Un buzón se define como?",
            "opciones": [
                "Una caja negra oculta donde se guarda la correspondencia en el cliente",
                "Un software agente llamado MBA (agente de buzón de correo)",
                "El espacio de almacenamiento donde un servidor guarda los email de las cuentas de los usuarios",
                "Nada de lo anterior"
            ],
            "respuesta": "El espacio de almacenamiento donde un servidor guarda los email de las cuentas de los usuarios"
        },
        {
            "pregunta": "La firma digital y el cifrado de la información de un correo electrónico se basan en?",
            "opciones": [
                "Una clave lógica y un algoritmo",
                "Una clave privada y otra pública",
                "Una clave matemática y un algoritmo",
                "Un hardware encriptador y una clave combinatoria"
            ],
            "respuesta": "Una clave privada y otra pública"
        },
        {
            "pregunta": "Algunas de las diferencias entre IMAP y POP3 es que?",
            "opciones": [
                "POP normalmente descarga el correo en el equipo cliente mientras IMAP los deja en el servidor",
                "Con IMAP se pueden organizar los correos en carpetas",
                "IMAP permite el acceso simultáneo a un buzón desde varios clientes y/o el acceso de un cliente a varios buzones al mismo tiempo",
                "Todas son correctas"
            ],
            "respuesta": "Todas son correctas"
        },
        {
            "pregunta": "Si tenemos la clave privada eso quiere decir que?",
            "opciones": [
                "Podemos obtener la clave pública a partir de ella",
                "Podemos generar tantas claves públicas distintas como queramos",
                "Podemos darla a quien queramos mantener correos seguros",
                "Hemos generado también la clave pública"
            ],
            "respuesta": "Hemos generado también la clave pública"
        },
        {
            "pregunta": "El MUA se ejecuta en un equipo?",
            "opciones": [
                "Funcionando como servidor de correo independientemente del sistema operativo",
                "Funcionando como cliente de correo independientemente del sistema operativo",
                "Con sistema operativo Windows",
                "Con sistema operativo Linux"
            ],
            "respuesta": "Funcionando como cliente de correo independientemente del sistema operativo"
        },
        {
            "pregunta": "La orden RCPT TO la emite el protocolo?",
            "opciones": [
                "POP3 para indicar el destinatario",
                "POP3 para indicar el remitente",
                "SMTP para indicar el destinatario",
                "IMAP para indicar el remitente"
            ],
            "respuesta": "SMTP para indicar el destinatario"
        },
        {
            "pregunta": "En un alias de una cuenta se da que?",
            "opciones": [
                "La cuenta alias está en diferente dominio que la cuenta de redirección",
                "Tanto la cuenta alias como la cuenta del destinatario se encuentran en el mismo dominio",
                "La cuenta del alias reemplaza a la cuenta del destinatario",
                "Ninguna de las anteriores"
            ],
            "respuesta": "Tanto la cuenta alias como la cuenta del destinatario se encuentran en el mismo dominio"
        },
        {
            "pregunta": "La cabecera de un e-mail que indica la dirección del remitente es?",
            "opciones": [
                "TO",
                "FROM",
                "CC",
                "SUBJECT"
            ],
            "respuesta": "FROM"
        },
        {
            "pregunta": "Los tipos MIME sirven para poder?",
            "opciones": [
                "Enviar información adjunta de diversos tipos (PDF, Fotos, Videos, Documentos Word, etc.)",
                "Gestionar el intercambio de mensajería por Internet Management Internet Mail Exchange (MIME)",
                "Gestor de envío de correo encriptado por Internet Management Internet Mail Encrypted (MIME)",
                "Nada de lo anterior"
            ],
            "respuesta": "Enviar información adjunta de diversos tipos (PDF, Fotos, Videos, Documentos Word, etc.)"
        },
        {
            "pregunta": "IMAPS usa el puerto X mientras que IMAP usa el puerto Y, siendo X e Y?",
            "opciones": [
                "143, 110",
                "995, 110",
                "993, 143",
                "110, 143"
            ],
            "respuesta": "993, 143"
        },
        {
            "pregunta": "Si queremos encriptar un correo electrónico usaremos?",
            "opciones": [
                "Primero la clave privada y luego la pública para finalizar",
                "La clave pública",
                "La clave privada",
                "Cualquiera de las dos vale"
            ],
            "respuesta": "La clave pública"
        },
        {
            "pregunta": "Si queremos enviar correo simultáneamente a múltiples destinatarios sin que ninguno tenga constancia de ese hecho?",
            "opciones": [
                "Ponemos los destinatarios en la cabecera CC",
                "Ponemos los destinatarios en la cabecera CO",
                "Ponemos los destinatarios en la cabecera BCO",
                "Ponemos los destinatarios en la cabecera CCO"
            ],
            "respuesta": "Ponemos los destinatarios en la cabecera CCO"
        },
        {
            "pregunta": "Pretty Good Privacy es un?",
            "opciones": [
                "Agente MUA (PGP)",
                "Protocolo bonito y bueno de privacidad",
                "Agente MDA (POP)",
                "Protocolo de privacidad bastante buena"
            ],
            "respuesta": "Protocolo de privacidad bastante buena"
        },
        {
            "pregunta": "Cuando en un cliente de correos configuramos el servidor de correo saliente y el servidor de correos entrante estamos?",
            "opciones": [
                "Configurando un servidor SMTP (servidor de correo saliente)",
                "Un servidor OPEN RELAY",
                "Configurando un servidor POP3 y/o IMAP (servidores de correo entrante)",
                "La a) y la c) son correctas"
            ],
            "respuesta": "La a) y la c) son correctas"
        },
        {
            "pregunta": "EHLO lo usa un cliente?",
            "opciones": [
                "TCP para confirmar la recepción del datagrama enviado (acuse de recibo)",
                "IMAP para confirmar la recepción del mensaje enviado (acuse de recibo)",
                "POP para solicitar al servidor el acuse de recibo del correo",
                "SMTP para saludar al servidor cuando éste acepta la conexión"
            ],
            "respuesta": "SMTP para saludar al servidor cuando éste acepta la conexión"
        },
        {
            "pregunta": "Sendmail es?",
            "opciones": [
                "Un cliente de correo que trabaja en modo texto para Linux",
                "Un cliente de correo que trabaja en modo gráfico para Linux",
                "Un servidor de correo (MTA) que trabaja con protocolos SMTP, POP3 e IMAP en Linux",
                "Un cliente de correo (MUA) que trabaja con protocolos SMTP, POP3 e IMAP en Linux"
            ],
            "respuesta": "Un servidor de correo (MTA) que trabaja con protocolos SMTP, POP3 e IMAP en Linux"
        },
        {
            "pregunta": "SMTP significa?",
            "opciones": [
                "Single Mail Transmission Protocol",
                "Single Mail Transfer Protocol",
                "Simple Mail Transfer Protocol",
                "Simple Mail Transmission Protocol"
            ],
            "respuesta": "Simple Mail Transfer Protocol"
        },
        {
            "pregunta": "Cuando a un servidor MTA le llega un mensaje este?",
            "opciones": [
                "Comprueba si la cuenta del destinatario pertenece al dominio",
                "Reenvía el correo hacia otro MTA si la cuenta del destinatario no pertenece al dominio",
                "Deja el mensaje en el buzón si la cuenta del destinatario pertenece a su dominio",
                "Todas son correctas"
            ],
            "respuesta": "Todas son correctas"
        },
        {
            "pregunta": "POP3 trabaja con el puerto?",
            "opciones": [
                "993",
                "995",
                "110",
                "143"
            ],
            "respuesta": "110"
        },
        {
            "pregunta": "Evolution es un agente?",
            "opciones": [
                "MTA",
                "MUA",
                "MDA",
                "SMTP"
            ],
            "respuesta": "MUA"
        }

    ]

def hacer_quiz():
    preguntas = cargar_preguntas()
    random.shuffle(preguntas)
    puntaje = 0
    
    for pregunta in preguntas:
        print("\n" + pregunta["pregunta"])
        opciones = pregunta["opciones"]
        random.shuffle(opciones)
        
        for i, opcion in enumerate(opciones, 1):
            print(f"{i}. {opcion}")
        
        respuesta_usuario = input("Selecciona el número de la respuesta correcta: ")
        
        try:
            respuesta_usuario = int(respuesta_usuario)
            if opciones[respuesta_usuario - 1] == pregunta["respuesta"]:
                print("\u00a1Correcto!")
                puntaje += 1
            else:
                print(f"Incorrecto. La respuesta correcta es: {pregunta['respuesta']}")
        except (IndexError, ValueError):
            print("Entrada no válida. Respuesta ignorada.")
    
    print(f"\nTu puntaje final es: {puntaje}/{len(preguntas)}")

if __name__ == "__main__":
    hacer_quiz()

