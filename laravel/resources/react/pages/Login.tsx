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

const loginSchema = z.object({
  email: z.string().email('Email inválido'),
  password: z.string().min(6, 'Senha deve ter pelo menos 6 caracteres'),
});

type LoginForm = z.infer<typeof loginSchema>;

export default function Login() {
  const navigate = useNavigate();
  const { login, isLoading, error, clearError, user } = useAuthStore();
  const [showPassword, setShowPassword] = useState(false);

  const {
    register,
    handleSubmit,
    formState: { errors },
    setError,
  } = useForm<LoginForm>({
    resolver: zodResolver(loginSchema),
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

  const onSubmit = async (data: LoginForm) => {
    try {
      clearError();
      await login(data.email, data.password);
      
      toast({
        title: 'Login realizado com sucesso!',
        description: 'Bem-vindo à Operação Alfa.',
      });
      navigate('/simulados');
    } catch (err: any) {
      // Handle validation errors from API
      if (err.response?.status === 422) {
        const validationErrors = err.response.data.errors;
        if (validationErrors) {
          Object.keys(validationErrors).forEach((key) => {
            setError(key as keyof LoginForm, {
              type: 'manual',
              message: validationErrors[key][0],
            });
          });
        }
      } else if (err.response?.status === 401) {
        toast({
          variant: 'destructive',
          title: 'Erro no login',
          description: 'Email ou senha incorretos.',
        });
      } else {
        toast({
          variant: 'destructive',
          title: 'Erro no login',
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
              className="opacity-90"
              style={{ maxWidth: '100%', height: '300px' }}
            />
          </div>
          <div>
            <p className="text-muted-foreground">Acesse sua conta</p>
          </div>
        </div>

        {/* Login Form */}
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

            <Button 
              type="submit" 
              variant="tactical"
              className="w-full"
              disabled={isLoading}
            >
              {isLoading ? (
                <>
                  <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                  Entrando...
                </>
              ) : (
                'Entrar'
              )}
            </Button>
          </form>

          <div className="space-y-4 text-center">
            <Link 
              to="/esqueci-senha" 
              className="text-sm text-primary hover:text-primary-600 transition-colors"
            >
              Esqueci minha senha
            </Link>

            <div className="border-t border-border pt-4">
              <p className="text-sm text-muted-foreground mb-3">
                Ainda não tem uma conta?
              </p>
              <Link to="/cadastro">
                <Button 
                  variant="outline" 
                  className="w-full border-primary text-primary hover:bg-primary hover:text-primary-foreground"
                >
                  Criar conta gratuita
                </Button>
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}