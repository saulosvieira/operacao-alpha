# Estrutura do Banco de Dados - MVP Simulados

## Diagrama de Relacionamentos

```
┌─────────────────┐
│    carreiras    │
│─────────────────│
│ id              │◄──┐
│ nome            │   │
│ descricao       │   │
│ slug            │   │
│ ativa           │   │
│ timestamps      │   │
└─────────────────┘   │
                      │
        ┌─────────────┼─────────────┐
        │             │             │
        │             │             │
┌───────▼──────┐ ┌────▼────────┐ ┌─▼──────────┐
│   editais    │ │  simulados  │ │ aprovados  │
│──────────────│ │─────────────│ │────────────│
│ id           │ │ id          │ │ id         │
│ carreira_id  │ │ carreira_id │ │ carreira_id│
│ titulo       │ │ titulo      │ │ edital_id  │
│ descricao    │ │ descricao   │ │ nome       │
│ data_public. │ │ tempo_limite│ │ posicao    │
│ ativo        │ │ ativo       │ │ ano        │
│ timestamps   │ │ timestamps  │ │ timestamps │
└──────────────┘ └──────┬──────┘ └────────────┘
                        │
                        │
                 ┌──────▼──────┐
                 │   questoes  │
                 │─────────────│
                 │ id          │◄──┐
                 │ simulado_id │   │
                 │ numero      │   │
                 │ enunciado   │   │
                 │ img_enunc.  │   │
                 │ alt_a-e     │   │
                 │ img_a-e     │   │
                 │ resposta    │   │
                 │ explicacao  │   │
                 │ timestamps  │   │
                 └─────────────┘   │
                                   │
┌─────────────────┐                │
│      users      │                │
│─────────────────│                │
│ id              │◄──┐            │
│ name            │   │            │
│ email           │   │            │
│ password        │   │            │
│ subscription_*  │   │            │
│ timestamps      │   │            │
└─────────────────┘   │            │
                      │            │
        ┌─────────────┼────────────┼─────────────┐
        │             │            │             │
┌───────▼──────┐ ┌────▼────────┐ ┌▼─────────────┐
│  rankings    │ │ resultados  │ │  respostas   │
│──────────────│ │─────────────│ │──────────────│
│ id           │ │ id          │ │ id           │
│ user_id      │ │ user_id     │ │ user_id      │
│ pont_diaria  │ │ simulado_id │ │ simulado_id  │
│ pont_semanal │ │ pontuacao   │ │ questao_id   │
│ data_calculo │ │ total_quest.│ │ resposta_esc.│
│ timestamps   │ │ acertos     │ │ correta      │
└──────────────┘ │ tempo_total │ │ tempo_resp.  │
                 │ finalizado  │ │ timestamps   │
                 │ timestamps  │ └──────────────┘
                 └─────────────┘
```

## Migrations Detalhadas

### 1. create_carreiras_table

```php
Schema::create('carreiras', function (Blueprint $table) {
    $table->id();
    $table->string('nome', 100);
    $table->text('descricao')->nullable();
    $table->string('slug', 120)->unique();
    $table->boolean('ativa')->default(true);
    $table->timestamps();
    
    $table->index('ativa');
    $table->index('slug');
});
```

### 2. create_editais_table

```php
Schema::create('editais', function (Blueprint $table) {
    $table->id();
    $table->foreignId('carreira_id')->constrained('carreiras')->onDelete('cascade');
    $table->string('titulo', 200);
    $table->text('descricao')->nullable();
    $table->date('data_publicacao')->nullable();
    $table->boolean('ativo')->default(true);
    $table->timestamps();
    
    $table->index('carreira_id');
    $table->index('ativo');
    $table->index('data_publicacao');
});
```

### 3. create_simulados_table

```php
Schema::create('simulados', function (Blueprint $table) {
    $table->id();
    $table->foreignId('carreira_id')->constrained('carreiras')->onDelete('cascade');
    $table->string('titulo', 200);
    $table->text('descricao')->nullable();
    $table->integer('tempo_limite_minutos')->default(60);
    $table->boolean('ativo')->default(true);
    $table->timestamps();
    
    $table->index('carreira_id');
    $table->index('ativo');
});
```

### 4. create_questoes_table

