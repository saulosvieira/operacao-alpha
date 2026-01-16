# Deploy Guide - Operação Alfa

Este documento descreve como executar o projeto em diferentes ambientes.

## Arquitetura de Arquivos

```
├── docker-compose.yml           # Configuração base (compartilhada)
├── docker-compose.override.yml  # Local dev (carregado automaticamente)
├── docker-compose.homolog.yml   # Homolog VPS (requer -f explícito)
├── docker-compose.proxy.yml     # Traefik standalone (VPS)
├── .env                         # Variáveis locais
├── .env.homolog.example         # Template para VPS
└── traefik/
    └── acme.json               # Certificados Let's Encrypt (VPS)
```

---

## 🖥️ Ambiente Local (Desenvolvimento)

### Comandos

```bash
# Subir todos os serviços
docker compose up -d

# Subir com Vite dev server (hot reload)
docker compose --profile dev up -d

# Ver logs
docker compose logs -f

# Parar tudo
docker compose down
```

### Acessos Locais

| Serviço | URL/Porta |
|---------|-----------|
| Aplicação | http://localhost:8090 |
| Vite (dev) | http://localhost:5173 |
| MySQL | localhost:33090 |
| Redis | localhost:63790 |

### Credenciais Padrão (Local)

- **MySQL Root**: `simulados_root_2024`
- **MySQL User**: `simulados_user` / `simulados_pass_2024`
- **Database**: `simulados_db`

---

## 🌐 Ambiente Homolog (VPS)

### Pré-requisitos

#### 1. DNS
- [ ] Criar registro A: `operacao-alfa.homolog.mydev.com.br` → IP da VPS
- [ ] Aguardar propagação DNS (pode levar até 48h)

#### 2. Cloudflare (se usar)
- [ ] Configurar como "DNS only" (nuvem cinza) inicialmente
- [ ] Após SSL funcionar, mudar para "Full (strict)"

#### 3. Firewall da VPS
- [ ] Porta 80 aberta (HTTP - necessário para Let's Encrypt)
- [ ] Porta 443 aberta (HTTPS)
- [ ] Porta 22 aberta (SSH)

#### 4. VPS
- [ ] Docker instalado
- [ ] Docker Compose instalado
- [ ] Git instalado


### Deploy Inicial na VPS

```bash
# 1. Clonar repositório
git clone <repo-url> /opt/operacao-alfa
cd /opt/operacao-alfa

# 2. Criar rede externa para Traefik
docker network create web

# 3. Configurar permissões do acme.json
chmod 600 traefik/acme.json

# 4. Configurar variáveis de ambiente
cp .env.homolog.example .env
nano .env  # Editar com valores reais

# 5. Configurar Laravel
cp laravel/.env.example laravel/.env
nano laravel/.env  # Configurar conforme .env.homolog.example

# 6. Subir Traefik (proxy reverso)
docker compose -f docker-compose.proxy.yml up -d

# 7. Subir aplicação
docker compose -f docker-compose.yml -f docker-compose.homolog.yml up -d

# 8. Executar migrations (primeira vez)
docker compose -f docker-compose.yml -f docker-compose.homolog.yml exec simulados-app php artisan migrate --force

# 9. Gerar chave da aplicação (se necessário)
docker compose -f docker-compose.yml -f docker-compose.homolog.yml exec simulados-app php artisan key:generate
```

### Atualizações na VPS

```bash
cd /opt/operacao-alfa

# Puxar alterações
git pull

# Rebuild se necessário
docker compose -f docker-compose.yml -f docker-compose.homolog.yml build

# Reiniciar serviços
docker compose -f docker-compose.yml -f docker-compose.homolog.yml up -d

# Executar migrations
docker compose -f docker-compose.yml -f docker-compose.homolog.yml exec simulados-app php artisan migrate --force

# Limpar caches
docker compose -f docker-compose.yml -f docker-compose.homolog.yml exec simulados-app php artisan config:cache
docker compose -f docker-compose.yml -f docker-compose.homolog.yml exec simulados-app php artisan route:cache
docker compose -f docker-compose.yml -f docker-compose.homolog.yml exec simulados-app php artisan view:cache
```

### Comandos Úteis (VPS)

```bash
# Ver status dos containers
docker compose -f docker-compose.yml -f docker-compose.homolog.yml ps

# Ver logs da aplicação
docker compose -f docker-compose.yml -f docker-compose.homolog.yml logs -f simulados-app

# Ver logs do Traefik
docker compose -f docker-compose.proxy.yml logs -f

# Acessar container da aplicação
docker compose -f docker-compose.yml -f docker-compose.homolog.yml exec simulados-app bash

# Acessar MySQL
docker compose -f docker-compose.yml -f docker-compose.homolog.yml exec simulados-db mysql -u simulados_user -p
```

---

## ✅ Checklist Pós-Deploy

### Validação SSL

```bash
# Verificar certificado
curl -vI https://operacao-alfa.homolog.mydev.com.br 2>&1 | grep "SSL certificate"

# Verificar redirect HTTP → HTTPS
curl -I http://operacao-alfa.homolog.mydev.com.br
# Deve retornar 301/302 para https://
```

### Validação da Aplicação

- [ ] Acessar https://operacao-alfa.homolog.mydev.com.br
- [ ] Verificar que não há redirect loops
- [ ] Testar login
- [ ] Verificar que URLs geradas usam HTTPS
- [ ] Verificar que assets carregam corretamente

---

## 🔧 Troubleshooting

### Certificado não emitido

**Sintomas**: Site mostra erro de certificado ou Traefik não consegue obter cert.

**Soluções**:
1. Verificar se porta 80 está aberta: `curl http://operacao-alfa.homolog.mydev.com.br`
2. Verificar logs do Traefik: `docker compose -f docker-compose.proxy.yml logs`
3. Se usar Cloudflare, garantir que está em "DNS only"
4. Verificar rate limits do Let's Encrypt

### Redirect Loop (ERR_TOO_MANY_REDIRECTS)

**Sintomas**: Browser mostra erro de muitos redirects.

**Soluções**:
1. Verificar `TRUSTED_PROXIES=*` no `laravel/.env`
2. Verificar `APP_URL=https://...` no `laravel/.env`
3. Limpar cache: `php artisan config:cache`

### Network "web" not found

**Sintomas**: Erro ao subir containers.

**Solução**:
```bash
docker network create web
```

### Banco não conecta

**Sintomas**: Erro de conexão com MySQL.

**Soluções**:
1. Verificar `DB_HOST=simulados-db` (nome do container, não localhost)
2. Verificar se container do banco está rodando
3. Verificar credenciais no `.env`

---

## 🔄 Adicionando Novos Projetos na Mesma VPS

Para adicionar outro projeto usando o mesmo Traefik:

1. Clone o novo projeto
2. Crie um `docker-compose.homolog.yml` similar
3. Adicione labels Traefik com Host diferente:
   ```yaml
   labels:
     - "traefik.http.routers.NOVO-PROJETO.rule=Host(`novo-projeto.homolog.mydev.com.br`)"
   ```
4. Conecte à rede `web`
5. Suba o projeto: `docker compose -f docker-compose.yml -f docker-compose.homolog.yml up -d`

O Traefik descobrirá automaticamente o novo serviço.
