async function fetchHealth(): Promise<{ ok?: boolean }> {
  const base = process.env.NEXT_PUBLIC_API_BASE_URL ?? 'http://127.0.0.1:8080';
  try {
    const res = await fetch(`${base}/health`, { next: { revalidate: 30 } });
    if (!res.ok) return {};
    return (await res.json()) as { ok?: boolean };
  } catch {
    return {};
  }
}

type User = { id: number; email: string; name: string };

async function fetchUsers(prefix: string): Promise<User[]> {
  const base = process.env.NEXT_PUBLIC_API_BASE_URL ?? 'http://127.0.0.1:8080';
  try {
    const url = new URL('/users', base);
    url.searchParams.set('prefix', prefix);
    url.searchParams.set('limit', '5');
    const res = await fetch(url.toString(), { next: { revalidate: 30 } });
    if (!res.ok) return [];
    const data = (await res.json()) as { users?: User[] };
    return data.users ?? [];
  } catch {
    return [];
  }
}

export default async function HomePage() {
  const health = await fetchHealth();
  const users = await fetchUsers('Al');

  return (
    <main style={{ padding: '2rem', maxWidth: 720 }}>
      <h1>Company stack demo</h1>
      <p>
        Sample <strong>Next.js</strong> page calling a PHP API health check. Push a branch and watch{' '}
        <a href="https://www.qodo.ai/">Qodo</a>
        ’s PR Agent comment with description, review, and improvement suggestions.
      </p>
      <section>
        <h2>API health</h2>
        <p data-testid="health-status">{health.ok ? 'OK' : 'Unreachable (start PHP server)'}</p>
      </section>
      <section>
        <h2>Sample users (prefix: Al)</h2>
        {users.length === 0 ? (
          <p>No users found. Seed the DB to test this endpoint.</p>
        ) : (
          <ul>
            {users.map((user) => (
              <li key={user.id}>
                {user.name} - {user.email}
              </li>
            ))}
          </ul>
        )}
      </section>
    </main>
  );
}
