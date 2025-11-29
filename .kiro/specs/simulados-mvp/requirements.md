# Documento de Requisitos - MVP Simulados

## Introdução

Este projeto visa desenvolver um MVP (Produto Mínimo Viável) para uma plataforma de simulados educacionais, composto por aplicativos móveis Android e iOS (wrappers WebView), painel administrativo expandido em Laravel 12 + AdminLTE, e integração com plataforma de pagamento/assinatura. O sistema permitirá que usuários realizem simulados cronometrados, vejam rankings globais e tenham acesso completo mediante assinatura.

## Requisitos

### Requisito 1 - Aplicação Web Progressiva (PWA)

**User Story:** Como usuário, eu quero acessar os simulados através de uma aplicação web responsiva que funcione como app, para que eu tenha uma experiência móvel otimizada tanto no navegador quanto em wrappers WebView.

#### Critérios de Aceitação

1. QUANDO o usuário acessar via navegador móvel ENTÃO o sistema DEVE oferecer instalação como PWA
2. QUANDO o usuário instalar o PWA ENTÃO o sistema DEVE funcionar offline para funcionalidades básicas
3. QUANDO o app WebView for criado ENTÃO o sistema DEVE carregar a aplicação web responsiva
4. QUANDO o usuário abrir o app ENTÃO o sistema DEVE funcionar de forma responsiva em diferentes tamanhos de tela
5. QUANDO necessário para publicação nas lojas ENTÃO wrappers WebView simples serão criados (Android/iOS)

### Requisito 2 - Painel Administrativo

**User Story:** Como administrador, eu quero gerenciar simulados, questões, carreiras, editais e aprovados através de um painel web, para que eu possa manter o conteúdo atualizado e organizado.

#### Critérios de Aceitação

1. QUANDO o administrador acessar o painel ENTÃO o sistema DEVE permitir gestão completa de Simulados
2. QUANDO o administrador importar um CSV ENTÃO o sistema DEVE processar e adicionar questões ao Banco de Questões
3. QUANDO o administrador gerenciar Carreiras ENTÃO o sistema DEVE permitir criar, editar e excluir carreiras
4. QUANDO o administrador gerenciar Editais ENTÃO o sistema DEVE permitir criar, editar e excluir editais
5. QUANDO o administrador gerenciar Aprovados ENTÃO o sistema DEVE permitir adicionar e gerenciar lista de aprovados
6. QUANDO o administrador usar o painel ENTÃO o sistema DEVE utilizar Laravel 12 + AdminLTE como base

### Requisito 3 - Sistema de Simulados

**User Story:** Como usuário, eu quero realizar simulados cronometrados com feedback imediato, para que eu possa praticar e acompanhar meu desempenho.

#### Critérios de Aceitação

1. QUANDO o usuário iniciar um simulado ENTÃO o sistema DEVE ativar um cronômetro visível
2. QUANDO o usuário responder questões ENTÃO o sistema DEVE permitir envio das respostas
3. QUANDO o usuário finalizar o simulado ENTÃO o sistema DEVE mostrar resultado imediato
4. QUANDO o usuário acessar histórico ENTÃO o sistema DEVE mostrar histórico básico de simulados realizados
5. QUANDO o tempo do simulado esgotar ENTÃO o sistema DEVE finalizar automaticamente e mostrar resultados

### Requisito 4 - Sistema de Ranking

**User Story:** Como usuário, eu quero ver minha posição em rankings globais, para que eu possa me comparar com outros usuários e me motivar a melhorar.

#### Critérios de Aceitação

1. QUANDO o usuário acessar o ranking ENTÃO o sistema DEVE mostrar placar diário atualizado
2. QUANDO o usuário acessar o ranking ENTÃO o sistema DEVE mostrar placar semanal atualizado
3. QUANDO o usuário completar simulados ENTÃO o sistema DEVE atualizar sua posição no ranking global
4. QUANDO o ranking for exibido ENTÃO o sistema DEVE mostrar posição, nome e pontuação dos usuários

### Requisito 5 - Sistema de Assinatura

**User Story:** Como usuário, eu quero assinar o sistema para ter acesso completo aos simulados, para que eu possa utilizar todas as funcionalidades disponíveis.

#### Critérios de Aceitação

1. QUANDO o usuário não for assinante ENTÃO o sistema DEVE restringir acesso a funcionalidades completas
2. QUANDO o usuário for assinante ativo ENTÃO o sistema DEVE liberar acesso completo
3. QUANDO a assinatura for processada ENTÃO o sistema DEVE ativar automaticamente o acesso do usuário
4. QUANDO a assinatura expirar ENTÃO o sistema DEVE desativar automaticamente o acesso do usuário

### Requisito 6 - Integração com Plataforma de Pagamento

**User Story:** Como administrador, eu quero que o sistema se integre automaticamente com a plataforma de pagamento escolhida, para que as assinaturas sejam gerenciadas sem intervenção manual.

#### Critérios de Aceitação

1. QUANDO um webhook de pagamento for recebido ENTÃO o sistema DEVE ativar o acesso do usuário assinante
2. QUANDO um webhook de cancelamento for recebido ENTÃO o sistema DEVE desativar o acesso do usuário
3. QUANDO a integração for configurada ENTÃO o sistema DEVE suportar plataformas como Kiwify/Hotmart
4. QUANDO houver erro na integração ENTÃO o sistema DEVE registrar logs para diagnóstico

### Requisito 7 - Responsividade e Compatibilidade

**User Story:** Como usuário, eu quero acessar o sistema tanto pelo app quanto pelo navegador web, para que eu tenha flexibilidade de uso em diferentes dispositivos.

#### Critérios de Aceitação

1. QUANDO o usuário acessar via navegador web ENTÃO o sistema DEVE funcionar de forma responsiva
2. QUANDO o usuário acessar via app móvel ENTÃO o sistema DEVE carregar corretamente no WebView
3. QUANDO o sistema for usado em diferentes resoluções ENTÃO o sistema DEVE adaptar a interface adequadamente
4. QUANDO o usuário navegar entre telas ENTÃO o sistema DEVE manter consistência visual

### Requisito 8 - Importação de Dados

**User Story:** Como administrador, eu quero importar questões via arquivo CSV, para que eu possa adicionar conteúdo em lote de forma eficiente.

#### Critérios de Aceitação

1. QUANDO o administrador enviar um CSV ENTÃO o sistema DEVE validar o formato do arquivo
2. QUANDO o CSV estiver válido ENTÃO o sistema DEVE importar todas as questões
3. QUANDO houver erro no CSV ENTÃO o sistema DEVE mostrar mensagens de erro específicas
4. QUANDO a importação for concluída ENTÃO o sistema DEVE confirmar quantas questões foram adicionadas

### Requisito 9 - Autenticação e Autorização

**User Story:** Como usuário, eu quero fazer login no sistema de forma segura, para que meus dados e progresso sejam protegidos.

#### Critérios de Aceitação

1. QUANDO o usuário fizer login ENTÃO o sistema DEVE autenticar credenciais
2. QUANDO o usuário estiver logado ENTÃO o sistema DEVE manter sessão ativa
3. QUANDO o usuário não estiver autorizado ENTÃO o sistema DEVE restringir acesso a funcionalidades premium
4. QUANDO o usuário fizer logout ENTÃO o sistema DEVE encerrar a sessão de forma segura