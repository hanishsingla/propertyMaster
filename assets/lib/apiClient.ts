import type { Violation } from './types';

export class ApiError extends Error {
  code: string;
  status: number;
  violations?: Violation[];

  constructor(status: number, code: string, message: string, violations?: Violation[]) {
    super(message);
    this.name = 'ApiError';
    this.status = status;
    this.code = code;
    this.violations = violations;
  }
}

/** In-memory CSRF token. Seeded from the <meta> tag, refreshed via /api/csrf. */
let csrfToken: string | null = null;

function readMetaToken(): string | null {
  const el = document.querySelector('meta[name="csrf-token"]');
  return el?.getAttribute('content') ?? null;
}

export function initCsrf(): void {
  csrfToken = readMetaToken();
}

export async function refreshCsrf(): Promise<void> {
  try {
    const res = await fetch('/api/csrf', { credentials: 'include' });
    if (res.ok) {
      const body = (await res.json()) as { token: string };
      csrfToken = body.token;
    }
  } catch {
    /* ignore; keep existing token */
  }
}

const MUTATION_METHODS = new Set(['POST', 'PATCH', 'PUT', 'DELETE']);

interface RequestOptions {
  method?: string;
  body?: unknown;
  /** When true, body is a FormData and Content-Type is left to the browser. */
  formData?: boolean;
  signal?: AbortSignal;
}

async function request<T>(path: string, opts: RequestOptions = {}): Promise<T> {
  const method = opts.method ?? 'GET';
  const headers: Record<string, string> = {};

  if (MUTATION_METHODS.has(method) && csrfToken) {
    headers['X-CSRF-Token'] = csrfToken;
  }

  let body: BodyInit | undefined;
  if (opts.formData) {
    body = opts.body as FormData;
  } else if (opts.body !== undefined) {
    headers['Content-Type'] = 'application/json';
    body = JSON.stringify(opts.body);
  }

  const res = await fetch(path, {
    method,
    headers,
    body,
    credentials: 'include',
    signal: opts.signal,
  });

  if (res.status === 204) {
    return undefined as T;
  }

  let payload: unknown = null;
  const text = await res.text();
  if (text) {
    try {
      payload = JSON.parse(text);
    } catch {
      payload = null;
    }
  }

  if (!res.ok) {
    const errObj = (payload as { error?: { code?: string; message?: string; violations?: Violation[] } })?.error;
    throw new ApiError(
      res.status,
      errObj?.code ?? (res.status === 401 ? 'unauthenticated' : 'error'),
      errObj?.message ?? res.statusText ?? 'Request failed',
      errObj?.violations,
    );
  }

  return payload as T;
}

export const apiClient = {
  get: <T>(path: string, signal?: AbortSignal) => request<T>(path, { method: 'GET', signal }),
  post: <T>(path: string, body?: unknown) => request<T>(path, { method: 'POST', body }),
  patch: <T>(path: string, body?: unknown) => request<T>(path, { method: 'PATCH', body }),
  delete: <T>(path: string, body?: unknown) => request<T>(path, { method: 'DELETE', body }),
  postForm: <T>(path: string, form: FormData) => request<T>(path, { method: 'POST', body: form, formData: true }),
};

export function isUnauthenticated(err: unknown): boolean {
  return err instanceof ApiError && err.status === 401;
}

/** Build a query string from a filter object, skipping empty values. */
export function buildQuery(params: Record<string, string | number | boolean | undefined | null>): string {
  const search = new URLSearchParams();
  for (const [key, value] of Object.entries(params)) {
    if (value === undefined || value === null || value === '') continue;
    search.set(key, String(value));
  }
  const str = search.toString();
  return str ? `?${str}` : '';
}
