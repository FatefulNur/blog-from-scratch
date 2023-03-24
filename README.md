# Blog Project

This is a simple beginner blog project with lightweight functionality. The project will be updated overtime.


## Table of Contents

- [Getting Started](#getting-started)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Running the tests](#running-the-tests)
- [Usage](#usage)
- [Features](#features)
- [Build with](#build-with)
- [Versioning](#versioning)


## Getting Started

Hello friends. Welcome to this blog project. This project is all about a template with few features that can be used for your site. Stay tune for all features to meet.


## Prerequisites

Before you can start using the project, make sure you have installed:

- Localhost for example [Xampp](https://www.apachefriends.org/)
- Dependency management [Composer](https://getcomposer.org/)


## Installation

- Clone the repository : `git clone https://github.com/FatefulNur/blog-from-scratch.git`
- Install dependency : `composer install`
- Copy .env.example file to .env : `cp .env.example .env`
- Generate an application key : `php artisan key:generate`
- Set up your database in the .env file
- Run database migrations : `php artisan migrate`
- Seed the database : `php artisan db:seed`
- Start the development server : `php artisan serve`
- Visit the first page [Home](http://localhost:8000/home)


## Running the tests

Available to run application under *Testing* environment.
simply run `php artisan test` command to check application status.


## Usage

Create account [Here](http://localhost:8000/register) or you can use default credentials 
1. **Admin :** 
    - Email: `admin@test.com`
    - Password: `admin123`
2. **User :**
    - Email: `user@test.com`
    - Password: `user123`
    
Manage Administration
- Visit administration panel [Here](http://localhost:8000/admin/dashboard)


## Features

What I include in this version of application

1. Admin registration
2. User registration
3. Blog gallery
4. Hierarchical blog comments
5. Hierarchical categories
6. Multiple tags 
7. Flexible admin control


## Build With

- [Laravel](https://laravel.com/) : The web framework used
- [Composer](https://getcomposer.org/) : Dependency management


## Versioning

We use [SemVer](https://semver.org/) for versioning
