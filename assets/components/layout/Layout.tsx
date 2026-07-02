import { Outlet, ScrollRestoration } from '@tanstack/react-router';
import { Header } from './Header';
import { Footer } from './Footer';
import { VerifyBanner } from './VerifyBanner';

export function Layout() {
  return (
    <div className="flex min-h-screen flex-col">
      <VerifyBanner />
      <Header />
      <main className="flex-1">
        <Outlet />
      </main>
      <Footer />
      <ScrollRestoration />
    </div>
  );
}
