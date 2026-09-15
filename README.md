# 🦅 Hyperodactyl

<p align="center">
  <img src="https://i.postimg.cc/VvWF53xk/hypernet-logo.png" alt="HyperNET Logo" width="200">
</p>

<p align="center">
  <strong>A modern, powerful and extensible server management platform.</strong>
</p>

<p align="center">
  Manage game servers, containers, infrastructure and users from one unified panel.
</p>

<p align="center">
  <a href="#features">Features</a> •
  <a href="#installation">Installation</a> •
  <a href="#requirements">Requirements</a> •
  <a href="#configuration">Configuration</a> •
  <a href="#architecture">Architecture</a> •
  <a href="#roadmap">Roadmap</a>
</p>

---

## 🚀 What is Hyperodactyl?

**Hyperodactyl** is a modern server management platform designed for managing game servers, applications, containers, and infrastructure through a powerful web interface.

Built as part of the **HyperNET LTD ecosystem**, Hyperodactyl aims to provide a clean, modern, and feature-rich platform for hosting providers, communities, developers, and infrastructure operators.

With Hyperodactyl, administrators can manage nodes, servers, users, networking, allocations, backups, containers, and more from one centralized dashboard.

---

# ✨ Features

## 🎮 Server Management

* Create and manage servers
* Start, stop, restart and kill servers
* Real-time server status monitoring
* Resource usage statistics
* CPU, RAM and disk limits
* Server suspension controls
* Server reinstall functionality
* Startup configuration
* Environment variables
* Server resource limits

## 🖥️ Server Console

* Real-time console access
* Live server logs
* Send commands directly to servers
* Server power controls
* Console permission management

## 📁 File Management

* Upload files
* Download files
* Create files and folders
* Edit files directly from the browser
* Rename files and directories
* Delete files and directories
* Archive files
* Extract archives
* File permission management

## 💾 Backup Management

* Create server backups
* Restore backups
* Download backups
* Delete backups
* Backup limits
* Backup scheduling support

## ⏰ Server Schedules

Automate server tasks including:

* Server restarts
* Command execution
* Power actions
* Backup creation
* Scheduled maintenance

## 🌐 Networking

* IP allocations
* Port allocations
* Server networking
* Node networking
* IPv4 support
* IPv6 support
* Allocation management

## 🖥️ Node Management

* Create and manage nodes
* Configure node resources
* CPU allocation
* Memory allocation
* Disk allocation
* Network configuration
* Node monitoring
* Server deployment management

## 📦 Container Support

* Container deployment
* Container management
* Resource allocation
* Container power controls
* Console access
* Template-based deployments

## 👥 User Management

* User accounts
* Authentication system
* Account management
* Password management
* Email verification
* Two-factor authentication
* Subusers
* Server permissions
* Role-based access control

## 🛡️ Administration Panel

* User management
* Server management
* Node management
* Allocation management
* Database management
* System configuration
* Role and permission management
* Platform monitoring

## 🔑 API

Hyperodactyl provides API capabilities for external applications and integrations.

API resources include:

* Servers
* Users
* Nodes
* Allocations
* Backups
* Schedules
* Templates
* Account management

Example API structure:

```text
/api/v1/servers
/api/v1/users
/api/v1/nodes
/api/v1/allocations
/api/v1/backups
```

---

# 📋 Requirements

Before installing Hyperodactyl, ensure your server meets the following requirements.

### Operating System

* Ubuntu 22.04 LTS or newer recommended
* Debian 12 or newer recommended

### Required Software

* Git
* Node.js
* npm
* PostgreSQL or MySQL
* Redis
* Docker
* Docker Compose
* Nginx or Caddy

### Recommended Server Resources

| Component        |      Minimum |  Recommended |
| ---------------- | -----------: | -----------: |
| CPU              |      2 Cores |     4+ Cores |
| RAM              |         4 GB |        8+ GB |
| Storage          |        20 GB |       50+ GB |
| Operating System | Ubuntu 22.04 | Ubuntu 24.04 |

---

# ⚡ Installation

## 1. Update Your Server

```bash
sudo apt update && sudo apt upgrade -y
```

Install required system packages:

```bash
sudo apt install -y git curl ca-certificates build-essential
```

---

## 2. Install Node.js

Install Node.js using NodeSource:

```bash
curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash -
sudo apt install -y nodejs
```

Verify the installation:

```bash
node -v
npm -v
```

---

## 3. Clone Hyperodactyl

Clone the official repository:

```bash
git clone https://github.com/NotFlexxy-1/hyperodactyl-panel.git
```

Enter the project directory:

```bash
cd hyperodactyl-panel
```

---

## 4. Install Dependencies

Install project dependencies:

```bash
npm install
```

If the project uses a lockfile and supports clean installation:

```bash
npm ci
```

---

## 5. Configure Environment Variables

Create the environment file:

```bash
cp .env.example .env
```

Edit the configuration:

```bash
nano .env
```

Configure the required values, including:

```env
NODE_ENV=production
PORT=3000

DATABASE_URL=

REDIS_URL=

APP_URL=
```

Replace the empty values with your actual infrastructure configuration.

---

## 6. Build Hyperodactyl

Build the production application:

```bash
npm run build
```

---

## 7. Start Hyperodactyl

Start the application:

```bash
npm start
```

For development:

```bash
npm run dev
```

The application should now be available on:

```text
http://YOUR_SERVER_IP:3000
```

---

# 🐳 Docker Installation

If your Hyperodactyl release includes Docker support, install Docker first.