```php
Schema::create('questoes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('simulado_id')->constrained('simulados')->onDelete('cascade');
    $table->integer('numero_questao');
    $table->text('enunciado');
    $table->string('imagem_enunciado', 255)->nullable();
    
    // Alternativas
    $table->text('alternativa_a');
    $table->string('imagem_a', 255)->nullable();
    $table->text('alternativa_b');
    $table->string('imagem_b', 255)->nullable();
    $table->text('alternativa_c');
    $table->string('imagem_c', 255)->nullable();
    $table->text('alternativa_d');
    $table->string('imagem_d', 255)->nullable();
    $table->text('alternativa_e');
    $table->string('imagem_e', 255)->nullable();
    
    $table->char('resposta_correta', 1); // A, B, C, D ou E
    $table->text('explicacao')->nullable();
    $table->timestamps();
    
    $table->index('simulado_id');
    $table->index(['simulado_id', 'numero_questao']);
});
```

### 5. update_users_table (adicionar campos de assinatura)

```php
Schema::table('users', function (Blueprint $table) {
    $table->enum('subscription_status', ['inactive', 'active', 'trial', 'expired'])
          ->default('inactive')
          ->after('password');
    $table->timestamp('subscription_expires_at')->nullable()->after('subscription_status');
    $table->string('subscription_platform_id', 100)->nullable()->after('subscription_expires_at');
    
    $table->index('subscription_status');
    $table->index('subscription_expires_at');
});
```

### 6. create_respostas_usuarios_table

```php
Schema::create('respostas_usuarios', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
    $table->foreignId('simulado_id')->constrained('simulados')->onDelete('cascade');
    $table->foreignId('questao_id')->constrained('questoes')->onDelete('cascade');
    $table->char('resposta_escolhida', 1); // A, B, C, D ou E
    $table->boolean('correta')->default(false);
    $table->integer('tempo_resposta_segundos')->nullable();
    $table->timestamps();
    
    $table->index('user_id');
    $table->index('simulado_id');
    $table->index(['user_id', 'simulado_id']);
    $table->unique(['user_id', 'simulado_id', 'questao_id']);
});
```

### 7. create_resultados_simulados_table

```php
Schema::create('resultados_simulados', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
    $table->foreignId('simulado_id')->constrained('simulados')->onDelete('cascade');
    $table->decimal('pontuacao', 5, 2); // Ex: 85.50
    $table->integer('total_questoes');
    $table->integer('acertos');
    $table->integer('tempo_total_segundos');
    $table->timestamp('finalizado_em');
    $table->timestamps();
    
    $table->index('user_id');
    $table->index('simulado_id');
    $table->index('finalizado_em');
    $table->index(['user_id', 'simulado_id']);
});
```

### 8. create_rankings_table

```php
Schema::create('rankings', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
    $table->decimal('pontuacao_diaria', 8, 2)->default(0);
    $table->decimal('pontuacao_semanal', 8, 2)->default(0);
    $table->date('data_calculo');
    $table->timestamps();
    
    $table->index('user_id');
    $table->index('data_calculo');
    $table->index(['pontuacao_diaria', 'data_calculo']);
    $table->index(['pontuacao_semanal', 'data_calculo']);
    $table->unique(['user_id', 'data_calculo']);
});
```

### 9. create_aprovados_table

```php
Schema::create('aprovados', function (Blueprint $table) {
    $table->id();
    $table->foreignId('carreira_id')->constrained('carreiras')->onDelete('cascade');
    $table->foreignId('edital_id')->nullable()->constrained('editais')->onDelete('set null');
    $table->string('nome', 200);
    $table->integer('posicao')->nullable();
    $table->year('ano');
    $table->timestamps();
    
    $table->index('carreira_id');
    $table->index('edital_id');
    $table->index('ano');
});
```

## Índices e Performance

### Índices Principais

1. **carreiras**: `ativa`, `slug`
2. **editais**: `carreira_id`, `ativo`, `data_publicacao`
3. **simulados**: `carreira_id`, `ativo`
4. **questoes**: `simulado_id`, `(simulado_id, numero_questao)`
5. **users**: `subscription_status`, `subscription_expires_at`
6. **respostas_usuarios**: `user_id`, `simulado_id`, `(user_id, simulado_id)`
7. **resultados_simulados**: `user_id`, `simulado_id`, `finalizado_em`
8. **rankings**: `user_id`, `data_calculo`, `(pontuacao_diaria, data_calculo)`
9. **aprovados**: `carreira_id`, `edital_id`, `ano`

### Índices Compostos Importantes

