# Implementation Plan: VPS Deploy with Traefik

## Overview

Este plano implementa a adaptação da stack Docker Compose para suportar deploy em VPS com Traefik como reverse proxy, mantendo o ambiente local funcionando sem alterações. A implementação segue a estratégia de override files do Docker Compose.

## Tasks

- [x] 1. Criar infraestrutura Traefik standalone
  - [x] 1.1 Criar arquivo docker-compose.proxy.yml com Traefik v3
    - Configurar entrypoints web (80) e websecure (443)
    - Configurar certificateResolver "le" com HTTP-01 challenge
    - Configurar volume para acme.json
    - Conectar à rede externa "web"
    - Desabilitar dashboard público
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6_

  - [x] 1.2 Criar diretório traefik/ e arquivo acme.json vazio
    - Criar estrutura de diretórios
    - Documentar permissões necessárias (chmod 600)
    - _Requirements: 2.3_

- [x] 2. Refatorar docker-compose.yml base
  - [x] 2.1 Remover port mappings do docker-compose.yml principal
    - Mover ports de simulados-webserver para override
    - Mover ports de simulados-db para override
    - Mover ports de simulados-redis para override
    - Manter apenas configuração base dos serviços
    - _Requirements: 1.4, 3.4, 3.5_

- [x] 3. Criar docker-compose.override.yml para ambiente local
  - [x] 3.1 Criar arquivo com port mappings locais
    - simulados-webserver: 8090:80
    - simulados-db: 33090:3306
    - simulados-redis: 63790:6379
    - _Requirements: 1.1, 1.2, 1.3_

- [x] 4. Criar docker-compose.homolog.yml para VPS
  - [x] 4.1 Criar arquivo com configurações de homolog
    - Adicionar rede externa "web" ao webserver
    - Adicionar labels Traefik completas ao webserver
    - NÃO incluir port mappings para db e redis
    - _Requirements: 3.2, 3.3, 3.4, 3.5, 3.6_

- [x] 5. Criar template de variáveis de ambiente para homolog
  - [x] 5.1 Criar arquivo .env.homolog.example
    - APP_URL com https://operacao-alfa.homolog.mydev.com.br
    - TRUSTED_PROXIES=*
    - Variáveis de banco apontando para container interno
    - Variáveis de Redis apontando para container interno
    - _Requirements: 5.1, 5.4, 5.5_

- [x] 6. Checkpoint - Validar configurações
  - Verificar que `docker compose config` local mostra portas
  - Verificar que `docker compose -f docker-compose.yml -f docker-compose.homolog.yml config` NÃO mostra portas de db/redis
  - Verificar labels Traefik presentes na config homolog
  - Testar `docker compose up -d` local funciona sem rede "web"

- [x] 7. Criar documentação de deploy
  - [x] 7.1 Criar arquivo DEPLOY.md com instruções completas
    - Comandos para ambiente local
    - Comandos para ambiente homolog
    - Checklist pré-deploy (DNS, firewall, Cloudflare)
    - Checklist pós-deploy (validação SSL, testes)
    - Troubleshooting comum
    - _Requirements: 4.5, 7.1, 7.2, 7.3, 7.4_

- [x] 8. Final checkpoint - Validação completa
  - Testar ambiente local: `docker compose up -d`
  - Verificar acesso http://localhost:8090
  - Verificar acesso ao banco localhost:33090
  - Documentar comandos finais de deploy

## Notes

- A implementação não requer testes automatizados pois é configuração de infraestrutura
- Validação é feita via comandos `docker compose config` e testes manuais
- O ambiente local deve continuar funcionando exatamente como antes
- Labels Traefik só são aplicadas no arquivo homolog, nunca no base ou override local