## Install Docker

```bash
curl -fsSL https://get.docker.com | sudo sh
```

Enable Docker:

```bash
sudo systemctl enable docker
sudo systemctl start docker
```

Verify Docker:

```bash
docker --version
```

Install Docker Compose:

```bash
sudo apt install -y docker-compose-plugin
```

Verify:

```bash
docker compose version
```

---

## Build With Docker

From the Hyperodactyl directory:

```bash
docker compose up -d --build
```

Check running containers:

```bash
docker compose ps
```

View logs:

```bash
docker compose logs -f
```

Stop the platform:

```bash
docker compose down
```

Restart:

```bash
docker compose restart
```

---

# 🌐 Production Deployment

For production environments, it is recommended to run Hyperodactyl behind a reverse proxy.

Supported reverse proxies include:

* Nginx
* Caddy
* Cloudflare

Your architecture should look similar to:

```text
Internet
    │
    ▼
Cloudflare / Reverse Proxy
    │
    ▼
Nginx / Caddy
    │
    ▼
Hyperodactyl Application
    │
    ├── Database
    ├── Redis
    └── Infrastructure Nodes
```

---

# 🔧 Configuration

Hyperodactyl uses environment variables for application configuration.

Example:

```env
NODE_ENV=production
PORT=3000

APP_URL=https://panel.example.com

DATABASE_URL=postgresql://USER:PASSWORD@localhost:5432/hyperodactyl

REDIS_URL=redis://127.0.0.1:6379
```

Never expose your production `.env` file publicly.

---

# 🏗️ Architecture

Hyperodactyl is built around a centralized management architecture.

```text
                         ┌─────────────────────┐
                         │    Hyperodactyl     │
                         │      Web Panel      │
                         └──────────┬──────────┘
                                    │
                         ┌──────────▼──────────┐
                         │      API Core       │
                         └──────────┬──────────┘
                                    │
              ┌─────────────────────┼─────────────────────┐
              │                     │                     │
      ┌───────▼────────┐   ┌────────▼────────┐   ┌────────▼────────┐
      │ Server Manager │   │  Node Manager   │   │   User Manager  │
      └───────┬────────┘   └────────┬────────┘   └─────────────────┘
              │                     │
              └──────────┬──────────┘
                         │
              ┌──────────▼──────────┐
              │ Infrastructure Nodes│
              │                     │
              │ Game Servers        │
              │ Applications        │
              │ Containers          │
              └─────────────────────┘
```

---

# 🔐 Security

Hyperodactyl includes security-focused infrastructure controls.

* Secure authentication
* Role-based access control
* Two-factor authentication support
* API authentication
* Permission isolation
* Secure server access
* Session management
* Environment-based configuration

Administrators should always:

* Use HTTPS
* Keep dependencies updated
* Protect environment variables
* Use strong passwords
* Restrict database access
* Restrict infrastructure node access
* Configure firewall rules

---

# 📊 Monitoring

Monitor infrastructure and server resources from the centralized panel.

Available monitoring areas may include:

* CPU usage
* Memory usage
* Disk usage
* Network usage
* Server status
* Node status
* Resource utilization

---

# 🗺️ Roadmap

## Core Platform

* [x] Hyperodactyl foundation
* [x] Dashboard
* [x] Server management
* [x] User management
* [x] Administration system

## Upcoming

* [ ] Improved registration system
* [ ] Social profile customization
* [ ] Layout customization
* [ ] Color customization
* [ ] Advanced infrastructure monitoring
* [ ] API improvements
* [ ] Performance improvements
* [ ] Advanced container management
* [ ] LXC support
* [ ] Proxmox integration
* [ ] Discord integration
* [ ] Plugin system

---

# 🤝 Contributing

Contributions are welcome.

## Getting Started

Fork the repository and clone your fork:

```bash
git clone https://github.com/YOUR_USERNAME/hyperodactyl-panel.git
```

Create a branch:

```bash
git checkout -b feature/my-feature
```

Make your changes and test them.

Commit:

```bash
git add .
git commit -m "Add my feature"
```

Push:

```bash
git push origin feature/my-feature
```

Then create a Pull Request.

---

# 🐛 Bug Reports

If you find a bug, please open an issue and include:

* Hyperodactyl version
* Operating system
* Node.js version
* Steps to reproduce
* Expected behavior
* Actual behavior
* Error logs
* Screenshots where applicable

---

# 📁 Project Structure

A typical Hyperodactyl installation may contain:

```text
hyperodactyl-panel/
├── src/                 # Application source code
├── public/              # Public assets
├── components/          # UI components
├── pages/               # Application pages
├── api/                 # API routes
├── database/            # Database configuration
├── config/              # Application configuration
├── .env                 # Environment variables
├── package.json         # Node.js dependencies
└── README.md            # Project documentation
```

---

# 📜 License

Please see the [LICENSE](LICENSE) file for licensing information.

---

# 🌐 HyperNET LTD

Hyperodactyl is developed as part of the HyperNET ecosystem.

### HyperNET Projects

* Hyperodactyl
* HyperVM
* HyperNAME
* HyperRADAR
* HyperTERM
* HyperOFFICE

---

# 👨‍💻 Developer

Developed by **NotFlexxy**.

Part of **HyperNET LTD**.

---

<p align="center">
  <strong>🦅 Hyperodactyl</strong>
</p>

<p align="center">
  Modern infrastructure and server management.
</p>

<p align="center">
  © 2026 HyperNET LTD. All rights reserved.
</p>
