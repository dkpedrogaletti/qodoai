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

export default async function HomePage() {
  const health = await fetchHealth();

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
    </main>
  );
}
