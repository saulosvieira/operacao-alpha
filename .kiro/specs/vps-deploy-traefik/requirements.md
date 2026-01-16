# Requirements Document

## Introduction

Este documento especifica os requisitos para adaptar a stack Docker Compose existente do projeto "Operação Alfa" para suportar deploy em VPS com reverse proxy Traefik e HTTPS automático, mantendo o ambiente de desenvolvimento local funcionando sem alterações no workflow atual.

## Glossary

- **VPS**: Virtual Private Server - servidor virtual onde a aplicação será hospedada em homologação
- **Traefik**: Reverse proxy moderno com suporte a descoberta automática de serviços via labels Docker
- **Let's Encrypt**: Autoridade certificadora que fornece certificados SSL/TLS gratuitos
- **ACME**: Protocolo usado pelo Let's Encrypt para emissão automática de certificados
- **Entrypoint_Web**: Container nginx (simulados-webserver) que recebe tráfego HTTP externo na porta 80 interna
- **Network_Web**: Rede Docker externa compartilhada entre Traefik e serviços que precisam de exposição pública
- **Profile**: Funcionalidade do Docker Compose que permite agrupar serviços para diferentes ambientes

## Requirements

### Requirement 1: Preservação do Ambiente Local

**User Story:** As a developer, I want to continue using the local development environment exactly as today, so that my workflow is not disrupted by the homolog deployment changes.

#### Acceptance Criteria

1. WHEN a developer runs `docker compose up -d` locally, THE System SHALL start all services without requiring Traefik or external network
2. WHEN running locally, THE System SHALL expose the webserver on port 8090 as currently configured
3. WHEN running locally, THE System SHALL expose database on port 33090 and Redis on port 63790 for debugging
4. THE System SHALL NOT require any additional configuration files or environment variables for local development beyond what exists today
5. WHEN the developer profile is activated with `--profile dev`, THE System SHALL also start the Vite development server

### Requirement 2: Traefik Reverse Proxy Infrastructure

**User Story:** As a DevOps engineer, I want a standalone Traefik reverse proxy configuration, so that I can manage multiple projects on the same VPS with a single proxy.

#### Acceptance Criteria

1. THE Traefik_Proxy SHALL listen on ports 80 and 443 for HTTP and HTTPS traffic respectively
2. THE Traefik_Proxy SHALL automatically obtain and renew SSL certificates via Let's Encrypt HTTP-01 challenge
3. THE Traefik_Proxy SHALL persist ACME certificates in a volume-mounted acme.json file with correct permissions (600)
4. THE Traefik_Proxy SHALL connect to an external Docker network named "web" for service discovery
5. THE Traefik_Proxy SHALL redirect all HTTP traffic to HTTPS automatically
6. THE Traefik_Proxy SHALL NOT expose its dashboard publicly by default
7. WHEN a new service with Traefik labels joins the "web" network, THE Traefik_Proxy SHALL automatically discover and route traffic to it

### Requirement 3: Homolog Environment Configuration

**User Story:** As a DevOps engineer, I want to deploy the application to homolog environment with HTTPS, so that stakeholders can test the application securely on a real domain.

#### Acceptance Criteria

1. WHEN deployed in homolog, THE System SHALL be accessible at https://operacao-alfa.homolog.mydev.com.br
2. WHEN deployed in homolog, THE Entrypoint_Web SHALL connect to the external "web" network for Traefik routing
3. WHEN deployed in homolog, THE Entrypoint_Web SHALL have Traefik labels configured for automatic HTTPS routing
4. WHEN deployed in homolog, THE System SHALL NOT expose database ports to the internet
5. WHEN deployed in homolog, THE System SHALL NOT expose Redis ports to the internet
6. WHEN deployed in homolog, THE System SHALL use persistent volumes for database data
7. WHEN deployed in homolog, THE System SHALL NOT start the Vite development server

### Requirement 4: Environment Separation Strategy

**User Story:** As a DevOps engineer, I want clear separation between local and homolog configurations, so that I can easily manage and deploy to different environments without confusion.

#### Acceptance Criteria

1. THE System SHALL use Docker Compose override files strategy: base `docker-compose.yml` + `docker-compose.override.yml` (local) + `docker-compose.homolog.yml` (VPS)
2. THE System SHALL provide a `.env.homolog.example` file with all required environment variables for homolog deployment
3. WHEN running locally, THE System SHALL automatically use `docker-compose.override.yml` without explicit file specification
4. WHEN running in homolog, THE System SHALL require explicit file specification: `-f docker-compose.yml -f docker-compose.homolog.yml`
5. THE System SHALL document the exact commands for each environment in a deployment guide

### Requirement 5: Laravel Proxy Configuration

**User Story:** As a developer, I want Laravel to correctly handle HTTPS behind Traefik proxy, so that URL generation and redirects work properly without infinite loops.

#### Acceptance Criteria

1. WHEN behind Traefik proxy, THE Laravel_App SHALL trust the X-Forwarded-* headers from Traefik
2. WHEN behind Traefik proxy, THE Laravel_App SHALL generate HTTPS URLs correctly
3. WHEN behind Traefik proxy, THE Laravel_App SHALL NOT create redirect loops between HTTP and HTTPS
4. THE `.env.homolog.example` SHALL include correct APP_URL with https:// protocol
5. THE `.env.homolog.example` SHALL include TRUSTED_PROXIES configuration for Traefik

### Requirement 6: Multi-Project Scalability

**User Story:** As a DevOps engineer, I want the infrastructure to support multiple projects on the same VPS, so that I can efficiently use server resources.

#### Acceptance Criteria

1. THE Traefik_Proxy SHALL be deployed independently from application stacks
2. THE Network_Web SHALL be created as an external network that multiple projects can join
3. WHEN a new project is added, THE System SHALL only require adding Traefik labels and connecting to the "web" network
4. THE Traefik_Proxy configuration SHALL NOT contain project-specific routing rules (all routing via labels)

### Requirement 7: Deployment Validation Checklist

**User Story:** As a DevOps engineer, I want a clear validation checklist, so that I can verify the deployment is working correctly.

#### Acceptance Criteria

1. THE Documentation SHALL include a pre-deployment checklist with DNS, firewall, and Cloudflare requirements
2. THE Documentation SHALL include post-deployment validation steps for SSL certificate verification
3. THE Documentation SHALL include troubleshooting guidance for common issues (redirect loops, certificate errors)
4. THE Documentation SHALL specify Cloudflare SSL/TLS mode requirements (DNS only initially, then Full strict)
