# 🔍 DevKraken USA Zip Search

![PHP Version](https://img.shields.io/badge/PHP-8.3-blue.svg)
![MySQL Version](https://img.shields.io/badge/MySQL-8.0-orange.svg)
![Redis Version](https://img.shields.io/badge/Redis-Alpine-red.svg)
![NGINX Version](https://img.shields.io/badge/NGINX-Alpine-green.svg)
![Docker](https://img.shields.io/badge/Docker-Enabled-blue.svg)

A robust and efficient search service built with PHP 8.3, leveraging Docker for easy deployment and scalability.

## 📋 Table of Contents

- [Features](#-features)
- [Prerequisites](#-prerequisites)
- [Installation](#-installation)
- [Usage](#-usage)
- [Docker Setup](#-docker-setup)
- [API Endpoints](#-api-endpoints)
- [Contributing](#-contributing)

## ✨ Features

- 🚀 High-performance search functionality
- 🐳 Dockerized environment for easy setup and deployment
- 🔒 Secure API with input validation
- 🗄️ MySQL database for persistent storage
- 🚦 Redis caching for improved performance
- 🌐 NGINX as a reverse proxy

## 🛠 Prerequisites

- Docker and Docker Compose
- Git

## 🚀 Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/devkraken-search-service.git
   cd devkraken-search-service
   ```
2. Build and start the Docker containers:
   ```bash
   docker-compose up -d --build
   ```

3. The service should now be running at http://localhost:8080.

## 🖥 Usage

To use the search service, send a GET request to the API endpoint with a search query:

```text
http://localhost:8080/?search=your_search_term
```
Replace `your_search_term` with the actual term you want to search for.

## 🐳 Docker Setup

The project uses Docker Compose to manage the following services:

* PHP-FPM (8.3)
* MySQL (8.0)
* Redis (Alpine)
* NGINX (Alpine)

To rebuild the Docker environment:

```bash
docker-compose down -v
docker-compose up -d --build
```

## 🔌 API Endpoints

### Search

- **URL:** `/`
- **Method:** `GET`
- **URL Params:**
   - Required: `search=[string]`
- **Success Response:**
   - Code: 200
   - Content:
     ```json
     [
       {
         "physical_city": "New York",
         "physical_state_abbr": "NY",
         "physical_zip": "10001",
         "district_name": "Manhattan"
       }
     ]
     ```
- **Error Response:**
   - Code: 400
   - Content:
     ```json
     {
       "error": "Search parameter is required"
     }
     ```

- **Example Request:** 
  - `GET /?search=New%20York`
   -
   - **Example Success Response:**
      ```json
         [
           {
             "physical_city": "New York",
             "physical_state_abbr": "NY",
             "physical_zip": "10001",
             "district_name": "Manhattan"
           },
           {
             "physical_city": "New York",
             "physical_state_abbr": "NY",
             "physical_zip": "10002",
             "district_name": "Lower East Side"
           }
         ]
      ```
  - **Example Error Response:**
     ```json
     {
       "error": "Search parameter is required"
     }
     ```

## 🤝 Contributing
Contributions are welcome! Please feel free to submit a Pull Request.

## Developed with ❤ ️by **DevKraken**
