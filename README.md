[![Tests](https://github.com/Xolof/fishlog-backend/actions/workflows/tests.yml/badge.svg)](https://github.com/Xolof/fishlog-backend/actions/workflows/tests.yml)

# Fishlog backend

This is the backend for the Fishlog app, an app that allows users to save data about their catches and view them on a map.

The backend is made as a REST-API with Laravel.

## Start the application
`php artisan serve`

## Routes

### Show all catches

Route: `/api/public_fishcatch`

Method: GET

### Register

Route: `/api/register`

Method: POST

Required values: <string>name, <string>email, <string>password

### Log in

Route:  `/api/login`

Metod: POST

Required values: <string>email, <string>password 

### Log out

Route:  `/api/logout`

Metod: POST

Required values: <string>token (The JWT token you received upon login.)

### Get user

Route:  `/api/get_user`

Metod: GET

Required values: <string>token (The JWT token you received upon login.)

### Add a catch

Route:  `/api/create`

Metod: POST

Required values: <string>species, <int>length, <int>weight, <string>location, <binary>uploadImage, <string>date

Must be authenticated with JWT. 
Set a Request Header like the following:
Authorization: "Bearer <JWT_token>"

### Update a catch

Route:  `/api/update/<catch-id>`

Required values: <string>species, <int>length, <int>weight, <string>location, <binary>uploadImage, <string>date

Must be authenticated with JWT
Set a Request Header like the following:
Authorization: "Bearer <JWT_token>"

### Get data for a specific catch

Route:  `/api/fishcatch/<catch-id>`

Metod: GET

Must be authenticated with JWT
Set a Request Header like the following:
Authorization: "Bearer <JWT_token>"

### Delete a catch

Route:  `/api/delete/<catch-id>`

Metod: DELETE

Must be authenticated with JWT
Set a Request Header like the following:
Authorization: "Bearer <JWT_token>"
