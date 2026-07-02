import { createRootRoute, createRoute, createRouter } from '@tanstack/react-router';
import { Layout } from '@/components/layout/Layout';
import { HomePage } from '@/pages/HomePage';
import { ListingPage } from '@/pages/ListingPage';
import { PropertyDetailPage } from '@/pages/PropertyDetailPage';
import { AgentsPage } from '@/pages/AgentsPage';
import { AgentDetailPage } from '@/pages/AgentDetailPage';
import { FavouritesPage } from '@/pages/FavouritesPage';
import { LoginPage } from '@/pages/LoginPage';
import { RegisterPage } from '@/pages/RegisterPage';
import { ResetPasswordRequestPage } from '@/pages/ResetPasswordRequestPage';
import { ResetPasswordPage } from '@/pages/ResetPasswordPage';
import { EmailVerifiedPage } from '@/pages/EmailVerifiedPage';
import { AccountPage } from '@/pages/AccountPage';
import { MyPropertiesPage } from '@/pages/MyPropertiesPage';
import { PropertyFormPage } from '@/pages/PropertyFormPage';
import { ContactPage } from '@/pages/ContactPage';
import { AboutPage } from '@/pages/AboutPage';
import { NotFoundPage } from '@/pages/NotFoundPage';

const rootRoute = createRootRoute({
  component: Layout,
  notFoundComponent: NotFoundPage,
});

const indexRoute = createRoute({ getParentRoute: () => rootRoute, path: '/', component: HomePage });

const buyRoute = createRoute({
  getParentRoute: () => rootRoute,
  path: '/buy',
  component: () => <ListingPage fixedListingType="sale" title="Properties for sale" subtitle="Find your next home to buy." />,
});
const saleRoute = createRoute({
  getParentRoute: () => rootRoute,
  path: '/sale',
  component: () => <ListingPage fixedListingType="sale" title="Properties for sale" subtitle="Find your next home to buy." />,
});
const rentRoute = createRoute({
  getParentRoute: () => rootRoute,
  path: '/rent',
  component: () => <ListingPage fixedListingType="rent" title="Properties for rent" subtitle="Discover rentals that fit your lifestyle." />,
});

const propertiesRoute = createRoute({
  getParentRoute: () => rootRoute,
  path: '/properties',
  component: () => <ListingPage title="All properties" subtitle="Browse every listing with filters and map view." />,
});
const propertyDetailRoute = createRoute({
  getParentRoute: () => rootRoute,
  path: '/properties/$slug',
  component: PropertyDetailPage,
});

const agentsRoute = createRoute({ getParentRoute: () => rootRoute, path: '/agents', component: AgentsPage });
const agentDetailRoute = createRoute({ getParentRoute: () => rootRoute, path: '/agents/$id', component: AgentDetailPage });

const favouritesRoute = createRoute({ getParentRoute: () => rootRoute, path: '/favourites', component: FavouritesPage });

const loginRoute = createRoute({ getParentRoute: () => rootRoute, path: '/login', component: LoginPage });
const registerRoute = createRoute({ getParentRoute: () => rootRoute, path: '/register', component: RegisterPage });
const resetRequestRoute = createRoute({ getParentRoute: () => rootRoute, path: '/reset-password', component: ResetPasswordRequestPage });
const resetTokenRoute = createRoute({ getParentRoute: () => rootRoute, path: '/reset-password/$token', component: ResetPasswordPage });
const emailVerifiedRoute = createRoute({ getParentRoute: () => rootRoute, path: '/email-verified', component: EmailVerifiedPage });

const accountRoute = createRoute({ getParentRoute: () => rootRoute, path: '/account', component: AccountPage });

const myPropertiesRoute = createRoute({ getParentRoute: () => rootRoute, path: '/my/properties', component: MyPropertiesPage });
const myPropertyNewRoute = createRoute({
  getParentRoute: () => rootRoute,
  path: '/my/properties/new',
  component: () => <PropertyFormPage mode="new" />,
});
const myPropertyEditRoute = createRoute({
  getParentRoute: () => rootRoute,
  path: '/my/properties/$id/edit',
  component: () => <PropertyFormPage mode="edit" />,
});

const contactRoute = createRoute({ getParentRoute: () => rootRoute, path: '/contact', component: ContactPage });
const aboutRoute = createRoute({ getParentRoute: () => rootRoute, path: '/about', component: AboutPage });

const routeTree = rootRoute.addChildren([
  indexRoute,
  buyRoute,
  saleRoute,
  rentRoute,
  propertiesRoute,
  propertyDetailRoute,
  agentsRoute,
  agentDetailRoute,
  favouritesRoute,
  loginRoute,
  registerRoute,
  resetRequestRoute,
  resetTokenRoute,
  emailVerifiedRoute,
  accountRoute,
  myPropertiesRoute,
  myPropertyNewRoute,
  myPropertyEditRoute,
  contactRoute,
  aboutRoute,
]);

export const router = createRouter({
  routeTree,
  defaultPreload: 'intent',
  defaultNotFoundComponent: NotFoundPage,
});
