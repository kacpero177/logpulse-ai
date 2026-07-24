# LogPulse AI - Intelligent Log Monitoring and Analysis System

LogPulse AI is a real-time application log monitoring system that uses artificial intelligence (Llama 3 via Groq API) to automatically diagnose errors and dispatch actionable alerts via Discord Webhooks.

---

## Application Preview
### Main Dashboard
<img width="1905" height="914" alt="Image" src="https://github.com/user-attachments/assets/3246bca4-28f3-4a46-aca6-05fe7e96e4a7" />


### Discord Notification
<img width="899" height="387" alt="Image" src="https://github.com/user-attachments/assets/2fac2ffa-af58-4cc2-923a-aab5adb52742" />

---

## Key Features

* **Inbound REST API:** Securely ingests log entries from external microservices using Bearer token authentication.
* **Asynchronous Processing:** Handles background jobs and queue workers powered by Laravel Queues.
* **Automated AI Analysis:** Analyzes error messages and stack traces using the Llama 3.1 8B Instant model to generate concise root causes and actionable fix instructions.
* **Discord Alerting:** Sends formatted, color-coded embed alerts directly to configured Discord channels.
* **Management Dashboard:** Clean web portal built with Laravel Blade and Tailwind CSS featuring metrics, real-time status indicators, and pagination.

---

## System Requirements

Before running the project, ensure your environment meets the following requirements:

* PHP >= 8.2
* Composer (Dependency Manager for PHP)
* Node.js (v18+) and NPM
* MySQL or MariaDB Database Server (e.g., XAMPP, Laragon, or standalone MySQL)
* Groq Cloud Account (for Llama 3 API access)
* Discord Server with Webhook creation permissions

---

## Complete Step-by-Step Installation Guide

Follow these instructions sequentially to set up and run the project locally.

### Step 1: Clone the Repository
Open your terminal and clone the repository to your local machine:

git clone https://github.com/kacpero177/logpulse-ai.git

cd logpulse-ai

### Step 2: Install PHP and JavaScript Dependencies
Install all required PHP packages and build the frontend assets:

composer install
npm install
npm run build

### Step 3: Configure Environment File
Create your local environment configuration file by copying the template:

cp .env.example .env

Generate the application encryption key:

php artisan key:generate

### Step 4: Create and Configure the Database
1. Ensure your MySQL server is running (via XAMPP, Laragon, or local service).
2. Create an empty database named logpulse_ai:
   * SQL Command: CREATE DATABASE logpulse_ai;
   * Or use phpMyAdmin at http://localhost/phpmyadmin
3. Open the .env file and verify your database connection settings:

DB_CONNECTION=mysql

DB_HOST=127.0.0.1

DB_PORT=3306

DB_DATABASE=logpulse_ai

DB_USERNAME=root

DB_PASSWORD=

4. Run the database migrations to set up required tables:

php artisan migrate

### Step 5: Configure API Credentials in .env

#### Groq API Key (AI Diagnostics)
1. Sign up or log in at https://console.groq.com/
2. Navigate to API Keys and click Create API Key.
3. Copy the generated key and set it in your .env file:

GROQ_API_KEY=your_actual_groq_api_key_here

#### Discord Webhook URL (Real-time Alerts)
1. Open your Discord server and go to Channel Settings -> Integrations -> Webhooks.
2. Click New Webhook, assign a name (e.g., "LogPulse Bot"), and copy the Webhook URL.
3. Paste the URL into your .env file:

DISCORD_WEBHOOK_URL=https://discord.com/api/webhooks/your_webhook_path_here

---

## Running the Application

To operate the full log ingestion and background analysis pipeline, you must keep two terminal processes running simultaneously.

### Terminal 1: Web Application Server
Start the local development server:

php artisan serve

Access the application dashboard in your web browser at:  
http://127.0.0.1:8000/dashboard

### Terminal 2: Background Queue Worker
Start the queue worker to process AI analysis and Discord notifications asynchronously:

php artisan queue:listen

---

## Testing & Verification

You can verify the system functionality using either method below.

### Method 1: Dashboard Simulator Button
1. Open the dashboard at http://127.0.0.1:8000/dashboard
2. Click the Simulate Error button in the upper right header.
3. Observe Terminal 2 processing the job and check your Discord channel for the incoming notification embed.

### Method 2: Ingest Logs via REST API
1. Generate an API Bearer token in your terminal:

php artisan logpulse:create-token "TestClient"

2. Send a sample log payload via cURL:

curl -X POST http://127.0.0.1:8000/api/v1/logs \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_GENERATED_BEARER_TOKEN" \
  -d '{
    "service_name": "PaymentGateway",
    "level": "critical",
    "message": "Stripe API Connection Timeout",
    "stack_trace": "TimeoutException at line 42 in StripeClient.php"
  }'
