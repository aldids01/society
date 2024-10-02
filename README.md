# Society Microfinance Loan Management using Laravel Filament

This is a Society Microfinance Loan Management  Laravel Filament application.

This web based application is hosted and running at https://apps.gechaan.com

## Members Features
- Loan Request
- Grain Request
- Summary Dashboard showing pending loan, pending grain, pending guaranteed loan, and total saving
- Saving Report
- Monthly Saving Modification
- Downloadable Loan Invoice
- Downloadable Grain Invoice
- Loan Report
- Grain Report

## Admin Features
- New Member Registration
- Request Approvals (Loan, Grain)
- Members Savings Report
- Members Loan and Grain Report
- Members Saving update using CSV Import

## How to Run Locally
### Prerequisites
- PHP 8.2 or Greater
- Composer
- MySQL

### Setup
1. Clone the repo:
   ```bash
   git clone https://github.com/aldids01/society.git
   cd your-repo
2. Set up your .env file
    - Database credentials
3. Run Migrations
   - php artisan migrate
4. Start your server 
    - php artisan serve or
    - virtual host
5. PHP Extension required
    - intl
    - zip
    - gd
