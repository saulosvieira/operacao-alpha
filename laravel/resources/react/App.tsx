import { lazy, Suspense } from "react";
import { Toaster } from "@/components/ui/toaster";
import { Toaster as Sonner } from "@/components/ui/sonner";
import { TooltipProvider } from "@/components/ui/tooltip";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { BrowserRouter, Routes, Route, Navigate } from "react-router-dom";
import { useAuthStore } from "@/stores/authStore";

// Lazy load pages
const Index = lazy(() => import("./pages/Index"));
const Login = lazy(() => import("./pages/Login"));
const Cadastro = lazy(() => import("./pages/Cadastro"));
const Simulados = lazy(() => import("./pages/Simulados"));
const Carreiras = lazy(() => import("./pages/Carreiras"));
const CarreiraSimulados = lazy(() => import("./pages/CarreiraSimulados"));
const Ranking = lazy(() => import("./pages/Ranking"));
const Desempenho = lazy(() => import("./pages/Desempenho"));
const Aprovados = lazy(() => import("./pages/Aprovados"));
const Assinar = lazy(() => import("./pages/Assinar"));
const Conta = lazy(() => import("./pages/Conta"));
const NotFound = lazy(() => import("./pages/NotFound"));
const Simulado = lazy(() => import("./pages/Simulado"));
const ExecutarSimulado = lazy(() => import("./pages/ExecutarSimulado"));

// Loading fallback component
function PageLoader() {
  return (
    <div className="flex items-center justify-center min-h-screen">
      <div className="flex flex-col items-center gap-4">
        <div className="w-12 h-12 border-4 border-primary border-t-transparent rounded-full animate-spin" />
        <p className="text-sm text-muted-foreground">Carregando...</p>
      </div>
    </div>
  );
}

const queryClient = new QueryClient();

// Protected Route wrapper
function ProtectedRoute({ children }: { children: React.ReactNode }) {
  const user = useAuthStore(state => state.user);
  
  if (!user) {
    return <Navigate to="/login" replace />;
  }
  
  return <>{children}</>;
}

const App = () => (
  <QueryClientProvider client={queryClient}>
    <TooltipProvider>
      <Toaster />
      <Sonner />
      <BrowserRouter>
        <Suspense fallback={<PageLoader />}>
          <Routes>
            {/* Public Routes */}
            <Route path="/login" element={<Login />} />
            <Route path="/cadastro" element={<Cadastro />} />
            <Route path="/assinar" element={<Assinar />} />
            
            {/* Protected Routes */}
            <Route path="/" element={
              <ProtectedRoute>
                <Index />
              </ProtectedRoute>
            } />
            <Route path="/simulados" element={
              <ProtectedRoute>
                <Simulados />
              </ProtectedRoute>
            } />
            <Route path="/simulado/:simuladoId" element={
              <ProtectedRoute>
                <Simulado />
              </ProtectedRoute>
            } />
            <Route path="/simulado/:simuladoId/executar/:tentativaId" element={
              <ProtectedRoute>
                <ExecutarSimulado />
              </ProtectedRoute>
            } />
            <Route path="/ranking" element={
              <ProtectedRoute>
                <Ranking />
              </ProtectedRoute>
            } />
            
            <Route path="/carreiras" element={
              <ProtectedRoute>
                <Carreiras />
              </ProtectedRoute>
            } />
            <Route path="/carreiras/:carreiraId/simulados" element={
              <ProtectedRoute>
                <CarreiraSimulados />
              </ProtectedRoute>
            } />
            <Route path="/desempenho" element={
              <ProtectedRoute>
                <Desempenho />
              </ProtectedRoute>
            } />
            <Route path="/aprovados" element={
              <ProtectedRoute>
                <Aprovados />
              </ProtectedRoute>
            } />
            <Route path="/conta" element={
              <ProtectedRoute>
                <Conta />
              </ProtectedRoute>
            } />
            
            {/* Redirects */}
            <Route path="/index" element={<Navigate to="/" replace />} />
            
            {/* 404 */}
            <Route path="*" element={<NotFound />} />
          </Routes>
        </Suspense>
      </BrowserRouter>
    </TooltipProvider>
  </QueryClientProvider>
);

export default App;
