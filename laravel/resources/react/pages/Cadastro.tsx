import { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { Eye, EyeOff, Loader2, AlertCircle } from 'lucide-react';
import { useAuthStore } from '@/stores/authStore';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { toast } from '@/hooks/use-toast';
import logoOficial from '@/assets/logo-oficial.png';

const registerSchema = z.object({
  name: z.string().min(1, 'Nome é obrigatório'),
  email: z.string().email('Email inválido'),
  password: z.string().min(6, 'Senha deve ter pelo menos 6 caracteres'),
  confirmPassword: z.string().min(6, 'Confirmação de senha é obrigatória'),
}).refine((data) => data.password === data.confirmPassword, {
  message: 'As senhas não conferem',
  path: ['confirmPassword'],
});

type RegisterForm = z.infer<typeof registerSchema>;

export default function Cadastro() {
  const navigate = useNavigate();
  const { register: registerUser, isLoading, error, clearError, user } = useAuthStore();
  const [showPassword, setShowPassword] = useState(false);
  const [showConfirmPassword, setShowConfirmPassword] = useState(false);

  const {
    register,
    handleSubmit,
    formState: { errors },
    setError,
  } = useForm<RegisterForm>({
    resolver: zodResolver(registerSchema),
  });

  // Redirect if already logged in
  useEffect(() => {
    if (user) {
      navigate('/simulados');
    }
  }, [user, navigate]);

  // Clear errors when component unmounts
  useEffect(() => {
    return () => clearError();
  }, [clearError]);

  const onSubmit = async (data: RegisterForm) => {
    try {
      clearError();
      await registerUser(data.name, data.email, data.password);
      
      toast({
        title: 'Cadastro realizado com sucesso!',
        description: 'Bem-vindo à Operação Alfa.',
      });
      navigate('/simulados');
    } catch (err: any) {
      // Handle validation errors from API
      if (err.response?.status === 422) {
        const validationErrors = err.response.data.errors;
        if (validationErrors) {
          Object.keys(validationErrors).forEach((key) => {
            // Map API field names to form field names
            const fieldMap: Record<string, keyof RegisterForm> = {
              name: 'name',
              email: 'email',
              password: 'password',
            };
            const formField = fieldMap[key] || key as keyof RegisterForm;
            setError(formField, {
              type: 'manual',
              message: validationErrors[key][0],
            });
          });
        }
      } else if (err.response?.status === 409) {
        toast({
          variant: 'destructive',
          title: 'Erro no cadastro',
          description: 'Este email já está cadastrado.',
        });
      } else {
        toast({
          variant: 'destructive',
          title: 'Erro no cadastro',
          description: err.response?.data?.message || 'Ocorreu um erro. Tente novamente.',
        });
      }
    }
  };

  return (
    <div className="min-h-screen flex items-center justify-center p-4 bg-background">
      <div className="w-full max-w-md space-y-8">
        {/* Logo */}
        <div className="text-center space-y-4">
          <div className="flex justify-center">
            <img 
              src={logoOficial} 
              alt="Operação Alfa Logo" 
              className="w-24 h-24 opacity-90"
            />
          </div>
          <div>
            <p className="text-muted-foreground">Crie sua conta gratuita</p>
          </div>
        </div>

        {/* Registration Form */}
        <div className="card-tactical p-6 space-y-6">
          {/* Error Alert */}
          {error && (
            <Alert variant="destructive">
              <AlertCircle className="h-4 w-4" />
              <AlertDescription>{error}</AlertDescription>
            </Alert>
          )}

          <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
            <div className="space-y-2">
              <Label htmlFor="name">Nome completo</Label>
              <Input
                id="name"
                type="text"
                placeholder="Seu nome"
                {...register('name')}
                className={errors.name ? 'border-destructive' : ''}
                disabled={isLoading}
              />
              {errors.name && (
                <p className="text-sm text-destructive">{errors.name.message}</p>
              )}
            </div>

            <div className="space-y-2">
              <Label htmlFor="email">Email</Label>
              <Input
                id="email"
                type="email"
                placeholder="seu@email.com"
                {...register('email')}
                className={errors.email ? 'border-destructive' : ''}
                disabled={isLoading}
              />
              {errors.email && (
                <p className="text-sm text-destructive">{errors.email.message}</p>
              )}
            </div>

            <div className="space-y-2">
              <Label htmlFor="password">Senha</Label>
              <div className="relative">
                <Input
                  id="password"
                  type={showPassword ? 'text' : 'password'}
                  placeholder="••••••••"
                  {...register('password')}
                  className={errors.password ? 'border-destructive pr-10' : 'pr-10'}
                  disabled={isLoading}
                />
                <button
                  type="button"
                  onClick={() => setShowPassword(!showPassword)}
                  className="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                  disabled={isLoading}
                >
                  {showPassword ? <EyeOff size={20} /> : <Eye size={20} />}
                </button>
              </div>
              {errors.password && (
                <p className="text-sm text-destructive">{errors.password.message}</p>
              )}
            </div>

            <div className="space-y-2">
              <Label htmlFor="confirmPassword">Confirmar senha</Label>
              <div className="relative">
                <Input
                  id="confirmPassword"
                  type={showConfirmPassword ? 'text' : 'password'}
                  placeholder="••••••••"
                  {...register('confirmPassword')}
                  className={errors.confirmPassword ? 'border-destructive pr-10' : 'pr-10'}
                  disabled={isLoading}
                />
                <button
                  type="button"
                  onClick={() => setShowConfirmPassword(!showConfirmPassword)}
                  className="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                  disabled={isLoading}
                >
                  {showConfirmPassword ? <EyeOff size={20} /> : <Eye size={20} />}
                </button>
              </div>
              {errors.confirmPassword && (
                <p className="text-sm text-destructive">{errors.confirmPassword.message}</p>
              )}
            </div>

            <Button 
              type="submit" 
              variant="tactical"
              className="w-full"
              disabled={isLoading}
            >
              {isLoading ? (
                <>
                  <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                  Criando conta...
                </>
              ) : (
                'Criar conta gratuita'
              )}
            </Button>
          </form>

          <div className="space-y-4 text-center">
            <div className="border-t border-border pt-4">
              <p className="text-sm text-muted-foreground mb-3">
                Já tem uma conta?
              </p>
              <Link to="/login">
                <Button 
                  variant="outline" 
                  className="w-full"
                >
                  Fazer login
                </Button>
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
