# Kahuna App

## Purpose

An app to help clients register products and send tickets and to help agents manage the products and reply to the clients. 

## Usage

1. Clone this repository.
2. Ensure Docker Desktop is running.
3. Open a terminal and change to the folder where you cloned this repository.
4. Run the run.cmd script.  
    4.1. On Windows, type **.\run.cmd**.    
    4.2. On macOS or Linux, type: **./run.cmd**.
5. Open [http://localhost:8001](https://localhost:8001) in your browser.

## Details

PHP has been setup as usual. A MariaDB server has also been created. Details follow:

- **Host**: mariadb
- **Database Name:** kahuna
- **User**: root
- **Pass**: root

The services started include:
- API Server on [http://localhost:8000](https://localhost:8000).
- Client on [http://localhost:8001](https://localhost:8001).

Uploaded on github:
https://github.com/il-bubli/MSWDNOV23/tree/f63f8c9d41b2688df49e5f0b42f1116e310407ed/FINAL%20PROJECT/sc-bed-finalproject-env