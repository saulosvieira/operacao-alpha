# Documento de Requisitos

## Introdução

Este documento define os requisitos para integração da interface React/Vite (alfa-quest) gerada no Lovable ao projeto Laravel existente, transformando-o em um Progressive Web App (PWA) instalável com sistema de notificações. O objetivo é criar uma aplicação web moderna, responsiva e instalável que ofereça uma experiência otimizada em dispositivos móveis e desktop, garantindo que o backend Laravel forneça todas as APIs necessárias para suportar integralmente as funcionalidades da interface.

## Glossário

- **PWA (Progressive Web App)**: Aplicação web que utiliza tecnologias modernas para oferecer experiência similar a aplicativos nativos
- **Manifest**: Arquivo JSON que define metadados da aplicação (nome, ícones, cores, etc.)
- **Backend Laravel**: Sistema backend existente que fornece APIs REST e autenticação
- **Frontend React**: Interface de usuário construída com React, TypeScript e Vite
- **Vite**: Ferramenta de build moderna para projetos frontend
- **Integração de API**: Conexão entre frontend React e backend Laravel via requisições HTTP
- **Fluxo de Autenticação**: Fluxo de autenticação usando Laravel Sanctum
- **Pipeline de Build**: Processo automatizado de compilação e deploy do frontend
- **Push Notifications**: Notificações enviadas pelo servidor para o navegador do usuário
- **Web Push API**: API do navegador para receber notificações push

## Requisitos

### Requisito 1

**História de Usuário:** Como desenvolvedor, eu quero integrar o projeto React existente ao Laravel, para que o frontend seja servido pelo backend Laravel e utilize suas APIs.

#### Critérios de Aceitação

1. QUANDO o processo de build é executado ENTÃO o sistema DEVE compilar a aplicação React e gerar arquivos estáticos no diretório public do Laravel
2. QUANDO um usuário acessa a URL raiz ENTÃO o sistema DEVE servir a aplicação React através do Laravel
3. QUANDO a aplicação React faz chamadas de API ENTÃO o sistema DEVE rotear as requisições para os endpoints da API Laravel
4. QUANDO assets são requisitados ENTÃO o sistema DEVE servi-los do diretório public correto com os tipos MIME apropriados
5. ONDE a aplicação está em modo de desenvolvimento ENTÃO o sistema DEVE suportar hot module replacement para desenvolvimento rápido

### Requisito 2

**História de Usuário:** Como desenvolvedor, eu quero configurar o ambiente de build, para que o frontend seja compilado corretamente e integrado ao Laravel.

#### Critérios de Aceitação

1. QUANDO o Vite compila a aplicação ENTÃO o sistema DEVE gerar arquivos no diretório `laravel/public/build`
2. QUANDO o Laravel serve páginas ENTÃO o sistema DEVE referenciar corretamente os assets compilados pelo Vite usando o manifest
3. QUANDO o comando de build é executado ENTÃO o sistema DEVE gerar bundles de produção otimizados com code splitting
4. QUANDO variáveis de ambiente são necessárias ENTÃO o sistema DEVE fornecê-las à aplicação React de forma segura
5. ONDE endpoints de API são configurados ENTÃO o sistema DEVE usar a URL base correta para diferentes ambientes

### Requisito 3

**História de Usuário:** Como desenvolvedor, eu quero implementar autenticação integrada, para que usuários possam fazer login através do frontend React usando o backend Laravel.

#### Critérios de Aceitação

1. QUANDO um usuário submete credenciais de login ENTÃO o sistema DEVE autenticar via API Laravel Sanctum
2. QUANDO a autenticação é bem-sucedida ENTÃO o sistema DEVE armazenar o token de autenticação de forma segura
3. QUANDO requisições autenticadas são feitas ENTÃO o sistema DEVE incluir o token de autenticação nos headers
4. QUANDO um token expira ENTÃO o sistema DEVE redirecionar o usuário para a página de login
5. QUANDO um usuário faz logout ENTÃO o sistema DEVE invalidar o token e limpar o armazenamento local

### Requisito 4

**História de Usuário:** Como desenvolvedor, eu quero configurar o PWA básico, para que a aplicação seja instalável em dispositivos móveis.

#### Critérios de Aceitação

