<?php

/**
 * Service para gerenciar operações relacionadas a clientes
 *
 * @category   Services
 *
 * @author     Seu Nome <seu@email.com>
 * @license    MIT
 *
 * @link       https://github.com/seu-usuario/seu-projeto
 */

namespace App\Services;

use App\Repositories\ClientRepository;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Classe responsável por gerenciar as operações de clientes
 */
class ClientService extends BaseService
{
    /**
     * Cria uma nova instância do serviço de clientes
     *
     * @param  ClientRepository  $repository  Repositório de clientes
     */
    public function __construct(ClientRepository $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Retorna uma lista paginada de clientes com filtros aplicados
     *
     * @param  array  $filters  Filtros para aplicar na consulta
     * @param  int  $perPage  Número de itens por página
     * @return LengthAwarePaginator
     */
    public function paginateWithFilters(array $filters = [], int $perPage = 15)
    {
        return $this->repository->paginateWithFilters($filters, $perPage);
    }

    /**
     * Busca um cliente pelo ID
     *
     * @param  int|string  $id  ID do cliente
     * @return \App\Models\Client|null
     */
    public function findById($id)
    {
        return $this->repository->findById($id);
    }
}
