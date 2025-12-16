@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1 class="mb-0">Bem-vindo à Operação Alpha</h1>
    <small class="text-muted">Plataforma de Simulados para Concursos Públicos</small>
@stop

@section('content')
<div class="row">
    <div class="col-lg-8 mb-3">
        <div class="card shadow">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <h4 class="mb-0">Operação Alpha - Sistema de Simulados</h4>
                </div>
                <p>Plataforma completa para gestão de simulados de concursos públicos. Gerencie carreiras, crie simulados, acompanhe o desempenho dos usuários e muito mais.</p>
                
                <div class="row mt-4">
                    <div class="col-md-4 mb-3">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3><i class="fas fa-graduation-cap"></i></h3>
                                <p>Carreiras</p>
                            </div>
                            <a href="{{ route('admin.careers.index') }}" class="small-box-footer">
                                Acessar <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><i class="fas fa-file-alt"></i></h3>
                                <p>Simulados</p>
                            </div>
                            <a href="#" class="small-box-footer">
                                Em breve <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><i class="fas fa-question-circle"></i></h3>
                                <p>Questões</p>
                            </div>
                            <a href="#" class="small-box-footer">
                                Em breve <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-info mt-4 mb-0">
                    <i class="fas fa-info-circle"></i> <b>Sistema em desenvolvimento:</b> Novas funcionalidades serão adicionadas em breve!
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 mb-3">
        <div class="card shadow h-100">
            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                <h5 class="mb-3">Atalhos Rápidos</h5>
                <a href="{{ route('admin.careers.index') }}" class="btn btn-outline-primary btn-block mb-2 w-100">
                    <i class="fas fa-graduation-cap"></i> Carreiras
                </a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-info btn-block mb-2 w-100">
                    <i class="fas fa-users"></i> Usuários
                </a>
                <a href="#" class="btn btn-outline-success btn-block w-100">
                    <i class="fas fa-chart-line"></i> Relatórios
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-info"><i class="fas fa-graduation-cap"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Carreiras</span>
                <span class="info-box-number">{{ \App\Domain\Career\Models\Career::count() }}</span>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-success"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Usuários</span>
                <span class="info-box-number">{{ \App\Domain\Auth\Models\User::count() }}</span>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-warning"><i class="fas fa-file-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Simulados</span>
                <span class="info-box-number">{{ \App\Domain\Exam\Models\Exam::count() }}</span>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-danger"><i class="fas fa-question-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Questões</span>
                <span class="info-box-number">{{ \App\Domain\Exam\Models\Question::count() }}</span>
            </div>
        </div>
    </div>
</div>
@stop