1. QUANDO o manifest é requisitado ENTÃO o sistema DEVE servir um web app manifest válido com ícones e metadados
2. QUANDO um usuário visita em dispositivo móvel ENTÃO o sistema DEVE exibir um prompt de instalação para adicionar à tela inicial
3. QUANDO o app instalado é aberto ENTÃO o sistema DEVE exibir em modo standalone sem a interface do navegador
4. QUANDO o app é aberto ENTÃO o sistema DEVE mostrar uma splash screen customizada com a marca
5. ONDE ícones são necessários ENTÃO o sistema DEVE fornecer múltiplos tamanhos para diferentes dispositivos

### Requisito 5

**História de Usuário:** Como desenvolvedor, eu quero migrar os componentes React existentes, para que toda a interface do alfa-quest funcione no ambiente Laravel.

#### Critérios de Aceitação

1. QUANDO componentes são importados ENTÃO o sistema DEVE resolver aliases de caminho corretamente (imports @/)
2. QUANDO Tailwind CSS é usado ENTÃO o sistema DEVE compilar e aplicar estilos corretamente
3. QUANDO componentes shadcn/ui são renderizados ENTÃO o sistema DEVE exibi-los com estilização apropriada
4. QUANDO roteamento ocorre ENTÃO o sistema DEVE gerenciar navegação client-side sem recarregar a página completa
5. ONDE dados de API são necessários ENTÃO o sistema DEVE buscar dos endpoints Laravel ao invés de dados mock

### Requisito 6

**História de Usuário:** Como desenvolvedor, eu quero configurar o roteamento, para que URLs sejam gerenciadas corretamente entre Laravel e React Router.

#### Critérios de Aceitação

1. QUANDO um usuário navega para qualquer rota frontend ENTÃO o sistema DEVE servir a aplicação React
2. QUANDO um usuário atualiza uma página ENTÃO o sistema DEVE manter a rota atual sem erros 404
3. QUANDO rotas de API são acessadas ENTÃO o sistema DEVE rotear para controllers Laravel
4. QUANDO autenticação é requerida ENTÃO o sistema DEVE proteger rotas e redirecionar usuários não autenticados
5. ONDE uma rota não existe ENTÃO o sistema DEVE exibir uma página 404 customizada

### Requisito 7

**História de Usuário:** Como desenvolvedor, eu quero substituir dados mock por APIs reais, para que a aplicação use dados do banco de dados Laravel.

#### Critérios de Aceitação

1. QUANDO a aplicação carrega ENTÃO o sistema DEVE buscar dados reais dos endpoints da API Laravel
2. QUANDO mutações de dados ocorrem ENTÃO o sistema DEVE enviar requisições POST/PUT/DELETE para o Laravel
3. QUANDO respostas de API são recebidas ENTÃO o sistema DEVE tratar estados de sucesso e erro apropriadamente
4. QUANDO dados estão carregando ENTÃO o sistema DEVE exibir indicadores de carregamento
5. ONDE validação de dados falha ENTÃO o sistema DEVE exibir mensagens de erro da validação Laravel

### Requisito 8

**História de Usuário:** Como desenvolvedor, eu quero otimizar o build para produção, para que a aplicação tenha performance máxima.

#### Critérios de Aceitação

1. QUANDO compilando para produção ENTÃO o sistema DEVE minificar arquivos JavaScript e CSS
2. QUANDO assets são servidos ENTÃO o sistema DEVE incluir hashes de cache-busting nos nomes de arquivo
3. QUANDO imagens são usadas ENTÃO o sistema DEVE otimizá-las e servi-las em formatos modernos
4. QUANDO a aplicação carrega ENTÃO o sistema DEVE alcançar boas pontuações no Lighthouse (>90)
5. ONDE code splitting é possível ENTÃO o sistema DEVE fazer lazy load de rotas e componentes

### Requisito 9

**História de Usuário:** Como desenvolvedor, eu quero configurar o Docker, para que o ambiente de desenvolvimento inclua o build do frontend.

#### Critérios de Aceitação

1. QUANDO containers Docker iniciam ENTÃO o sistema DEVE instalar dependências Node.js automaticamente
2. QUANDO em modo de desenvolvimento ENTÃO o sistema DEVE executar o servidor dev do Vite com hot reload
3. QUANDO compilando para produção ENTÃO o sistema DEVE executar o comando de build no container
4. QUANDO volumes são montados ENTÃO o sistema DEVE sincronizar mudanças de arquivo entre host e container
5. ONDE scripts npm são executados ENTÃO o sistema DEVE executá-los no container apropriado

