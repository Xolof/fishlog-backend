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

Required values: name, email, password

### Log in

Route:  `/api/login`

Metod: POST

Required values: email, password 

### Log out

Route:  `/api/logout`

Metod: POST

Required values: token

### Get user

Route:  `/api/get_user`

Metod: GET

Required values: token

### Add a catch

Route:  `/api/create`

Metod: POST

Required values: species, length, weight, location, uploadImage, date

Must be authenticated with JWT

### Update a catch

Route:  `/api/update/<catch-id>`

Required values: species, length, weight, location, uploadImage, date

Must be authenticated with JWT

### Get data for a specific catch

Route:  `/api/fishcatch/<catch-id>`

Metod: GET

Must be authenticated with JWT

### Delete a catch

Route:  `/api/delete/<catch-id>`

Metod: DELETE

Must be authenticated with JWT
