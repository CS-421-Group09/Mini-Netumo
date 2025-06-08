# Netumo Monitoring Microservice

**Live Demo:** [http://16.171.253.227/](http://16.171.253.227/)
**API Base URL:** [http://16.171.253.227/](http://16.171.253.227/)

## Overview

Netumo is a production-style microservice project for scheduled monitoring, SSL/domain checks, and contextual notifications. It uses Docker Compose to orchestrate multiple services including a React frontend, Laravel backend API, worker, MySQL, Redis, and Nginx load balancer.

---

## Features & Architecture

- **Monitoring Worker:** Periodically (every 5 min) issues HTTP/HTTPS requests to each target URL, logging status code, latency, and timestamp. Uses Laravel's async job queue (Redis-backed) and backs off on repeated failures.
- **Certificate & Domain Checks:** Once per day, queries SSL validity (OpenSSL) and domain expiry (WHOIS). Persists days-to-expiry in the database and raises alerts when ≤ 14 days remain.
- **Notification Service:** Sends alerts when (a) two successive downtime checks fail, or (b) SSL/domain threshold breached. Alerts via e-mail (Mailtrap/SES) and Slack/Discord webhook integration.
- **REST API:** CRUD endpoints for `/targets`, `/status/{id}`, `/history/{id}`, `/alerts`. Secured with JWT. Swagger/OpenAPI spec available.
- **Frontend Dashboard:** React dashboard displays target list with colour-coded status, latency, SSL/domain countdown, and a 24-h uptime chart. Shows responding frontend node ID.
- **Data Store:** MySQL (relational DB) with connection pooling and daily backup support.
- **Containerisation & Orchestration:** Docker Compose stack: load-balancer, 3 front-end instances, API, worker, database, Redis/queue. Health-checks, named volumes, environment overrides via `.env`.
- **CI/CD & Ops:** GitHub Actions workflow lints, tests, builds images, pushes to Docker Hub, and redeploys EC2 instance on main-branch merge. Zero-downtime rolling update (recreate strategy).

---

## Prerequisites

- [Docker](https://docs.docker.com/get-docker/) and [Docker Compose](https://docs.docker.com/compose/install/) installed
- (For deployment) An AWS EC2 instance with Docker and Docker Compose
- (Optional) [Git](https://git-scm.com/) for cloning the repository

---

## Project Structure

- `frontend/` — React app (dashboard)
- `backend/` — Laravel API, worker, and jobs
- `nginx.conf` — Nginx load balancer config
- `docker-compose.yml` — Orchestration for all services

---

## 1. Clone the Repository

```sh
git clone https://github.com/<your-org>/<your-repo>.git
cd <your-repo>
```

---

## 2. Environment Setup

### Backend

- Copy `.env.example` to `.env` in `backend/` and set your environment variables (DB, Redis, etc.).
- Generate Laravel app key:

  ```sh
  cd backend
  php artisan key:generate
  cd ..
  ```

- **Generate JWT secret:**

  ```sh
  cd backend
  php artisan jwt:secret
  cd ..
  ```

- (Optional) Configure mail settings for Mailtrap/SES and Slack/Discord webhook in `.env`.

### Frontend

- (Optional) Set environment variables in `frontend/.env` if needed (e.g., API URL, node ID).

---

## 3. Build and Run with Docker Compose

From the project root:

```sh
docker-compose up --build
```

- This will build and start all services: Nginx, 3 React frontends, backend API, worker, MySQL, and Redis.
- The app will be available at [http://localhost](http://localhost)

---

## 4. Database Migration & Seeding

In a new terminal, run:

```sh
docker-compose exec backend php artisan migrate --seed
```

- This will create the database schema and seed test data (including a test user and targets).

---

## 5. Accessing the Application

- **Frontend Dashboard:** [http://localhost](http://localhost)
- **API:** [http://localhost:9000](http://localhost:9000) (if exposed)
- **Nginx Load Balancer:** Handles routing to frontend instances
- **Swagger/OpenAPI Docs:** [http://localhost:9000/api/documentation](http://localhost:9000/api/documentation) (if enabled)

---

## 6. Stopping the Project

```sh
docker-compose down
```

- This will stop and remove all containers, but data in named volumes will persist.

---

## 7. Deployment to EC2 (Production)

1. SSH into your EC2 instance:

   ```sh
   ssh -i "/path/to/your-key.pem" ec2-user@<EC2_PUBLIC_IP>
   ```

2. Clone your repo and follow steps 2–6 above.
3. Make sure ports 80/443 are open in your EC2 security group.

---

## 8. CI/CD Pipeline

- GitHub Actions workflow lints, tests, builds, pushes Docker images, and deploys to EC2 on main-branch merge.
- See `.github/workflows/deploy.yml` for an example pipeline.
- Zero-downtime rolling update is achieved with `docker-compose up -d --remove-orphans --force-recreate`.

---

## 9. Useful Commands

- View running containers:

  ```sh
  docker ps
  ```

- View logs for a service:

  ```sh
  docker-compose logs <service>
  ```

- Run artisan commands:

  ```sh
  docker-compose exec backend php artisan <command>
  ```

- Run queue worker manually:

  ```sh
  docker-compose exec worker php artisan queue:work
  ```

---

## 10. Troubleshooting

- Ensure Docker and Docker Compose are installed and running.
- Check `.env` files for correct configuration.
- Use `docker-compose logs` to debug issues.
- For JWT issues, ensure you have generated the secret with `php artisan jwt:secret`.
- For mail/webhook notifications, verify your `.env` settings and credentials.

---

## 11. Credits

- Developed by the Netumo team for a microservice monitoring assignment.

---

## 12. License

- MIT or as specified in the repository.

---

## Post-Deployment Checklist

- [ ] Visit your EC2 public IP in a browser and verify the dashboard loads and all features work.
- [ ] Test email notifications (Mailtrap/SES) by simulating a downtime or SSL/domain expiry event.
- [ ] Test webhook notifications (Slack/Discord) by simulating a downtime or SSL/domain expiry event.
- [ ] Check that daily database backups are being created in `/var/backups/netumo` on the server.

## Database Backups

A daily backup script (`backup_db.sh`) is provided. To schedule automatic daily backups, add this line to your crontab on the EC2 instance:

```bash
0 2 * * * /bin/bash /home/ec2-user/Mini-Netumo/backup_db.sh
```

This will run the backup every day at 2:00 AM server time.

## Database Connection Pooling

Laravel uses persistent connections by default if configured. Ensure your `.env` contains:

```env
DB_CONNECTION=mysql
```

For advanced pooling, consider using a MySQL proxy (e.g., ProxySQL) if required by your rubric.
