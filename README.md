# intranet-konectados

url_api http://apiest.konecsys.com:8080

Endpoints: 

Clientes: /clientes
Retorno: 
```json
{
    "id": 1,
    "name": "Globalvía Pre Judicial",
    "prefix": "AUTO"
},
```

Canales: /canales

Estructuras: /estructura/id

    id: identificador del cliente en bd
    ex: /estructuras/11 -> ACSA
    Retorno: 

    ```json
        [
            {
                "COLUMN_NAME": "rut",
                "COLUMN_TYPE": "varchar(10)",
                "DATA_TYPE": "varchar",
                "TABLE_NAME": "cartera_primer_dia"
            },
        ]
        ```

