import { useEffect, useRef, useState } from 'react';
import { Link, useNavigate } from '@tanstack/react-router';
import { Home, Heart, User, LogOut, Building2, Menu, X, ChevronDown } from 'lucide-react';
import { apiClient } from '@/lib/apiClient';
import { useAuth } from '@/features/auth/AuthProvider';
import { useToast } from '@/components/ui/toast';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';

const NAV = [
  { to: '/buy', label: 'Buy' },
  { to: '/rent', label: 'Rent' },
  { to: '/properties', label: 'All Listings' },
  { to: '/agents', label: 'Agents' },
  { to: '/about', label: 'About' },
  { to: '/contact', label: 'Contact' },
] as const;

export function Header() {
  const { user, isAuthenticated, isAgent, refresh } = useAuth();
  const navigate = useNavigate();
  const toast = useToast();
  const [menuOpen, setMenuOpen] = useState(false);
  const [mobileOpen, setMobileOpen] = useState(false);
  const menuRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const onClick = (e: MouseEvent) => {
      if (menuRef.current && !menuRef.current.contains(e.target as Node)) setMenuOpen(false);
    };
    document.addEventListener('mousedown', onClick);
    return () => document.removeEventListener('mousedown', onClick);
  }, []);

  const logout = async () => {
    try {
      await apiClient.post('/api/auth/logout');
    } catch {
      /* ignore */
    }
    await refresh();
    setMenuOpen(false);
    toast.success('Signed out');
    navigate({ to: '/' });
  };

  return (
    <header className="sticky top-0 z-40 border-b bg-background/95 backdrop-blur">
      <div className="container flex h-16 items-center justify-between gap-4">
        <Link to="/" className="flex items-center gap-2 font-bold text-lg">
          <span className="flex h-8 w-8 items-center justify-center rounded-md bg-primary text-primary-foreground">
            <Home className="h-5 w-5" />
          </span>
          <span>PropertyMaster</span>
        </Link>

        <nav className="hidden items-center gap-1 md:flex">
          {NAV.map((item) => (
            <Link
              key={item.to}
              to={item.to}
              className="rounded-md px-3 py-2 text-sm font-medium text-muted-foreground transition-colors hover:bg-secondary hover:text-foreground [&.active]:text-primary"
            >
              {item.label}
            </Link>
          ))}
        </nav>

        <div className="flex items-center gap-2">
          {isAuthenticated ? (
            <div className="relative hidden md:block" ref={menuRef}>
              <button
                onClick={() => setMenuOpen((o) => !o)}
                className="flex items-center gap-2 rounded-full border p-1 pr-2 transition-colors hover:bg-secondary"
                aria-haspopup="menu"
                aria-expanded={menuOpen}
              >
                <Avatar user={user} />
                <span className="max-w-[8rem] truncate text-sm font-medium">{user?.name}</span>
                <ChevronDown className="h-4 w-4 text-muted-foreground" />
              </button>
              {menuOpen && (
                <div
                  role="menu"
                  className="absolute right-0 mt-2 w-52 animate-fade-in overflow-hidden rounded-lg border bg-card py-1 shadow-lg"
                >
                  <MenuLink to="/account" icon={User} label="Account" onClick={() => setMenuOpen(false)} />
                  <MenuLink to="/favourites" icon={Heart} label="Favourites" onClick={() => setMenuOpen(false)} />
                  {isAgent && (
                    <MenuLink
                      to="/my/properties"
                      icon={Building2}
                      label="My Properties"
                      onClick={() => setMenuOpen(false)}
                    />
                  )}
                  <button
                    role="menuitem"
                    onClick={logout}
                    className="flex w-full items-center gap-2 px-4 py-2 text-left text-sm text-destructive hover:bg-secondary"
                  >
                    <LogOut className="h-4 w-4" /> Logout
                  </button>
                </div>
              )}
            </div>
          ) : (
            <div className="hidden items-center gap-2 md:flex">
              <Button variant="ghost" size="sm" onClick={() => navigate({ to: '/login' })}>
                Login
              </Button>
              <Button size="sm" onClick={() => navigate({ to: '/register' })}>
                Register
              </Button>
            </div>
          )}

          <button
            className="rounded-md p-2 md:hidden"
            onClick={() => setMobileOpen((o) => !o)}
            aria-label="Toggle menu"
          >
            {mobileOpen ? <X className="h-6 w-6" /> : <Menu className="h-6 w-6" />}
          </button>
        </div>
      </div>

      {mobileOpen && (
        <div className="border-t md:hidden">
          <nav className="container flex flex-col py-2">
            {NAV.map((item) => (
              <Link
                key={item.to}
                to={item.to}
                onClick={() => setMobileOpen(false)}
                className="rounded-md px-3 py-2 text-sm font-medium hover:bg-secondary"
              >
                {item.label}
              </Link>
            ))}
            <div className="my-2 border-t" />
            {isAuthenticated ? (
              <>
                <Link to="/account" onClick={() => setMobileOpen(false)} className="rounded-md px-3 py-2 text-sm hover:bg-secondary">
                  Account
                </Link>
                <Link to="/favourites" onClick={() => setMobileOpen(false)} className="rounded-md px-3 py-2 text-sm hover:bg-secondary">
                  Favourites
                </Link>
                {isAgent && (
                  <Link to="/my/properties" onClick={() => setMobileOpen(false)} className="rounded-md px-3 py-2 text-sm hover:bg-secondary">
                    My Properties
                  </Link>
                )}
                <button onClick={logout} className="rounded-md px-3 py-2 text-left text-sm text-destructive hover:bg-secondary">
                  Logout
                </button>
              </>
            ) : (
              <div className="flex gap-2 px-3 py-2">
                <Button variant="outline" size="sm" className="flex-1" onClick={() => { setMobileOpen(false); navigate({ to: '/login' }); }}>
                  Login
                </Button>
                <Button size="sm" className="flex-1" onClick={() => { setMobileOpen(false); navigate({ to: '/register' }); }}>
                  Register
                </Button>
              </div>
            )}
          </nav>
        </div>
      )}
    </header>
  );
}

function Avatar({ user }: { user: { name: string; avatarUrl: string | null } | null }) {
  if (user?.avatarUrl) {
    return <img src={user.avatarUrl} alt="" className="h-7 w-7 rounded-full object-cover" />;
  }
  const initial = user?.name?.charAt(0).toUpperCase() ?? '?';
  return (
    <span className="flex h-7 w-7 items-center justify-center rounded-full bg-primary text-xs font-semibold text-primary-foreground">
      {initial}
    </span>
  );
}

function MenuLink({
  to,
  icon: Icon,
  label,
  onClick,
}: {
  to: string;
  icon: typeof User;
  label: string;
  onClick: () => void;
}) {
  return (
    <Link
      to={to}
      role="menuitem"
      onClick={onClick}
      className={cn('flex items-center gap-2 px-4 py-2 text-sm hover:bg-secondary')}
    >
      <Icon className="h-4 w-4" /> {label}
    </Link>
  );
}
