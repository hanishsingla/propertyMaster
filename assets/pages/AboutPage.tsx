import { Link } from '@tanstack/react-router';
import { Building2, ShieldCheck, Users, Heart } from 'lucide-react';
import { Button } from '@/components/ui/button';

export function AboutPage() {
  return (
    <div>
      <section className="bg-gradient-to-br from-primary to-indigo-700 py-16 text-primary-foreground">
        <div className="container">
          <h1 className="text-4xl font-bold">About PropertyMaster</h1>
          <p className="mt-4 max-w-2xl text-lg text-primary-foreground/80">
            We connect buyers, renters and trusted agents on a single modern platform, making it easier
            than ever to find and list great properties.
          </p>
        </div>
      </section>

      <section className="container grid gap-6 py-12 sm:grid-cols-2 lg:grid-cols-4">
        {[
          { icon: Building2, title: 'Thousands of listings', text: 'A wide range of homes for sale and rent.' },
          { icon: ShieldCheck, title: 'Verified agents', text: 'Work with trusted, verified professionals.' },
          { icon: Users, title: 'For everyone', text: 'Whether buying, renting or listing — we have you covered.' },
          { icon: Heart, title: 'Save favourites', text: 'Keep track of the properties you love.' },
        ].map((f) => (
          <div key={f.title} className="rounded-xl border bg-card p-6 shadow-sm">
            <span className="flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10 text-primary">
              <f.icon className="h-6 w-6" />
            </span>
            <h3 className="mt-4 font-semibold">{f.title}</h3>
            <p className="mt-1 text-sm text-muted-foreground">{f.text}</p>
          </div>
        ))}
      </section>

      <section className="container pb-16 text-center">
        <h2 className="text-2xl font-bold">Ready to find your next home?</h2>
        <div className="mt-4 flex justify-center gap-3">
          <Link to="/properties">
            <Button size="lg">Browse properties</Button>
          </Link>
          <Link to="/contact">
            <Button size="lg" variant="outline">
              Contact us
            </Button>
          </Link>
        </div>
      </section>
    </div>
  );
}
