import { TopBar } from './TopBar';
import { BottomNav } from './BottomNav';

interface AppLayoutProps {
  children: React.ReactNode;
}

export function AppLayout({ children }: AppLayoutProps) {
  return (
    <div className="min-h-screen bg-background">
      <TopBar />
      <main className="pb-20"> {/* Bottom nav height */}
        {children}
      </main>
      <BottomNav />
    </div>
  );
}