### Requisito 10

**História de Usuário:** Como usuário final, eu quero acessar a aplicação de forma responsiva, para que eu tenha uma boa experiência em qualquer dispositivo.

#### Critérios de Aceitação

1. QUANDO um usuário acessa em mobile ENTÃO o sistema DEVE exibir uma interface otimizada para mobile
2. QUANDO um usuário acessa em tablet ENTÃO o sistema DEVE adaptar o layout apropriadamente
3. QUANDO um usuário acessa em desktop ENTÃO o sistema DEVE utilizar o espaço de tela disponível efetivamente
4. QUANDO o viewport muda ENTÃO o sistema DEVE responder com ajustes de layout apropriados
5. ONDE interações touch estão disponíveis ENTÃO o sistema DEVE suportar gestos e interações touch

### Requisito 11

**História de Usuário:** Como desenvolvedor, eu quero implementar sistema de notificações push, para que usuários possam receber alertas importantes sobre simulados e resultados.

#### Critérios de Aceitação

1. QUANDO um usuário permite notificações ENTÃO o sistema DEVE registrar o dispositivo para receber push notifications
2. QUANDO uma notificação é enviada pelo backend ENTÃO o sistema DEVE entregar a notificação ao dispositivo do usuário
3. QUANDO uma notificação é recebida ENTÃO o sistema DEVE exibi-la mesmo com o app fechado
4. QUANDO um usuário clica em uma notificação ENTÃO o sistema DEVE abrir o app na tela relevante
5. ONDE o usuário desabilita notificações ENTÃO o sistema DEVE remover o registro do dispositivo

### Requisito 12

**História de Usuário:** Como desenvolvedor, eu quero garantir que todas as APIs do backend existam, para que o frontend tenha todos os endpoints necessários para funcionar completamente.

#### Critérios de Aceitação

1. QUANDO o frontend requisita dados de carreiras ENTÃO o sistema DEVE fornecer endpoint de listagem e detalhes de carreiras
2. QUANDO o frontend requisita simulados ENTÃO o sistema DEVE fornecer endpoints para listar, criar, executar e finalizar simulados
3. QUANDO o frontend requisita questões ENTÃO o sistema DEVE fornecer endpoints para buscar questões e registrar respostas
4. QUANDO o frontend requisita rankings ENTÃO o sistema DEVE fornecer endpoint de ranking com filtros por carreira e período
5. QUANDO o frontend requisita dados de desempenho ENTÃO o sistema DEVE fornecer endpoint com estatísticas e histórico do usuário
6. QUANDO o frontend requisita dados de aprovados ENTÃO o sistema DEVE fornecer endpoint de listagem de aprovados por edital
7. QUANDO o frontend gerencia assinaturas ENTÃO o sistema DEVE fornecer endpoints para planos, pagamento e status de assinatura
8. ONDE o frontend precisa gerenciar conta ENTÃO o sistema DEVE fornecer endpoints para perfil, atualização de dados e exclusão de conta

### Requisito 13

**História de Usuário:** Como desenvolvedor, eu quero ter seeders completos para popular o banco de dados, para que eu possa desenvolver e testar o frontend com dados realistas sem depender de criação manual.

#### Critérios de Aceitação

1. QUANDO o comando de seed é executado ENTÃO o sistema DEVE criar usuários de teste com diferentes perfis (admin, premium, free)
2. QUANDO seeders são executados ENTÃO o sistema DEVE popular carreiras com dados realistas (PM, Bombeiros, Exército, etc)
3. QUANDO seeders criam editais ENTÃO o sistema DEVE associá-los às carreiras correspondentes com datas e informações completas
4. QUANDO seeders criam simulados ENTÃO o sistema DEVE gerar simulados variados com questões associadas para diferentes carreiras
5. QUANDO seeders criam questões ENTÃO o sistema DEVE incluir enunciados, alternativas, gabaritos e explicações
6. QUANDO seeders populam rankings ENTÃO o sistema DEVE criar resultados de simulados para múltiplos usuários
7. QUANDO seeders criam aprovados ENTÃO o sistema DEVE popular a lista com dados de aprovados em diferentes editais
8. ONDE o banco é resetado ENTÃO o sistema DEVE permitir reexecutar os seeders para restaurar dados de desenvolvimento rapidamente
