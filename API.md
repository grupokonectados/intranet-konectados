# API

URL: `http://apiest.konecsys.com`

Puerto: `:8080`

Metodos aceptados: 
- `GET`
- `POST`
- `PUT`
- `DELETE`



## Endpoints: 
1. [Clientes](#clientes)
2. [Canales](#canales)
3. [Estructuras](#estructuras)
4. [Estrategias](#estrategias)


&nbsp;
<a id="clientes"></a>

#### 1. Clientes: 
##### 1.1 Todos los clientes
&nbsp;

|Ruta|Metodo|Descripcion|
|-|-|-|
|`/clientes`| `GET`|Obtengo todos los clientes.|

Respuesta: 
```json
[
    [
        {
          "id": 1,
          "name": "Globalvía Pre Judicial",
          "prefix": "AUTO",
          "channels": null
        },
        {
          "id": 11,
          "name": "Autopista Central",
          "prefix": "ACSA",
          "channels": null
        },
    ]
]
```
&nbsp;

##### 1.2 Actualizar los canales.
&nbsp;

|Ruta|Metodo|Parametros|Descripcion|
|-|-|-|-|
|`cliente/canales`|`PUT`|`idClient`: identificador del cliente \| `channels`: json con la configuracion de los canales. | Actualizo la configuracion de los canales del cliente.|





---
<a id="canales"></a>

#### 2. Canales: 
##### 2.1 Todos los caneles
&nbsp;

|Ruta|Metodo|Descripcion|
|-|-|-|
|`/canales`|`GET`|Obtengo todos los canales.|

Resultado: 
```json
[
    [
        {
            "id": 1,
            "name": "Agente",
            "createdAt": "2023-07-10T00:00:00.000Z",
            "active": 1
        },
        {
            "id": 2,
            "name": "eMail",
            "createdAt": "2023-07-10T00:00:00.000Z",
            "active": 1
        },
    ]
]
```
---
<a id="estructuras"></a>

#### 3. Estructuras: 
##### 3.1 Toda la estructura por cliente demandante
&nbsp;

|Ruta|Metodo|Parametros|Descripcion|
|-|-|-|-|
|`/estructura/:id`|`GET`|`id`: identificador del cliente |Obtengo toda la estructura del cliente demandante.|

Resultado: 
```json
[
    [
        {
            "COLUMN_NAME": "ic",
            "COLUMN_TYPE": "int(11)",
            "DATA_TYPE": "int",
            "TABLE_NAME": "cartera_primer_dia"
        },
        {
            "COLUMN_NAME": "rut",
            "COLUMN_TYPE": "varchar(10)",
            "DATA_TYPE": "varchar",
            "TABLE_NAME": "cartera_primer_dia"
        },
    ]
]
 ```
---

<a id="estrategias"></a>
#### 4. Estrategias: 
&nbsp;
##### 4.1 Estrategias de un cliente demandante especifico.
&nbsp;
Entrega todas las estrategias que posee el demandante.

|Ruta|Metodo|Parametros|Descripcion|
|-|-|-|-|
|`/estrategias/:PREFIX`|`GET`|`PREFIX`: Prefijo del cliente |Obtengo toda las estrategias del cliente.|

Resultado: 

```json
[
    [
        {
            "id": 1,
            "prefix_client": "ACSA",
            "channels": 0,
            "table_name": "cartera_primer_dia",
            "onlyWhere": "dias <= 361",
            "repeatUsers": 0,
            "activation_date": null,
            "activation_time": null,
            "registros_unicos": 2126,
            "registros_repetidos": 2560,
            "total_registros": 4686,
            "cobertura": 21.26,
            "registros": "[\"1705969-6\", \"3113958-9\", \"3196801-1\", \"3350576-0\",\"9877943-4\"]",
            "type": 1,
            "isActive": 0,
            "isDelete": 0,
            "created_at": "2023-07-12T17:51:18.000Z",
            "updated_at": null
        },
    ]
]

```
&nbsp;
##### 4.2 Registrar una estrategia.
&nbsp;

|Ruta|Metodo|Parametros|Descripcion|
|-|-|-|-|
|`/estrategias/:PREFIX`|`POST`|`jsonRequest`: Objeto json con los datos para registrar una estrategia |Obtengo toda las estrategias del cliente.|

Peticion:

```json
{
    "onlyWhere": "monto <= 10000",
    "channels": "0",
    "table_name": "cartera_primer_dia",
    "prefix_client": "ACSA",
    "registros_unicos": "357",
    "registros_repetidos": "1984",
    "total_registros": "2341",
    "cobertura": "3.57",
    "type": 1,
    "repeatUsers": 0,
    "registros": "[\"1705969-6\",\"3113958-9\",\"3196801-1\",\"3350576-0\",\"3356611-5\",\"9877943-4\"]"
}
```

Resultados: 
**NOTA: Siempre va a retornar un 200 asi exista un error.**

Pero tenemos 2 casos: 
1. Registro exitoso.
    ```json
    {
        "fieldCount": 0,
        "affectedRows": 1,
        "insertId": 0,
        "serverStatus": 2,
        "warningCount": 0,
        "message": "",
        "protocol41": true,
        "changedRows": 0
    }
    ```

2. Registro no exitoso
    ```
    false
    ```
    > El error es retornado directamente por la ejecucion del MySQL, no se esta devolviendo mensaje de error tampoco un `badrequest` o `estatus 400`o cualquier mensaje de retorno utilizado en API REST.

&nbsp;
##### 4.3 Estrategias de `type` 1, 2:

Para el diseño entrega todas las estrategias que no se encuentren en el historico.

|Ruta|Metodo|Parametros|Descripcion|
|-|-|-|-|
|`/estrategias/diseno/:PREFIX`|`GET`|`PREFIX`: Prefijo del cliente |Obtengo toda las estrategias del cliente.|

Resultado: 

```json
[
    [
        {
            "id": 1,
            "prefix_client": "ACSA",
            "channels": 0,
            "table_name": "cartera_primer_dia",
            "onlyWhere": "dias <= 361",
            "repeatUsers": 0,
            "activation_date": null,
            "activation_time": null,
            "registros_unicos": 2126,
            "registros_repetidos": 2560,
            "total_registros": 4686,
            "cobertura": 21.26,
            "registros": "[\"1705969-6\", \"3113958-9\", \"3196801-1\", \"3350576-0\",\"9877943-4\"]",
            "type": 1,
            "isActive": 0,
            "isDelete": 0,
            "created_at": "2023-07-12T17:51:18.000Z",
            "updated_at": null
        },
        {
            "id": 2,
            "prefix_client": "ACSA",
            "channels": 0,
            "table_name": "cartera_primer_dia",
            "onlyWhere": "dias <= 361",
            "repeatUsers": 0,
            "activation_date": null,
            "activation_time": null,
            "registros_unicos": 2126,
            "registros_repetidos": 2560,
            "total_registros": 4686,
            "cobertura": 21.26,
            "registros": "[\"1705969-6\", \"3113958-9\", \"3196801-1\", \"3350576-0\",\"9877943-4\"]",
            "type": 2,
            "isActive": 0,
            "isDelete": 0,
            "created_at": "2023-07-12T17:51:18.000Z",
            "updated_at": null
        },
    ]
]

```

&nbsp;
##### 4.4 Eliminar una estrategia:
 
|Ruta|Metodo|Parametros|Descripcion|
|-|-|-|-|
|`/estrategia/eliminar/:id`|`DELETE`|`id`: id de la estrategia a eliminar |Obtengo toda las estrategias del cliente.|

Resultado exitoso: 

```json
{
  "status": "201",
  "message": "success"
}

```
> No retorna resultados de error.

&nbsp;
##### 4.5 Activar una estrategia:
 
|Ruta|Metodo|Parametros|Descripcion|
|-|-|-|-|
|`/estrategia/activar/:id`|`PUT`|`id`: id de la estrategia que se activara |Obtengo toda las estrategias del cliente.|

Resultado exitoso: 

```json
{
  "status": "201",
  "message": "success"
}

```


&nbsp;
##### 4.6 Detener una estrategia:
 
|Ruta|Metodo|Parametros|Descripcion|
|-|-|-|-|
|`/estrategia/detener/:id`|`PUT`|`id`: id de la estrategia que se va a detener y pasar al historico |Obtengo toda las estrategias del cliente.|

Resultado exitoso: 

```json
{
  "status": "201",
  "message": "success"
}

```


&nbsp;
##### 4.7 Filtro por tipo:
 
|Ruta|Metodo|Parametros|Descripcion|
|-|-|-|-|
|`/estrategia/tipo`|`GET`|`prefix`: Prefijo del cliente `type`: tipo de estrategias que quiero consultar |Obtengo toda las estrategias del cliente segun el tipo que se este consultando.|

Resultado exitoso: 

```json
[
    [
        {
            "id": 1,
            "prefix_client": "ACSA",
            "channels": 0,
            "table_name": "cartera_primer_dia",
            "onlyWhere": "dias <= 361",
            "repeatUsers": 0,
            "activation_date": null,
            "activation_time": null,
            "registros_unicos": 2126,
            "registros_repetidos": 2560,
            "total_registros": 4686,
            "cobertura": 21.26,
            "registros": "[\"1705969-6\", \"3113958-9\", \"3196801-1\", \"3350576-0\",\"9877943-4\"]",
            "type": 1,
            "isActive": 0,
            "isDelete": 0,
            "created_at": "2023-07-12T17:51:18.000Z",
            "updated_at": null
        },
        {
            "id": 2,
            "prefix_client": "ACSA",
            "channels": 0,
            "table_name": "cartera_primer_dia",
            "onlyWhere": "dias <= 361",
            "repeatUsers": 0,
            "activation_date": null,
            "activation_time": null,
            "registros_unicos": 2126,
            "registros_repetidos": 2560,
            "total_registros": 4686,
            "cobertura": 21.26,
            "registros": "[\"1705969-6\", \"3113958-9\", \"3196801-1\", \"3350576-0\",\"9877943-4\"]",
            "type": 2,
            "isActive": 0,
            "isDelete": 0,
            "created_at": "2023-07-12T17:51:18.000Z",
            "updated_at": null
        },
    ]
]

```


&nbsp;
##### 4.8 Filtro historico:
 
|Ruta|Metodo|Parametros|Descripcion|
|-|-|-|-|
|`/estrategia/historico`|`GET`|`prefix`: Prefijo del cliente `canal`: tipo de estrategias que quiero consultar | Obtengo toda las estrategias del cliente de tipo 3 unicamente, pero segun el canal que estoy consultando, esto para el filtro de historico.|

Resultado exitoso: 

```json
[
    [
        {
            "id": 1,
            "prefix_client": "ACSA",
            "channels": 0,
            "table_name": "cartera_primer_dia",
            "onlyWhere": "dias <= 361",
            "repeatUsers": 0,
            "activation_date": null,
            "activation_time": null,
            "registros_unicos": 2126,
            "registros_repetidos": 2560,
            "total_registros": 4686,
            "cobertura": 21.26,
            "registros": "[\"1705969-6\", \"3113958-9\", \"3196801-1\", \"3350576-0\",\"9877943-4\"]",
            "type": 1,
            "isActive": 0,
            "isDelete": 0,
            "created_at": "2023-07-12T17:51:18.000Z",
            "updated_at": null
        },
        {
            "id": 2,
            "prefix_client": "ACSA",
            "channels": 0,
            "table_name": "cartera_primer_dia",
            "onlyWhere": "dias <= 361",
            "repeatUsers": 0,
            "activation_date": null,
            "activation_time": null,
            "registros_unicos": 2126,
            "registros_repetidos": 2560,
            "total_registros": 4686,
            "cobertura": 21.26,
            "registros": "[\"1705969-6\", \"3113958-9\", \"3196801-1\", \"3350576-0\",\"9877943-4\"]",
            "type": 2,
            "isActive": 0,
            "isDelete": 0,
            "created_at": "2023-07-12T17:51:18.000Z",
            "updated_at": null
        },
    ]
]

```