```sql
-- Para buscar respostas de um usuário em um simulado específico
INDEX idx_user_simulado ON respostas_usuarios(user_id, simulado_id);

-- Para ranking diário ordenado
INDEX idx_ranking_diario ON rankings(pontuacao_diaria DESC, data_calculo);

-- Para ranking semanal ordenado
INDEX idx_ranking_semanal ON rankings(pontuacao_semanal DESC, data_calculo);

-- Para histórico de simulados do usuário
INDEX idx_user_historico ON resultados_simulados(user_id, finalizado_em DESC);
```

## Relacionamentos Eloquent

### Carreira Model

```php
class Carreira extends Model
{
    public function editais()
    {
        return $this->hasMany(Edital::class);
    }
    
    public function simulados()
    {
        return $this->hasMany(Simulado::class);
    }
    
    public function aprovados()
    {
        return $this->hasMany(Aprovado::class);
    }
}
```

### Simulado Model

```php
class Simulado extends Model
{
    public function carreira()
    {
        return $this->belongsTo(Carreira::class);
    }
    
    public function questoes()
    {
        return $this->hasMany(Questao::class)->orderBy('numero_questao');
    }
    
    public function resultados()
    {
        return $this->hasMany(ResultadoSimulado::class);
    }
    
    public function respostas()
    {
        return $this->hasMany(RespostaUsuario::class);
    }
}
```

### User Model

```php
class User extends Authenticatable
{
    public function respostas()
    {
        return $this->hasMany(RespostaUsuario::class);
    }
    
    public function resultados()
    {
        return $this->hasMany(ResultadoSimulado::class);
    }
    
    public function ranking()
    {
        return $this->hasOne(Ranking::class)->latest('data_calculo');
    }
    
    public function isSubscribed(): bool
    {
        return $this->subscription_status === 'active' 
            && $this->subscription_expires_at 
            && $this->subscription_expires_at->isFuture();
    }
}
```

## Queries Comuns

### Buscar simulados ativos de uma carreira

```php
$simulados = Simulado::where('carreira_id', $carreiraId)
    ->where('ativo', true)
    ->with('questoes')
    ->get();
```

### Buscar ranking diário

```php
$ranking = Ranking::where('data_calculo', today())
    ->orderBy('pontuacao_diaria', 'desc')
    ->with('user:id,name')
    ->limit(100)
    ->get();
```

### Buscar histórico de simulados do usuário

```php
$historico = ResultadoSimulado::where('user_id', $userId)
    ->with('simulado:id,titulo')
    ->orderBy('finalizado_em', 'desc')
    ->paginate(10);
```

### Calcular pontuação de um simulado

```php
$resultado = ResultadoSimulado::create([
    'user_id' => $userId,
    'simulado_id' => $simuladoId,
    'total_questoes' => $totalQuestoes,
    'acertos' => $acertos,
    'pontuacao' => ($acertos / $totalQuestoes) * 100,
    'tempo_total_segundos' => $tempoTotal,
    'finalizado_em' => now(),
]);
```

## Seeders de Exemplo

### CarreiraSeeder

```php
Carreira::create([
    'nome' => 'Polícia Federal',
    'descricao' => 'Concursos para Polícia Federal',
    'slug' => 'policia-federal',
    'ativa' => true,
]);

Carreira::create([
    'nome' => 'Polícia Rodoviária Federal',
    'descricao' => 'Concursos para PRF',
    'slug' => 'prf',
    'ativa' => true,
]);
```

### SimuladoSeeder

```php
$carreira = Carreira::where('slug', 'policia-federal')->first();

Simulado::create([
    'carreira_id' => $carreira->id,
    'titulo' => 'Simulado PF - Direito Constitucional',
    'descricao' => 'Simulado com 50 questões de Direito Constitucional',
    'tempo_limite_minutos' => 90,
    'ativo' => true,
]);
```

## Considerações de Performance

1. **Paginação**: Sempre usar paginação em listagens grandes
2. **Eager Loading**: Usar `with()` para evitar N+1 queries
3. **Cache**: Cachear rankings e listagens de simulados
4. **Índices**: Manter índices atualizados e otimizados
5. **Soft Deletes**: Considerar para auditoria (opcional)

## Backup e Manutenção

```bash
# Backup diário do banco
mysqldump -u simulados_user -p simulados_db > backup_$(date +%Y%m%d).sql

# Otimizar tabelas
OPTIMIZE TABLE carreiras, simulados, questoes, respostas_usuarios;

# Limpar rankings antigos (manter últimos 90 dias)
DELETE FROM rankings WHERE data_calculo < DATE_SUB(NOW(), INTERVAL 90 DAY);
```

---

**Próximo Passo**: Criar as migrations na ordem especificada e testar relacionamentos.
