============
Docs
============

API
===============

Create Index
------------------
:Purpose: Create a new index in the Document Management System
:Role: Administrator
:Url: <app_url>/api/create_index
:Verb: POST
:Body: 
 - type: x-www-form-urlencoded   
 - Params: 
    - **index_name**: The name of the index to create
:Response:
 - type: application/json
 - **message**: Message that describes operation
 - **result**:
    - **status**: 1 = OK, 0 = Error
    - **native_result**: Raw result sent by registered DMS

Delete Index
------------------
:Purpose: Delete index in the Document Management System
:Role: Administrator
:Url: <app_url>/api/delete_index
:Verb: DELETE
:Body: 
 - type: x-www-form-urlencoded   
 - Params: 
    - **index_name**: The name of the index to delete
:Response:
 - type: application/json
 - **message**: Message that describes operation
 - **result**:
    - **status**: 1 = OK, 0 = Error
    - **native_result**: Raw result sent by registered DMS

Index Document
------------------
:Purpose: Index document in the Document Management System
:Role: User
:Url: <app_url>/api/index_document
:Verb: POST
:Body: 
 - type: form-data 
 - Params: 
    - **file_to_upload**: (type=file) The file to upload to Storage System
    - **key0**: (type=string) 1st metadata key name
    - **value0**: (type=string) 1st metadata value
    - **key<n>**: (type=string) <n>th metadata key name
    - **value<n>**: (type=string) <n>th metadata value
:Response:
 - type: application/json
 - **message**: Message that describes operation
 - **result**:
    - **status**: 1 = OK, 0 = Error
    - **id**: Indexed document ID
    - **native_result**: Raw result sent by registered DMS
