import { Link } from '@tanstack/react-router';
import { Home } from 'lucide-react';

export function Footer() {
  return (
    <footer className="mt-16 border-t bg-secondary/40">
      <div className="container grid gap-8 py-12 md:grid-cols-4">
        <div className="space-y-3">
          <div className="flex items-center gap-2 font-bold">
            <span className="flex h-7 w-7 items-center justify-center rounded-md bg-primary text-primary-foreground">
              <Home className="h-4 w-4" />
            </span>
            PropertyMaster
          </div>
          <p className="text-sm text-muted-foreground">
            Find your next home. Buy, rent and list properties with trusted agents.
          </p>
        </div>
        <FooterCol
          title="Explore"
          links={[
            { to: '/buy', label: 'Buy' },
            { to: '/rent', label: 'Rent' },
            { to: '/properties', label: 'All Listings' },
            { to: '/agents', label: 'Agents' },
          ]}
        />
        <FooterCol
          title="Company"
          links={[
            { to: '/about', label: 'About' },
            { to: '/contact', label: 'Contact' },
          ]}
        />
        <FooterCol
          title="Account"
          links={[
            { to: '/login', label: 'Login' },
            { to: '/register', label: 'Register' },
            { to: '/favourites', label: 'Favourites' },
          ]}
        />
      </div>
      <div className="border-t py-4 text-center text-xs text-muted-foreground">
        © {new Date().getFullYear()} PropertyMaster. All rights reserved.
      </div>
    </footer>
  );
}

function FooterCol({ title, links }: { title: string; links: { to: string; label: string }[] }) {
  return (
    <div>
      <h4 className="mb-3 text-sm font-semibold">{title}</h4>
      <ul className="space-y-2">
        {links.map((l) => (
          <li key={l.to}>
            <Link to={l.to} className="text-sm text-muted-foreground hover:text-foreground">
              {l.label}
            </Link>
          </li>
        ))}
      </ul>
    </div>
  );
}
